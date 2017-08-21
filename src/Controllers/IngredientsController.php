<?php

namespace InetStudio\Ingredients\Controllers;

use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use InetStudio\Ingredients\Models\IngredientModel;
use InetStudio\Ingredients\Requests\SaveIngredientRequest;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Ingredients\Transformers\IngredientTransformer;

/**
 * Контроллер для управления ингредиентами.
 *
 * Class ContestByTagStatusesController
 */
class IngredientsController extends Controller
{
    /**
     * Список ингредиентов.
     *
     * @param Datatables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Datatables $dataTable)
    {
        $table = $dataTable->getHtmlBuilder();

        $table->columns($this->getColumns());
        $table->ajax($this->getAjaxOptions());
        $table->parameters($this->getTableParameters());

        return view('admin.module.ingredients::pages.ingredients.index', compact('table'));
    }

    /**
     * Свойства колонок datatables.
     *
     * @return array
     */
    private function getColumns()
    {
        return [
            ['data' => 'title', 'name' => 'title', 'title' => 'Заголовок'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
            ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
        ];
    }

    /**
     * Свойства ajax datatables.
     *
     * @return array
     */
    private function getAjaxOptions()
    {
        return [
            'url' => route('back.ingredients.data'),
            'type' => 'POST',
            'data' => 'function(data) { data._token = $(\'meta[name="csrf-token"]\').attr(\'content\'); }',
        ];
    }

    /**
     * Свойства datatables.
     *
     * @return array
     */
    private function getTableParameters()
    {
        return [
            'paging' => true,
            'pagingType' => 'full_numbers',
            'searching' => true,
            'info' => false,
            'searchDelay' => 350,
            'language' => [
                'url' => asset('admin/js/plugins/datatables/locales/russian.json'),
            ],
        ];
    }

    /**
     * Datatables serverside.
     *
     * @return mixed
     */
    public function data()
    {
        $items = IngredientModel::query();

        return Datatables::of($items)
            ->setTransformer(new IngredientTransformer)
            ->escapeColumns(['actions'])
            ->make();
    }

    /**
     * Добавление ингредиента.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('admin.module.ingredients::pages.ingredients.form', [
            'item' => new IngredientModel(),
        ]);
    }

    /**
     * Создание ингредиента.
     *
     * @param SaveIngredientRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(SaveIngredientRequest $request)
    {
        return $this->save($request);
    }

    /**
     * Редактирование ингредиента.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id = null)
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {

            return view('admin.module.ingredients::pages.ingredients.form', [
                'item' => $item,
            ]);
        } else {
            abort(404);
        }
    }

    /**
     * Обновление ингредиента.
     *
     * @param SaveIngredientRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(SaveIngredientRequest $request, $id = null)
    {
        return $this->save($request, $id);
    }

    /**
     * Сохранение ингредиента.
     *
     * @param SaveIngredientRequest $request
     * @param null $id
     * @return \Illuminate\Http\RedirectResponse
     */
    private function save($request, $id = null)
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {
            $action = 'отредактирован';
        } else {
            $action = 'создан';
            $item = new IngredientModel();
        }

        $item->title = strip_tags($request->get('title'));
        $item->slug = strip_tags($request->get('slug'));
        $item->description = strip_tags($request->input('description.text'));
        $item->content = $request->input('content.text');
        $item->save();

        $this->saveMeta($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content']);

        Session::flash('success', 'Ингредиент «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.ingredients.edit', $item->fresh()->id));
    }

    /**
     * Сохраняем мета теги.
     *
     * @param IngredientModel $item
     * @param SaveIngredientRequest $request
     */
    private function saveMeta($item, $request)
    {
        if ($request->has('meta')) {
            foreach ($request->get('meta') as $key => $value) {
                $item->updateMeta($key, $value);
            }
        }
    }

    /**
     * Сохраняем изображения.
     *
     * @param IngredientModel $item
     * @param SaveIngredientRequest $request
     * @param array $images
     */
    private function saveImages($item, $request, $images)
    {
        foreach ($images as $name) {
            $properties = $request->get($name);

            if (isset($properties['images'])) {
                $item->clearMediaCollectionExcept($name, $properties['images']);

                foreach ($properties['images'] as $image) {
                    if ($image['id']) {
                        $media = $item->media->find($image['id']);
                        $media->custom_properties = $image['properties'];
                        $media->save();
                    } else {
                        $filename = $image['filename'];

                        $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image['tempname'];

                        $item->addMedia($file)
                            ->withCustomProperties($image['properties'])
                            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                            ->usingFileName($image['tempname'])
                            ->toMediaCollection($name, 'ingredients');
                    }
                }
            } else {
                if (isset($properties['tempname']) && isset($properties['filename'])) {
                    $image = $properties['tempname'];
                    $filename = $properties['filename'];

                    $item->clearMediaCollection($name);

                    array_forget($properties, ['tempname', 'temppath', 'filename']);
                    $properties = array_filter($properties);

                    $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image;

                    $item->addMedia($file)
                        ->withCustomProperties($properties)
                        ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                        ->usingFileName($image)
                        ->toMediaCollection($name, 'ingredients');
                } else {
                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);

                    if ($media) {
                        $media->custom_properties = $properties;
                        $media->save();
                    }
                }
            }
        }
    }

    /**
     * Удаление ингредиента.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null)
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {
            $item->delete();

            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
            ]);
        }
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSlug(Request $request)
    {
        $name = $request->get('name');
        $slug = SlugService::createSlug(IngredientModel::class, 'slug', $name);

        return response()->json($slug);
    }

    /**
     * Возвращаем ингредиенты для поля.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request)
    {
        $search = $request->get('q');
        $data = [];

        $data['items'] = IngredientModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();

        return response()->json($data);
    }
}
