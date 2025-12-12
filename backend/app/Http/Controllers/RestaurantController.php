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

        // Get filter options
        $cuisines = Restaurant::active()->distinct()->pluck('cuisine')->filter()->sort()->values();
        $priceRanges = Restaurant::active()->distinct()->pluck('price_range')->filter()->sort()->values();
        $locations = Restaurant::active()->distinct()->pluck('location')->filter()->sort()->values();

        // Paginate
        $restaurants = $query->withCount('reviews')
                            ->orderBy('average_rating', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->paginate(12);

        return view('restaurants.index', compact('restaurants', 'cuisines', 'priceRanges', 'locations'));
    }

    /**
     * Display the specified restaurant
     */
    public function show($id)
    {
        $restaurant = Restaurant::with('images')
                                ->withCount('reviews')
                                ->findOrFail($id);

        // Get reviews with pagination
        $reviews = Review::where('restaurant_id', $id)
                        ->where('status', 'approved')
                        ->with(['user', 'images'])
                        ->orderBy('created_at', 'desc')
                        ->paginate(10);

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

        return view('restaurants.show', compact('restaurant', 'reviews', 'ratingBreakdown'));
    }
}

