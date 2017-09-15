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

        $table->columns($this->getColumns('ingredients'));
        $table->ajax($this->getAjaxOptions('ingredients'));
        $table->parameters($this->getTableParameters());

        return view('admin.module.ingredients::pages.index', compact('table'));
    }

    /**
     * Свойства колонок datatables.
     *
     * @param $model
     * @return array
     */
    private function getColumns($model)
    {
        if ($model == 'ingredients') {
            return [
                ['data' => 'title', 'name' => 'title', 'title' => 'Заголовок'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Дата создания'],
                ['data' => 'updated_at', 'name' => 'updated_at', 'title' => 'Дата обновления'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ];
        } elseif ($model == 'products') {
            return [
                ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'orderable' => false, 'searchable' => false, 'visible' => false],
                ['data' => 'preview', 'name' => 'preview', 'title' => 'Изображение', 'orderable' => false, 'searchable' => false],
                ['data' => 'brand', 'name' => 'brand', 'title' => 'Бренд'],
                ['data' => 'title', 'name' => 'title', 'title' => 'Название'],
                ['data' => 'description', 'name' => 'description', 'title' => 'Описание'],
                ['data' => 'actions', 'name' => 'actions', 'title' => 'Действия', 'orderable' => false, 'searchable' => false],
            ];
        }
    }

    /**
     * Свойства ajax datatables.
     *
     * @param $model
     * @param $type
     * @return array
     */
    private function getAjaxOptions($model, $type = '')
    {
        return [
            'url' => (! $type) ? route('back.'.$model.'.data') : route('back.'.$model.'.data', ['type' => $type]),
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
     * @param Datatables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Datatables $dataTable)
    {
        $table = $dataTable->getHtmlBuilder();

        $table->columns($this->getColumns('products'));
        $table->ajax($this->getAjaxOptions('products', 'embedded'));
        $table->parameters($this->getTableParameters());

        return view('admin.module.ingredients::pages.form', [
            'item' => new IngredientModel(),
            'productsTable' => $table,
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
     * @param Datatables $dataTable
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Datatables $dataTable, $id = null)
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {

            $table = $dataTable->getHtmlBuilder();

            $table->columns($this->getColumns('products'));
            $table->ajax($this->getAjaxOptions('products', 'embedded'));
            $table->parameters($this->getTableParameters());

            return view('admin.module.ingredients::pages.form', [
                'item' => $item,
                'productsTable' => $table,
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
        $this->saveProducts($item, $request);
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
     * Сохраняем продукты.
     *
     * @param IngredientModel $item
     * @param SaveIngredientRequest $request
     */
    private function saveProducts($item, $request)
    {
        if ($request->has('products')) {
            $ids = [];

            foreach ($request->get('products') as $product) {
                $ids[] = $product['id'];
            }

            $item->syncProducts($ids)->get();
        } else {
            $item->detachProducts($item->products);
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

                        $media = $item->addMedia($file)
                            ->withCustomProperties($image['properties'])
                            ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                            ->usingFileName($image['tempname'])
                            ->toMediaCollection($name, 'ingredients');
                    }

                    $item->update([
                        $name => str_replace($image['src'], '/img/' . $media->id, $item[$name]),
                    ]);
                }
            } else {
                $manipulations = [];

                if (isset($properties['crop']) and config('ingredients.images.conversions')) {
                    foreach ($properties['crop'] as $key => $cropJSON) {
                        $cropData = json_decode($cropJSON, true);

                        foreach (config('ingredients.images.conversions.'.$name.'.'.$key) as $conversion) {
                            $manipulations[$conversion['name']] = [
                                'manualCrop' => implode(',', [
                                    round($cropData['width']),
                                    round($cropData['height']),
                                    round($cropData['x']),
                                    round($cropData['y']),
                                ]),
                            ];
                        }
                    }
                }

                if (isset($properties['tempname']) && isset($properties['filename'])) {
                    $image = $properties['tempname'];
                    $filename = $properties['filename'];

                    $item->clearMediaCollection($name);

                    array_forget($properties, ['tempname', 'temppath', 'filename']);
                    $properties = array_filter($properties);

                    $file = Storage::disk('temp')->getDriver()->getAdapter()->getPathPrefix().$image;

                    $media = $item->addMedia($file)
                        ->withCustomProperties($properties)
                        ->usingName(pathinfo($filename, PATHINFO_FILENAME))
                        ->usingFileName($image)
                        ->toMediaCollection($name, 'ingredients');

                    $media->manipulations = $manipulations;
                    $media->save();
                } else {
                    $properties = array_filter($properties);

                    $media = $item->getFirstMedia($name);

                    if ($media) {
                        $media->custom_properties = $properties;
                        $media->manipulations = $manipulations;
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
        if ($request->has('type') && $request->get('type') == 'autocomplete') {
            $search = $request->get('query');
            $data['suggestions'] = [];

            $ingredients = IngredientModel::where('title', 'LIKE', '%'.$search.'%')->get();

            foreach ($ingredients as $ingredient) {
                $data['suggestions'][] = [
                    'value' => $ingredient->title,
                    'data' => [
                        'id' => $ingredient->id,
                        'title' => $ingredient->title,
                        'href' => url($ingredient->href),
                        'preview' => ($ingredient->getFirstMedia('preview')) ? url($ingredient->getFirstMedia('preview')->getUrl('preview_default')) : '',
                    ]
                ];
            }
        } else {
            $search = $request->get('q');
            $data = [];

            $data['items'] = IngredientModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();
        }

        return response()->json($data);
    }
}
