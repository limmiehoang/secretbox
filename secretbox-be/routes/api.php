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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/files/{group_id?}/{id?}', 'FileController@index')->middleware('jwt');

Route::post('files/add', 'FileController@store');

Route::get('/external', function (Request $request) {
    return response()->json(["msg" => "Your Access Token was successfully validated!"]);
})->middleware('jwt');

Route::middleware('jwt')->group( function () {
    Route::resource('groups', 'GroupController');
});

Route::post('upload', 'UploadController@uploadFile');

Route::get('download/{id}', 'UploadController@downloadFile')->middleware('jwt');