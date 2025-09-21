<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketTypeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;

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

// Public routes (no authentication required)
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);
Route::get("/events", [EventController::class, "index"]); //
Route::get("/events/{event}", [EventController::class, "show"]);

Route::get('/qr', function () {
    return QrCode::size(200)->generate('Hello World');
});
Route::get('/pdf', function () {
    $html = '<h1>Hello PDF</h1>';
    $pdf = Pdf::loadHTML($html);
    return $pdf->stream('sample.pdf');
});


// Protected routes (authentication required)
Route::middleware(["auth:sanctum"])->group(function () {
    // Auth Routes
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/user", [AuthController::class, "user"]);

    // Notification Routes
    Route::get("/notifications", [NotificationController::class, "index"]);
    Route::post("/notifications/{id}/read", [NotificationController::class, "markAsRead"]);
    Route::post("/notifications/mark-all-read", [NotificationController::class, "markAllAsRead"]);
    Route::get("/notifications/unread-count", [NotificationController::class, "unreadCount"]);

    // Attendee Specific Routes
    Route::middleware(["role:attendee"])->group(function () {
        Route::post("/events/{event}/orders", [OrderController::class, "store"]);
        // Route::post("/orders/{order}/pay", [OrderController::class, "pay"]);
        Route::get("/user/orders", [OrderController::class, "userOrders"]);
        Route::get("/orders/{order}", [OrderController::class, "show"]);
        Route::get("/user/tickets", [TicketController::class, "userTickets"]);
        Route::get("/tickets/{ticket}", [TicketController::class, "show"]);
        Route::get("/tickets/{ticket}/download", [TicketController::class, "download"]);
    });

    // Organizer Specific Routes (or Admin)
    Route::middleware(["role:organizer,admin"])->group(function () {
        Route::get("/organizer/events", [EventController::class, "organizerEvents"]);
        Route::post("/organizer/events", [EventController::class, "store"]);
        Route::post("/organizer/events/{event}", [EventController::class, "update"]);
        Route::delete("/organizer/events/{event}", [EventController::class, "destroy"]);

        // Ticket Types Management
        Route::post("/organizer/events/{event}/ticket-types", [TicketTypeController::class, "store"]);
        Route::put("/organizer/ticket-types/{ticketType}", [TicketTypeController::class, "update"]);
        Route::delete("/organizer/ticket-types/{ticketType}", [TicketTypeController::class, "destroy"]);

        // Ticket Validation (for organizers/admins to scan tickets)
        Route::post("/tickets/{ticket}/validate", [TicketController::class, "validateTicket"]);
    });

    // Admin Specific Routes
    Route::middleware(["role:admin"])->group(function () {
        Route::get("/admin/dashboard", [DashboardController::class, "adminDashboard"]);
        Route::post("/admin/events/{event}/status", [EventController::class, "approveReject"]);

    });
});

// Test route (can be removed later)
Route::get("/test", function () {
    return response()->json(["message" => "API is working!"]);
});
