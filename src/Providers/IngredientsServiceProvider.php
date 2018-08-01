<?php

namespace InetStudio\Ingredients\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

/**
 * Class IngredientsServiceProvider.
 */
class IngredientsServiceProvider extends ServiceProvider
{
    /**
     * Загрузка сервиса.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerConsoleCommands();
        $this->registerPublishes();
        $this->registerRoutes();
        $this->registerViews();
        $this->registerViewComposers();
        $this->registerObservers();
    }

    /**
     * Регистрация привязки в контейнере.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerBindings();
    }

    /**
     * Регистрация команд.
     *
     * @return void
     */
    protected function registerConsoleCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                'InetStudio\Ingredients\Console\Commands\SetupCommand',
                'InetStudio\Ingredients\Console\Commands\CreateFoldersCommand',
            ]);
        }
    }

    /**
     * Регистрация ресурсов.
     *
     * @return void
     */
    protected function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../../config/ingredients.php' => config_path('ingredients.php'),
        ], 'config');

        $this->mergeConfigFrom(
            __DIR__.'/../../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateIngredientsTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../../database/migrations/create_ingredients_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_ingredients_tables.php'),
                ], 'migrations');
            }
        }
    }

    /**
     * Регистрация путей.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
    }

    /**
     * Регистрация представлений.
     *
     * @return void
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'admin.module.ingredients');
    }

    /**
     * Register Ingredient's view composers.
     *
     * @return void
     */
    public function registerViewComposers(): void
    {
        view()->composer('admin.module.ingredients::back.partials.analytics.materials.statistic', function ($view) {
            $ingredients = app()->make('InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract')
                ->getAllItems([], [], true)
                ->select(['status_id', DB::raw('count(*) as total')])
                ->with('status')
                ->groupBy('status_id')
                ->get();

            $view->with('ingredients', $ingredients);
        });
    }

    /**
     * Регистрация наблюдателей.
     *
     * @return void
     */
    public function registerObservers(): void
    {
        $this->app->make('InetStudio\Ingredients\Contracts\Models\IngredientModelContract')::observe($this->app->make('InetStudio\Ingredients\Contracts\Observers\IngredientObserverContract'));
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        // Controllers
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsControllerContract', 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsController');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsDataControllerContract', 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsDataController');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Controllers\Back\IngredientsUtilityControllerContract', 'InetStudio\Ingredients\Http\Controllers\Back\IngredientsUtilityController');

        // Events
        $this->app->bind('InetStudio\Ingredients\Contracts\Events\Back\ModifyIngredientEventContract', 'InetStudio\Ingredients\Events\Back\ModifyIngredientEvent');

        // Models
        $this->app->bind('InetStudio\Ingredients\Contracts\Models\IngredientModelContract', 'InetStudio\Ingredients\Models\IngredientModel');

        // Observers
        $this->app->bind('InetStudio\Ingredients\Contracts\Observers\IngredientObserverContract', 'InetStudio\Ingredients\Observers\IngredientObserver');

        // Repositories
        $this->app->bind('InetStudio\Ingredients\Contracts\Repositories\IngredientsRepositoryContract', 'InetStudio\Ingredients\Repositories\IngredientsRepository');

        // Requests
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Requests\Back\SaveIngredientRequestContract', 'InetStudio\Ingredients\Http\Requests\Back\SaveIngredientRequest');

        // Responses
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\DestroyResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\DestroyResponse');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\FormResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\FormResponse');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\IndexResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\IndexResponse');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Ingredients\SaveResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Ingredients\SaveResponse');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SlugResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Utility\SlugResponse');
        $this->app->bind('InetStudio\Ingredients\Contracts\Http\Responses\Back\Utility\SuggestionsResponseContract', 'InetStudio\Ingredients\Http\Responses\Back\Utility\SuggestionsResponse');

        // Services
        $this->app->bind('InetStudio\Ingredients\Contracts\Services\Back\IngredientsDataTableServiceContract', 'InetStudio\Ingredients\Services\Back\IngredientsDataTableService');
        $this->app->bind('InetStudio\Ingredients\Contracts\Services\Back\IngredientsObserverServiceContract', 'InetStudio\Ingredients\Services\Back\IngredientsObserverService');
        $this->app->bind('InetStudio\Ingredients\Contracts\Services\Back\IngredientsServiceContract', 'InetStudio\Ingredients\Services\Back\IngredientsService');
        $this->app->bind('InetStudio\Ingredients\Contracts\Services\Front\IngredientsServiceContract', 'InetStudio\Ingredients\Services\Front\IngredientsService');

        // Transformers
        $this->app->bind('InetStudio\Ingredients\Contracts\Transformers\Back\IngredientTransformerContract', 'InetStudio\Ingredients\Transformers\Back\IngredientTransformer');
        $this->app->bind('InetStudio\Ingredients\Contracts\Transformers\Back\SuggestionTransformerContract', 'InetStudio\Ingredients\Transformers\Back\SuggestionTransformer');
        $this->app->bind('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsFeedItemsTransformerContract', 'InetStudio\Ingredients\Transformers\Front\IngredientsFeedItemsTransformer');
        $this->app->bind('InetStudio\Ingredients\Contracts\Transformers\Front\IngredientsSiteMapTransformerContract', 'InetStudio\Ingredients\Transformers\Front\IngredientsSiteMapTransformer');
    }
}
