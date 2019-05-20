<?php

namespace InetStudio\IngredientsPackage\Ingredients\Services\Front;

use Illuminate\Support\Collection;
use InetStudio\AdminPanel\Base\Services\BaseService;
use InetStudio\AdminPanel\Base\Services\Traits\SlugsServiceTrait;
use InetStudio\Favorites\Services\Front\Traits\FavoritesServiceTrait;
use InetStudio\TagsPackage\Tags\Services\Front\Traits\TagsServiceTrait;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Front\ItemsServiceContract;

/**
 * Class ItemsService.
 */
class ItemsService extends BaseService implements ItemsServiceContract
{
    use TagsServiceTrait;
    use SlugsServiceTrait;
    use FavoritesServiceTrait;

    /**
     * @var string
     */
    protected $favoritesType = 'ingredient';

    /**
     * ItemsService constructor.
     *
     * @param  IngredientModelContract  $model
     */
    public function __construct(IngredientModelContract $model)
    {
        parent::__construct($model);
    }

    /**
     * Возвращаем объекты, привязанные к материалам.
     *
     * @param  Collection  $materials
     *
     * @return Collection
     */
    public function getItemsByMaterials(Collection $materials): Collection
    {
        return $materials->map(
            function ($item) {
                return $item['ingredients'] ?? [];
            }
        )->filter()->collapse()->unique('id');
    }
}
