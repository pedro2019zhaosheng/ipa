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
    return view('auth\login');
});

Route::get('logout', 'Auth\LoginController@logout');
Auth::routes();
Route::get('/home', 'HomeController@index')->name('home');
Route::get('game/index', 'GameController@index');
Route::get('game.create', 'GameController@index');


Route::resource('games', 'GameController');

Route::resource('banners', 'BannerController');

Route::resource('hots', 'HotController');

Route::resource('tiebas', 'TiebaController');

Route::resource('topics', 'TopicController');

Route::resource('gameTypes', 'GameTypeController');

Route::resource('actives', 'ActiveController');

Route::resource('accounts', 'AccountController');

Route::get('search', 'GameController@search');