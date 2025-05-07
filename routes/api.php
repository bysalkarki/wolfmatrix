<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImportUserCsvController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;

Route::post("/register", [AuthController::class, "register"]);
Route::post("/login", [AuthController::class, "login"])->middleware(
    "throttle:3,1"
);
Route::middleware("auth:api")->group(function () {
    Route::post("/refresh", [AuthController::class, "refresh"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::resource("/category", CategoryController::class);
    Route::post("/user-csv-import", [ImportUserCsvController::class, "import"]);
    Route::post("/ticket/{ticketId}/reserve", [
        TicketController::class,
        "reserve",
    ]);
});
