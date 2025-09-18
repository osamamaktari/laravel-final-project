<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// This is a default route that comes with Laravel. It's protected by Sanctum.
Route::middleware(["auth:sanctum"])->get("/user", function (Request $request) {
    return $request->user();
});

//  a test route to make sure everything is working
Route::get("/test", function () {
    return response()->json(["message" => "API is working!"]);
});
