<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\MeController;
use App\Http\Controllers\Category\CategoryController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Department\DepartmentController;
use App\Http\Controllers\Ticket\TicketCommentController;
use App\Http\Controllers\Ticket\TicketController;
use App\Http\Controllers\Ticket\TicketAttachmentController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', LoginController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', LogoutController::class);
        Route::get('/me', MeController::class);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{user}', [UserController::class, 'show']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/departments', [DepartmentController::class, 'index']);
    Route::post('/departments', [DepartmentController::class, 'store']);
    Route::get('/departments/{department}', [DepartmentController::class, 'show']);
    Route::put('/departments/{department}', [DepartmentController::class, 'update']);
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy']);

    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

    Route::get('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'index']);
    Route::post('/tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store']);
    Route::delete('/tickets/{ticket}/attachments/{attachment}', [TicketAttachmentController::class, 'destroy']);

    Route::get('/tickets', [TicketController::class, 'index']);
    Route::post('/tickets', [TicketController::class, 'store']);
    Route::get('/tickets/{ticket}', [TicketController::class, 'show']);
    Route::put('/tickets/{ticket}', [TicketController::class, 'update']);
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy']);
    Route::post('/tickets/{ticket}/assign', [TicketController::class, 'assign']);
    Route::post('/tickets/{ticket}/status', [TicketController::class, 'changeStatus']);

    Route::get('/tickets/{ticket}/comments', [TicketCommentController::class, 'index']);
    Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store']);
    Route::get('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'show']);
    Route::put('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'update']);
    Route::delete('/tickets/{ticket}/comments/{comment}', [TicketCommentController::class, 'destroy']);

    Route::get('/dashboard/metrics', [DashboardController::class, 'metrics']);
    Route::get('/dashboard/by-status', [DashboardController::class, 'byStatus']);
    Route::get('/dashboard/by-priority', [DashboardController::class, 'byPriority']);
    Route::get('/dashboard/by-category', [DashboardController::class, 'byCategory']);
    Route::get('/dashboard/by-technician', [DashboardController::class, 'byTechnician']);
    Route::get('/dashboard/recent-tickets', [DashboardController::class, 'recentTickets']);
    Route::get('/dashboard/avg-resolution-time', [DashboardController::class, 'avgResolutionTime']);
    Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
});
