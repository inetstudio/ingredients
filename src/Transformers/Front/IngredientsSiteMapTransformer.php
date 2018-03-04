<?php

namespace InetStudio\Ingredients\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsSiteMapTransformerContract;

/**
 * Class IngredientsSiteMapTransformer.
 */
class IngredientsSiteMapTransformer extends TransformerAbstract implements IngredientsSiteMapTransformerContract
{
    /**
     * Подготовка данных для отображения в карте сайта.
     *
     * @param IngredientModelContract $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModelContract $item): array
    {
        return [
            'loc' => $item->href,
            'lastmod' => $item->updated_at->toW3cString(),
            'priority' => '0.9',
            'freq' => 'weekly',
        ];
    }

    /**
     * Обработка коллекции объектов.
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
