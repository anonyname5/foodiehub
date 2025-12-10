<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Restaurant;
use App\Models\Image;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Jalan Alor Food Street',
                'cuisine' => 'Malaysian Street Food',
                'description' => 'Famous street food destination featuring satay, char kway teow, Hokkien mee, and various local delicacies. A bustling night market atmosphere with authentic Malaysian flavors.',
                'address' => 'Jalan Alor, Bukit Bintang, 50200 Kuala Lumpur, Federal Territory of Kuala Lumpur',
                'phone' => '+603-2144 3297',
                'hours' => [
                    'monday' => '6:00 PM - 2:00 AM',
                    'tuesday' => '6:00 PM - 2:00 AM',
                    'wednesday' => '6:00 PM - 2:00 AM',
                    'thursday' => '6:00 PM - 2:00 AM',
                    'friday' => '6:00 PM - 3:00 AM',
                    'saturday' => '6:00 PM - 3:00 AM',
                    'sunday' => '6:00 PM - 2:00 AM',
                ],
                'price_range' => '$',
                'location' => 'Bukit Bintang, Kuala Lumpur',
                'latitude' => 3.1478,
                'longitude' => 101.7123,
                'main_image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1558618047-c8b1bec9c4e3?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&h=600&fit=crop',
                ],
                'features' => ['Street Food', 'Late Night', 'Outdoor Seating', 'Cash Only', 'Local Favorites'],
                'average_rating' => 4.6,
                'review_count' => 324,
            ],
            [
                'name' => 'Restaurant Rebung Chef Ismail',
                'cuisine' => 'Traditional Malay',
                'description' => 'Chef Ismail\'s renowned restaurant serving authentic Malay cuisine with a modern presentation. Famous for rendang, nasi kerabu, and traditional kuih desserts.',
                'address' => '7, Lorong Maarof, Bangsar, 59000 Kuala Lumpur, Federal Territory of Kuala Lumpur',
                'phone' => '+603-2287 8359',
                'hours' => [
                    'monday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'tuesday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'wednesday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'thursday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'friday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'saturday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                    'sunday' => '12:00 PM - 3:00 PM, 7:00 PM - 11:00 PM',
                ],
                'price_range' => '$$',
                'location' => 'Bangsar, Kuala Lumpur',
                'latitude' => 3.1281,
                'longitude' => 101.6669,
                'main_image' => 'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1558618047-c8b1bec9c4e3?w=800&h=600&fit=crop',
                ],
                'features' => ['Fine Dining', 'Reservations Recommended', 'Halal', 'Air Conditioned', 'Traditional Recipes'],
                'average_rating' => 4.5,
                'review_count' => 189,
            ],
            [
                'name' => 'Gurney Drive Hawker Centre',
                'cuisine' => 'Penang Street Food',
                'description' => 'Famous hawker centre offering the best Penang street food including assam laksa, char kway teow, rojak, and cendol. Seaside dining with spectacular sunset views.',
                'address' => 'Gurney Dr, George Town, 10250 George Town, Pulau Pinang',
                'phone' => '+604-261 6161',
                'hours' => [
                    'monday' => '6:00 PM - 1:00 AM',
                    'tuesday' => '6:00 PM - 1:00 AM',
                    'wednesday' => '6:00 PM - 1:00 AM',
                    'thursday' => '6:00 PM - 1:00 AM',
                    'friday' => '6:00 PM - 2:00 AM',
                    'saturday' => '6:00 PM - 2:00 AM',
                    'sunday' => '6:00 PM - 1:00 AM',
                ],
                'price_range' => '$',
                'location' => 'George Town, Penang',
                'latitude' => 5.4382,
                'longitude' => 100.3094,
                'main_image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&h=600&fit=crop',
                ],
                'features' => ['Hawker Centre', 'Sea View', 'Street Food', 'Cash Only', 'Local Institution'],
                'average_rating' => 4.7,
                'review_count' => 456,
            ],
            [
                'name' => 'Auntie Gaik Lean\'s Old School Eatery',
                'cuisine' => 'Nyonya Peranakan',
                'description' => 'Michelin-starred Nyonya restaurant serving authentic Peranakan dishes. Famous for ayam pongteh, jiu hu char, and nyonya laksa in a charming heritage shophouse.',
                'address' => '1, Bishop St, George Town, 10200 George Town, Pulau Pinang',
                'phone' => '+604-261 2626',
                'hours' => [
                    'monday' => 'Closed',
                    'tuesday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                    'wednesday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                    'thursday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                    'friday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                    'saturday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                    'sunday' => '11:30 AM - 2:30 PM, 5:30 PM - 9:30 PM',
                ],
                'price_range' => '$$',
                'location' => 'George Town, Penang',
                'latitude' => 5.4164,
                'longitude' => 100.3327,
                'main_image' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1558618047-c8b1bec9c4e3?w=800&h=600&fit=crop',
                ],
                'features' => ['Michelin Star', 'Heritage Building', 'Authentic Nyonya', 'Reservations Required', 'Air Conditioned'],
                'average_rating' => 4.8,
                'review_count' => 278,
            ],
            [
                'name' => 'Hiap Joo Bakery & Biscuit Factory',
                'cuisine' => 'Local Bakery',
                'description' => 'Historic bakery established in 1919, famous for traditional banana cake, kaya toast, and local coffee. A heritage institution preserving old-school baking methods.',
                'address' => '13, 15, Jln Tan Hiok Nee, Bandar Johor Bahru, 80000 Johor Bahru, Johor',
                'phone' => '+607-224 6475',
                'hours' => [
                    'monday' => '6:30 AM - 6:30 PM',
                    'tuesday' => '6:30 AM - 6:30 PM',
                    'wednesday' => '6:30 AM - 6:30 PM',
                    'thursday' => '6:30 AM - 6:30 PM',
                    'friday' => '6:30 AM - 6:30 PM',
                    'saturday' => '6:30 AM - 6:30 PM',
                    'sunday' => '6:30 AM - 6:30 PM',
                ],
                'price_range' => '$',
                'location' => 'Johor Bahru, Johor',
                'latitude' => 1.4581,
                'longitude' => 103.7618,
                'main_image' => 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1555507036-ab794f1d3ff3?w=800&h=600&fit=crop',
                ],
                'features' => ['Historic Bakery', 'Traditional Recipes', 'Kaya Toast', 'Local Coffee', 'Heritage Building'],
                'average_rating' => 4.4,
                'review_count' => 167,
            ],
            [
                'name' => 'Top Spot Food Court',
                'cuisine' => 'Sarawakian Seafood',
                'description' => 'Famous rooftop food court specializing in fresh seafood and Sarawakian dishes. Known for butter prawns, midin fern, and kampua noodles with panoramic city views.',
                'address' => 'Jln Padang, Kuching, 93100 Kuching, Sarawak',
                'phone' => '+608-223 3681',
                'hours' => [
                    'monday' => '5:00 PM - 11:00 PM',
                    'tuesday' => '5:00 PM - 11:00 PM',
                    'wednesday' => '5:00 PM - 11:00 PM',
                    'thursday' => '5:00 PM - 11:00 PM',
                    'friday' => '5:00 PM - 12:00 AM',
                    'saturday' => '5:00 PM - 12:00 AM',
                    'sunday' => '5:00 PM - 11:00 PM',
                ],
                'price_range' => '$$',
                'location' => 'Kuching, Sarawak',
                'latitude' => 1.5541,
                'longitude' => 110.3592,
                'main_image' => 'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1559847844-5315695dadae?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=600&fit=crop',
                ],
                'features' => ['Rooftop Dining', 'Fresh Seafood', 'City View', 'Sarawakian Cuisine', 'Open Air'],
                'average_rating' => 4.3,
                'review_count' => 298,
            ],
            [
                'name' => 'Restaurant Yusof Dan Zakhir',
                'cuisine' => 'Indian Muslim',
                'description' => 'Legendary nasi kandar restaurant serving authentic North Malaysian Indian Muslim cuisine. Famous for their aromatic curries, tender meat dishes, and fluffy naan bread.',
                'address' => 'Jalan Putra, 05100 Alor Setar, Kedah',
                'phone' => '+604-731 7336',
                'hours' => [
                    'monday' => '6:00 AM - 10:00 PM',
                    'tuesday' => '6:00 AM - 10:00 PM',
                    'wednesday' => '6:00 AM - 10:00 PM',
                    'thursday' => '6:00 AM - 10:00 PM',
                    'friday' => '6:00 AM - 10:00 PM',
                    'saturday' => '6:00 AM - 10:00 PM',
                    'sunday' => '6:00 AM - 10:00 PM',
                ],
                'price_range' => '$',
                'location' => 'Alor Setar, Kedah',
                'latitude' => 6.1254,
                'longitude' => 100.3673,
                'main_image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1596040033229-a9821ebd058d?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1551218808-94e220e084d2?w=800&h=600&fit=crop',
                ],
                'features' => ['Nasi Kandar', 'Halal', 'Authentic Curries', 'Local Institution', '24/7 Service'],
                'average_rating' => 4.6,
                'review_count' => 203,
            ],
            [
                'name' => 'Kedai Makanan dan Minuman Chow Kit',
                'cuisine' => 'Chinese Malaysian',
                'description' => 'Traditional Chinese kopitiam serving authentic wonton noodles, Hainanese chicken rice, and kopi. A local favorite for breakfast and lunch in the heart of KL.',
                'address' => 'Jalan Raja Alang, Chow Kit, 50300 Kuala Lumpur, Federal Territory of Kuala Lumpur',
                'phone' => '+603-2691 2468',
                'hours' => [
                    'monday' => '7:00 AM - 3:00 PM',
                    'tuesday' => '7:00 AM - 3:00 PM',
                    'wednesday' => '7:00 AM - 3:00 PM',
                    'thursday' => '7:00 AM - 3:00 PM',
                    'friday' => '7:00 AM - 3:00 PM',
                    'saturday' => '7:00 AM - 3:00 PM',
                    'sunday' => '7:00 AM - 3:00 PM',
                ],
                'price_range' => '$',
                'location' => 'Chow Kit, Kuala Lumpur',
                'latitude' => 3.1569,
                'longitude' => 101.6984,
                'main_image' => 'https://images.unsplash.com/photo-1563379091339-03246963d96a?w=800&h=600&fit=crop',
                'images' => [
                    'https://images.unsplash.com/photo-1563379091339-03246963d96a?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1547592180-85f173990554?w=800&h=600&fit=crop',
                    'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=800&h=600&fit=crop',
                ],
                'features' => ['Kopitiam', 'Traditional Coffee', 'Wonton Noodles', 'Local Breakfast', 'Cash Only'],
                'average_rating' => 4.5,
                'review_count' => 156,
            ],
        ];

        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Clear related tables first
        DB::table('favorites')->truncate();
        DB::table('reviews')->truncate();
        DB::table('images')->where('imageable_type', 'App\\Models\\Restaurant')->delete();
        
        // Clear existing restaurants
        Restaurant::truncate();
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create restaurants with images
        foreach ($restaurants as $restaurantData) {
            // Extract images array before creating restaurant
            $images = $restaurantData['images'] ?? [];
            unset($restaurantData['images']); // Remove images from restaurant data
            
            // Create restaurant
            $restaurant = Restaurant::create($restaurantData);
            
            // Create related images if they exist
            if (!empty($images)) {
                foreach ($images as $index => $imageUrl) {
                    $filename = basename($imageUrl);
                    Image::create([
                        'imageable_type' => Restaurant::class,
                        'imageable_id' => $restaurant->id,
                        'filename' => $filename,
                        'original_name' => $filename,
                        'path' => $imageUrl,
                        'url' => $imageUrl,
                        'size' => 0, // Default to 0 for external images
                        'mime_type' => 'image/jpeg', // Assume JPEG for external images
                        'sort_order' => $index,
                        'is_primary' => $index === 0 // First image is primary
                    ]);
                }
            }
        }
    }
}