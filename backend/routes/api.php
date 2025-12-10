<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\AdminController;

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

// Auth routes (session-based, no CSRF)
Route::middleware('api-session')->prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('check', [AuthController::class, 'checkAuth']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::put('profile', [AuthController::class, 'updateProfile']);
});

// Public restaurant routes
Route::prefix('restaurants')->group(function () {
    Route::get('/', [RestaurantController::class, 'index']);
    Route::get('filter-options', [RestaurantController::class, 'filterOptions']);
    Route::get('{id}', [RestaurantController::class, 'show']);
    Route::get('{id}/reviews', [RestaurantController::class, 'reviews']);
    Route::get('{id}/rating-breakdown', [RestaurantController::class, 'ratingBreakdown']);
    Route::get('{id}/related', [RestaurantController::class, 'related']);
});

// Dashboard statistics route
Route::get('statistics', [RestaurantController::class, 'statistics']);

// Public review routes
Route::prefix('reviews')->group(function () {
    Route::get('/', [ReviewController::class, 'index']);
    Route::get('{id}', [ReviewController::class, 'show']);
});

// Protected routes (require session-based authentication)
Route::middleware('api-session')->group(function () {

    // Review routes
    Route::prefix('reviews')->group(function () {
        Route::post('/', [ReviewController::class, 'store']);
        Route::put('{id}', [ReviewController::class, 'update']);
        Route::delete('{id}', [ReviewController::class, 'destroy']);
        Route::get('my/reviews', [ReviewController::class, 'userReviews']);
    });

    // User routes
    Route::prefix('users')->group(function () {
        Route::get('{id}', [UserController::class, 'show']);
        Route::put('{id}', [UserController::class, 'update']);
        Route::get('{id}/reviews', [UserController::class, 'reviews']);
        Route::get('{id}/favorites', [UserController::class, 'favorites']);
        Route::post('{id}/favorites', [UserController::class, 'addFavorite']);
        Route::delete('{id}/favorites/{restaurant_id}', [UserController::class, 'removeFavorite']);
    });

});

// Image routes (require session-based authentication)
Route::middleware('api-session')->prefix('images')->group(function () {
    Route::post('upload', [ImageController::class, 'upload']);
    Route::delete('{id}', [ImageController::class, 'destroy']);
    Route::put('{id}/primary', [ImageController::class, 'setPrimary']);
    Route::put('reorder', [ImageController::class, 'reorder']);
});

// Health check route
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running',
        'timestamp' => now()->toISOString()
    ]);
});

// Simple test route
Route::get('test', function () {
    return response()->json(['message' => 'API is working!']);
});

// Test image upload without authentication (temporary)
Route::post('test-upload', [ImageController::class, 'upload']);

// Admin routes (require admin authentication)
Route::middleware(['api-session', 'admin'])->prefix('admin')->group(function () {
    
    // Dashboard and statistics
    Route::get('dashboard/stats', [AdminController::class, 'getDashboardStats']);
    Route::get('dashboard/activity', [AdminController::class, 'getRecentActivity']);
    
    // User management
    Route::prefix('users')->group(function () {
        Route::get('/', [AdminController::class, 'getUsers']);
        Route::get('{id}', [AdminController::class, 'getUser']);
        Route::put('{id}', [AdminController::class, 'updateUser']);
        Route::post('{id}/ban', [AdminController::class, 'banUser']);
        Route::post('{id}/unban', [AdminController::class, 'unbanUser']);
        Route::delete('{id}', [AdminController::class, 'deleteUser']);
    });
    
    // Review management (use existing public endpoints with admin access)
    Route::prefix('reviews')->group(function () {
        Route::post('{id}/approve', [AdminController::class, 'approveReview']);
        Route::post('{id}/reject', [AdminController::class, 'rejectReview']);
    });
    
    // System settings
    Route::get('settings', [AdminController::class, 'getSettings']);
    Route::put('settings', [AdminController::class, 'updateSettings']);
});

// Admin access to existing public endpoints (restaurants, reviews)
Route::middleware(['api-session', 'admin'])->group(function () {
    // Admin can access restaurant management through existing endpoints
    Route::put('restaurants/{id}', [RestaurantController::class, 'update']);
    Route::delete('restaurants/{id}', [RestaurantController::class, 'destroy']);
});

// Redirect dashboard statistics to admin endpoint
Route::middleware(['api-session', 'admin'])->get('statistics', [AdminController::class, 'getDashboardStats']);
