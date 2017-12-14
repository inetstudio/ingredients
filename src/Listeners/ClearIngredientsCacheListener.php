<?php

namespace InetStudio\Ingredients\Listeners;

use Illuminate\Support\Facades\Cache;

class ClearIngredientsCacheListener
{
    /**
     * ClearIngredientsCacheListener constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param $event
     */
    public function handle($event): void
    {
        Cache::tags(['materials'])->flush();
        Cache::tags(['ingredients'])->flush();
    }
}
