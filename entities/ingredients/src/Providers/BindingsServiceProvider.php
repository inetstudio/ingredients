<?php

namespace InetStudio\IngredientsPackage\Ingredients\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class BindingsServiceProvider.
 */
class BindingsServiceProvider extends BaseServiceProvider implements DeferrableProvider
{
    /**
     * @var array
     */
    public $bindings = [
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Events\Back\ModifyItemEventContract' => 'InetStudio\IngredientsPackage\Ingredients\Events\Back\ModifyItemEvent',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Controllers\Back\ResourceControllerContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Controllers\Back\ResourceController',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Controllers\Back\DataControllerContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Controllers\Back\DataController',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Controllers\Back\UtilityControllerContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Controllers\Back\UtilityController',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Requests\Back\SaveItemRequestContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Requests\Back\SaveItemRequest',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\DestroyResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource\DestroyResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\FormResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource\FormResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\IndexResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource\IndexResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\SaveResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource\SaveResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Resource\ShowResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Resource\ShowResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Utility\SlugResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract' => 'InetStudio\IngredientsPackage\Ingredients\Http\Responses\Back\Utility\SuggestionsResponse',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Models\IngredientModelContract' => 'InetStudio\IngredientsPackage\Ingredients\Models\IngredientModel',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\DataTableServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Back\DataTableService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\ItemsServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Back\ItemsService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Back\UtilityServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Back\UtilityService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Front\FeedsServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Front\FeedsService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Front\ItemsServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Front\ItemsService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Services\Front\SitemapServiceContract' => 'InetStudio\IngredientsPackage\Ingredients\Services\Front\SitemapService',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Back\Resource\IndexTransformerContract' => 'InetStudio\IngredientsPackage\Ingredients\Transformers\Back\Resource\IndexTransformer',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Back\Utility\SuggestionTransformerContract' => 'InetStudio\IngredientsPackage\Ingredients\Transformers\Back\Utility\SuggestionTransformer',
        'InetStudio\IngredientsPackage\Ingredients\Contracts\Transformers\Front\Sitemap\ItemTransformerContract' => 'InetStudio\IngredientsPackage\Ingredients\Transformers\Front\Sitemap\ItemTransformer',
    ];

    /**
     * Получить сервисы от провайдера.
     *
     * @return array
     */
    public function provides()
    {
        return array_keys($this->bindings);
    }
}
