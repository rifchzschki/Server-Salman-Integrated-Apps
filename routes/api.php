<?php

use App\Http\Controllers\NewsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::get('/visitors', [VisitorController::class, 'getMonthlyVisitors']);
Route::post('/visitors', [VisitorController::class, 'addVisitors']);
Route::patch('/visitors', [VisitorController::class, 'updateVisitors']);

Route::post('/news', [NewsController::class, 'store']);
Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);
Route::patch('/news/{id}', [NewsController::class, 'update']);
Route::delete('/news/{id}', [NewsController::class, 'destroy']);
