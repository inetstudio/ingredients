<?php

namespace InetStudio\Ingredients\Traits;

use InetStudio\Ingredients\Models\IngredientModel;

trait IngredientsManipulationsTrait
{
    /**
     * Сохраняем ингредиенты.
     *
     * @param $item
     * @param $request
     */
    private function saveIngredients($item, $request)
    {
        if ($request->has('ingredients')) {
            $item->syncIngredients(IngredientModel::whereIn('id', (array) $request->get('ingredients'))->get());
        } else {
            $item->detachIngredients($item->categories);
        }
    }
}
