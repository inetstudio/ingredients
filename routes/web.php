<?php

Route::group([
    'namespace' => 'InetStudio\Ingredients\Contracts\Http\Controllers\Back',
    'middleware' => ['web', 'back.auth'],
    'prefix' => 'back',
], function () {
    Route::any('ingredients/data', 'IngredientsDataControllerContract@data')->name('back.ingredients.data.index');
    Route::post('ingredients/slug', 'IngredientsUtilityControllerContract@getSlug')->name('back.ingredients.getSlug');
    Route::post('ingredients/suggestions', 'IngredientsUtilityControllerContract@getSuggestions')->name('back.ingredients.getSuggestions');

    Route::resource('ingredients', 'IngredientsControllerContract', ['except' => [
        'show',
    ], 'as' => 'back']);
});
