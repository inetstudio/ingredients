<?php

namespace InetStudio\IngredientsPackage\Ingredients\Services\Front;

use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Front\FeedsServiceContract;

/**
 * Class FeedsService.
 */
class FeedsService extends BaseService implements FeedsServiceContract
{
    /**
     * FeedsService constructor.
     *
     * @param  IngredientModelContract  $model
     */
    public function __construct(IngredientModelContract $model)
    {
        parent::__construct($model);
    }
}
