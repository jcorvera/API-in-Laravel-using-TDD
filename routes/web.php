<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/token', 'Auth\AuthController@store');

// provider it could be Faceebook or Google
Route::get('/social/auth/{provider}','Auth\AuthController@redirect');
Route::get('/social/auth/{provider}/callback','Auth\AuthController@callback');
