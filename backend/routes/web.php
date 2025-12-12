<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ProfileController;

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Restaurant routes
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{id}', [RestaurantController::class, 'show'])->name('restaurants.show');

// Review routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{id}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::put('/reviews/{id}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Helpful votes
    Route::post('/reviews/{id}/helpful', [\App\Http\Controllers\HelpfulVoteController::class, 'toggle'])->name('reviews.helpful');
    
    // Review responses (restaurant owners)
    Route::post('/reviews/{id}/response', [\App\Http\Controllers\ReviewResponseController::class, 'store'])->name('reviews.response.store');
    Route::put('/reviews/{id}/response', [\App\Http\Controllers\ReviewResponseController::class, 'update'])->name('reviews.response.update');
    Route::delete('/reviews/{id}/response', [\App\Http\Controllers\ReviewResponseController::class, 'destroy'])->name('reviews.response.destroy');
});

// Profile routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
    // Notification routes
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [\App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::get('/notifications/recent', [\App\Http\Controllers\NotificationController::class, 'recent'])->name('notifications.recent');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

// Restaurant Owner routes (authenticated + restaurant owner)
Route::middleware(['auth', 'restaurant_owner'])->prefix('restaurant-owner')->name('restaurant-owner.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\RestaurantOwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/edit', [\App\Http\Controllers\RestaurantOwnerController::class, 'edit'])->name('edit');
    Route::put('/update', [\App\Http\Controllers\RestaurantOwnerController::class, 'update'])->name('update');
    Route::get('/reviews', [\App\Http\Controllers\RestaurantOwnerController::class, 'reviews'])->name('reviews');
});

// Restaurant claiming (authenticated users only, no restaurant owner requirement)
Route::middleware('auth')->prefix('restaurant-owner')->name('restaurant-owner.')->group(function () {
    Route::get('/claim', [\App\Http\Controllers\RestaurantOwnerController::class, 'showClaim'])->name('claim');
    Route::post('/claim', [\App\Http\Controllers\RestaurantOwnerController::class, 'claim'])->name('claim.store');
});

// Image routes (authenticated)
Route::middleware('auth')->group(function () {
    Route::post('/images/upload', [\App\Http\Controllers\ImageController::class, 'upload'])->name('images.upload');
    Route::delete('/images/{id}', [\App\Http\Controllers\ImageController::class, 'destroy'])->name('images.destroy');
    Route::put('/images/{id}/primary', [\App\Http\Controllers\ImageController::class, 'setPrimary'])->name('images.primary');
    Route::put('/images/reorder', [\App\Http\Controllers\ImageController::class, 'reorder'])->name('images.reorder');
});

// Redirect old admin HTML files to new routes
Route::get('/admin/index.html', function () {
    return redirect()->route('admin.dashboard');
})->middleware(['auth', 'admin']);

// Admin routes (require admin authentication)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    
    // User management
    Route::get('/users', [\App\Http\Controllers\Admin\AdminController::class, 'users'])->name('users');
    Route::get('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'showUser'])->name('users.show');
    Route::put('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/ban', [\App\Http\Controllers\Admin\AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{id}/unban', [\App\Http\Controllers\Admin\AdminController::class, 'unbanUser'])->name('users.unban');
    Route::delete('/users/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteUser'])->name('users.delete');
    
    // Restaurant management
    Route::get('/restaurants', [\App\Http\Controllers\Admin\AdminController::class, 'restaurants'])->name('restaurants');
    Route::get('/restaurants/create', [\App\Http\Controllers\Admin\AdminController::class, 'createRestaurant'])->name('restaurants.create');
    Route::post('/restaurants', [\App\Http\Controllers\Admin\AdminController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::get('/restaurants/{id}/edit', [\App\Http\Controllers\Admin\AdminController::class, 'editRestaurant'])->name('restaurants.edit');
    Route::put('/restaurants/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::delete('/restaurants/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'deleteRestaurant'])->name('restaurants.delete');
    Route::post('/restaurants/{id}/toggle-status', [\App\Http\Controllers\Admin\AdminController::class, 'toggleRestaurantStatus'])->name('restaurants.toggle-status');
    
    // Review management
    Route::get('/reviews', [\App\Http\Controllers\Admin\AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews/{id}/approve', [\App\Http\Controllers\Admin\AdminController::class, 'approveReview'])->name('reviews.approve');
    Route::post('/reviews/{id}/reject', [\App\Http\Controllers\Admin\AdminController::class, 'rejectReview'])->name('reviews.reject');
    
    // Settings
    Route::get('/settings', [\App\Http\Controllers\Admin\AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [\App\Http\Controllers\Admin\AdminController::class, 'updateSettings'])->name('settings.update');
});
