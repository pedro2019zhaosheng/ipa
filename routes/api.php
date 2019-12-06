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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::group(['middleware' => 'check_token'], function () {
    Route::post('user', 'Api\ApiController@userinfo');
});

Route::post('mlogin', 'Api\ApiController@mlogin');
Route::post('tlogin', 'Api\ApiController@tlogin');
Route::post('logout', 'Api\ApiController@logout');
Route::post('getArea', 'Api\ApiController@getArea');


Route::any('apple/generatePackage','Api\AppleController@generatePackage')->name('generatePackage');

Route::any('apple/download','Api\AppleController@download')->name('download');

Route::any('apple/ipa','Api\AppleController@ipa')->name('ipa');
Route::any('apple/init','Api\AppleController@init')->name('init');

Route::any('apple/qrcode','Api\AppleController@qrcode')->name('qrcode');

Route::any('apple/savePackageId','Api\AppleController@savePackageId')->name('savePackageId');
Route::any('apple/packageInfo','Api\AppleController@packageInfo')->name('packageInfo');
Route::any('apple/downStatistics','Api\AppleController@downStatistics')->name('downStatistics');//下载统计

Route::any('apple/generateXml','Api\AppleController@generateXml')->name('generateXml');//下载统计

Route::any('user/register','Api\UserController@register')->name('register');//注册

Route::any('user/login','Api\UserController@login')->name('login');//登录

Route::any('user/summary','Api\UserController@summary')->name('summary');//概述

Route::any('user/upload','Api\UserController@upload')->name('upload');//上传文件

Route::any('user/modifyUserInfo','Api\UserController@modifyUserInfo')->name('modifyUserInfo');//修改个人信息

Route::any('user/realNameAuth','Api\UserController@realNameAuth')->name('realNameAuth');//实名认证

Route::any('user/modifyPassword','Api\UserController@modifyPassword')->name('modifyPassword');//修改密码

Route::any('user/uploadPackage','Api\UserController@uploadPackage')->name('uploadPackage');//内测发布应用

Route::any('user/packageList','Api\UserController@packageList')->name('packageList');//内测发布应用列表


//order
Route::any('order/buyConfig','Api\OrderController@buyConfig')->name('buyConfig');//订单购买配置
Route::any('order/makeOrder','Api\OrderController@makeOrder')->name('makeOrder');//下单
Route::any('order/vacallback','Api\OrderController@vacallback')->name('vacallback');//订单回调
Route::any('order/getOrderList','Api\OrderController@getOrderList')->name('getOrderList');//订单列表



//common
Route::any('common/sendSms','Api\CommonController@sendSms')->name('sendSms');//获取手机验证码