<?php

use Illuminate\Support\Facades\Route;

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

Route::middleware(['web'])->group(function () {
    Route::get('/ping', 'UtilController@ping')->name('ping');

    /** Physlet [API] */
    Route::prefix('physlet_api')->name('physlet.')->group(function () {
        Route::middleware('auth')->group(function () {
            Route::get('/logout', 'UserAuthController@logout')->name('logout');
            Route::post('/changePassword', 'UserAuthController@changePassword')->name('changePassword');
            Route::get('/myInfo', 'UserAuthController@myInfo')->name('userInfo');
            Route::post('/changeInfo', 'UserAuthController@changeInfo')->name('changeInfo');
            Route::post('/uploadAvatar', 'UserAuthController@uploadAvatar')->name('uploadAvatar');
            Route::post('/follow', 'UserAuthController@follow')->name('follow');

            Route::post('/getMySims', 'ListController@getMySimulations');
            Route::post('/getFollowings', 'ListController@getSimulations');
            Route::post('/followingList', 'ListController@getUsers');
            Route::post('/followerList', 'ListController@getUsers');
            Route::get('/messageList', 'ListController@getMessages');

            Route::post('/sendCom', 'SimulationController@sendComment');
            Route::post('/deleteCom', 'SimulationController@deleteComment');
            Route::post('/checkLike', 'SimulationController@checkLike');
            Route::post('/like', 'SimulationController@like');
            Route::post('/download', 'SimulationController@download');
            Route::post('/editSim', 'SimulationController@editSimulation');
            Route::post('/deleteSim', 'SimulationController@deleteSimulation');
            Route::post('/uploadSim', 'SimulationController@uploadSimulation');
            Route::post('/read', 'SimulationController@read');
            Route::post('/mark', 'SimulationController@mark');

            Route::get('/addUserTime', 'OtherController@addUserTime');
            Route::get('/getUserTime', 'OtherController@addUserTime');
//            Route::get('/getMySimulations', 'SimulationController@getMySimulations')->name('getMySimulations');
//            Route::post('/uploadSimulation', 'SimulationController@uploadSimulation')->name('uploadSimulation');
//            Route::post('/editSimulation', 'SimulationController@editSimulation')->name('editSimulation');
//            Route::post('/deleteSimulation', 'SimulationController@deleteSimulation')->name('deleteSimulation');
//            Route::get('/getVersion', 'SimulationController@getVersion')->name('getVersion');
//            Route::post('/setVersion', 'SimulationController@setVersion')->name('setVersion');
//            Route::post('/addVersion', 'SimulationController@addVersion')->name('addVersion');
//            Route::post('/editVersion', 'SimulationController@editVersion')->name('editVersion');
//            Route::post('/deleteVersion', 'SimulationController@deleteVersion')->name('deleteVersion');
//            Route::get('/getMyPackage', 'SimulationController@getMyPackage')->name('getMyPackage');
        });
        Route::post('/register', 'UserAuthController@register')->name('register');
        Route::post('/login', 'UserAuthController@login')->name('login');
        Route::get('/confirmEmail/{token}', 'UserAuthController@confirmEmail')->name('confirmEmail');
        Route::get('/checkLogin', 'UserAuthController@checkLogin')->name('checkLogin');
        Route::post('/userInfo', 'UserAuthController@userInfo')->name('userInfo');

        Route::post('/getSims', 'ListController@getSimulations')->name('getSims');
        Route::post('/search', 'ListController@getSimulations')->name('search');
        Route::post('/filter', 'ListController@getSimulations')->name('filter');
        Route::post('/getHisSims', 'ListController@getSimulations')->name('getHisSims');
        Route::get('/getCats', 'ListController@getCategories')->name('getCats');
        Route::post('/getComs', 'ListController@getComments')->name('getComs');
        Route::post('/searchUser', 'ListController@getUsers');

        Route::post('/getSim', 'SimulationController@getSimulation')->name('getSim');

        Route::get('/mainViews', 'OtherController@mainViews');
        Route::get('/addTime', 'OtherController@addTime');
        Route::get('/getTime', 'OtherController@addTime');
    });

    /** Physlet [View] */
    Route::get('/', 'GlobalController@view')->name('physlet');
    Route::get('/{any}', 'GlobalController@view')->where('any', '.*');
});
