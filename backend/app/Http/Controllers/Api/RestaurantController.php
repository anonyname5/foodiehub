<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    /**
     * Get all restaurants with optional filtering and pagination.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if this is an admin request
        $isAdminRequest = $request->user() && $request->user()->isAdmin();
        
        // For admin requests, don't filter by active status
        $query = $isAdminRequest ? 
            Restaurant::with(['images', 'primaryImage']) : 
            Restaurant::active()->with(['images', 'primaryImage']);

        // Search
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('cuisine', 'like', "%{$searchTerm}%")
                  ->orWhere('location', 'like', "%{$searchTerm}%")
                  ->orWhere('address', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by cuisine
        if ($request->has('cuisine') && $request->cuisine) {
            $query->where('cuisine', $request->cuisine);
        }

        // Filter by price range
        if ($request->has('price_range') && $request->price_range) {
            $query->where('price_range', $request->price_range);
        }

        // Filter by location
        if ($request->has('location') && $request->location) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Filter by status (admin only)
        if ($isAdminRequest && $request->has('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by minimum rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'rating');
        switch ($sortBy) {
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'reviews':
                $query->orderBy('review_count', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderByRaw("LENGTH(price_range) ASC");
                break;
            case 'price_high':
                $query->orderByRaw("LENGTH(price_range) DESC");
                break;
            default:
                $query->orderBy('average_rating', 'desc');
        }

        // Pagination
        $perPage = $request->get('per_page', 12);
        $restaurants = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $restaurants->items(),
            'pagination' => [
                'current_page' => $restaurants->currentPage(),
                'last_page' => $restaurants->lastPage(),
                'per_page' => $restaurants->perPage(),
                'total' => $restaurants->total(),
                'has_more' => $restaurants->hasMorePages(),
            ]
        ]);
    }

    /**
     * Get a specific restaurant by ID.
     */
    public function show($id): JsonResponse
    {
        $restaurant = Restaurant::active()->with(['images', 'primaryImage', 'reviews.user'])->find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $restaurant
        ]);
    }

    /**
     * Get restaurant reviews.
     */
    public function reviews(Request $request, $id): JsonResponse
    {
        $restaurant = Restaurant::active()->find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        $query = $restaurant->reviews()->with(['user:id,name,avatar', 'images']);

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
     * Get restaurant rating breakdown.
     */
    public function ratingBreakdown($id): JsonResponse
    {
        $restaurant = Restaurant::active()->find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'average_rating' => $restaurant->average_rating,
                'review_count' => $restaurant->review_count,
                'breakdown' => $restaurant->rating_breakdown
            ]
        ]);
    }

    /**
     * Get related restaurants.
     */
    public function related($id): JsonResponse
    {
        $restaurant = Restaurant::active()->find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        $related = Restaurant::active()
            ->where('id', '!=', $id)
            ->where(function ($query) use ($restaurant) {
                $query->where('cuisine', $restaurant->cuisine)
                      ->orWhere('location', $restaurant->location);
            })
            ->orderBy('average_rating', 'desc')
            ->limit(3)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $related
        ]);
    }

    /**
     * Get available filter options.
     */
    public function filterOptions(): JsonResponse
    {
        $cuisines = Restaurant::active()->distinct()->pluck('cuisine')->sort()->values();
        $locations = Restaurant::active()->distinct()->pluck('location')->sort()->values();
        $priceRanges = Restaurant::active()->distinct()->pluck('price_range')->sort()->values();

        return response()->json([
            'success' => true,
            'data' => [
                'cuisines' => $cuisines,
                'locations' => $locations,
                'price_ranges' => $priceRanges,
            ]
        ]);
    }

    /**
     * Get dashboard statistics.
     */
    public function statistics(): JsonResponse
    {
        $restaurantCount = Restaurant::count();
        $reviewCount = Review::count();
        $userCount = User::count();
        
        // Count unique cities from restaurant locations
        $cityCount = Restaurant::distinct('location')
            ->whereNotNull('location')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'restaurants' => $restaurantCount,
                'reviews' => $reviewCount,
                'users' => $userCount,
                'cities' => $cityCount,
            ]
        ]);
    }

    /**
     * Update a restaurant (admin only).
     */
    public function update(Request $request, $id): JsonResponse
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'cuisine' => 'sometimes|string|max:100',
            'location' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'phone' => 'sometimes|string|max:20',
            'email' => 'sometimes|email|max:255',
            'website' => 'sometimes|url|max:255',
            'description' => 'sometimes|string|max:2000',
            'price_range' => 'sometimes|string|max:10',
            'opening_hours' => 'sometimes|json',
            'is_active' => 'sometimes|boolean',
        ]);

        $restaurant->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Restaurant updated successfully',
            'data' => $restaurant->fresh()
        ]);
    }

    /**
     * Delete a restaurant (admin only).
     */
    public function destroy($id): JsonResponse
    {
        $restaurant = Restaurant::find($id);

        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }

        $restaurant->delete();

        return response()->json([
            'success' => true,
            'message' => 'Restaurant deleted successfully'
        ]);
    }
}
