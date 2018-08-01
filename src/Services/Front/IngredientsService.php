<?php

namespace InetStudio\Ingredients\Services\Front;

use League\Fractal\Manager;
use Illuminate\Support\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Services\Front\IngredientsServiceContract;
use InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract;

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
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getIngredientBySlug(string $slug, bool $returnBuilder = false)
    {
        return $this->repository->getItemBySlug($slug, $returnBuilder);
    }

    /**
     * Получаем все объекты.
     *
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getAllIngredients(bool $returnBuilder = false)
    {
        return $this->repository->getAllItems($returnBuilder);
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
        return $this->repository->getItemsByMaterials($materials);
    }

    /**
     * Получаем информацию по ингредиентам для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $items = $this->repository->getAllItems(true)
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
     * @return array
     */
    public function getMindboxFeedItems(): array
    {
        $items = $this->repository->getAllItems(['title', 'description', 'status_id'], ['media', 'categories', 'tags'], true)->get();

        $resource = app()->make('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsMindboxFeedItemsTransformerContract')
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
