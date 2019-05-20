<?php

namespace InetStudio\IngredientsPackage\Ingredients\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Contracts\Container\BindingResolutionException;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Trait HasIngredients.
 */
trait HasIngredients
{
    use HasIngredientsCollection;

    /**
     * The Queued Ingredients.
     *
     * @var array
     */
    protected $queuedIngredients = [];

    /**
     * Get Ingredient class name.
     *
     * @return string
     *
     * @throws BindingResolutionException
     */
    public function getIngredientClassName(): string
    {
        $model = app()->make(IngredientModelContract::class);

        return get_class($model);
    }

    /**
     * Get all attached ingredients to the model.
     *
     * @return MorphToMany
     *
     * @throws BindingResolutionException
     */
    public function ingredients(): MorphToMany
    {
        $className = $this->getIngredientClassName();

        return $this->morphToMany($className, 'ingredientable')->withTimestamps();
    }

    /**
     * Attach the given ingredient(s) to the model.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @throws BindingResolutionException
     */
    public function setIngredientsAttribute($ingredients): void
    {
        if (! $this->exists) {
            $this->queuedIngredients = $ingredients;

            return;
        }

        $this->attachIngredients($ingredients);
    }

    /**
     * Boot the ingredientgable trait for a model.
     */
    public static function bootHasIngredients()
    {
        static::created(
            function (Model $ingredientgableModel) {
                if ($ingredientgableModel->queuedIngredients) {
                    $ingredientgableModel->attachIngredients($ingredientgableModel->queuedIngredients);
                    $ingredientgableModel->queuedIngredients = [];
                }
            }
        );

        static::deleted(
            function (Model $ingredientgableModel) {
                $ingredientgableModel->syncIngredients(null);
            }
        );
    }

    /**
     * Get the ingredient list.
     *
     * @param  string  $keyColumn
     *
     * @return array
     *
     * @throws BindingResolutionException
     */
    public function ingredientList(string $keyColumn = 'slug'): array
    {
        return $this->ingredients()->pluck('name', $keyColumn)->toArray();
    }

    /**
     * Scope query with all the given ingredients.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAllIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = $this->isIngredientsStringBased($ingredients)
            ? $ingredients : $this->hydrateIngredients($ingredients)->pluck($column);

        collect($ingredients)->each(
            function ($ingredient) use ($query, $column) {
                $query->whereHas(
                    'ingredients',
                    function (Builder $query) use ($ingredient, $column) {
                        return $query->where($column, $ingredient);
                    }
                );
            }
        );

        return $query;
    }

    /**
     * Scope query with any of the given ingredients.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithAnyIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = $this->isIngredientsStringBased($ingredients)
            ? $ingredients : $this->hydrateIngredients($ingredients)->pluck($column);

        return $query->whereHas(
            'ingredients',
            function (Builder $query) use ($ingredients, $column) {
                $query->whereIn($column, (array) $ingredients);
            }
        );
    }

    /**
     * Scope query with any of the given ingredients.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        return $this->scopeWithAnyIngredients($query, $ingredients, $column);
    }

    /**
     * Scope query without the given ingredients.
     *
     * @param  Builder  $query
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     * @param  string  $column
     *
     * @return Builder
     *
     * @throws BindingResolutionException
     */
    public function scopeWithoutIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = $this->isIngredientsStringBased($ingredients)
            ? $ingredients : $this->hydrateIngredients($ingredients)->pluck($column);

        return $query->whereDoesntHave(
            'ingredients',
            function (Builder $query) use ($ingredients, $column) {
                $query->whereIn($column, (array) $ingredients);
            }
        );
    }

    /**
     * Scope query without any ingredients.
     *
     * @param  Builder  $query
     *
     * @return Builder
     */
    public function scopeWithoutAnyIngredients(Builder $query): Builder
    {
        return $query->doesntHave('ingredients');
    }

    /**
     * Attach the given ingredient(s) to the model.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function attachIngredients($ingredients): self
    {
        $this->setIngredients($ingredients, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given ingredient(s) to the model.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract|null  $ingredients
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function syncIngredients($ingredients): self
    {
        $this->setIngredients($ingredients, 'sync');

        return $this;
    }

    /**
     * Detach the given ingredient(s) from the model.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return $this
     *
     * @throws BindingResolutionException
     */
    public function detachIngredients($ingredients): self
    {
        $this->setIngredients($ingredients, 'detach');

        return $this;
    }

    /**
     * Set the given ingredient(s) to the model.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     * @param  string  $action
     *
     * @throws BindingResolutionException
     */
    protected function setIngredients($ingredients, string $action): void
    {
        // Fix exceptional event name
        $event = $action === 'syncWithoutDetaching' ? 'attach' : $action;

        // Hydrate Ingredients
        $ingredients = $this->hydrateIngredients($ingredients)->pluck('id')->toArray();

        // Fire the Ingredient syncing event
        static::$dispatcher->dispatch('inetstudio.ingredients.'.$event.'ing', [$this, $ingredients]);

        // Set Ingredients
        $this->ingredients()->$action($ingredients);

        // Fire the Ingredient synced event
        static::$dispatcher->dispatch('inetstudio.ingredients.'.$event.'ed', [$this, $ingredients]);
    }

    /**
     * Hydrate ingredients.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return Collection
     *
     * @throws BindingResolutionException
     */
    protected function hydrateIngredients($ingredients): Collection
    {
        $isIngredientsStringBased = $this->isIngredientsStringBased($ingredients);
        $isIngredientsIntBased = $this->isIngredientsIntBased($ingredients);
        $field = $isIngredientsStringBased ? 'slug' : 'id';
        $className = $this->getIngredientClassName();

        return $isIngredientsStringBased || $isIngredientsIntBased
            ? $className::query()->whereIn($field, (array) $ingredients)->get() : collect($ingredients);
    }
}
