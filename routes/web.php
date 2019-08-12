<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('/auth/token', 'Auth\AuthController@store');
