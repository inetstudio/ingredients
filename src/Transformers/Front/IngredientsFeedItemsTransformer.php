<?php

namespace Inetstudio\Ingredients\Transformers\Front;

use League\Fractal\TransformerAbstract;
use InetStudio\Ingredients\Models\IngredientModel;
use League\Fractal\Resource\Collection as FractalCollection;

/**
 * Class IngredientsFeedItemsTransformer
 * @package Inetstudio\Ingredients\Transformers\Front
 */
class IngredientsFeedItemsTransformer extends TransformerAbstract
{
    /**
     * Подготовка данных для отображения в фиде.
     *
     * @param IngredientModel $ingredient
     *
     * @return array
     *
     * @throws \Throwable
     */
    public function transform(IngredientModel $ingredient): array
    {
        return [
            'title' => $ingredient->title,
            'author' => $this->getAuthor($ingredient),
            'link' => $ingredient->href,
            'pubdate' => $ingredient->publish_date,
            'description' => $ingredient->description,
            'content' => $ingredient->content,
            'enclosure' => [],
            'category' => 'Ингредиенты',
        ];
    }

    /**
     * Обработка коллекции статей.
     *
     * @param $ingredients
     *
     * @return FractalCollection
     */
    public function transformCollection($ingredients): FractalCollection
    {
        return new FractalCollection($ingredients, $this);
    }

    /**
     * Получаем автора статьи.
     *
     * @param IngredientModel $ingredient
     *
     * @return string
     */
    private function getAuthor(IngredientModel $ingredient): string
    {
        foreach ($ingredient->revisionHistory as $history) {
            if ($history->key == 'created_at' && ! $history->old_value) {
                $user = $history->userResponsible();

                return ($user) ? $user->name : '';
            }
        }

        return '';
    }
}
