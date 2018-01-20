<?php

namespace InetStudio\Ingredients\Http\Controllers\Back;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use InetStudio\Ingredients\Models\IngredientModel;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Ingredients\Events\ModifyIngredientEvent;
use InetStudio\Ingredients\Transformers\Back\IngredientTransformer;
use InetStudio\Ingredients\Http\Requests\Back\SaveIngredientRequest;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\DatatablesTrait;
use InetStudio\Meta\Http\Controllers\Back\Traits\MetaManipulationsTrait;
use InetStudio\Tags\Http\Controllers\Back\Traits\TagsManipulationsTrait;
use InetStudio\AdminPanel\Http\Controllers\Back\Traits\ImagesManipulationsTrait;
use InetStudio\Products\Http\Controllers\Back\Traits\ProductsManipulationsTrait;
use InetStudio\Classifiers\Http\Controllers\Back\Traits\ClassifiersManipulationsTrait;

/**
 * Контроллер для управления ингредиентами.
 *
 * Class ContestByTagStatusesController
 */
class IngredientsController extends Controller
{
    use DatatablesTrait;
    use MetaManipulationsTrait;
    use TagsManipulationsTrait;
    use ImagesManipulationsTrait;
    use ProductsManipulationsTrait;
    use ClassifiersManipulationsTrait;

    /**
     * Список ингредиентов.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function index(): View
    {
        $table = $this->generateTable('ingredients', 'index');

        return view('admin.module.ingredients::back.pages.index', compact('table'));
    }

    /**
     * DataTables ServerSide.
     *
     * @return mixed
     * @throws \Exception
     */
    public function data()
    {
        $items = IngredientModel::with('status');

        return DataTables::of($items)
            ->setTransformer(new IngredientTransformer)
            ->rawColumns(['status', 'actions'])
            ->make();
    }

    /**
     * Добавление ингредиента.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function create(): View
    {
        $table = $this->generateTable('products', 'embedded');

        return view('admin.module.ingredients::back.pages.form', [
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
    public function store(SaveIngredientRequest $request): RedirectResponse
    {
        return $this->save($request);
    }

    /**
     * Редактирование ингредиента.
     *
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function edit($id = null): View
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {
            $table = $this->generateTable('products', 'embedded');

            return view('admin.module.ingredients::back.pages.form', [
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
    public function update(SaveIngredientRequest $request, $id = null): RedirectResponse
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
    private function save($request, $id = null): RedirectResponse
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
        $item->status_id = ($request->filled('status_id')) ? $request->get('status_id') : 1;
        $item->publish_date = ($request->filled('publish_date')) ? date('Y-m-d H:i', \DateTime::createFromFormat('!d.m.Y H:i', $request->get('publish_date'))->getTimestamp()) : null;
        $item->save();

        $this->saveMeta($item, $request);
        $this->saveTags($item, $request);
        $this->saveClassifiers($item, $request);
        $this->saveProducts($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content'], 'ingredients');

        // Обновление поискового индекса.
        $item->searchable();

        event(new ModifyIngredientEvent($item));

        Session::flash('success', 'Ингредиент «'.$item->title.'» успешно '.$action);

        return response()->redirectToRoute('back.ingredients.edit', [
            $item->fresh()->id,
        ]);
    }

    /**
     * Удаление ингредиента.
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id = null): JsonResponse
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {
            $item->delete();

            event(new ModifyIngredientEvent($item));

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
    public function getSlug(Request $request): JsonResponse
    {
        $name = $request->get('name');
        $slug = ($name) ? SlugService::createSlug(IngredientModel::class, 'slug', $name) : '';

        return response()->json($slug);
    }

    /**
     * Возвращаем ингредиенты для поля.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSuggestions(Request $request): JsonResponse
    {
        $data = [];

        if ($request->filled('type') && $request->get('type') == 'autocomplete') {
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
                    ],
                ];
            }
        } else {
            $search = $request->get('q');

            $data['items'] = IngredientModel::select(['id', 'title as name'])->where('title', 'LIKE', '%'.$search.'%')->get()->toArray();
        }

        return response()->json($data);
    }
}
