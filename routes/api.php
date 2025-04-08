<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/visitors', [VisitorController::class, 'getMonthlyVisitors']);
Route::post('/visitors', [VisitorController::class, 'addVisitors']);
Route::patch('/visitors', [VisitorController::class, 'updateVisitors']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:api')->get('/me', [App\Http\Controllers\API\AuthController::class, 'me']);
Route::post('/news', [NewsController::class, 'store']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::patch('/news/{id}', [NewsController::class, 'update']);
Route::delete('/news/{id}', [NewsController::class, 'destroy']);
