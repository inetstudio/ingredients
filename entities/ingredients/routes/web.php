<?php

use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'InetStudio\IngredientsPackage\Ingredients\Contracts\Http\Controllers\Back',
        'middleware' => ['web', 'back.auth'],
        'prefix' => 'back',
    ],
    function () {
        Route::any('ingredients/data', 'DataControllerContract@data')
            ->name('back.ingredients.data.index');

        Route::post('ingredients/slug', 'UtilityControllerContract@getSlug')
            ->name('back.ingredients.getSlug');

        Route::post('ingredients/suggestions', 'UtilityControllerContract@getSuggestions')
            ->name('back.ingredients.getSuggestions');

        Route::resource(
            'ingredients',
            'ResourceControllerContract',
            [
                'except' => [
                    'show',
                ],
                'as' => 'back',
            ]
        );
    }
);
