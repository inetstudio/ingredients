<?php

namespace InetStudio\Ingredients\Events;

use Illuminate\Queue\SerializesModels;

class ModifyIngredientEvent
{
    use SerializesModels;

    public $object;

    /**
     * Create a new event instance.
     *
     * ModifyIngredientEvent constructor.
     * @param $object
     */
    public function __construct($object)
    {
        $this->object = $object;
    }
}
