<?php

namespace InetStudio\Ingredients\Services\Front;

use League\Fractal\Manager;
use Illuminate\Support\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Services\Front\IngredientsServiceContract;

/**
 * Class IngredientsService.
 */
class IngredientsService implements IngredientsServiceContract
{
    /**
     * @var
     */
    public $repository;

    /**
     * IngredientsService constructor.
     */
    public function __construct()
    {
        $this->repository = app()->make('InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract');
    }

    /**
     * Получаем объект по id.
     *
     * @param int $id
     *
     * @return mixed
     */
    public function getIngredientById(int $id = 0)
    {
        return $this->repository->getItemByID($id);
    }

    /**
     * Получаем объекты по id.
     *
     * @param $ids
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientsByIDs($ids, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsByIDs($ids, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientBySlug(string $slug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemBySlug($slug, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем объекты по тегу.
     *
     * @param string $tagSlug
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientByTag(string $tagSlug, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsByTag($tagSlug, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем сохраненные объекты пользователя.
     *
     * @param int $userID
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientsFavoritedByUser(int $userID, array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getItemsFavoritedByUser($userID, $extColumns, $with, $returnBuilder);
    }

    /**
     * Получаем все объекты.
     *
     * @param array $extColumns
     * @param array $with
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllIngredients(array $extColumns = [], array $with = [], bool $returnBuilder = false)
    {
        return $this->repository->getAllItems($extColumns, $with, $returnBuilder);
    }

    /**
     * Возвращаем объекты, привязанные к материалам.
     *
     * @param Collection $materials
     *
     * @return Collection
     */
    public function getIngredientsByMaterials(Collection $materials): Collection
    {
        return $materials->map(function ($item) {
            return (method_exists($item, 'ingredients')) ? $item->ingredients : [];
        })->filter()->collapse()->unique('id');
    }

    /**
     * Получаем информацию по ингредиентам для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getAllItems([], [], true)
            ->whereNotNull('publish_date')
            ->orderBy('publish_date', 'desc')
            ->limit(500)
            ->get();

        $resource = app()->make('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsFeedItemsTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }

    /**
     * Получаем информацию по статьям для фида mindbox.
     *
     * @return mixed
     */
    public function getMindboxFeedItems()
    {
        $items = $this->repository->getAllItems(['title', 'description', 'status_id'], ['media', 'categories', 'tags'], true)->get();

        $resource = app()->make('InetStudio\Ingredients\Contracts\Transformers\Front\Feeds\Mindbox\IngredientTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }

    /**
     * Получаем информацию по ингредиентам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $items = $this->repository->getAllItems();

        $resource = app()->make('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
