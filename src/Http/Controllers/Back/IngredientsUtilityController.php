<?php

namespace InetStudio\Ingredients\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsUtilityControllerContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;

/**
 * Class IngredientsUtilityController.
 */
class IngredientsUtilityController extends Controller implements IngredientsUtilityControllerContract
{
    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return SlugResponseContract
     */
    public function getSlug(Request $request): SlugResponseContract
    {
        $name = $request->get('name');
        $slug = ($name) ? SlugService::createSlug(app()->make('InetStudio\Ingredients\Contracts\Models\IngredientModelContract'), 'slug', $name) : '';

        return app()->makeWith('InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract', [
            'slug' => $slug,
        ]);
    }

    /**
     * Возвращаем объекты для поля.
     *
     * @param Request $request
     *
     * @return SuggestionsResponseContract
     */
    public function getSuggestions(Request $request): SuggestionsResponseContract
    {
        $search = $request->get('q');
        $type = $request->get('type');

        $data = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract')
            ->getSuggestions($search, $type);

        return app()->makeWith('InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', [
            'suggestions' => $data,
        ]);
    }
}
