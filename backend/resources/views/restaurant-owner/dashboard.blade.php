@extends('layouts.app')

@section('title', 'Restaurant Owner Dashboard - FoodieHub')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">My Restaurant Dashboard</h1>
        <p class="text-gray-600 mt-2">Manage your restaurant information and view statistics</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-star text-blue-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Average Rating</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['average_rating'], 1) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-comments text-green-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Total Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_reviews'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Reviews</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_reviews'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-lg">
                    <i class="fas fa-heart text-red-500 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Favorites</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_favorites'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Restaurant Info Card -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Restaurant Information</h2>
                    <a href="{{ route('restaurant-owner.edit') }}" class="text-orange-500 hover:text-orange-600">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $restaurant->name }}</h3>
                        <p class="text-gray-600">{{ $restaurant->cuisine }} • {{ $restaurant->price_range }}</p>
                    </div>
                    <div>
                        <p class="text-gray-700"><i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>{{ $restaurant->address }}</p>
                        <p class="text-gray-700 mt-1"><i class="fas fa-city text-orange-500 mr-2"></i>{{ $restaurant->location }}</p>
                        @if($restaurant->phone)
                        <p class="text-gray-700 mt-1"><i class="fas fa-phone text-orange-500 mr-2"></i>{{ $restaurant->phone }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-gray-700">{{ $restaurant->description }}</p>
                    </div>
                    <div class="pt-4 border-t border-gray-200">
                        <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600">
                            <i class="fas fa-eye mr-2"></i>View on Site
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('restaurant-owner.edit') }}" class="block w-full bg-orange-500 text-white text-center py-2 px-4 rounded-lg hover:bg-orange-600 transition">
                    <i class="fas fa-edit mr-2"></i>Edit Restaurant
                </a>
                <a href="{{ route('restaurant-owner.reviews') }}" class="block w-full bg-blue-500 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-600 transition">
                    <i class="fas fa-comments mr-2"></i>View Reviews
                </a>
                <a href="{{ route('restaurants.show', $restaurant->id) }}" class="block w-full bg-gray-200 text-gray-700 text-center py-2 px-4 rounded-lg hover:bg-gray-300 transition">
                    <i class="fas fa-eye mr-2"></i>View on Site
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Reviews -->
    @if($recentReviews->count() > 0)
    <div class="mt-6 bg-white rounded-lg shadow">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Recent Reviews</h2>
                <a href="{{ route('restaurant-owner.reviews') }}" class="text-orange-500 hover:text-orange-600 text-sm">
                    View All <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @foreach($recentReviews as $review)
                <div class="border-b border-gray-200 pb-4 last:border-0 last:pb-0">
                    <div class="flex items-start">
                        <img src="{{ image_url($review->user->avatar) }}" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full mr-3">
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $review->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <h4 class="font-semibold text-gray-900 mt-2">{{ $review->title }}</h4>
                            <p class="text-gray-700 mt-1">{{ \Illuminate\Support\Str::limit($review->content, 150) }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Analytics Section -->
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Rating Breakdown Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Rating Distribution</h2>
            </div>
            <div class="p-6">
                @foreach([5, 4, 3, 2, 1] as $rating)
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $rating }} Star{{ $rating > 1 ? 's' : '' }}</span>
                        <span class="text-sm text-gray-600">{{ $analytics['rating_breakdown'][$rating] ?? 0 }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $total = array_sum($analytics['rating_breakdown']);
                            $percentage = $total > 0 ? (($analytics['rating_breakdown'][$rating] ?? 0) / $total) * 100 : 0;
                        @endphp
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Category Ratings -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900">Category Ratings</h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Food</span>
                            <span class="text-sm font-semibold">{{ number_format($analytics['category_ratings']['food'], 1) }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($analytics['category_ratings']['food'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Service</span>
                            <span class="text-sm font-semibold">{{ number_format($analytics['category_ratings']['service'], 1) }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($analytics['category_ratings']['service'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Ambiance</span>
                            <span class="text-sm font-semibold">{{ number_format($analytics['category_ratings']['ambiance'], 1) }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($analytics['category_ratings']['ambiance'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">Value</span>
                            <span class="text-sm font-semibold">{{ number_format($analytics['category_ratings']['value'], 1) }}/5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($analytics['category_ratings']['value'] / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

