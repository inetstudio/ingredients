<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back;

use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\AdminPanel\Base\Contracts\Services\BaseServiceContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Interface ItemsServiceContract.
 */
interface ItemsServiceContract extends BaseServiceContract
{
    /**
     * Сохраняем модель.
     *
     * @param  array  $data
     * @param  int  $id
     *
     * @return IngredientModelContract
     *
     * @throws BindingResolutionException
     */
    public function save(array $data, int $id): IngredientModelContract;
}
