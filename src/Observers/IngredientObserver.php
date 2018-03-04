<?php

namespace InetStudio\Ingredients\Observers;

use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Observers\IngredientObserverContract;

/**
 * Class IngredientObserver.
 */
class IngredientObserver implements IngredientObserverContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    protected $services;

    /**
     * IngredientObserver constructor.
     */
    public function __construct()
    {
        $this->services['ingredientsObserver'] = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsObserverServiceContract');
    }

    /**
     * Событие "объект создается".
     *
     * @param IngredientModelContract $item
     */
    public function creating(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->creating($item);
    }

    /**
     * Событие "объект создан".
     *
     * @param IngredientModelContract $item
     */
    public function created(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->created($item);
    }

    /**
     * Событие "объект обновляется".
     *
     * @param IngredientModelContract $item
     */
    public function updating(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->updating($item);
    }

    /**
     * Событие "объект обновлен".
     *
     * @param IngredientModelContract $item
     */
    public function updated(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->updated($item);
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param IngredientModelContract $item
     */
    public function deleting(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->deleting($item);
    }

    /**
     * Событие "объект удален".
     *
     * @param IngredientModelContract $item
     */
    public function deleted(IngredientModelContract $item): void
    {
        $this->services['ingredientsObserver']->deleted($item);
    }
}
