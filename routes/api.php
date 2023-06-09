<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\PaymentCallbackController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware(['auth:api'])->prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:api']);
    Route::post('register', [AuthController::class, 'register'])->withoutMiddleware(['auth:api']);
    Route::get('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'user']);
});

Route::group([
    'middleware' => 'auth:api',
    'prefix' => 'admin'
], function() {
    Route::post('/getTokenTransaction', [TransactionController::class, 'getTransactionToken']);
    Route::post('/midtrans-notofication', [PaymentCallbackController::class, 'receive']);
    // Route::post('/create-signature', [PaymentCallbackController::class, 'receive']);
});


// Route::get('/test', function() {
//     \Log::channel('nicesnippets')->info('This is testing for nicesnippets.com!');
//     dd('done');
// });

