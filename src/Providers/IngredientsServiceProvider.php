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
}
