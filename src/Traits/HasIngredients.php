<?php

namespace InetStudio\Ingredients\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use InetStudio\Ingredients\Models\IngredientModel;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasIngredients
{
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
     */
    public static function getIngredientClassName(): string
    {
        return IngredientModel::class;
    }

    /**
     * Get all attached ingredients to the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function ingredients(): MorphToMany
    {
        return $this->morphToMany(static::getIngredientClassName(), 'ingredientable')->withTimestamps();
    }

    /**
     * Attach the given ingredient(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return void
     */
    public function setIngredientsAttribute($ingredients)
    {
        if (! $this->exists) {
            $this->queuedIngredients = $ingredients;

            return;
        }

        $this->attachIngredients($ingredients);
    }

    /**
     * Boot the ingredientable trait for a model.
     *
     * @return void
     */
    public static function bootIngredientable()
    {
        static::created(function (Model $ingredientableModel) {
            if ($ingredientableModel->queuedIngredients) {
                $ingredientableModel->attachIngredients($ingredientableModel->queuedIngredients);
                $ingredientableModel->queuedIngredients = [];
            }
        });

        static::deleted(function (Model $ingredientableModel) {
            $ingredientableModel->syncIngredients(null);
        });
    }

    /**
     * Get the ingredient list.
     *
     * @param string $keyColumn
     *
     * @return array
     */
    public function ingredientList(string $keyColumn = 'slug'): array
    {
        return $this->ingredients()->pluck('title', $keyColumn)->toArray();
    }

    /**
     * Scope query with all the given ingredients.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     * @param string $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAllIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = static::isIngredientsStringBased($ingredients)
            ? $ingredients : static::hydrateIngredients($ingredients)->pluck($column);

        collect($ingredients)->each(function ($ingredient) use ($query, $column) {
            $query->whereHas('ingredients', function (Builder $query) use ($ingredient, $column) {
                return $query->where($column, $ingredient);
            });
        });

        return $query;
    }

    /**
     * Scope query with any of the given ingredients.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     * @param string $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnyIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = static::isIngredientsStringBased($ingredients)
            ? $ingredients : static::hydrateIngredients($ingredients)->pluck($column);

        return $query->whereHas('ingredients', function (Builder $query) use ($ingredients, $column) {
            $query->whereIn($column, (array) $ingredients);
        });
    }

    /**
     * Scope query with any of the given ingredients.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     * @param string $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        return static::scopeWithAnyIngredients($query, $ingredients, $column);
    }

    /**
     * Scope query without the given ingredients.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     * @param string $column
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutIngredients(Builder $query, $ingredients, string $column = 'slug'): Builder
    {
        $ingredients = static::isIngredientsStringBased($ingredients)
            ? $ingredients : static::hydrateIngredients($ingredients)->pluck($column);

        return $query->whereDoesntHave('ingredients', function (Builder $query) use ($ingredients, $column) {
            $query->whereIn($column, (array) $ingredients);
        });
    }

    /**
     * Scope query without any ingredients.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithoutAnyIngredients(Builder $query): Builder
    {
        return $query->doesntHave('ingredients');
    }

    /**
     * Attach the given Ingredient(ies) to the model.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return $this
     */
    public function attachIngredients($ingredients)
    {
        static::setIngredients($ingredients, 'syncWithoutDetaching');

        return $this;
    }

    /**
     * Sync the given ingredient(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel|null $ingredients
     *
     * @return $this
     */
    public function syncIngredients($ingredients)
    {
        static::setIngredients($ingredients, 'sync');

        return $this;
    }

    /**
     * Detach the given Ingredient(s) from the model.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return $this
     */
    public function detachIngredients($ingredients)
    {
        static::setIngredients($ingredients, 'detach');

        return $this;
    }

