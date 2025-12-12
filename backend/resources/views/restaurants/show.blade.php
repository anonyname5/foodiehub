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
                        @if($restaurant->phone)
                        <p class="text-gray-600">
                            <i class="fas fa-phone text-orange-500 mr-2"></i><a href="tel:{{ $restaurant->phone }}" class="hover:text-orange-600">{{ $restaurant->phone }}</a>
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

    <!-- Restaurant Details Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Hours -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-clock text-orange-500 mr-3 text-xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Working Hours</h2>
                    </div>
                    <div class="space-y-2">
                        @if($restaurant->hours)
                            @php
                                $hours = is_string($restaurant->hours) ? json_decode($restaurant->hours, true) : $restaurant->hours;
                            @endphp
                            @if(is_array($hours) && count($hours) > 0)
                                @foreach($hours as $day => $time)
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <span class="font-semibold text-gray-800 capitalize">{{ $day }}</span>
                                    <span class="text-gray-600 font-mono text-sm bg-gray-100 px-3 py-1 rounded-md">{{ $time }}</span>
                                </div>
                                @endforeach
                            @elseif(is_string($restaurant->hours))
                                <p class="text-gray-600">{{ $restaurant->hours }}</p>
                            @else
                                <p class="text-gray-500 italic">Hours not specified</p>
                            @endif
                        @else
                            <p class="text-gray-500 italic">Hours not specified</p>
                        @endif
                    </div>
                </div>

                <!-- Features -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Features</h2>
                    <div class="flex flex-wrap gap-2">
                        @if($restaurant->features)
                            @php
                                $features = is_string($restaurant->features) ? json_decode($restaurant->features, true) : $restaurant->features;
                            @endphp
                            @if(is_array($features) && count($features) > 0)
                                @foreach($features as $feature)
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>{{ $feature }}
                                </span>
                                @endforeach
                            @elseif(is_string($restaurant->features))
                                <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                                    {{ $restaurant->features }}
                                </span>
                            @else
                                <p class="text-gray-500 italic">No features listed</p>
                            @endif
                        @else
                            <p class="text-gray-500 italic">No features listed</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Contact Info Card -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Contact Information</h3>
                    <div class="space-y-3">
                        @if($restaurant->address)
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-3 mt-1"></i>
                            <span class="text-gray-700">{{ $restaurant->address }}</span>
                        </div>
                        @endif
                        @if($restaurant->phone)
                        <div class="flex items-center">
                            <i class="fas fa-phone text-orange-500 mr-3"></i>
                            <a href="tel:{{ $restaurant->phone }}" class="text-gray-700 hover:text-orange-500">{{ $restaurant->phone }}</a>
                        </div>
                        @endif
                        @if($restaurant->location)
                        <div class="flex items-center">
                            <i class="fas fa-city text-orange-500 mr-3"></i>
                            <span class="text-gray-700">{{ $restaurant->location }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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

