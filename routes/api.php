<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->namespace('Api')->group(function () {
    Route::prefix('products')->group(function () {
        Route::post('/','Product\ProductController@store');
        Route::get('/','Product\ProductController@index');
        Route::put('/update/{id}','Product\ProductController@update')->where('id','[0-9]+');
        Route::get('/show/{id}','Product\ProductController@show')->where('id','[0-9]+');
        Route::delete('/delete/{id}','Product\ProductController@destroy')->where('id','[0-9]+');
    });
});
