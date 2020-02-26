<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use InetStudio\AdminPanel\Base\Contracts\Models\BaseModelContract;

/**
 * Interface IngredientModelContract.
 */
interface IngredientModelContract extends BaseModelContract, Auditable, HasMedia
{
}
