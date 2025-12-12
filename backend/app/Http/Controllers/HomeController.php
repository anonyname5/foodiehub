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

        return view('home', compact('statistics', 'featuredRestaurants', 'recentReviews'));
    }
}

