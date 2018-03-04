<?php

namespace InetStudio\Ingredients\Events\Back;

use Illuminate\Queue\SerializesModels;
use InetStudio\Ingredients\Contracts\Events\Back\ModifyIngredientEventContract;

/**
 * Class ModifyIngredientEvent.
 */
class ModifyIngredientEvent implements ModifyIngredientEventContract
{
    use SerializesModels;

    public $object;

    /**
     * ModifyIngredientEvent constructor.
     *
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
