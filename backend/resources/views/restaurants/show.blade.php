@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodieHub')

@section('content')
    <!-- Restaurant Header -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-1/3">
                    <div class="relative h-64 rounded-lg overflow-hidden">
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
                                <i class="fas fa-utensils text-6xl text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="md:w-2/3">
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">{{ $restaurant->name }}</h1>
                    <div class="flex items-center mb-4">
                        <div class="flex items-center mr-4">
                            <span class="text-2xl font-bold text-orange-500 mr-2">{{ number_format($restaurant->average_rating, 1) }}</span>
                            <div class="flex text-orange-500">
                                @for($i = 0; $i < 5; $i++)
                                    <i class="fas fa-star {{ $i < floor($restaurant->average_rating) ? '' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <span class="text-gray-600">({{ $restaurant->reviews_count }} reviews)</span>
                    </div>
                    <div class="space-y-2 mb-6">
                        <p class="text-gray-600">
                            <i class="fas fa-utensils text-orange-500 mr-2"></i>{{ $restaurant->cuisine }}
                        </p>
                        <p class="text-gray-600">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>{{ $restaurant->location }}
                        </p>
                        <p class="text-gray-600">
                            <i class="fas fa-dollar-sign text-orange-500 mr-2"></i>{{ $restaurant->price_range }}
                        </p>
                        @if($restaurant->address)
                        <p class="text-gray-600">
                            <i class="fas fa-address-card text-orange-500 mr-2"></i>{{ $restaurant->address }}
                        </p>
                        @endif
                    </div>
                    @auth
                    <div class="flex gap-4">
                        <a href="{{ route('reviews.create', ['restaurant_id' => $restaurant->id]) }}" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-edit mr-2"></i>Write Review
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Breakdown -->
    @if($ratingBreakdown)
    <div class="bg-gray-50 border-b py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Rating Breakdown</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Food</span>
                        <span class="text-sm font-semibold">{{ number_format($ratingBreakdown->avg_food, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($ratingBreakdown->avg_food / 5) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Service</span>
                        <span class="text-sm font-semibold">{{ number_format($ratingBreakdown->avg_service, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($ratingBreakdown->avg_service / 5) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Ambiance</span>
                        <span class="text-sm font-semibold">{{ number_format($ratingBreakdown->avg_ambiance, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($ratingBreakdown->avg_ambiance / 5) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm text-gray-600">Value</span>
                        <span class="text-sm font-semibold">{{ number_format($ratingBreakdown->avg_value, 1) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full" style="width: {{ ($ratingBreakdown->avg_value / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Reviews Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Reviews</h2>
        
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <img src="{{ image_url($review->user->avatar) }}" 
                             alt="{{ $review->user->name }}" 
                             class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $review->user->name }}</h3>
                            <p class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="flex items-center mb-1">
                            <span class="text-lg font-bold text-orange-500 mr-2">{{ number_format(($review->food_rating + $review->service_rating + $review->ambiance_rating + $review->value_rating) / 4, 1) }}</span>
                            <i class="fas fa-star text-orange-500"></i>
                        </div>
                        <p class="text-sm text-gray-600">{{ $review->title }}</p>
                    </div>
                </div>
                <p class="text-gray-700 mb-4">{{ $review->content }}</p>
                
                @if($review->images->count() > 0)
                <div class="grid grid-cols-4 gap-2 mb-4">
                    @foreach($review->images->take(4) as $image)
                        <img src="{{ image_url($image->path) }}" alt="Review image" class="w-full h-24 object-cover rounded">
                    @endforeach
                </div>
                @endif
                
                @if($review->recommend)
                <div class="flex items-center text-green-600">
                    <i class="fas fa-thumbs-up mr-2"></i>
                    <span class="text-sm font-medium">Recommended</span>
                </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-lg shadow-md">
                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No reviews yet</h3>
                <p class="text-gray-500 mb-4">Be the first to review this restaurant!</p>
                @auth
                <a href="{{ route('reviews.create', ['restaurant_id' => $restaurant->id]) }}" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition inline-block">
                    Write Review
                </a>
                @endauth
            </div>
        @endforelse

        <!-- Pagination -->
        <div class="mt-8">
            {{ $reviews->links() }}
        </div>
    </div>
@endsection

