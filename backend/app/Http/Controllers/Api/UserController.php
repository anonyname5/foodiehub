<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Get user profile by ID.
     */
    public function show($id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Only show public profile unless it's the authenticated user
        $authUser = Auth::user();
        if (!$user->is_public && (!$authUser || $authUser->id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User profile is private'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $user->only(['id', 'name', 'bio', 'avatar', 'created_at'])
        ]);
    }

    /**
     * Update user profile.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Users can only update their own profile
        if ($user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this profile'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|string|max:255',
            'is_public' => 'sometimes|boolean',
            'email_notifications' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->only([
            'name', 'bio', 'avatar', 'is_public', 'email_notifications'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'data' => $user->only(['id', 'name', 'email', 'bio', 'avatar', 'is_public', 'email_notifications'])
        ]);
    }

    /**
     * Get user's reviews.
     */
    public function reviews(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if profile is public
        $authUser = Auth::user();
        if (!$user->is_public && (!$authUser || $authUser->id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User profile is private'
            ], 403);
        }

        $query = $user->reviews()->with('restaurant:id,name,cuisine,location');

        // Sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'newest':
                $query->newest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'highest':
                $query->highestRated();
                break;
            case 'lowest':
                $query->lowestRated();
                break;
            default:
                $query->newest();
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reviews->items(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
                'has_more' => $reviews->hasMorePages(),
            ]
        ]);
    }

    /**
     * Get user's favorite restaurants.
     */
    public function favorites(Request $request, $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        // Check if profile is public
        $authUser = Auth::user();
        if (!$user->is_public && (!$authUser || $authUser->id !== $user->id)) {
            return response()->json([
                'success' => false,
                'message' => 'User profile is private'
            ], 403);
        }

        $query = $user->favoriteRestaurants();

        // Sorting
        $sortBy = $request->get('sort_by', 'recent');
        switch ($sortBy) {
            case 'recent':
                $query->orderBy('favorites.created_at', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->orderBy('favorites.created_at', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $favorites = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $favorites->items(),
            'pagination' => [
                'current_page' => $favorites->currentPage(),
                'last_page' => $favorites->lastPage(),
                'per_page' => $favorites->perPage(),
                'total' => $favorites->total(),
                'has_more' => $favorites->hasMorePages(),
            ]
        ]);
    }

    /**
     * Add restaurant to favorites.
     */
    public function addFavorite(Request $request, $id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Users can only manage their own favorites
        if ($user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to manage this user\'s favorites'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $restaurantId = $request->restaurant_id;

        // Check if already favorited
        if ($user->favoriteRestaurants()->where('restaurant_id', $restaurantId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant already in favorites'
            ], 422);
        }

        $user->favoriteRestaurants()->attach($restaurantId);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant added to favorites'
        ]);
    }

    /**
     * Remove restaurant from favorites.
     */
    public function removeFavorite($id, $restaurantId): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        // Users can only manage their own favorites
        if ($user->id != $id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to manage this user\'s favorites'
            ], 403);
        }

        $user->favoriteRestaurants()->detach($restaurantId);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant removed from favorites'
        ]);
    }
}
