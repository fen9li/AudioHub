<?php

Route::get('/', function () {
    return view('welcome');
});

Route::get('verify/{token}', 'Auth\VerifyEmailController@verifyEmail')->name('verify');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
