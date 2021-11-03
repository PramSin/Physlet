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
            Route::get('/getCategories', 'SimulationController@getCategories')->name('getCategories');
            Route::get('/getSimulations', 'SimulationController@getSimulations')->name('getCategories');
            Route::post('/uploadSimulation', 'SimulationController@uploadSimulation')->name('uploadSimulation');
            Route::post('/editSimulation', 'SimulationController@editSimulation')->name('editSimulation');
            Route::post('/deleteSimulation', 'SimulationController@deleteSimulation')->name('deleteSimulation');
            Route::get('/getVersion', 'SimulationController@getVersion')->name('getVersion');
            Route::post('/setVersion', 'SimulationController@setVersion')->name('setVersion');
            Route::post('/addVersion', 'SimulationController@addVersion')->name('addVersion');
            Route::post('/editVersion', 'SimulationController@editVersion')->name('editVersion');
            Route::post('/deleteVersion', 'SimulationController@deleteVersion')->name('deleteVersion');
            Route::get('/getPackage', 'SimulationController@getPackage')->name('getPackage');

        });
        Route::post('/login', 'UserAuthController@login')->name('login');
        Route::post('/register', 'UserAuthController@register')->name('register');
        Route::get('/confirmEmail/{token}', 'UserAuthController@confirmEmail')->name('confirmEmail');
    });

    /** Physlet [View] */
    Route::get('/', 'GlobalController@view')->name('physlet');
    Route::get('/{any}', 'GlobalController@view')->where('any', '.*');
});
