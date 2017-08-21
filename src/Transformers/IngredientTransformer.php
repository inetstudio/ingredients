<?php

namespace Inetstudio\Ingredients\Transformers;

use League\Fractal\TransformerAbstract;
use InetStudio\Ingredients\Models\IngredientModel;

class IngredientTransformer extends TransformerAbstract
{
    /**
     * @param IngredientModel $ingredient
     * @return array
     */
    public function transform(IngredientModel $ingredient)
    {
        return [
            'id' => (int) $ingredient->id,
            'title' => $ingredient->title,
            'created_at' => (string) $ingredient->created_at,
            'updated_at' => (string) $ingredient->updated_at,
            'actions' => view('admin.module.ingredients::pages.ingredients.datatables.actions', ['id' => $ingredient->id])->render(),
        ];
    }
}
