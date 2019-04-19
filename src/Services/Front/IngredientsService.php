<?php

namespace InetStudio\Ingredients\Services\Front;

use League\Fractal\Manager;
use Illuminate\Support\Collection;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\AdminPanel\Services\Front\BaseService;
use InetStudio\TagsPackage\Tags\Services\Front\Traits\TagsServiceTrait;
use InetStudio\AdminPanel\Services\Front\Traits\SlugsServiceTrait;
use InetStudio\Favorites\Services\Front\Traits\FavoritesServiceTrait;
use InetStudio\Ingredients\Contracts\Services\Front\IngredientsServiceContract;

/**
 * Class IngredientsService.
 */
class IngredientsService extends BaseService implements IngredientsServiceContract
{
    use TagsServiceTrait;
    use SlugsServiceTrait;
    use FavoritesServiceTrait;

    /**
     * IngredientsService constructor.
     */
    public function __construct()
    {
        parent::__construct(app()->make('InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract'));
    }

    /**
     * Возвращаем объекты, привязанные к материалам.
     *
     * @param Collection $materials
     *
     * @return Collection
     */
    public function getItemsByMaterials(Collection $materials): Collection
    {
        return $materials->map(function ($item) {
            return (isset($item['ingredients'])) ? $item['ingredients'] : [];
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
