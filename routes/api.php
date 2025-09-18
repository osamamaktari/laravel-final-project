<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|

|
*/

// Public routes (no authentication required)
Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"]);

// Protected routes (authentication required)
Route::middleware(["auth:sanctum"])->group(function () {
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/user", [AuthController::class, "user"]);

  //the rest added here
});

//  a test route to make sure everything is working (can be removed later)
Route::get("/test", function () {
    return response()->json(["message" => "API is working!"]);
});
