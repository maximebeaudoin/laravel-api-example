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

Route::get('/', function () {
    return Response::json([
        'message' => 'Welcome to Laravel API example'
    ]);
});


Route::group([
    'prefix' => 'users',
    'as' => 'users',
    'middleware' => ['auth:sanctum']
], function () {

    Route::get('/', ['as' => 'index', 'uses' => 'UserController@index']);
    Route::post('/', ['as' => 'store', 'uses' => 'UserController@store']);
    Route::get('/{user}', ['as' => 'show', 'uses' => 'UserController@show']);
    Route::patch('/{user}', ['as' => 'update', 'uses' => 'UserController@update']);
    Route::delete('/{user}', ['as' => 'delete', 'uses' => 'UserController@delete']);
});
