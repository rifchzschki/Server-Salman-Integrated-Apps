<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\API\AuthController;
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::get('/visitors', [VisitorController::class, 'getMonthlyVisitors']);
Route::post('/visitors', [VisitorController::class, 'addVisitors']);
Route::patch('/visitors', [VisitorController::class, 'updateVisitors']);

//route untuk auth
Route::post('/register', [AuthController::class, 'register']);
