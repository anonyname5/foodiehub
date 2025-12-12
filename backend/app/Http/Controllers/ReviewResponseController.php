<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ReviewResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewResponseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a response to a review (restaurant owner only)
     */
    public function store(Request $request, $reviewId)
    {
        $review = Review::with('restaurant')->findOrFail($reviewId);
        
        // Check if user is the restaurant owner
        if (!$review->restaurant->owner_id || $review->restaurant->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Only the restaurant owner can respond to reviews.');
        }

        // Check if response already exists
        if ($review->response) {
            return redirect()->back()->with('error', 'You have already responded to this review.');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:2000',
        ]);

        ReviewResponse::create([
            'review_id' => $reviewId,
            'user_id' => Auth::id(),
            'response' => $validated['response'],
        ]);

        return redirect()->back()->with('success', 'Response posted successfully!');
    }

    /**
     * Update a response
     */
    public function update(Request $request, $reviewId)
    {
        $review = Review::with('restaurant')->findOrFail($reviewId);
        
        // Check if user is the restaurant owner
        if (!$review->restaurant->owner_id || $review->restaurant->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Only the restaurant owner can update responses.');
        }

        $response = $review->response;
        if (!$response) {
            return redirect()->back()->with('error', 'Response not found.');
        }

        $validated = $request->validate([
            'response' => 'required|string|max:2000',
        ]);

        $response->update($validated);

        return redirect()->back()->with('success', 'Response updated successfully!');
    }

    /**
     * Delete a response
     */
    public function destroy($reviewId)
    {
        $review = Review::with('restaurant')->findOrFail($reviewId);
        
        // Check if user is the restaurant owner
        if (!$review->restaurant->owner_id || $review->restaurant->owner_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Only the restaurant owner can delete responses.');
        }

        $response = $review->response;
        if ($response) {
            $response->delete();
        }

        return redirect()->back()->with('success', 'Response deleted successfully!');
    }
}
