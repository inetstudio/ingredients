<?php

namespace InetStudio\IngredientsPackage\Ingredients\Models\Traits;

use ArrayAccess;
use Illuminate\Support\Collection;
use InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract;

/**
 * Trait HasIngredientsCollection.
 */
trait HasIngredientsCollection
{
    /**
     * Determine if the model has any the given ingredients.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return bool
     */
    public function hasIngredient($ingredients): bool
    {
        if ($this->isIngredientsStringBased($ingredients)) {
            return ! $this->ingredients->pluck('slug')->intersect((array) $ingredients)->isEmpty();
        }

        if ($this->isIngredientsIntBased($ingredients)) {
            return ! $this->ingredients->pluck('id')->intersect((array) $ingredients)->isEmpty();
        }

        if ($ingredients instanceof IngredientModelContract) {
            return $this->ingredients->contains('slug', $ingredients['slug']);
        }

        if ($ingredients instanceof Collection) {
            return ! $ingredients->intersect($this->ingredients->pluck('slug'))->isEmpty();
        }

        return false;
    }

    /**
     * Determine if the model has any the given ingredients.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return bool
     */
    public function hasAnyIngredient($ingredients): bool
    {
        return $this->hasIngredient($ingredients);
    }

    /**
     * Determine if the model has all of the given ingredients.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return bool
     */
    public function hasAllIngredients($ingredients): bool
    {
        if ($this->isIngredientsStringBased($ingredients)) {
            $ingredients = (array) $ingredients;

            return $this->ingredients->pluck('slug')->intersect($ingredients)->count() == count($ingredients);
        }

        if ($this->isIngredientsIntBased($ingredients)) {
            $ingredients = (array) $ingredients;

            return $this->ingredients->pluck('id')->intersect($ingredients)->count() == count($ingredients);
        }

        if ($ingredients instanceof IngredientModelContract) {
            return $this->ingredients->contains('slug', $ingredients['slug']);
        }

        if ($ingredients instanceof Collection) {
            return $this->ingredients->intersect($ingredients)->count() == $ingredients->count();
        }

        return false;
    }

    /**
     * Determine if the given ingredient(s) are string based.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return bool
     */
    protected function isIngredientsStringBased($ingredients): bool
    {
        return is_string($ingredients) || (is_array($ingredients) && isset($ingredients[0]) && is_string($ingredients[0]));
    }

    /**
     * Determine if the given ingredient(s) are integer based.
     *
     * @param  int|string|array|ArrayAccess|IngredientModelContract  $ingredients
     *
     * @return bool
     */
    protected function isIngredientsIntBased($ingredients): bool
    {
        return is_int($ingredients) || (is_array($ingredients) && isset($ingredients[0]) && is_int($ingredients[0]));
    }
}
