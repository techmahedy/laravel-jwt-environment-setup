<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('signup', 'ApiController@register');
Route::post('signin', 'ApiController@login');

Route::group(['middleware' => 'auth:api'], function () {
    
    Route::get('signout', 'ApiController@logout');
    Route::get('user', 'ApiController@getAuthenticatedUser');
 
    Route::get('/products', 'ProductController@index');
    Route::get('/product/{id}', 'ProductController@show');
    Route::post('/product', 'ProductController@store');
    Route::post('/product/{id}', 'ProductController@update');
    Route::delete('/product/{id}', 'ProductController@destroy');

});