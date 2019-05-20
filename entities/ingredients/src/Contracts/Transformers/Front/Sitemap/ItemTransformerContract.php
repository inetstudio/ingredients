<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Front\Sitemap;

use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Interface ItemTransformerContract.
 */
interface ItemTransformerContract
{
    /**
     * Трансформация данных.
     *
     * @param  IngredientModelContract  $item
     *
     * @return array
     */
    public function transform(IngredientModelContract $item): array;

    /**
     * Обработка коллекции объектов.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection;
}
