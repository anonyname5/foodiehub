@extends('layouts.app')

@section('title', 'Restaurants - FoodieHub')

@section('content')
    <!-- Page Header -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">All Restaurants</h1>
            <p class="text-lg text-gray-600">Discover amazing dining experiences in your area</p>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <!-- Search Bar -->
            <form action="{{ route('restaurants.index') }}" method="GET" class="mb-8">
                <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl p-6 shadow-lg border border-orange-100">
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <div class="flex-1 relative w-full">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-orange-400 text-lg"></i>
                                </div>
                                <input type="text" name="search" id="search-input" placeholder="Search restaurants, cuisines, or locations..." 
                                       value="{{ request('search') }}"
                                       class="w-full pl-12 pr-4 py-4 text-gray-700 bg-white border-2 border-orange-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-orange-300 focus:border-orange-400 transition-all duration-200 shadow-sm text-lg placeholder-gray-400" 
                                       autocomplete="off">
                            </div>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center justify-center min-w-[140px]">
                            <i class="fas fa-search mr-2 text-lg"></i>
                            <span class="text-lg">Search</span>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Filters -->
            <form action="{{ route('restaurants.index') }}" method="GET" id="filter-form" class="flex flex-wrap gap-4 items-center">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Cuisine:</label>
                    <select name="cuisine" id="cuisine-filter" class="form-input py-2 px-3 text-sm">
                        <option value="">All Cuisines</option>
                        @foreach($cuisines as $cuisine)
                            <option value="{{ $cuisine }}" {{ request('cuisine') == $cuisine ? 'selected' : '' }}>{{ $cuisine }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Price:</label>
                    <select name="price_range" id="price-filter" class="form-input py-2 px-3 text-sm">
                        <option value="">All Prices</option>
                        @foreach($priceRanges as $price)
                            <option value="{{ $price }}" {{ request('price_range') == $price ? 'selected' : '' }}>{{ $price }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Rating:</label>
                    <select name="min_rating" id="rating-filter" class="form-input py-2 px-3 text-sm">
                        <option value="">All Ratings</option>
                        <option value="4.5" {{ request('min_rating') == '4.5' ? 'selected' : '' }}>4.5+ Stars</option>
                        <option value="4.0" {{ request('min_rating') == '4.0' ? 'selected' : '' }}>4.0+ Stars</option>
                        <option value="3.5" {{ request('min_rating') == '3.5' ? 'selected' : '' }}>3.5+ Stars</option>
                    </select>
                </div>
                
                <button type="submit" class="text-orange-500 hover:text-orange-600 text-sm font-medium">
                    Apply Filters
                </button>
                
                @if(request()->hasAny(['cuisine', 'price_range', 'min_rating', 'location']))
                    <a href="{{ route('restaurants.index') }}" class="text-gray-500 hover:text-gray-600 text-sm font-medium">
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div id="restaurants-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($restaurants as $restaurant)
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
                        <p class="text-gray-600 text-sm mb-4">
                            <i class="fas fa-dollar-sign text-orange-500 mr-1"></i>{{ $restaurant->price_range }}
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-comments mr-1"></i>{{ $restaurant->reviews_count ?? 0 }} reviews
                            </span>
                            <a href="{{ route('restaurants.show', $restaurant->id) }}" class="text-orange-500 hover:text-orange-600 font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-utensils text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2">No restaurants found</h3>
                    <p class="text-gray-500">Try adjusting your search or filters</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $restaurants->links() }}
        </div>
    </div>
@endsection