    /**
     * Determine if the model has any the given ingredients.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return bool
     */
    public function hasIngredient($ingredients): bool
    {
        // Single Ingredient slug
        if (is_string($ingredients)) {
            return $this->ingredients->contains('slug', $ingredients);
        }

        // Single Ingredient id
        if (is_int($ingredients)) {
            return $this->ingredients->contains('id', $ingredients);
        }

        // Single Ingredient model
        if ($ingredients instanceof IngredientModel) {
            return $this->ingredients->contains('slug', $ingredients->slug);
        }

        // Array of Ingredient slugs
        if (is_array($ingredients) && isset($ingredients[0]) && is_string($ingredients[0])) {
            return ! $this->ingredients->pluck('slug')->intersect($ingredients)->isEmpty();
        }

        // Array of Ingredient ids
        if (is_array($ingredients) && isset($ingredients[0]) && is_int($ingredients[0])) {
            return ! $this->ingredients->pluck('id')->intersect($ingredients)->isEmpty();
        }

        // Collection of Ingredient models
        if ($ingredients instanceof Collection) {
            return ! $ingredients->intersect($this->ingredients->pluck('slug'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given ingredients.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return bool
     */
    public function hasAnyIngredient($ingredients): bool
    {
        return static::hasIngredient($ingredients);
    }

    /**
     * Determine if the model has all of the given ingredients.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return bool
     */
    public function hasAllIngredients($ingredients): bool
    {
        // Single ingredient slug
        if (is_string($ingredients)) {
            return $this->ingredients->contains('slug', $ingredients);
        }

        // Single ingredient id
        if (is_int($ingredients)) {
            return $this->ingredients->contains('id', $ingredients);
        }

        // Single ingredient model
        if ($ingredients instanceof IngredientModel) {
            return $this->ingredients->contains('slug', $ingredients->slug);
        }

        // Array of ingredient slugs
        if (is_array($ingredients) && isset($ingredients[0]) && is_string($ingredients[0])) {
            return $this->ingredients->pluck('slug')->count() === count($ingredients)
                && $this->ingredients->pluck('slug')->diff($ingredients)->isEmpty();
        }

        // Array of ingredient ids
        if (is_array($ingredients) && isset($ingredients[0]) && is_int($ingredients[0])) {
            return $this->ingredients->pluck('id')->count() === count($ingredients)
                && $this->ingredients->pluck('id')->diff($ingredients)->isEmpty();
        }

        // Collection of ingredient models
        if ($ingredients instanceof Collection) {
            return $this->ingredients->count() === $ingredients->count() && $this->ingredients->diff($ingredients)->isEmpty();
        }

        return false;
    }

    /**
     * Set the given ingredient(s) to the model.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     * @param string $action
     *
     * @return void
     */
    protected function setIngredients($ingredients, string $action)
    {
        // Fix exceptional event name
        $event = $action === 'syncWithoutDetaching' ? 'attach' : $action;

        // Hydrate Ingredients
        $ingredients = static::hydrateIngredients($ingredients)->pluck('id')->toArray();

        // Fire the Ingredient syncing event
        static::$dispatcher->dispatch("inetstudio.ingredients.{$event}ing", [$this, $ingredients]);

        // Set Ingredients
        $this->ingredients()->$action($ingredients);

        // Fire the Ingredient synced event
        static::$dispatcher->dispatch("inetstudio.ingredients.{$event}ed", [$this, $ingredients]);
    }

    /**
     * Hydrate ingredients.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return \Illuminate\Support\Collection
     */
    protected function hydrateIngredients($ingredients)
    {
        $isIngredientsStringBased = static::isIngredientsStringBased($ingredients);
        $isIngredientsIntBased = static::isIngredientsIntBased($ingredients);
        $field = $isIngredientsStringBased ? 'slug' : 'id';
        $className = static::getIngredientClassName();

        return $isIngredientsStringBased || $isIngredientsIntBased
            ? $className::query()->whereIn($field, (array) $ingredients)->get() : collect($ingredients);
    }

    /**
     * Determine if the given ingredient(s) are string based.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return bool
     */
    protected function isIngredientsStringBased($ingredients)
    {
        return is_string($ingredients) || (is_array($ingredients) && isset($ingredients[0]) && is_string($ingredients[0]));
    }

    /**
     * Determine if the given ingredient(s) are integer based.
     *
     * @param int|string|array|\ArrayAccess|IngredientModel $ingredients
     *
     * @return bool
     */
    protected function isIngredientsIntBased($ingredients)
    {
        return is_int($ingredients) || (is_array($ingredients) && isset($ingredients[0]) && is_int($ingredients[0]));
    }
}
