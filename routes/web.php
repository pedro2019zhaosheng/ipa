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

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');

Route::group(['middleware' => ['check_login']], function () {
    Route::get('/home', 'HomeController@index')->name('home');

    Route::get('/apple/apple','AppleController@apple')->name('apple');


    Route::post('/upload', 'UploadController@upload');

    Route::resource('friends', 'FriendController');
    Route::resource('groups', 'GroupController');
    Route::resource('robots', 'RobotController');
    Route::resource('users', 'UserController');

    Route::get('/friend/auto', 'FriendController@auto');
    Route::post('/friend/addauto', 'FriendController@addauto');
    Route::get('/friend/autoreply', 'FriendController@autoreply');
    Route::any('/friend/addreply', 'FriendController@addreply');
    Route::any('/friend/response', 'FriendController@response');
    Route::any('/friend/nearby', 'FriendController@nearby');
    Route::any('/friend/batchSearch', 'FriendController@batchSearch');

    Route::any('/friend/saveRobotId', 'FriendController@saveRobotId');

    Route::any('/circle/circle', 'CircleController@circle');
    Route::any('/circle/saveCircle', 'CircleController@saveCircle');
    Route::any('/circle/getCity', 'CircleController@getCity');

    Route::any('/robot/updateRobot', 'RobotController@updateRobot');

    Route::any('/user/user', 'UserController@user');
    /*
     * group
     */
    Route::any('/group/group', 'GroupController@group');
    Route::any('/group/comeGroup', 'GroupController@comeGroup');

    Route::any('/group/saveRobotId', 'GroupController@saveRobotId');
    Route::post('/group/addauto', 'GroupController@addauto');
    Route::any('/group/groupKeywordReply', 'GroupController@groupKeywordReply');
    Route::any('/group/wallet', 'GroupController@wallet');
    Route::any('/group/link', 'GroupController@link');
    Route::any('/group/addreply', 'GroupController@addreply');
    Route::any('/group/groupSend', 'GroupController@groupSend');
    Route::any('/group/addSend', 'GroupController@addSend');
    Route::any('/group/groupSign', 'GroupController@groupSign');
    Route::any('/group/addSign', 'GroupController@addSign');
    Route::any('/group/destroySend', 'GroupController@destroySend');
    Route::any('/group/destroySign', 'GroupController@destroySign');
    Route::any('/group/groupMember', 'GroupController@groupMember');

    Route::any('/group/saveGroupId', 'GroupController@saveGroupId');
    Route::any('/group/doWithGroupSend', 'GroupController@doWithGroupSend');
    Route::any('/group/groupKick', 'GroupController@groupKick');
    Route::any('/group/groupComplain', 'GroupController@groupComplain');



});


Route::get('/', 'Auth\LoginController@logout')->name('logout');
//Auth::routes();
//
//Route::get('/home', 'HomeController@index')->name('home');
//
//Auth::routes();
//
//Route::get('/home', 'HomeController@index')->name('home');
//
//Auth::routes();
//
//Route::get('/home', 'HomeController@index')->name('home');
