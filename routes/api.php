<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Api\EventCategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AdminDashboardController;


Route::get('/test', function() {
    return response()->json(['message' => 'Test Route is working']);
});

// Register route for API
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::middleware('auth:sanctum')->post('/logout', [LogoutController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('categories', EventCategoryController::class);
Route::get('/events/upcoming', [EventController::class, 'upcoming'])->name('events.upcoming');
Route::apiResource('events', controller: EventController::class);

//Route::middleware('auth:sanctum')->get('/admin/dashboard', [AdminDashboardController::class, 'stats']);
Route::get('/admin/dashboard', action: [AdminDashboardController::class, 'stats']);