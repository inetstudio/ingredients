<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Back\Utility;

use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Interface SuggestionTransformerContract.
 */
interface SuggestionTransformerContract
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
