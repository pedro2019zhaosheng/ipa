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
    //apple
    Route::get('/apple/apple','AppleController@apple')->name('apple');
    Route::get('/apple/create','AppleController@create')->name('apple.create');
    Route::post('/apple/store','AppleController@store')->name('apple.store');
    Route::get('/apple/destroy/{id}','AppleController@destroy')->name('apple.destroy');
    Route::get('/apple/edit/{id}','AppleController@edit')->name('apple.edit');
    //device
    Route::get('/device/device','DeviceController@device')->name('device');
    Route::get('/device/create','DeviceController@create')->name('device.create');
    Route::post('/device/store','DeviceController@store')->name('device.store');
    Route::get('/device/destroy/{id}','DeviceController@destroy')->name('device.destroy');
    Route::get('/device/edit/{id}','DeviceController@edit')->name('device.edit');
    //package
    Route::get('/package/package','PackageController@package')->name('package');
    Route::get('/package/create','PackageController@create')->name('package.create');
    Route::post('/package/store','PackageController@store')->name('package.store');
    Route::get('/package/destroy/{id}','PackageController@destroy')->name('package.destroy');
    Route::get('/package/edit','PackageController@edit')->name('package.edit');
    Route::any('/package/sonPackageList', 'PackageController@sonPackageList')->name('package.PackageController');


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
