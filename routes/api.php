<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthControlller;
use App\Http\Controllers\ProductController;

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

Route::post('register', [AuthControlller::class, 'register']);
Route::post('login', [AuthControlller::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->group(function () {
    // Product Api Routes....
     Route::group(['prefix' => '/product'], function () {
        Route::get('/list', [ProductController::class, 'index']);
        Route::post('/store', [ProductController::class, 'store']);
        Route::post('/update/{id}', [ProductController::class, 'update']);
        Route::delete('/toggle-status/{id}', [ProductController::class, 'toggleStatus']);
        Route::delete('/destroy/{id}', [ProductController::class, 'destroy']);
    });

    // Cart Management Api Routes

    Route::group(['prefix' => '/cart'], function () {
        Route::post('/cartadd', [CardController::class, 'cartadd']);
        Route::post('/cartupdate', [CardController::class, 'cartupdate']);
        Route::post('/cartconfirm', [CardController::class, 'cartconfirm']);
        Route::get('cartremove/{cartitemid}', [CardController::class, 'cartRemove']);
    });
});
