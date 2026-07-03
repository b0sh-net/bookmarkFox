<?php

use Illuminate\Support\Facades\Route;
use App\Controllers\AuthController;
use App\Controllers\BookmarkSyncController;
use App\Controllers\PublicPageController;

Route::prefix('api/v1')->group(function () {

    // Public auth routes (rate limited: 5 attempts per minute)
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/bookmarks/sync', [BookmarkSyncController::class, 'sync']);
    });
});

// Homepage
Route::get('/', [PublicPageController::class, 'home']);

// Public bookmark browsing pages (unlimited nesting depth)
Route::get('/{email}/{path?}', [PublicPageController::class, 'browse'])
    ->where('email', '[^/]+@[^/]+')
    ->where('path', '.*');
