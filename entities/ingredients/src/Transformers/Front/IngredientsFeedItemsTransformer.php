<?php

namespace InetStudio\IngredientsPackage\Ingredients\Transformers\Front;

use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Collection as FractalCollection;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Front\IngredientsFeedItemsTransformerContract;

/**
 * Class IngredientsFeedItemsTransformer.
 */
class IngredientsFeedItemsTransformer extends TransformerAbstract implements IngredientsFeedItemsTransformerContract
{
    /**
     * Подготовка данных для отображения в фиде.
     *
     * @param  IngredientModelContract  $item
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModelContract $item): array
    {
        return [
            'title' => $item->title,
            'author' => $this->getAuthor($item),
            'link' => $item->href,
            'pubdate' => $item->publish_date,
            'description' => $item->description,
            'content' => $item->content,
            'enclosure' => [],
            'category' => 'Ингредиенты',
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

    /**
     * Получаем автора материала.
     *
     * @param  IngredientModelContract  $item
     *
     * @return string
     */
    protected function getAuthor(IngredientModelContract $item): string
    {
        foreach ($item->revisionHistory as $history) {
            if ($history->key == 'created_at' && ! $history->old_value) {
                $user = $history->userResponsible();

                return ($user) ? $user->name : '';
            }
        }

        return '';
    }
}
