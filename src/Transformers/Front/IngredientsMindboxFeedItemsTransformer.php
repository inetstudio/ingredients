<?php

namespace InetStudio\Ingredients\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsMindboxFeedItemsTransformerContract;

/**
 * Class IngredientsMindboxFeedItemsTransformer.
 */
class IngredientsMindboxFeedItemsTransformer extends TransformerAbstract implements IngredientsMindboxFeedItemsTransformerContract
{
    /**
     * Подготовка данных для отображения в фиде.
     *
     * @param IngredientModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModelContract $item): array
    {
        $picture = '';

        try {
            $picture = asset($item->getFirstMediaUrl('preview', 'preview_3_2'));
        } catch (\Exception $e) {}

        return [
            'id' => $item->id,
            'available' => $item->status->classifiers->contains('alias', 'status_display_for_users') ? 'true' : 'false',
            'picture' => $picture,
            'name' => $item->title,
            'url' => $item->href,
            'description' => html_entity_decode(strip_tags($item->description)),
            'categories' => [],
            'tags' => ($item->tags->count() > 0) ? implode('|', $item->tags->pluck('name')->toArray()) : '',
            'type' => 'Ингредиент',
        ];
    }

    /**
     * Обработка коллекции статей.
     *
     * @param $items
     *
     * @return FractalCollection
     */
    public function transformCollection($items): FractalCollection
    {
        return new FractalCollection($items, $this);
    }
}
