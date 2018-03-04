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
     * @param bool $returnBuilder
     *
     * @return mixed
     */
    public function getItemsByIngredient(string $slug, bool $returnBuilder = false)
    {
        $builder = $this->model::select(['id', 'title', 'description', 'slug'])
            ->with(['meta' => function ($query) {
                $query->select(['metable_id', 'metable_type', 'key', 'value']);
            }, 'media' => function ($query) {
                $query->select(['id', 'model_id', 'model_type', 'collection_name', 'file_name', 'disk']);
            }])
            ->withIngredients($slug);

        if ($returnBuilder) {
            return $builder;
        }

        return $builder->get();
    }
}
