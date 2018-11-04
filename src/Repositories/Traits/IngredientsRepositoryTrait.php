<?php

namespace InetStudio\Ingredients\Repositories\Traits;

/**
 * Trait IngredientsRepositoryTrait.
 */
trait IngredientsRepositoryTrait
{
    /**
     * Получаем объекты по ингредиентам.
     *
     * @param string $slug
     * @param array $params
     *
     * @return mixed
     */
    public function getItemsByIngredient(string $slug, array $params = [])
    {
        $builder = $this->getItemsQuery($params)
            ->withIngredients($slug);

        return $builder->get();
    }
}
