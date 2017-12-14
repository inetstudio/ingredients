<?php

Route::group(['namespace' => 'InetStudio\Ingredients\Http\Controllers\Back'], function () {
    Route::group(['middleware' => 'web', 'prefix' => 'back'], function () {
        Route::group(['middleware' => 'back.auth'], function () {
            Route::post('ingredients/slug', 'IngredientsController@getSlug')->name('back.ingredients.getSlug');
            Route::post('ingredients/suggestions', 'IngredientsController@getSuggestions')->name('back.ingredients.getSuggestions');
            Route::any('ingredients/data', 'IngredientsController@data')->name('back.ingredients.data');
            Route::resource('ingredients', 'IngredientsController', ['except' => [
                'show',
            ], 'as' => 'back']);
        });
    });
});
