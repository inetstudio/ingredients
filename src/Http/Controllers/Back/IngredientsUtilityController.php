<?php

namespace InetStudio\Ingredients\Http\Controllers\Back;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Cviebrock\EloquentSluggable\Services\SlugService;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract;
use InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract;
use InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsUtilityControllerContract;

/**
 * Class IngredientsUtilityController.
 */
class IngredientsUtilityController extends Controller implements IngredientsUtilityControllerContract
{
    /**
     * Используемые сервисы.
     *
     * @var array
     */
    public $services;

    /**
     * IngredientsUtilityController constructor.
     */
    public function __construct()
    {
        $this->services['ingredients'] = app()->make('InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract');
    }

    /**
     * Получаем slug для модели по строке.
     *
     * @param Request $request
     *
     * @return SlugResponseContract
     */
    public function getSlug(Request $request): SlugResponseContract
    {
        $id = (int) $request->get('id');
        $name = $request->get('name');

        $model = $this->services['ingredients']->getIngredientObject($id);

        $slug = ($name) ? SlugService::createSlug($model, 'slug', $name) : '';

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

        $data = $this->services['ingredients']->getSuggestions($search, $type);

        return app()->makeWith('InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', [
            'suggestions' => $data,
        ]);
    }
}
