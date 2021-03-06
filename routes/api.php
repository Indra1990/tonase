<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogActivitiesController;
use App\Http\Controllers\TransactionsController;
use App\Http\Controllers\OrderController;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group([

//     'middleware' => 'api',
//     'namespace' => 'App\Http\Controllers',
//     'prefix' => 'auth'

// ], function ($router) {

//     Route::post('login', [AuthController::class, 'login']);
//     Route::post('register', [AuthController::class, 'register']);
//     Route::post('logout', [AuthController::class, 'logout']);

//     Route::get('profile', [AuthController::class, 'profile']);
//     Route::get('log-activity', [LogActivitiesController::class, 'index']);

//     //transaction
//     Route::post('transaction', [TransactionsController::class, 'create_save']);
//     Route::get('transaction/mutasi', [TransactionsController::class, 'get_mutasi']);

// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);


Route::group(['middleware' => ['jwt.verify']], function () {
        
        Route::post('logout', [AuthController::class, 'logout']);
        //profile
        Route::get('profile', [AuthController::class, 'profile']);
        //log activity
        Route::get('log-activity', [LogActivitiesController::class, 'index']);
    
        //transaction
        Route::post('transaction', [TransactionsController::class, 'create_save']);
        Route::get('transaction/mutasi', [TransactionsController::class, 'get_mutasi']);

        Route::post('orders', [OrderController::class, 'save_order']);
        Route::get('orders/status/{order}', [OrderController::class, 'status_payment']);

});