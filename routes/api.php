<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Auth
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // ! Protected
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth', [AuthController::class, 'get']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::get('/files/{filename}', [FileController::class, 'show']);

        // Reply(s)
        Route::resource('/ticket/reply', TicketReplyController::class);
        // Ticket(s)
        Route::resource('/ticket', TicketController::class);
        Route::put('/ticket/{id}/close', [TicketController::class, 'close']);
        // User(s)
        Route::resource('/user', UserController::class);
    });
});
