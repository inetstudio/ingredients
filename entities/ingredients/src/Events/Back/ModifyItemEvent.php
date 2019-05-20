<?php

namespace InetStudio\IngredientsPackage\Ingredients\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Events\Back\ModifyItemEventContract;

/**
 * Class ModifyItemEvent.
 */
class ModifyItemEvent implements ModifyItemEventContract
{
    use SerializesModels;

    /**
     * @var IngredientModelContract
     */
    public $item;

    /**
     * ModifyItemEvent constructor.
     *
     * @param  IngredientModelContract  $item
     */
    public function __construct(IngredientModelContract $item)
    {
        $this->item = $item;
    }
}
