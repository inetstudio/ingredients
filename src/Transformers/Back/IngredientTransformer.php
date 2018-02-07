<?php

namespace InetStudio\Ingredients\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Ingredients\Models\IngredientModel;

/**
 * Class IngredientTransformer
 * @package InetStudio\Ingredients\Transformers\Back
 */
class IngredientTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param IngredientModel $ingredient
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModel $ingredient)
    {
        return [
            'id' => (int) $ingredient->id,
            'title' => $ingredient->title,
            'status' => view('admin.module.ingredients::back.partials.datatables.status', [
                'status' => $ingredient->status,
            ])->render(),
            'created_at' => (string) $ingredient->created_at,
            'updated_at' => (string) $ingredient->updated_at,
            'publish_date' => (string) $ingredient->publish_date,
            'actions' => view('admin.module.ingredients::back.partials.datatables.actions', [
                'id' => $ingredient->id,
                'href' => $ingredient->href,
            ])->render(),
        ];
    }
}
