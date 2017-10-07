<?php

namespace InetStudio\Ingredients\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use InetStudio\AdminPanel\Traits\DatatablesTrait;
use InetStudio\Ingredients\Models\IngredientModel;
use InetStudio\Tags\Traits\TagsManipulationsTrait;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\AdminPanel\Traits\MetaManipulationsTrait;
use InetStudio\AdminPanel\Traits\ImagesManipulationsTrait;
use InetStudio\Ingredients\Requests\SaveIngredientRequest;
use InetStudio\Products\Traits\ProductsManipulationsTrait;
use InetStudio\Ingredients\Transformers\IngredientTransformer;

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

    /**
     * Список ингредиентов.
     *
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(DataTables $dataTable)
    {
        $table = $this->generateTable($dataTable, 'ingredients', 'index');

        return view('admin.module.ingredients::pages.index', compact('table'));
    }

    /**
     * Datatables serverside.
     *
     * @return mixed
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
     * @param DataTables $dataTable
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(DataTables $dataTable)
    {
        $table = $this->generateTable($dataTable, 'products', 'embedded');

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
     * @param DataTables $dataTable
     * @param null $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(DataTables $dataTable, $id = null)
    {
        if (! is_null($id) && $id > 0 && $item = IngredientModel::find($id)) {
            $table = $this->generateTable($dataTable, 'products', 'embedded');

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
        $item->status_id = ($request->filled('status_id')) ? $request->get('status_id') : 1;
        $item->save();

        $this->saveMeta($item, $request);
        $this->saveTags($item, $request);
        $this->saveProducts($item, $request);
        $this->saveImages($item, $request, ['og_image', 'preview', 'content'], 'ingredients');

        \Event::fire('inetstudio.ingredients.cache.clear');

        Session::flash('success', 'Ингредиент «'.$item->title.'» успешно '.$action);

        return redirect()->to(route('back.ingredients.edit', $item->fresh()->id));
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

            \Event::fire('inetstudio.ingredients.cache.clear');

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
