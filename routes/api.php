<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::get('/visitors', [VisitorController::class, 'getMonthlyVisitors']);
Route::post('/visitors', [VisitorController::class, 'addVisitors']);
Route::patch('/visitors', [VisitorController::class, 'updateVisitors']);