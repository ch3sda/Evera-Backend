<?php

use Illuminate\Support\Facades\Route;
use App\Http\controllers\EventController;
use App\Http\Controllers\AdminDashboardController;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/events/notify', [EventController::class, 'sendNotification'])->name('events.notify');
Route::get('send-notification-form', function() {
    return view('send-notification');

});
Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
Route::get('/admin/stats', [AdminDashboardController::class, 'stats'])->name('admin.stats');

