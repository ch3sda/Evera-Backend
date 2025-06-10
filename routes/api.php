<?php
// Evera-Backend/routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\{LoginController, RegisterController, LogoutController};
use App\Http\Controllers\Api\Admin\{AdminDashboardController, EventCategoryController, ApprovalController};
use App\Http\Controllers\Api\Organizer\{EventController, TicketPolicyController, OrganizerNotificationController};
use App\Http\Controllers\Api\Attendee\{EventViewController, TicketController, ReminderController};
use App\Http\Controllers\Api\User\RequestController;

// Public (guest) routes
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Protected routes requiring authentication (Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/logout', [LogoutController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Admin routes - add role check middleware if you have one, e.g. 'role:admin'
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::apiResource('categories', EventCategoryController::class);
        Route::get('dashboard', [AdminDashboardController::class, 'stats']);
        Route::post('approvals', [ApprovalController::class, 'approveOrganizerRequest']);
    });

    // Organizer routes
    Route::middleware('role:organizer')->prefix('organizer')->group(function () {
        Route::apiResource('events', EventController::class);
        Route::apiResource('ticket-policies', TicketPolicyController::class);
        Route::post('notifications/send', [OrganizerNotificationController::class, 'send']);
        Route::get('dashboard', [EventController::class, 'dashboardStats']); // example dashboard method
    });

    // Attendee routes
    Route::middleware('role:attendee')->prefix('attendee')->group(function () {
        Route::get('events/upcoming', [EventViewController::class, 'upcoming']);
        Route::apiResource('tickets', TicketController::class);
        Route::apiResource('reminders', ReminderController::class);
    });

    // User routes (generic authenticated user)
    Route::middleware('role:user')->prefix('user')->group(function () {
        Route::post('request-organizer', [RequestController::class, 'store']);
        
    });

    // Stripe payment method routes
    Route::middleware('auth:sanctum')->post('/stripe/payment-method', [StripeController::class, 'storePaymentMethod']);

});
