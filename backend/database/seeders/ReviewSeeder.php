<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\User;
use App\Models\Restaurant;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $restaurants = Restaurant::all();

        if ($users->isEmpty() || $restaurants->isEmpty()) {
            return;
        }

        $sampleReviews = [
            [
                'user_id' => $users->first()->id,
                'restaurant_id' => $restaurants->first()->id,
                'overall_rating' => 5.0,
                'food_rating' => 5.0,
                'service_rating' => 4.5,
                'ambiance_rating' => 4.5,
                'value_rating' => 4.0,
                'title' => 'Absolutely Amazing!',
                'comment' => 'Absolutely amazing! The pasta was perfectly cooked and the service was excellent. Will definitely come back! The atmosphere was cozy and the staff was very friendly.',
                'visit_date' => now()->subDays(5),
                'recommend' => true,
                'helpful_count' => 12,
            ],
            [
                'user_id' => $users->skip(1)->first()->id ?? $users->first()->id,
                'restaurant_id' => $restaurants->skip(1)->first()->id ?? $restaurants->first()->id,
                'overall_rating' => 4.0,
                'food_rating' => 4.5,
                'service_rating' => 3.5,
                'ambiance_rating' => 4.0,
                'value_rating' => 3.5,
                'title' => 'Great Food, Slow Service',
                'comment' => 'Great food and atmosphere. The portions were generous and the staff was friendly. Only minor issue was the wait time for our order.',
                'visit_date' => now()->subDays(3),
                'recommend' => true,
                'helpful_count' => 8,
            ],
            [
                'user_id' => $users->skip(2)->first()->id ?? $users->first()->id,
                'restaurant_id' => $restaurants->skip(2)->first()->id ?? $restaurants->first()->id,
                'overall_rating' => 5.0,
                'food_rating' => 5.0,
                'service_rating' => 5.0,
                'ambiance_rating' => 4.5,
                'value_rating' => 4.5,
                'title' => 'Perfect Date Night Spot',
                'comment' => 'Perfect date night spot! The ambiance was romantic and the food was outstanding. Highly recommend the chef\'s special.',
                'visit_date' => now()->subDays(1),
                'recommend' => true,
                'helpful_count' => 15,
            ],
        ];

        foreach ($sampleReviews as $reviewData) {
            Review::create($reviewData);
        }
    }
}
