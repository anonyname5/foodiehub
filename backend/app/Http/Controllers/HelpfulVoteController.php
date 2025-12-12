<?php

namespace App\Http\Controllers;

use App\Models\HelpfulVote;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HelpfulVoteController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle helpful vote for a review
     */
    public function toggle(Request $request, $reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $userId = Auth::id();

        // Check if user already voted
        $vote = HelpfulVote::where('review_id', $reviewId)
                           ->where('user_id', $userId)
                           ->first();

        if ($vote) {
            // Remove vote
            $vote->delete();
            $helpful = false;
        } else {
            // Add vote
            HelpfulVote::create([
                'review_id' => $reviewId,
                'user_id' => $userId,
            ]);
            $helpful = true;
        }

        // Update helpful count
        $review->updateHelpfulCount();
        $review->refresh();
        $helpfulCount = $review->helpful_count;

        return response()->json([
            'success' => true,
            'helpful' => $helpful,
            'helpful_count' => $helpfulCount,
        ]);
    }
}
