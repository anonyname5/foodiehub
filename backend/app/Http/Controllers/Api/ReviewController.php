<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends Controller
{
    /**
     * Get all reviews with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        // Check if this is an admin request
        $isAdminRequest = $request->user() && $request->user()->isAdmin();
        
        $query = Review::with(['user:id,name,avatar', 'restaurant:id,name,cuisine,location', 'images']);

        // For non-admin requests, only show approved reviews
        if (!$isAdminRequest) {
            $query->where('status', 'approved');
        }

        // Filter by restaurant
        if ($request->has('restaurant_id') && $request->restaurant_id) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        // Filter by user
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by rating
        if ($request->has('rating') && $request->rating) {
            $query->where('overall_rating', $request->rating);
        }

        // Filter by minimum rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->where('overall_rating', '>=', $request->min_rating);
        }

        // Filter by status (admin only)
        if ($isAdminRequest && $request->has('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality (admin)
        if ($isAdminRequest && $request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('comment', 'like', "%{$searchTerm}%")
                  ->orWhere('title', 'like', "%{$searchTerm}%")
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'like', "%{$searchTerm}%")
                                ->orWhere('email', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('restaurant', function($restaurantQuery) use ($searchTerm) {
                      $restaurantQuery->where('name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'newest');
        switch ($sortBy) {
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('overall_rating', 'desc');
                break;
            case 'lowest':
                $query->orderBy('overall_rating', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
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
     * Get a specific review by ID.
     */
    public function show($id): JsonResponse
    {
        $review = Review::with(['user:id,name,avatar', 'restaurant:id,name,cuisine,location', 'images'])->find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $review
        ]);
    }

    /**
     * Create a new review.
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required|exists:restaurants,id',
            'overall_rating' => 'required|numeric|min:1|max:5',
            'food_rating' => 'nullable|numeric|min:1|max:5',
            'service_rating' => 'nullable|numeric|min:1|max:5',
            'ambiance_rating' => 'nullable|numeric|min:1|max:5',
            'value_rating' => 'nullable|numeric|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'required|string|min:50|max:2000',
            'visit_date' => 'nullable|date|before_or_equal:today',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'string|max:255',
            'recommend' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user already reviewed this restaurant
        $existingReview = Review::where('user_id', $user->id)
            ->where('restaurant_id', $request->restaurant_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this restaurant'
            ], 422);
        }

        $review = Review::create([
            'user_id' => $user->id,
            'restaurant_id' => $request->restaurant_id,
            'overall_rating' => $request->overall_rating,
            'food_rating' => $request->food_rating,
            'service_rating' => $request->service_rating,
            'ambiance_rating' => $request->ambiance_rating,
            'value_rating' => $request->value_rating,
            'title' => $request->title,
            'comment' => $request->comment,
            'visit_date' => $request->visit_date,
            'photos' => $request->photos,
            'recommend' => $request->recommend,
        ]);

        // Update restaurant rating stats
        $restaurant = Restaurant::find($request->restaurant_id);
        if ($restaurant) {
            $restaurant->updateRatingStats();
        }

        $review->load(['user:id,name,avatar', 'restaurant:id,name,cuisine,location']);

        return response()->json([
            'success' => true,
            'message' => 'Review created successfully',
            'data' => $review
        ], 201);
    }

    /**
     * Update a review.
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

        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user owns the review
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this review'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'overall_rating' => 'sometimes|required|numeric|min:1|max:5',
            'food_rating' => 'nullable|numeric|min:1|max:5',
            'service_rating' => 'nullable|numeric|min:1|max:5',
            'ambiance_rating' => 'nullable|numeric|min:1|max:5',
            'value_rating' => 'nullable|numeric|min:1|max:5',
            'title' => 'nullable|string|max:255',
            'comment' => 'sometimes|required|string|min:50|max:2000',
            'visit_date' => 'nullable|date|before_or_equal:today',
            'photos' => 'nullable|array|max:5',
            'photos.*' => 'string|max:255',
            'recommend' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $review->update($request->only([
            'overall_rating', 'food_rating', 'service_rating', 'ambiance_rating', 'value_rating',
            'title', 'comment', 'visit_date', 'photos', 'recommend'
        ]));

        // Update restaurant rating stats
        $restaurant = Restaurant::find($review->restaurant_id);
        if ($restaurant) {
            $restaurant->updateRatingStats();
        }

        $review->load(['user:id,name,avatar', 'restaurant:id,name,cuisine,location']);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => $review
        ]);
    }

    /**
     * Delete a review.
     */
    public function destroy($id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $review = Review::find($id);

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        // Check if user owns the review
        if ($review->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this review'
            ], 403);
        }

        $restaurantId = $review->restaurant_id;
        $review->delete();

        // Update restaurant rating stats
        $restaurant = Restaurant::find($restaurantId);
        if ($restaurant) {
            $restaurant->updateRatingStats();
        }

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * Get user's reviews.
     */
    public function userReviews(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
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
}
