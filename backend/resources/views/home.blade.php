@extends('layouts.app')

@section('title', 'FoodieHub - Discover & Review Restaurants')

@section('content')
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-orange-500 to-red-500 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-6">Discover Amazing Restaurants</h1>
            <p class="text-xl md:text-2xl mb-8 text-orange-100">Share your dining experiences and find your next favorite meal</p>
            
            <!-- Search Bar -->
            <div class="max-w-2xl mx-auto">
                <form action="{{ route('restaurants.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1 relative">
                        <input type="text" name="search" id="search-input" placeholder="Search restaurants, cuisines, or locations..." 
                               value="{{ request('search') }}"
                               class="w-full px-6 py-4 rounded-lg text-gray-800 text-lg focus:outline-none focus:ring-4 focus:ring-orange-300"
                               autocomplete="off">
                    </div>
                    <button type="submit" class="bg-white text-orange-500 px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition">
                        <i class="fas fa-search mr-2"></i>Search
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
                <div class="p-6">
                    <div id="restaurant-count" class="text-4xl font-bold text-orange-500 mb-2">{{ number_format($statistics['restaurants']) }}</div>
                    <div class="text-gray-600">Restaurants</div>
                </div>
                <div class="p-6">
                    <div id="review-count" class="text-4xl font-bold text-orange-500 mb-2">{{ number_format($statistics['reviews']) }}</div>
                    <div class="text-gray-600">Reviews</div>
                </div>
                <div class="p-6">
                    <div id="user-count" class="text-4xl font-bold text-orange-500 mb-2">{{ number_format($statistics['users']) }}</div>
                    <div class="text-gray-600">Food Lovers</div>
                </div>
                <div class="p-6">
                    <div id="city-count" class="text-4xl font-bold text-orange-500 mb-2">{{ number_format($statistics['cities']) }}</div>
                    <div class="text-gray-600">Cities</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Restaurants -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Featured Restaurants</h2>
                <p class="text-xl text-gray-600">Discover top-rated dining spots in your area</p>
            </div>
            
            <div id="featured-restaurants" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($featuredRestaurants as $restaurant)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition cursor-pointer" onclick="window.location.href='{{ route('restaurants.show', $restaurant->id) }}'">
                        <div class="relative h-48 bg-gray-200">
                            @php
                                $displayImage = null;
                                if ($restaurant->images && $restaurant->images->isNotEmpty()) {
                                    $primaryImage = $restaurant->images->where('is_primary', true)->first();
                                    $displayImage = $primaryImage ?? $restaurant->images->first();
                                }
                            @endphp
                            @if($displayImage)
                                <img src="{{ image_url($displayImage->path) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <i class="fas fa-utensils text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white rounded-full px-3 py-1 shadow-lg">
                                <span class="text-orange-500 font-bold">{{ number_format($restaurant->average_rating, 1) }}</span>
                                <i class="fas fa-star text-orange-500 text-xs"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-utensils text-orange-500 mr-1"></i>{{ $restaurant->cuisine }}
                            </p>
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>{{ $restaurant->location }}
                            </p>
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600 font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">No featured restaurants yet.</p>
                @endforelse
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('restaurants.index') }}" class="bg-orange-500 text-white px-8 py-3 rounded-lg font-semibold hover:bg-orange-600 transition">
                    View All Restaurants
                </a>
            </div>
        </div>
    </section>

    <!-- Recommendations -->
    @if($recommendations->count() > 0)
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                    @auth
                        Recommended for You
                    @else
                        Top Rated Restaurants
                    @endauth
                </h2>
                <p class="text-xl text-gray-600">
                    @auth
                        Based on your preferences
                    @else
                        Discover the best dining experiences
                    @endauth
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($recommendations as $restaurant)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition cursor-pointer" onclick="window.location.href='{{ route('restaurants.show', $restaurant->id) }}'">
                        <div class="relative h-48 bg-gray-200">
                            @php
                                $displayImage = null;
                                if ($restaurant->images && $restaurant->images->isNotEmpty()) {
                                    $primaryImage = $restaurant->images->where('is_primary', true)->first();
                                    $displayImage = $primaryImage ?? $restaurant->images->first();
                                }
                            @endphp
                            @if($displayImage)
                                <img src="{{ image_url($displayImage->path) }}" alt="{{ $restaurant->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                    <i class="fas fa-utensils text-4xl text-gray-400"></i>
                                </div>
                            @endif
                            <div class="absolute top-4 right-4 bg-white rounded-full px-3 py-1 shadow-lg">
                                <span class="text-orange-500 font-bold">{{ number_format($restaurant->average_rating, 1) }}</span>
                                <i class="fas fa-star text-orange-500 text-xs"></i>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $restaurant->name }}</h3>
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-utensils text-orange-500 mr-1"></i>{{ $restaurant->cuisine }}
                            </p>
                            <p class="text-gray-600 text-sm mb-2">
                                <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>{{ $restaurant->location }}
                            </p>
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600 font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Recent Reviews -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Recent Reviews</h2>
                <p class="text-xl text-gray-600">See what our community is saying</p>
            </div>
            
            <div id="recent-reviews" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($recentReviews as $review)
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center mb-4">
                            <img src="{{ image_url($review->user->avatar) }}" 
                                 alt="{{ $review->user->name }}" 
                                 class="w-10 h-10 rounded-full mr-3">
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $review->user->name }}</h4>
                                <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <h5 class="font-bold text-gray-800 mb-2">
                            <a href="{{ route('restaurants.show', $review->restaurant_id) }}" class="hover:text-orange-500">
                                {{ $review->restaurant->name }}
                            </a>
                        </h5>
                        <p class="text-gray-700 text-sm mb-2">{{ Str::limit($review->content, 100) }}</p>
                        <div class="flex items-center">
                            <span class="text-orange-500 font-bold mr-1">{{ number_format(($review->food_rating + $review->service_rating + $review->ambiance_rating + $review->value_rating) / 4, 1) }}</span>
                            <i class="fas fa-star text-orange-500 text-xs"></i>
                        </div>
                    </div>
                @empty
                    <p class="col-span-full text-center text-gray-500">No recent reviews yet.</p>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">Browse by Cuisine</h2>
                <p class="text-xl text-gray-600">Find restaurants by your favorite cuisine type</p>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                <a href="{{ route('restaurants.index', ['cuisine' => 'Italian']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-pizza-slice text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">Italian</div>
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => 'Japanese']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-fish text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">Japanese</div>
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => 'Mexican']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-pepper-hot text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">Mexican</div>
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => 'American']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-drumstick-bite text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">American</div>
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => 'Thai']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-seedling text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">Thai</div>
                </a>
                <a href="{{ route('restaurants.index', ['cuisine' => 'French']) }}" class="bg-white p-6 rounded-lg shadow-md text-center hover:shadow-lg transition cursor-pointer">
                    <i class="fas fa-bread-slice text-3xl text-orange-500 mb-3"></i>
                    <div class="font-semibold text-gray-800">French</div>
                </a>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // Update API base URL to use relative paths (no CORS needed)
    if (window.api) {
        window.api.baseURL = '{{ url("/") }}';
    }
</script>
@endpush

