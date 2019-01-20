<?php

namespace InetStudio\Ingredients\Repositories;

use InetStudio\AdminPanel\Repositories\BaseRepository;
use InetStudio\Tags\Repositories\Traits\TagsRepositoryTrait;
use InetStudio\AdminPanel\Repositories\Traits\SlugsRepositoryTrait;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Favorites\Repositories\Traits\FavoritesRepositoryTrait;
use InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract;

/**
 * Class IngredientsRepository.
 */
class IngredientsRepository extends BaseRepository implements IngredientsRepositoryContract
{
    use TagsRepositoryTrait;
    use SlugsRepositoryTrait;
    use FavoritesRepositoryTrait;

    /**
     * @var string
     */
    protected $favoritesType = 'ingredient';

    /**
     * IngredientsRepository constructor.
     *
     * @param IngredientModelContract $model
     */
    public function __construct(IngredientModelContract $model)
    {
        $this->model = $model;

        $this->defaultColumns = ['id', 'title', 'slug'];
        $this->relations = [
            'meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            },

            'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk', 'mime_type', 'custom_properties', 'responsive_images']);
            },

            'tags' => function ($query) {
                $query->select(['id', 'name', 'slug']);
            },

            'counters' => function ($query) {
                $query->select(['countable_id', 'countable_type', 'type', 'counter']);
            },

            'products' => function ($query) {
                $query->select(['id', 'title', 'brand'])
                    ->with(['media' => function ($query) {
                        $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
                    }, 'links' => function ($linksQuery) {
                        $linksQuery->select(['id', 'product_id', 'link']);
                    }]);
            },

            'status' => function ($query) {
                $query->select(['id', 'name', 'alias']);
            },
        ];
    }
}
