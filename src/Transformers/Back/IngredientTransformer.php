<?php

namespace InetStudio\Ingredients\Transformers\Back;

use League\Fractal\TransformerAbstract;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Transformers\Back\IngredientTransformerContract;

/**
 * Class IngredientTransformer.
 */
class IngredientTransformer extends TransformerAbstract implements IngredientTransformerContract
{
    /**
     * Подготовка данных для отображения в таблице.
     *
     * @param IngredientModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModelContract $item)
    {
        return [
            'id' => (int) $item->id,
            'title' => $item->title,
            'status' => view('admin.module.ingredients::back.partials.datatables.status', [
                'status' => $item->status,
            ])->render(),
            'created_at' => (string) $item->created_at,
            'updated_at' => (string) $item->updated_at,
            'publish_date' => (string) $item->publish_date,
            'actions' => view('admin.module.ingredients::back.partials.datatables.actions', [
                'id' => $item->id,
                'href' => $item->href,
            ])->render(),
        ];
    }
}
