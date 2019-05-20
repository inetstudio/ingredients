<?php

namespace InetStudio\IngredientsPackage\Ingredients\Services\Front\Traits;

/**
 * Trait IngredientsServiceTrait.
 */
trait IngredientsServiceTrait
{
    /**
     * Получаем объекты по ингредиенту.
     *
     * @param  string  $slug
     * @param  array  $params
     *
     * @return mixed
     */
    public function getItemsByIngredient(string $slug, array $params = [])
    {
        return $this->model
            ->buildQuery($params)
            ->withIngredients($slug);
    }
}
