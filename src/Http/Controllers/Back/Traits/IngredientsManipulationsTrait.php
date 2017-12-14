<?php

namespace InetStudio\Ingredients\Http\Controllers\Back\Traits;

use InetStudio\Ingredients\Models\IngredientModel;

trait IngredientsManipulationsTrait
{
    /**
     * Сохраняем ингредиенты.
     *
     * @param $item
     * @param $request
     */
    private function saveIngredients($item, $request): void
    {
        if ($request->filled('ingredients')) {
            $item->syncIngredients(IngredientModel::whereIn('id', (array) $request->get('ingredients'))->get());
        } else {
            $item->detachIngredients($item->ingredients);
        }
    }
}
