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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', 'Api\V1\AuthController@login');
    Route::post('refresh', 'Api\V1\AuthController@refresh');
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('me', 'Api\v1\AuthController@me');
        Route::post('logout', 'Api\V1\AuthController@logout');
    });
});

Route::get('/users', function () {
    return \App\User::all();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::post('/list-posts', 'Api\V1\PostController@index')->name('list_post');
        Route::post('/posts', 'Api\V1\PostController@store')->name('create_post');
        Route::patch('/posts/{id}', 'Api\V1\PostController@update')->name('update_post');
    });
});
