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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::group(['prefix' => 'v1/auth'], function () {
    Route::post('login', 'Api\V1\AuthController@login');
    Route::post('refresh', 'Api\V1\AuthController@refresh');
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('me', 'Api\v1\AuthController@me');
        Route::post('logout', 'Api\V1\AuthController@logout');
    });
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('/post/create', 'Api\v1\PostController@store')->name('create_post');
        Route::delete('/post/{id}/delete', 'Api\v1\PostController@destroy')->name('delete_post');
    });
});
