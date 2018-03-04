<?php

namespace InetStudio\Ingredients\Services\Back;

use League\Fractal\Manager;
use Illuminate\Support\Facades\Session;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract;
use InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract;
use InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract;

/**
 * Class IngredientsService.
 */
class IngredientsService implements IngredientsServiceContract
{
    /**
     * @var IngredientsRepositoryContract
     */
    private $repository;

    /**
     * IngredientsService constructor.
     *
     * @param IngredientsRepositoryContract $repository
     */
    public function __construct(IngredientsRepositoryContract $repository)
    {
        $this->repository = $repository;
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
     * Получаем объекты по списку id.
     *
     * @param array|int $ids
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientsByIDs($ids, bool $returnBuilder = false)
    {
        return $this->repository->getItemsByIDs($ids, $returnBuilder);
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
        $item = $this->repository->save($request, $id);

        app()->make('InetStudio\Meta\Contracts\Services\Back\MetaServiceContract')
            ->attachToObject($request, $item);

        $images = (config('ingredients.images.conversions.ingredient')) ? array_keys(config('ingredients.images.conversions.ingredient')) : [];
        app()->make('InetStudio\Uploads\Contracts\Services\Back\ImagesServiceContract')
            ->attachToObject($request, $item, $images, 'ingredients', 'ingredient');

        app()->make('InetStudio\Tags\Contracts\Services\Back\TagsServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Products\Contracts\Services\Back\ProductsServiceContract')
            ->attachToObject($request, $item);

        app()->make('InetStudio\Classifiers\Contracts\Services\Back\ClassifiersServiceContract')
            ->attachToObject($request, $item);

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
        $items = $this->repository->searchItemsByField('title', $search);

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
            $item->detachIngredients($item->tags);
        }
    }
}
