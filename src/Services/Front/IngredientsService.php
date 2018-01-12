<?php

namespace InetStudio\Ingredients\Services\Front;

use League\Fractal\Manager;
use InetStudio\Ingredients\Models\IngredientModel;
use League\Fractal\Serializer\DataArraySerializer;
use InetStudio\Ingredients\Contracts\Services\IngredientsServiceContract;
use InetStudio\Ingredients\Transformers\Front\IngredientsSiteMapTransformer;
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

        $resource = (new IngredientsFeedItemsTransformer())->transformCollection($ingredients);

        return $this->serializeToArray($resource);
    }

    /**
     * Получаем информацию по ингредиентам для карты сайта.
     *
     * @return array
     */
    public function getSiteMapItems(): array
    {
        $ingredients = IngredientModel::select(['slug', 'created_at', 'status_id', 'updated_at'])
            ->whereHas('status', function ($statusQuery) {
                $statusQuery->whereIn('alias', ['seo_check', 'published']);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $resource = (new IngredientsSiteMapTransformer())->transformCollection($ingredients);

        return $this->serializeToArray($resource);
    }

    /**
     * Преобразовываем данные в массив.
     *
     * @param $resource
     *
     * @return array
     */
    private function serializeToArray($resource): array
    {
        $manager = new Manager();
        $manager->setSerializer(new DataArraySerializer());

        $transformation = $manager->createData($resource)->toArray();

        return $transformation['data'];
    }
}
