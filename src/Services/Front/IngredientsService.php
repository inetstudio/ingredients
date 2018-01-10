<?php

namespace InetStudio\Ingredients\Services\Front;

use League\Fractal\Manager;
use InetStudio\Ingredients\Models\IngredientModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Services\IngredientsServiceContract;
use InetStudio\Ingredients\Transformers\Front\IngredientsFeedItemsTransformer;

/**
 * Class IngredientsService
 * @package InetStudio\Ingredients\Services\Front
 */
class IngredientsService implements IngredientsServiceContract
{
    /**
     * Получаем информацию по ингредиентам для фида.
     *
     * @return array
     */
    public function getFeedItems(): array
    {
        $ingredients = IngredientModel::whereHas('status', function ($statusQuery) {
            $statusQuery->whereIn('alias', ['seo_check', 'published']);
        })->whereNotNull('publish_date')->orderBy('publish_date', 'desc')->limit(500)->get();

        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());
        $resource = (new IngredientsFeedItemsTransformer())->transformCollection($ingredients);

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
