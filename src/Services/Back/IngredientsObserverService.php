<?php

namespace InetStudio\Ingredients\Services\Back;

use InetStudio\Ingredients\Contracts\Models\IngredientModelContract;
use InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract;
use InetStudio\Ingredients\Contracts\Services\Back\IngredientsObserverServiceContract;

/**
 * Class IngredientsObserverService.
 */
class IngredientsObserverService implements IngredientsObserverServiceContract
{
    /**
     * @var IngredientsRepositoryContract
     */
    private $repository;

    /**
     * IngredientsService constructor.
     *
     * @param IngredientsRepositoryContract $repository
     */
    public function __construct(IngredientsRepositoryContract $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Событие "объект создается".
     *
     * @param IngredientModelContract $item
     */
    public function creating(IngredientModelContract $item): void
    {
    }

    /**
     * Событие "объект создан".
     *
     * @param IngredientModelContract $item
     */
    public function created(IngredientModelContract $item): void
    {
    }

    /**
     * Событие "объект обновляется".
     *
     * @param IngredientModelContract $item
     */
    public function updating(IngredientModelContract $item): void
    {
    }

    /**
     * Событие "объект обновлен".
     *
     * @param IngredientModelContract $item
     */
    public function updated(IngredientModelContract $item): void
    {
    }

    /**
     * Событие "объект подписки удаляется".
     *
     * @param IngredientModelContract $item
     */
    public function deleting(IngredientModelContract $item): void
    {
    }

    /**
     * Событие "объект удален".
     *
     * @param IngredientModelContract $item
     */
    public function deleted(IngredientModelContract $item): void
    {
    }
}
