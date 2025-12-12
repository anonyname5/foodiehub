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
                    <div class="flex gap-4">
                        @auth
                        <a href="{{ route('reviews.create', ['restaurant_id' => $restaurant->id]) }}" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600 transition">
                            <i class="fas fa-edit mr-2"></i>Write Review
                        </a>
                        @endauth
                        <!-- Social Sharing -->
                        <div class="relative group">
                            <button onclick="toggleShareMenu()" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                                <i class="fas fa-share-alt mr-2"></i>Share
                            </button>
                            <div id="share-menu" class="absolute top-full left-0 mt-2 bg-white rounded-lg shadow-lg border border-gray-200 p-3 hidden z-10">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('restaurants.show', $restaurant->id)) }}" target="_blank" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded mb-1">
                                    <i class="fab fa-facebook text-blue-600 mr-2"></i>Facebook
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('restaurants.show', $restaurant->id)) }}&text={{ urlencode($restaurant->name) }}" target="_blank" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded mb-1">
                                    <i class="fab fa-twitter text-blue-400 mr-2"></i>Twitter
                                </a>
                                <a href="https://wa.me/?text={{ urlencode($restaurant->name . ' - ' . route('restaurants.show', $restaurant->id)) }}" target="_blank" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded mb-1">
                                    <i class="fab fa-whatsapp text-green-600 mr-2"></i>WhatsApp
                                </a>
                                <button onclick="copyLink('{{ route('restaurants.show', $restaurant->id) }}')" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded">
                                    <i class="fas fa-link text-gray-600 mr-2"></i>Copy Link
                                </button>
                            </div>
                        </div>
                    </div>
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
                        @if($restaurant->latitude && $restaurant->longitude)
                        <div class="mt-4">
                            <a href="https://www.google.com/maps?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" class="inline-flex items-center text-orange-600 hover:text-orange-800">
                                <i class="fas fa-map-marked-alt mr-2"></i>
                                <span>View on Google Maps</span>
                                <i class="fas fa-external-link-alt ml-2 text-xs"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Map Card -->
                @if($restaurant->latitude && $restaurant->longitude)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Location</h3>
                    <div id="restaurant-map" class="w-full h-64 rounded-lg overflow-hidden" style="min-height: 256px;"></div>
                    <p class="text-sm text-gray-600 mt-3 text-center">
                        <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>
                        {{ $restaurant->address ?? $restaurant->location }}
                    </p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 md:mb-0">Reviews ({{ $reviews->total() }})</h2>
            
            <!-- Review Sorting and Filtering -->
            <form method="GET" action="{{ route('restaurants.show', $restaurant->id) }}" class="flex flex-wrap gap-3">
                <!-- Sort -->
                <select name="sort_reviews" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="newest" {{ request('sort_reviews') == 'newest' || !request('sort_reviews') ? 'selected' : '' }}>Newest First</option>
                    <option value="highest" {{ request('sort_reviews') == 'highest' ? 'selected' : '' }}>Highest Rated</option>
                    <option value="helpful" {{ request('sort_reviews') == 'helpful' ? 'selected' : '' }}>Most Helpful</option>
                    <option value="lowest" {{ request('sort_reviews') == 'lowest' ? 'selected' : '' }}>Lowest Rated</option>
                </select>
                
                <!-- Filter by Rating -->
                <select name="rating_filter" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">All Ratings</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating_filter') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                    @endfor
                </select>
                
                <!-- Verified Only -->
                <label class="flex items-center px-3 py-2 border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50">
                    <input type="checkbox" name="verified_only" value="1" {{ request('verified_only') ? 'checked' : '' }} onchange="this.form.submit()" class="mr-2">
                    <span class="text-sm">Verified Only</span>
                </label>
                
                <!-- Clear Filters -->
                @if(request('sort_reviews') || request('rating_filter') || request('verified_only'))
                    <a href="{{ route('restaurants.show', $restaurant->id) }}" class="px-3 py-2 text-sm text-gray-600 hover:text-gray-800">Clear</a>
                @endif
            </form>
        </div>
        
        @forelse($reviews as $review)
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center">
                        <img src="{{ image_url($review->user->avatar) }}" 
                             alt="{{ $review->user->name }}" 
                             class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="font-semibold text-gray-800">{{ $review->user->name }}</h3>
                                @if($review->is_verified)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" title="Verified Review">
                                        <i class="fas fa-check-circle mr-1"></i>Verified
                                    </span>
                                @endif
                            </div>
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
                
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                    <div class="flex items-center gap-4">
                        @if($review->recommend)
                        <div class="flex items-center text-green-600">
                            <i class="fas fa-thumbs-up mr-2"></i>
                            <span class="text-sm font-medium">Recommended</span>
                        </div>
                        @endif
                        
                        <!-- Helpful Vote Button -->
                        @auth
                        <button type="button" 
                                onclick="toggleHelpful({{ $review->id }})" 
                                class="flex items-center gap-2 text-sm text-gray-600 hover:text-orange-500 transition {{ in_array($review->id, $userHelpfulVotes ?? []) ? 'text-orange-500' : '' }}"
                                id="helpful-btn-{{ $review->id }}">
                            <i class="fas fa-thumbs-up"></i>
                            <span>Helpful</span>
                            <span id="helpful-count-{{ $review->id }}">{{ $review->helpful_count }}</span>
                        </button>
                        @else
                        <div class="flex items-center gap-2 text-sm text-gray-400">
                            <i class="fas fa-thumbs-up"></i>
                            <span>Helpful</span>
                            <span>{{ $review->helpful_count }}</span>
                        </div>
                        @endauth
                    </div>
                </div>
                
                <!-- Restaurant Owner Response -->
                @if($review->response)
                <div class="mt-4 pt-4 border-t border-gray-200 bg-orange-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-store text-orange-500 text-xl"></i>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-800">Restaurant Owner Response</h4>
                                <span class="text-xs text-gray-500">{{ $review->response->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-700">{{ $review->response->response }}</p>
                            @auth
                                @if($restaurant->owner_id == Auth::id())
                                <div class="mt-3 flex gap-2">
                                    <button onclick="editResponse({{ $review->id }})" class="text-sm text-orange-600 hover:text-orange-800">Edit</button>
                                    <form method="POST" action="{{ route('reviews.response.destroy', $review->id) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this response?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Delete</button>
                                    </form>
                                </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
                @elseif(auth()->check() && $restaurant->owner_id == Auth::id())
                <!-- Response Form (for restaurant owners) -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <button onclick="showResponseForm({{ $review->id }})" class="text-sm text-orange-600 hover:text-orange-800">
                        <i class="fas fa-reply mr-1"></i>Respond to this review
                    </button>
                    <form id="response-form-{{ $review->id }}" method="POST" action="{{ route('reviews.response.store', $review->id) }}" class="hidden mt-3">
                        @csrf
                        <textarea name="response" rows="3" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Write a response to this review..."></textarea>
                        <div class="mt-2 flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 text-sm">Post Response</button>
                            <button type="button" onclick="hideResponseForm({{ $review->id }})" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">Cancel</button>
                        </div>
                    </form>
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

@push('styles')
<style>
    .custom-restaurant-marker {
        background: transparent;
        border: none;
    }
    #restaurant-map {
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script>
    function toggleHelpful(reviewId) {
        fetch(`/reviews/${reviewId}/helpful`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const btn = document.getElementById(`helpful-btn-${reviewId}`);
                const countEl = document.getElementById(`helpful-count-${reviewId}`);
                
                countEl.textContent = data.helpful_count;
                
                if (data.helpful) {
                    btn.classList.add('text-orange-500');
                    btn.classList.remove('text-gray-600');
                } else {
                    btn.classList.remove('text-orange-500');
                    btn.classList.add('text-gray-600');
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update helpful vote. Please try again.');
        });
    }
    
    function showResponseForm(reviewId) {
        const form = document.getElementById(`response-form-${reviewId}`);
        form.classList.remove('hidden');
    }
    
    function hideResponseForm(reviewId) {
        const form = document.getElementById(`response-form-${reviewId}`);
        form.classList.add('hidden');
        form.querySelector('textarea').value = '';
    }
    
    function editResponse(reviewId) {
        // This would need to fetch the current response and populate the form
        // For now, just show the form
        showResponseForm(reviewId);
    }
    
    // Image Modal
    function openImageModal(imageSrc) {
        const modal = document.createElement('div');
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="relative max-w-4xl max-h-full">
                <img src="${imageSrc}" alt="Restaurant photo" class="max-w-full max-h-[90vh] object-contain rounded-lg">
                <button onclick="this.closest('.fixed').remove()" class="absolute top-4 right-4 bg-white text-gray-800 rounded-full w-10 h-10 flex items-center justify-center hover:bg-gray-200 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        modal.onclick = function(e) {
            if (e.target === modal) {
                modal.remove();
            }
        };
        document.body.appendChild(modal);
    }
    
    // Share Menu
    function toggleShareMenu() {
        const menu = document.getElementById('share-menu');
        menu.classList.toggle('hidden');
    }
    
    // Close share menu when clicking outside
    document.addEventListener('click', function(event) {
        const shareMenu = document.getElementById('share-menu');
        const shareButton = event.target.closest('button[onclick="toggleShareMenu()"]');
        if (shareMenu && !shareMenu.contains(event.target) && !shareButton) {
            shareMenu.classList.add('hidden');
        }
    });
    
    // Copy Link
    function copyLink(url) {
        navigator.clipboard.writeText(url).then(function() {
            alert('Link copied to clipboard!');
            document.getElementById('share-menu').classList.add('hidden');
        });
    }
    
    // Initialize OpenStreetMap with Leaflet (Free, no API key required)
    @if($restaurant->latitude && $restaurant->longitude)
    function initMap() {
        // Initialize map centered on restaurant location
        const map = L.map('restaurant-map').setView([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], 15);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);
        
        // Create custom icon for restaurant marker
        const restaurantIcon = L.divIcon({
            className: 'custom-restaurant-marker',
            html: '<div style="background-color: #f97316; width: 32px; height: 32px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3); position: relative;"><i class="fas fa-utensils" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) rotate(45deg); color: white; font-size: 14px;"></i></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });
        
        // Add marker
        const marker = L.marker([{{ $restaurant->latitude }}, {{ $restaurant->longitude }}], {
            icon: restaurantIcon
        }).addTo(map);
        
        // Create popup content
        const popupContent = `
            <div class="p-2" style="min-width: 200px;">
                <h3 class="font-bold text-gray-800 mb-1 text-base">{{ addslashes($restaurant->name) }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ addslashes($restaurant->address ?? $restaurant->location) }}</p>
                @if($restaurant->phone)
                <p class="text-sm text-gray-600 mb-2">
                    <i class="fas fa-phone mr-1"></i>{{ $restaurant->phone }}
                </p>
                @endif
                <div class="flex gap-2 mt-3">
                    <a href="https://www.google.com/maps?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" class="text-orange-600 hover:text-orange-800 text-xs">
                        <i class="fas fa-external-link-alt mr-1"></i>Google Maps
                    </a>
                    <a href="https://www.openstreetmap.org/?mlat={{ $restaurant->latitude }}&mlon={{ $restaurant->longitude }}&zoom=15" target="_blank" class="text-blue-600 hover:text-blue-800 text-xs">
                        <i class="fas fa-map mr-1"></i>OpenStreetMap
                    </a>
                </div>
            </div>
        `;
        
        // Bind popup to marker and open it
        marker.bindPopup(popupContent).openPopup();
    }
    
    // Load Leaflet CSS
    if (!document.querySelector('link[href*="leaflet"]')) {
        const leafletCSS = document.createElement('link');
        leafletCSS.rel = 'stylesheet';
        leafletCSS.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        leafletCSS.integrity = 'sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=';
        leafletCSS.crossOrigin = '';
        document.head.appendChild(leafletCSS);
    }
    
    // Load Leaflet JS
    if (!window.L) {
        const leafletJS = document.createElement('script');
        leafletJS.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        leafletJS.integrity = 'sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=';
        leafletJS.crossOrigin = '';
        leafletJS.onload = function() {
            initMap();
        };
        document.head.appendChild(leafletJS);
    } else {
        // Leaflet already loaded
        initMap();
    }
    @endif
</script>
@endpush

