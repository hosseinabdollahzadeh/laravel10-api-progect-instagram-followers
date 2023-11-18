<?php

use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\UserController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix('v1')->group(function () {

    Route::prefix('orders')->group(function () {
        Route::post('/follower-request', [OrderController::class, 'followerRequest']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/followable-list', [UserController::class, 'followableList'])->name('followable.list');
        Route::post('/follow-user', [UserController::class, 'followUser']);
    });
});
