<?php

namespace InetStudio\Ingredients\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract;
use InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract;

/**
 * Class IngredientsService.
 */
class IngredientsService implements IngredientsServiceContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services = [];

    /**
     * @var
     */
    public $repository;

    /**
     * ArticlesService constructor.
     */
    public function __construct()
    {
        $this->services['meta'] = app()->make('InetStudio\Meta\Contracts\Services\Back\MetaServiceContract');
        $this->services['uploads'] = app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract');
        $this->services['tags'] = app()->make('InetStudio\Tags\Contracts\Services\Back\TagsServiceContract');
        $this->services['classifiers'] = app()->make('InetStudio\Classifiers\Contracts\Services\Back\ClassifiersServiceContract');
        $this->services['products'] = app()->make('InetStudio\Products\Contracts\Services\Back\ProductsServiceContract');
        $this->services['widgets'] = app()->make('InetStudio\Widgets\Contracts\Services\Back\WidgetsServiceContract');

        $this->repository = app()->make('InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract');
    }

    /**
     * Получаем объект модели.
     *
     * @param int $id
     *
     * @return IngredientModelContract
     */
    public function getIngredientObject(int $id = 0)
    {
        return $this->repository->getItemByID($id);
    }

    /**
     * Возвращаем объекты по списку id.
     *
     * @param array|int $ids
     * @param array $params
     *
     * @return mixed
     */
    public function getIngredientsByIDs($ids, array $params = [])
    {
        return $this->repository->getItemsByIDs($ids, $params);
    }

    /**
     * Сохраняем модель.
     *
     * @param SaveIngredientRequestContract $request
     * @param int $id
     *
     * @return IngredientModelContract
     */
    public function save(SaveIngredientRequestContract $request, int $id): IngredientModelContract
    {
        $action = ($id) ? 'отредактирован' : 'создан';
        $item = $this->repository->save($request->only($this->repository->getModel()->getFillable()), $id);

        $this->services['meta']->attachToObject($request, $item);

        $images = (config('ingredients.images.conversions.ingredient')) ? array_keys(config('ingredients.images.conversions.ingredient')) : [];
        $this->services['uploads']->attachToObject($request, $item, $images, 'ingredients', 'ingredient');

        $this->services['tags']->attachToObject($request, $item);
        $this->services['classifiers']->attachToObject($request, $item);
        $this->services['products']->attachToObject($request, $item);
        $this->services['widgets']->attachToObject($request, $item);

        $item->searchable();

        event(app()->makeWith('InetStudio\Ingredients\Contracts\Events\Back\ModifyIngredientEventContract', [
            'object' => $item,
        ]));

        Session::flash('success', 'Ингредиент «'.$item->title.'» успешно '.$action);

        return $item;
    }

    /**
     * Удаляем модель.
     *
     * @param $id
     *
     * @return bool
     */
    public function destroy(int $id): ?bool
    {
        return $this->repository->destroy($id);
    }

    /**
     * Получаем подсказки.
     *
     * @param string $search
     * @param $type
     *
     * @return array
     */
    public function getSuggestions(string $search, $type): array
    {
        $items = $this->repository->searchItems([['title', 'LIKE', '%'.$search.'%']]);

        $resource = (app()->makeWith('InetStudio\Ingredients\Contracts\Transformers\Back\SuggestionTransformerContract', [
            'type' => $type,
        ]))->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        if ($type && $type == 'autocomplete') {
            $data['suggestions'] = $transformation['data'];
        } else {
            $data['items'] = $transformation['data'];
        }

        return $data;
    }

    /**
     * Возвращаем статистику объектов по статусу.
     *
     * @return mixed
     */
    public function getIngredientsStatisticByStatus()
    {
        $ingredients = $this->repository->getItemsQuery([
                'relations' => ['status'],
            ])
            ->select(['status_id', DB::raw('count(*) as total')])
            ->groupBy('status_id')
            ->get();

        return $ingredients;
    }

    /**
     * Присваиваем ингредиенты объекту.
     *
     * @param $request
     *
     * @param $item
     */
    public function attachToObject($request, $item)
    {
        if ($request->filled('ingredients')) {
            $item->syncIngredients($this->repository->getItemsByIDs((array) $request->get('ingredients')));
        } else {
            $item->detachIngredients($item->ingredients);
        }
    }
}
