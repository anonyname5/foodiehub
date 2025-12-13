@extends('layouts.admin')

@section('title', 'Create Restaurant - FoodieHub')
@section('page-title', 'Create Restaurant')
@section('page-description', 'Add a new restaurant to the platform')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-plus text-orange-500"></i> New Restaurant</h2>
            <a href="{{ route('admin.restaurants') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to List
            </a>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="cuisine" class="block text-sm font-medium text-gray-700 mb-2">Cuisine *</label>
                        <input type="text" id="cuisine" name="cuisine" value="{{ old('cuisine') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g., Italian, Chinese, Mexican">
                        @error('cuisine')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="price_range" class="block text-sm font-medium text-gray-700 mb-2">Price Range *</label>
                        <select id="price_range" name="price_range" required
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">Select price range</option>
                            <option value="Budget" {{ old('price_range') == 'Budget' ? 'selected' : '' }}>Budget</option>
                            <option value="Standard" {{ old('price_range') == 'Standard' ? 'selected' : '' }}>Standard</option>
                            <option value="Exclusive" {{ old('price_range') == 'Exclusive' ? 'selected' : '' }}>Exclusive</option>
                            <option value="Premium" {{ old('price_range') == 'Premium' ? 'selected' : '' }}>Premium</option>
                        </select>
                        @error('price_range')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                        <textarea id="description" name="description" rows="4" required
                                  class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
                        @error('description')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Working Hours -->
                    <div class="md:col-span-2">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-sm font-medium text-gray-700">Working Hours</label>
                            <span class="text-xs text-gray-500">Quick apply or set per day</span>
                        </div>
                        <!-- Quick apply to all days -->
                        <div class="border border-orange-100 bg-orange-50 rounded-lg p-3 mb-3">
                            <div class="flex flex-wrap items-center gap-3">
                                <label class="flex items-center space-x-2 text-sm text-gray-800">
                                    <input type="checkbox" id="hours-all-same" class="form-checkbox h-4 w-4 text-orange-500">
                                    <span>Use same hours for all days</span>
                                </label>
                                <input type="time" id="hours-all-open" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Open">
                                <span class="text-gray-500 text-sm">to</span>
                                <input type="time" id="hours-all-close" class="px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500" placeholder="Close">
                                <label class="flex items-center space-x-2 text-sm text-gray-800">
                                    <input type="checkbox" id="hours-all-closed" class="form-checkbox h-4 w-4 text-orange-500">
                                    <span>Closed</span>
                                </label>
                                <button type="button" onclick="applyHoursToAll()" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 text-sm">
                                    Apply to all days
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Fill once and apply to every day. You can still tweak individual days below.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                            @php
                                $days = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
                                $dayLabels = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                            @endphp
                            @foreach($days as $idx => $day)
                                @php
                                    $saved = old("hours.$day");
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="font-semibold text-gray-800">{{ $dayLabels[$idx] }}</span>
                                        <label class="flex items-center text-sm text-gray-700 space-x-2">
                                            <input type="checkbox" class="form-checkbox h-4 w-4 text-orange-500 hours-closed" data-day="{{ $day }}"
                                                   name="hours[{{ $day }}][closed]" value="1" {{ isset($saved['closed']) && $saved['closed'] ? 'checked' : '' }}>
                                            <span>Closed</span>
                                        </label>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <input type="time" name="hours[{{ $day }}][open]" data-day-open="{{ $day }}"
                                               value="{{ $saved['open'] ?? '' }}"
                                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                        <span class="text-gray-500 text-sm">to</span>
                                        <input type="time" name="hours[{{ $day }}][close]" data-day-close="{{ $day }}"
                                               value="{{ $saved['close'] ?? '' }}"
                                               class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <input type="text" id="address" name="address" value="{{ old('address') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('address')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Location/City *</label>
                        <input type="text" id="location" name="location" value="{{ old('location') }}" required
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500"
                               placeholder="e.g., New York, Los Angeles">
                        @error('location')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('phone')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Location Map Picker -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-orange-500 mr-1"></i>Restaurant Location (Click on map to set coordinates)
                        </label>
                        <div class="mb-2 flex gap-2">
                            <div class="flex-1">
                                <label for="latitude" class="block text-xs text-gray-600 mb-1">Latitude</label>
                                <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" readonly
                                       class="w-full px-3 py-2 border rounded-lg bg-gray-50 text-gray-700">
                            </div>
                            <div class="flex-1">
                                <label for="longitude" class="block text-xs text-gray-600 mb-1">Longitude</label>
                                <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" readonly
                                       class="w-full px-3 py-2 border rounded-lg bg-gray-50 text-gray-700">
                            </div>
                            <div class="flex items-end">
                                <button type="button" onclick="geocodeAddress()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 text-sm">
                                    <i class="fas fa-search mr-1"></i>Find from Address
                                </button>
                            </div>
                        </div>
                        <div class="relative">
                            <div id="map" class="w-full h-64 rounded-lg border-2 border-gray-300" style="z-index: 1;"></div>
                            <div id="map-loading" class="hidden absolute inset-0 bg-white/70 backdrop-blur-sm rounded-lg flex items-center justify-center">
                                <div class="flex items-center space-x-2 text-orange-600 font-semibold">
                                    <svg class="animate-spin h-5 w-5 text-orange-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4A8 8 0 104 12z"></path>
                                    </svg>
                                    <span>Finding location...</span>
                                </div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>Click on the map to set the restaurant location. Coordinates will be filled automatically.
                        </p>
                        @error('latitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('longitude')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Restaurant Images -->
                    <div class="md:col-span-2">
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-images text-orange-500 mr-1"></i>Restaurant Images
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-orange-400 transition">
                            <input type="file" id="images" name="images[]" multiple accept="image/*"
                                   class="hidden" onchange="previewImages(this)">
                            <div class="text-center">
                                <label for="images" class="cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600 mb-1">Click to upload images</p>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB each (max 10 images)</p>
                                </label>
                            </div>
                            <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4 hidden">
                                <!-- Preview images will be inserted here -->
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>The first image will be set as the primary image.
                        </p>
                        @error('images')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        @error('images.*')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="owner_id" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Owner (Optional)</label>
                        <select id="owner_id" name="owner_id"
                                class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            <option value="">No owner assigned</option>
                            @foreach($owners as $user)
                                <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('owner_id')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Assign a user as the restaurant owner. They will be able to manage this restaurant.</p>
                    </div>

                    <div>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                   class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                            <span class="text-sm text-gray-700">Active (visible to users)</span>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                        <i class="fas fa-save mr-2"></i>Create Restaurant
                    </button>
                    <a href="{{ route('admin.restaurants') }}" class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin=""/>
<style>
    #map { min-height: 300px; }
    .image-preview-item {
        position: relative;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }
    .image-preview-item img {
        width: 100%;
        height: 150px;
        object-fit: cover;
    }
    .image-preview-item .remove-btn {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(239, 68, 68, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
    .image-preview-item .remove-btn:hover {
        background: rgba(220, 38, 38, 1);
    }
    .primary-badge {
        position: absolute;
        bottom: 5px;
        left: 5px;
        background: rgba(249, 115, 22, 0.9);
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: bold;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<script>
    // Debug existing images (should be empty on create, but log anyway)
    try {
        console.log('[ImageDebug] Admin create images should be empty');
    } catch (e) {
        console.warn('[ImageDebug] Failed to log admin create images', e);
    }

    let map, marker;
    let selectedImages = [];

    // Initialize map
    document.addEventListener('DOMContentLoaded', function() {
        // Default location (can be changed based on user's location or a default city)
        const defaultLat = {{ old('latitude', 40.7128) }};
        const defaultLng = {{ old('longitude', -74.0060) }};

        map = L.map('map').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);

        // Add marker if coordinates exist
        if (defaultLat && defaultLng) {
            marker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(map);
            marker.on('dragend', function(e) {
                const pos = marker.getLatLng();
                document.getElementById('latitude').value = pos.lat.toFixed(8);
                document.getElementById('longitude').value = pos.lng.toFixed(8);
            });
        }

        // Add click event to map
        map.on('click', function(e) {
            const lat = e.latlng.lat;
            const lng = e.latlng.lng;
            
            document.getElementById('latitude').value = lat.toFixed(8);
            document.getElementById('longitude').value = lng.toFixed(8);

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                marker.on('dragend', function(e) {
                    const pos = marker.getLatLng();
                    document.getElementById('latitude').value = pos.lat.toFixed(8);
                    document.getElementById('longitude').value = pos.lng.toFixed(8);
                });
            }
        });
    });

    // Geocode address to get coordinates
    async function geocodeAddress() {
        const address = document.getElementById('address').value;
        const location = document.getElementById('location').value;
        
        if (!address && !location) {
            alert('Please enter an address or location first');
            return;
        }

        const query = address ? `${address}, ${location}` : location;
        const loadingEl = document.getElementById('map-loading');
        loadingEl.classList.remove('hidden');
        
        try {
            const fetchLocation = async (q) => {
                const res = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(q)}&limit=1&addressdetails=1`);
                return await res.json();
            };

            // Try full query first
            let data = await fetchLocation(query);

            // Fallback: try city/location only if first attempt fails
            if ((!data || data.length === 0) && location) {
                data = await fetchLocation(location);
            }
            
            if (data && data.length > 0) {
                const lat = parseFloat(data[0].lat);
                const lng = parseFloat(data[0].lon);
                
                document.getElementById('latitude').value = lat.toFixed(8);
                document.getElementById('longitude').value = lng.toFixed(8);
                
                if (marker) {
                    marker.setLatLng([lat, lng]);
                } else {
                    marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                    marker.on('dragend', function(e) {
                        const pos = marker.getLatLng();
                        document.getElementById('latitude').value = pos.lat.toFixed(8);
                        document.getElementById('longitude').value = pos.lng.toFixed(8);
                    });
                }
                
                map.setView([lat, lng], 15);
            } else {
                alert('Location not found. Please try a simpler version (city + country) or set it manually on the map.');
            }
        } catch (error) {
            console.error('Geocoding error:', error);
            alert('Error finding location. Please set location manually on the map.');
        } finally {
            loadingEl.classList.add('hidden');
        }
    }

    // Preview uploaded images
    function previewImages(input) {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';
        preview.classList.remove('hidden');

        if (input.files && input.files.length > 0) {
            selectedImages = Array.from(input.files);
            
            selectedImages.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'image-preview-item';
                        div.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <button type="button" class="remove-btn" onclick="removeImage(${index})" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                            ${index === 0 ? '<span class="primary-badge">Primary</span>' : ''}
                        `;
                        preview.appendChild(div);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }

    // Remove image from preview
    function removeImage(index) {
        selectedImages.splice(index, 1);
        
        // Recreate file input
        const input = document.getElementById('images');
        const dt = new DataTransfer();
        selectedImages.forEach(file => dt.items.add(file));
        input.files = dt.files;
        
        // Refresh preview
        previewImages(input);
    }

    // Disable times when "Closed" checked
    document.querySelectorAll('.hours-closed').forEach(cb => {
        const day = cb.dataset.day;
        const open = document.querySelector(`[data-day-open=\"${day}\"]`);
        const close = document.querySelector(`[data-day-close=\"${day}\"]`);

        const toggle = () => {
            const disabled = cb.checked;
            open.disabled = disabled;
            close.disabled = disabled;
            open.classList.toggle('bg-gray-100', disabled);
            close.classList.toggle('bg-gray-100', disabled);
        };

        cb.addEventListener('change', toggle);
        toggle();
    });

    // Apply same hours to all days
    function applyHoursToAll() {
        const openVal = document.getElementById('hours-all-open').value;
        const closeVal = document.getElementById('hours-all-close').value;
        const closedVal = document.getElementById('hours-all-closed').checked;

        document.querySelectorAll('.hours-closed').forEach(cb => {
            const day = cb.dataset.day;
            const open = document.querySelector(`[data-day-open=\"${day}\"]`);
            const close = document.querySelector(`[data-day-close=\"${day}\"]`);

            cb.checked = closedVal;
            open.value = closedVal ? '' : openVal;
            close.value = closedVal ? '' : closeVal;

            const disabled = cb.checked;
            open.disabled = disabled;
            close.disabled = disabled;
            open.classList.toggle('bg-gray-100', disabled);
            close.classList.toggle('bg-gray-100', disabled);
        });
    }
</script>
@endpush

