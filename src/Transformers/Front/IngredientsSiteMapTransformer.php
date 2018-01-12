<?php

namespace InetStudio\Ingredients\Transformers\Front;

use League\Fractal\TransformerAbstract;
use InetStudio\Ingredients\Models\IngredientModel;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class IngredientsSiteMapTransformer
 * @package InetStudio\Ingredients\Transformers\Front
 */
class IngredientsSiteMapTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param IngredientModel $ingredient
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModel $ingredient): array
    {
        return [
            'loc' => $ingredient->href,
            'lastmod' => $ingredient->updated_at->toW3cString(),
            'priority' => '0.9',
            'freq' => 'weekly',
        ];
    }

    /**
     * Обработка коллекции ингредиентов.
     *
     * @param $ingredients
     *
     * @return FractalCollection
     */
    public function transformCollection($ingredients): FractalCollection
    {
        return new FractalCollection($ingredients, $this);
    }
}
