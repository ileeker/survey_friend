<?php

use App\Http\Controllers\SampleCubeController;
use App\User;
use Illuminate\Support\Facades\Auth;
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

// 网站首页
Route::get('/', function () {

    return view('welcome');
    
})->name('index_home');

// 自动登录的跳转Url
Route::get('/autologin/{user}', function (User $user) {
    
    Auth::login($user);
    return redirect()->home();
    
})->name('autologin')->middleware('signed');

// 注册登录等授权
Auth::routes();

// 登陆后的面板
Route::middleware('auth')->group(function () {
    
    // 获取自动登录的链接
    Route::get('/url-login', 'HomeController@url_login')->name('urllogin');
    
    // 面板首页
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home/opinionetwork', 'HomeController@opinionetwork_uuid')->name('opinionetwork_uuid');
    Route::post('/home/dynata', 'HomeController@dynata_user')->name('dynata_user');
    Route::post('/home/best/add', 'HomeController@add')->name('add');
    Route::post('/home/best/del', 'HomeController@del')->name('del');
    Route::post('/home/store-best', 'HomeController@store')->name('store');
    Route::post('/home/remark', 'HomeController@remark')->name('remark');
    Route::post('/home/jscode', 'HomeController@jscode')->name('jscode');
    Route::get('/success','HomeController@success')->name('success');
    Route::get('/revenue/{year}/{month}','HomeController@innovateRevenue')->name('innovateRevenue');

    // Dynata开始
    Route::get('/dynata', 'DynataController@index')->name('dynata');
    Route::get('/dynata/project/{id}', 'DynataController@project')->where('id', '[0-9]+');
    Route::get('/dynata/store', 'DynataController@store');
    Route::get('/dynata/filter/{id}', 'DynataController@filter')->where('id', '[0-9]+');
    Route::get('/dynata/quota/{id}', 'DynataController@quota')->where('id', '[0-9]+');
    Route::get('/dynata/{itemId}/{id}', 'DynataController@id')->where('id', '[0-9]+')->where('itemId', '[0-9]+');
    Route::get('/dynata/remain/{projectId}/{itemId}', 'DynataController@remain')->where(['projectId' => '[0-9]+', 'itemId' => '[0-9]+']);
    Route::get('/dynata/check/{itemId}', 'DynataController@check')->where('itemId', '[0-9]+');
    Route::get('/dynata/remain/store', 'DynataController@remain_store');
    Route::get('/dynata/attribute/{country}/{language}/{id}', 'DynataController@attribute')->where(['id' => '[0-9]+', 'country' => '[A-Za-z]+', 'language' => '[A-Za-z]+']);

    // Sample-Cube开始
    // Route::get('/sample-cube', function(){return '<h1>Not suitable for making money.</h1>';})->name('samplecube');
    Route::get('/sample-cube/store', 'SampleCubeController@sample_cube_store')->name('samplecube_store');
    Route::get('/sample-cube','SampleCubeController@samplecube')->name('samplecube');
    Route::get('/sample-cube/{id}','SampleCubeController@sample_cube_id')->where('id', '[0-9]+');
    Route::get('/sample-cube/quota/{id}/{country}','SampleCubeController@sample_cube_quota')->where('id', '[0-9]+')->name('samplecube_quota');
    Route::get('/sample-cube/{country}','SampleCubeController@sample_cube_country')->where('country', '[[a-zA-Z]+');
    Route::get('/sample-cube/group/{id}','SampleCubeController@sample_cube_group');
    
    // History
    Route::get('/history', 'SettingController@history')->name('history');
    Route::get('/individual/{startDate}/{endDate}', 'SettingController@individual')->name('individual');

});

// 查询收入

Route::get('/money/{id}/{startDate}/{endDate}', 'SettingController@money')->name('money');

// 链接跳转
// Dynata
Route::get('/callback/dynata','RedirectController@dynata');

// test
Route::get('/test','HomeController@test');
// Route::get('/test1','HomeController@test1');
