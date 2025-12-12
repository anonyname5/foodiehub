<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the form for creating a new review
     */
    public function create(Request $request)
    {
        $restaurantId = $request->get('restaurant_id');
        $restaurant = $restaurantId ? Restaurant::findOrFail($restaurantId) : null;
        
        return view('reviews.create', compact('restaurant'));
    }

    /**
     * Store a newly created review
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'food_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'ambiance_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'recommend' => 'nullable|boolean',
        ]);

        // Calculate overall rating as average of all ratings
        $overallRating = (
            $validated['food_rating'] + 
            $validated['service_rating'] + 
            $validated['ambiance_rating'] + 
            $validated['value_rating']
        ) / 4;

        $review = Review::create([
            'user_id' => Auth::id(),
            'restaurant_id' => $validated['restaurant_id'],
            'overall_rating' => round($overallRating, 2),
            'food_rating' => $validated['food_rating'],
            'service_rating' => $validated['service_rating'],
            'ambiance_rating' => $validated['ambiance_rating'],
            'value_rating' => $validated['value_rating'],
            'title' => $validated['title'],
            'content' => $validated['content'],
            'recommend' => $validated['recommend'] ?? false,
            'status' => 'pending', // Reviews need approval
        ]);

        // Handle image uploads if any
        if ($request->hasFile('images')) {
            // Image upload logic here
        }

        return redirect()->route('restaurants.show', $validated['restaurant_id'])
                        ->with('success', 'Review submitted successfully! It will be reviewed before being published.');
    }

    /**
     * Show the form for editing a review
     */
    public function edit($id)
    {
        $review = Review::where('user_id', Auth::id())
                        ->with(['restaurant', 'images'])
                        ->findOrFail($id);
        return view('reviews.edit', compact('review'));
    }

    /**
     * Update a review
     */
    public function update(Request $request, $id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);

        $validated = $request->validate([
            'food_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'ambiance_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:5000',
            'recommend' => 'nullable|boolean',
        ]);

        // Calculate overall rating
        $overallRating = (
            $validated['food_rating'] + 
            $validated['service_rating'] + 
            $validated['ambiance_rating'] + 
            $validated['value_rating']
        ) / 4;
        
        $validated['overall_rating'] = round($overallRating, 2);
        $review->update($validated);
        $review->update(['status' => 'pending']); // Re-submit for approval

        return redirect()->route('restaurants.show', $review->restaurant_id)
                        ->with('success', 'Review updated successfully!');
    }

    /**
     * Delete a review
     */
    public function destroy($id)
    {
        $review = Review::where('user_id', Auth::id())->findOrFail($id);
        $restaurantId = $review->restaurant_id;
        $review->delete();

        return redirect()->route('restaurants.show', $restaurantId)
                        ->with('success', 'Review deleted successfully!');
    }
}

