<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    /**
     * Display a listing of restaurants
     */
    public function index(Request $request)
    {
        $query = Restaurant::active()->with('images');

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

        // Filter by rating
        if ($request->has('min_rating') && $request->min_rating) {
            $query->where('average_rating', '>=', $request->min_rating);
        }

        // Filter by features
        if ($request->has('features') && is_array($request->features)) {
            foreach ($request->features as $feature) {
                $query->whereJsonContains('features', $feature);
            }
        }

        // Filter by open now (using working hours) - simplified version
        // Note: This is a basic implementation. For production, you'd want more sophisticated time parsing
        if ($request->has('open_now') && $request->open_now) {
            $currentDay = strtolower(now()->format('l')); // e.g., 'monday'
            
            $query->where(function($q) use ($currentDay) {
                $q->whereNotNull('hours')
                  ->whereJsonContains('hours', $currentDay);
                // Note: Full time range checking would require parsing the hours format
                // For now, we just check if the restaurant has hours for today
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'rating'); // rating, reviews, newest
        switch ($sortBy) {
            case 'reviews':
                $query->orderBy('review_count', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'rating':
            default:
                $query->orderBy('average_rating', 'desc');
                break;
        }
        $query->orderBy('created_at', 'desc'); // Secondary sort

        // Get filter options
        $cuisines = Restaurant::active()->distinct()->pluck('cuisine')->filter()->sort()->values();
        $priceRanges = Restaurant::active()->distinct()->pluck('price_range')->filter()->sort()->values();
        $locations = Restaurant::active()->distinct()->pluck('location')->filter()->sort()->values();
        
        // Get all unique features from restaurants
        $allFeatures = Restaurant::active()
            ->whereNotNull('features')
            ->get()
            ->pluck('features')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        // Paginate
        $restaurants = $query->withCount('reviews')
                            ->paginate(12);

        return view('restaurants.index', compact('restaurants', 'cuisines', 'priceRanges', 'locations', 'allFeatures'));
    }

    /**
     * Display the specified restaurant
     */
    public function show($id)
    {
        $restaurant = Restaurant::with('images')
                                ->withCount('reviews')
                                ->findOrFail($id);

        // Get reviews with sorting and filtering
        $reviewsQuery = Review::where('restaurant_id', $id)
                        ->where('status', 'approved')
                        ->with(['user', 'images', 'helpfulVotes', 'response.user']);

        // Filter by rating
        if ($request->has('rating_filter') && $request->rating_filter) {
            $reviewsQuery->where('overall_rating', $request->rating_filter);
        }

        // Filter by verified only
        if ($request->has('verified_only') && $request->verified_only) {
            $reviewsQuery->where('is_verified', true);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $reviewsQuery->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $reviewsQuery->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_reviews', 'newest'); // newest, highest, helpful, lowest
        switch ($sortBy) {
            case 'highest':
                $reviewsQuery->orderBy('overall_rating', 'desc');
                break;
            case 'lowest':
                $reviewsQuery->orderBy('overall_rating', 'asc');
                break;
            case 'helpful':
                $reviewsQuery->orderBy('helpful_count', 'desc');
                break;
            case 'newest':
            default:
                $reviewsQuery->orderBy('created_at', 'desc');
                break;
        }

        $reviews = $reviewsQuery->paginate(10);
        
        // Check which reviews the current user has voted helpful for
        $userHelpfulVotes = [];
        if (auth()->check()) {
            $userHelpfulVotes = \App\Models\HelpfulVote::where('user_id', auth()->id())
                ->whereIn('review_id', $reviews->pluck('id'))
                ->pluck('review_id')
                ->toArray();
        }

        // Get rating breakdown
        $ratingBreakdown = Review::where('restaurant_id', $id)
                                ->where('status', 'approved')
                                ->selectRaw('
                                    AVG(food_rating) as avg_food,
                                    AVG(service_rating) as avg_service,
                                    AVG(ambiance_rating) as avg_ambiance,
                                    AVG(value_rating) as avg_value
                                ')
                                ->first();

        return view('restaurants.show', compact('restaurant', 'reviews', 'ratingBreakdown', 'userHelpfulVotes'));
    }
}

