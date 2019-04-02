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

/*Route::get('/', function () {
    return view('welcome');
});*/
Route::view('/', 'welcome');

Route::get('/api/v2/coupons/test', 'CouponController@test');
Route::post('/api/v2/coupons', 'CouponController@set');