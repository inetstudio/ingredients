<?php

namespace InetStudio\Ingredients\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class IngredientsBindingsServiceProvider.
 */
class IngredientsBindingsServiceProvider extends ServiceProvider
{
    /**
    * @var  bool
    */
    protected $defer = true;

    /**
    * @var  array
    */
    public $bindings = [
        'InetStudio\Ingredients\Contracts\Events\Back\ModifyIngredientEventContract' => 'InetStudio\Ingredients\Events\Back\ModifyIngredientEvent',
        'InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsControllerContract' => 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsController',
        'InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsDataControllerContract' => 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsDataController',
        'InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsUtilityControllerContract' => 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsUtilityController',
        'InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract' => 'InetStudio\Ingredients\Http\Requests\Back\SaveIngredientRequest',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\DestroyResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\DestroyResponse',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\FormResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\FormResponse',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\IndexResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\IndexResponse',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\SaveResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\SaveResponse',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\Ingredients\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\Ingredients\Contracts\Models\IngredientModelContract' => 'InetStudio\Ingredients\Models\IngredientModel',
        'InetStudio\Ingredients\Contracts\Observers\IngredientObserverContract' => 'InetStudio\Ingredients\Observers\IngredientObserver',
        'InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract' => 'InetStudio\Ingredients\Repositories\IngredientsRepository',
        'InetStudio\Ingredients\Contracts\Services\Back\IngredientsDataTableServiceContract' => 'InetStudio\Ingredients\Services\Back\IngredientsDataTableService',
        'InetStudio\Ingredients\Contracts\Services\Back\IngredientsObserverServiceContract' => 'InetStudio\Ingredients\Services\Back\IngredientsObserverService',
        'InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract' => 'InetStudio\Ingredients\Services\Back\IngredientsService',
        'InetStudio\Ingredients\Contracts\Services\Front\IngredientsServiceContract' => 'InetStudio\Ingredients\Services\Front\IngredientsService',
        'InetStudio\Ingredients\Contracts\Transformers\Back\IngredientTransformerContract' => 'InetStudio\Ingredients\Transformers\Back\IngredientTransformer',
        'InetStudio\Ingredients\Contracts\Transformers\Back\SuggestionTransformerContract' => 'InetStudio\Ingredients\Transformers\Back\SuggestionTransformer',
        'InetStudio\Ingredients\Contracts\Transformers\Front\Feeds\Mindbox\IngredientTransformerContract' => 'InetStudio\Ingredients\Transformers\Front\Feeds\Mindbox\IngredientTransformer',
        'InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsFeedItemsTransformerContract' => 'InetStudio\Ingredients\Transformers\Front\IngredientsFeedItemsTransformer',
        'InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsSiteMapTransformerContract' => 'InetStudio\Ingredients\Transformers\Front\IngredientsSiteMapTransformer',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return  array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
