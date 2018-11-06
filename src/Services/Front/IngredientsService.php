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
     * @param array $params
     *
     * @return mixed
     */
    public function getIngredientsByIDs($ids, array $params = [])
    {
        return $this->repository->getItemsByIDs($ids, $params);
    }

    /**
     * Получаем объект по slug.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getIngredientBySlug(string $slug, array $params = [])
    {
        return $this->repository->getItemBySlug($slug, $params);
    }

    /**
     * Получаем объекты по тегу.
     *
     * @param string $tagSlug
     * @param array $params
     *
     * @return mixed
     */
    public function getIngredientsByTag(string $tagSlug, array $params = [])
    {
        return $this->repository->getItemsByTag($tagSlug, $params);
    }

    /**
     * Получаем сохраненные объекты пользователя.
     *
     * @param mixed $userID
     * @param array $params
     *
     * @return mixed
     */
    public function getIngredientsFavoritedByUser($userID, array $params = [])
    {
        return $this->repository->getItemsFavoritedByUser($userID, $params);
    }

    /**
     * Получаем все объекты.
     *
     * @param array $params
     *
     * @return mixed
     */
    public function getAllIngredients(array $params = [])
    {
        return $this->repository->getAllItems($params);
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
        $items = $this->repository->getItemsQuery([
                'columns' => ['title', 'description', 'content', 'publish_date'],
                'order' => ['publish_date' => 'desc'],
                'paging' => [
                    'page' => 0,
                    'limit' => 500,
                ],
            ])
            ->whereNotNull('publish_date')
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
        $items = $this->repository->getAllItems([
            'columns' => ['title', 'description', 'status_id'],
            'relations' => ['media', 'tags'],
        ]);

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
        $items = $this->repository->getAllItems([
            'columns' => ['created_at', 'updated_at'],
            'order' => ['created_at' => 'desc'],
        ]);

        $resource = app()->make('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsSiteMapTransformerContract')
            ->transformCollection($items);

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
