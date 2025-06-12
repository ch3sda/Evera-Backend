<?php
// Evera-Backend/routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{LoginController, RegisterController, LogoutController,OtpController,ProfileController};
use App\Http\Controllers\Api\Admin\{AdminDashboardController, EventCategoryController, ApprovalController};
use App\Http\Controllers\Api\Organizer\{EventController, TicketPolicyController, OrganizerNotificationController};
use App\Http\Controllers\Api\Attendee\{EventViewController, TicketController, ReminderController,OrganizerRequestController};
use App\Http\Controllers\Api\StripeController;

// Public (guest) routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);
Route::post('/verify-otp', [OtpController::class, 'verify']);
Route::post('/resend-otp', [OtpController::class, 'resend']);

// Protected routes requiring authentication (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [LogoutController::class, 'logout']);
    
// routes/api.php
    Route::get('/user', [ProfileController::class, 'show']);

    // Admin routes - add role check middleware if you have one, e.g. 'role:admin'
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('categories', EventCategoryController::class);
        Route::get('user-stats', [AdminDashboardController::class, 'userStats']);
        Route::post('/organizer-requests', [ApprovalController::class, 'approveOrganizerRequest']);
        Route::get('/organizer-requests', [\App\Http\Controllers\Api\Admin\OrganizerRequestController::class, 'index']);

    });

    // Organizer routes        Route::post('approvals', [ApprovalController::class, 'approveOrganizerRequest']);

    Route::middleware('role:organizer')->prefix('organizer')->group(function () {
        Route::apiResource('events', EventController::class);
        Route::apiResource('ticket-policies', TicketPolicyController::class);
        Route::post('notifications/send', [OrganizerNotificationController::class, 'send']);
        Route::get('dashboard', [EventController::class, 'dashboardStats']); // example dashboard method
    });

    // Attendee routes
    Route::middleware('role:attendee')->prefix('attendee')->group(function () {
        Route::post('request-organizer', [OrganizerRequestController::class, 'request']);
        Route::get('events/upcoming', [EventViewController::class, 'upcoming']);
        Route::apiResource('tickets', TicketController::class);
        Route::apiResource('reminders', ReminderController::class);
    });

    // Stripe payment method routes
    Route::middleware('auth:sanctum')->post('/stripe/payment-method', [StripeController::class, 'storePaymentMethod']);

});
