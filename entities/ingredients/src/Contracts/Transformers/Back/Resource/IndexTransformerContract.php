<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Back\Resource;

use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Interface IndexTransformerContract.
 */
interface IndexTransformerContract
{
    /**
     * Трансформация данных.
     *
     * @param  IngredientModelContract  $item
     *
     * @return array
     */
    public function transform(IngredientModelContract $item): array;
}
