<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EventsController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\DiscussionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\QuotesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/visitors', [VisitorController::class, 'getMonthlyVisitors']);
Route::post('/visitors', [VisitorController::class, 'addVisitors']);
Route::patch('/visitors', [VisitorController::class, 'updateVisitors']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/me', [App\Http\Controllers\API\AuthController::class, 'me']);
Route::middleware('auth:api')->group(function () {
    Route::get('/discussions', [DiscussionController::class, 'index']);
    Route::post('/discussions', [DiscussionController::class, 'store']);
    Route::put('/discussions/{discussion}', [DiscussionController::class, 'update']);
    Route::delete('/discussions/{discussion}', [DiscussionController::class, 'destroy']);
});
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

Route::post('/news', [NewsController::class, 'store']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::patch('/news/{id}', [NewsController::class, 'update']);
Route::delete('/news/{id}', [NewsController::class, 'destroy']);

Route::get('/get-finance', [FinanceController::class, 'getMonthlyFinance']);
Route::post('/update-finance', [FinanceController::class, 'updateMonthlyFinance']);

Route::apiResource('/quotes', QuotesController::class);

Route::apiResource('/events', EventsController::class);
