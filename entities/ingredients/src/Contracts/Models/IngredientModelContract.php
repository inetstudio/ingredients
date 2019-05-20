<?php

namespace InetStudio\IngredientsPackage\Ingredients\Contracts\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use InetStudio\Rating\Contracts\Models\Traits\RateableContract;
use InetStudio\AdminPanel\Base\Contracts\Models\BaseModelContract;
use InetStudio\Favorites\Contracts\Models\Traits\FavoritableContract;

/**
 * Interface IngredientModelContract.
 */
interface IngredientModelContract extends BaseModelContract, Auditable, FavoritableContract, HasMedia, RateableContract
{
}
