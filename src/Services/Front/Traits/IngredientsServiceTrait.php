<?php

namespace InetStudio\Ingredients\Services\Front\Traits;

/**
 * Trait IngredientsServiceTrait.
 */
trait IngredientsServiceTrait
{
    /**
     * Получаем объекты по тегу.
     *
     * @param string $ingredientSlug
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByIngredient(string $ingredientSlug, array $params = [])
    {
        return $this->repository->getItemsByIngredient($ingredientSlug, $params);
    }
}
