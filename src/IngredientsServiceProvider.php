<?php

namespace InetStudio\Ingredients;

use Illuminate\Support\ServiceProvider;

class IngredientsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'admin.module.ingredients');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        $this->mergeConfigFrom(
            __DIR__.'/../config/filesystems.php', 'filesystems.disks'
        );

        if ($this->app->runningInConsole()) {
            if (! class_exists('CreateIngredientsTables')) {
                $timestamp = date('Y_m_d_His', time());
                $this->publishes([
                    __DIR__.'/../database/migrations/create_ingredients_tables.php.stub' => database_path('migrations/'.$timestamp.'_create_ingredients_tables.php'),
                ], 'migrations');
            }

            $this->commands([
                Commands\SetupCommand::class,
                Commands\CreateFoldersCommand::class,
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
