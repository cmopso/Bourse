<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Auth::routes();
Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');

Route::get('/order', 'OrderController@index')->name('orderIndex')->middleware('auth');
Route::put('/order', 'OrderController@store')->name('orderStore')->middleware('auth');
Route::get('/order/create', 'OrderController@create')->name('orderCreate')->middleware('auth');
//Route::get('/order/{order}', 'OrderController@show')->name('orderShow')->middleware('auth');
Route::get('/order/{order}/edit', 'OrderController@edit')->name('orderEdit')->middleware('auth');
Route::put('/order/{order}', 'OrderController@update')->name('orderUpdate')->middleware('auth');
Route::get('/order/{order}/delete', 'OrderController@destroy')->name('orderDelete')->middleware('auth');

Route::get('/share', 'shareController@index')->name('shareIndex')->middleware('auth');
Route::get('/share/{share}/detail', 'shareController@detail')->name('shareDetail')->middleware('auth');
Route::put('/share', 'shareController@store')->name('shareStore')->middleware('auth');
Route::get('/share/create', 'shareController@create')->name('shareCreate')->middleware('auth');
//Route::get('/share/{id}', 'shareController@show')->name('shareShow')->middleware('auth');
Route::get('/share/{share}/edit', 'shareController@edit')->name('shareEdit')->middleware('auth');
Route::put('/share/{share}', 'shareController@update')->name('shareUpdate')->middleware('auth');
Route::get('/share/{share}/delete', 'shareController@destroy')->name('shareDelete')->middleware('auth');