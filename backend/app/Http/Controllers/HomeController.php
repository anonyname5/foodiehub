<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Get statistics
        $statistics = [
            'restaurants' => Restaurant::active()->count(),
            'reviews' => Review::where('status', 'approved')->count(),
            'users' => User::count(),
            'cities' => Restaurant::active()->distinct('location')->count('location'),
        ];

        // Get featured restaurants (top rated)
        $featuredRestaurants = Restaurant::active()
            ->with('images')
            ->withCount('reviews')
            ->orderBy('average_rating', 'desc')
            ->take(6)
            ->get();

        // Get recent reviews
        $recentReviews = Review::where('status', 'approved')
            ->with(['user', 'restaurant', 'images'])
            ->latest()
            ->take(6)
            ->get();

        // Get personalized recommendations for authenticated users
        $recommendations = collect();
        if (auth()->check()) {
            $user = auth()->user();
            
            // Get user's favorite cuisines from their reviews
            $favoriteCuisines = $user->reviews()
                ->join('restaurants', 'reviews.restaurant_id', '=', 'restaurants.id')
                ->selectRaw('restaurants.cuisine, COUNT(*) as count')
                ->groupBy('restaurants.cuisine')
                ->orderByDesc('count')
                ->limit(3)
                ->pluck('restaurants.cuisine');
            
            // Get restaurants with similar cuisines that user hasn't reviewed
            if ($favoriteCuisines->isNotEmpty()) {
                $reviewedRestaurantIds = $user->reviews()->pluck('restaurant_id')->toArray();
                
                $recommendations = Restaurant::active()
                    ->whereIn('cuisine', $favoriteCuisines)
                    ->whereNotIn('id', $reviewedRestaurantIds)
                    ->with('images')
                    ->withCount('reviews')
                    ->orderBy('average_rating', 'desc')
                    ->take(6)
                    ->get();
            }
            
            // If not enough recommendations, add popular restaurants
            if ($recommendations->count() < 6) {
                $reviewedRestaurantIds = $user->reviews()->pluck('restaurant_id')->toArray();
                $additional = Restaurant::active()
                    ->whereNotIn('id', $reviewedRestaurantIds)
                    ->with('images')
                    ->withCount('reviews')
                    ->orderBy('average_rating', 'desc')
                    ->orderBy('review_count', 'desc')
                    ->take(6 - $recommendations->count())
                    ->get();
                
                $recommendations = $recommendations->merge($additional);
            }
        } else {
            // For guests, show top-rated restaurants
            $recommendations = Restaurant::active()
                ->with('images')
                ->withCount('reviews')
                ->orderBy('average_rating', 'desc')
                ->take(6)
                ->get();
        }

        return view('home', compact('statistics', 'featuredRestaurants', 'recentReviews', 'recommendations'));
    }
}

