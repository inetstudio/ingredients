<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Front\Sitemap;

use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

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
