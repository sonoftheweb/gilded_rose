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

Route::post('authentication', [\App\Http\Controllers\AuthenticationController::class, 'login'])->name('authenticate.login');
Route::delete('logout', [\App\Http\Controllers\AuthenticationController::class, 'logout'])->name('authenticate.logout');

Route::get('{resource}', [\App\Http\Controllers\ApiController::class, 'getCollection']);
Route::get('{resource}/{id}', [\App\Http\Controllers\ApiController::class, 'getItem']);
Route::post('{resource}/{id}', [\App\Http\Controllers\ApiController::class, 'updateItem']);
