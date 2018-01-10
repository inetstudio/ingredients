<?php

namespace InetStudio\Ingredients\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use InetStudio\Ingredients\Events\ModifyIngredientEvent;
use InetStudio\Ingredients\Console\Commands\SetupCommand;
use InetStudio\Ingredients\Services\Front\IngredientsService;
use InetStudio\Ingredients\Console\Commands\CreateFoldersCommand;
use InetStudio\Ingredients\Listeners\ClearIngredientsCacheListener;
use InetStudio\Ingredients\Contracts\Services\IngredientsServiceContract;

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
        $this->registerEvents();
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
                SetupCommand::class,
                CreateFoldersCommand::class,
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
     * Регистрация событий.
     *
     * @return void
     */
    protected function registerEvents(): void
    {
        Event::listen(ModifyIngredientEvent::class, ClearIngredientsCacheListener::class);
    }

    /**
     * Регистрация привязок, алиасов и сторонних провайдеров сервисов.
     *
     * @return void
     */
    protected function registerBindings(): void
    {
        $this->app->singleton(IngredientsServiceContract::class, IngredientsService::class);
    }
}
