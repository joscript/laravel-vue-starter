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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources(['user' => 'API\UsersController']);

Route::get('profile', 'API\UsersController@profile')->name('profile');
Route::put('update-profile', 'API\UsersController@updateProfileInfo')->name('update_profile');
Route::put('get-updated-photo', 'API\UsersController@getUpdatedPhoto')->name('get_updated_photo');
Route::get('find-user', 'API\UsersController@search')->name('search');
