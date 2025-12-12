@extends('layouts.admin')

@section('title', 'User Details - FoodieHub')
@section('page-title', 'User Details')
@section('page-description', 'View and manage user information')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <section class="panel mb-4">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-user text-orange-500"></i> User Information</h2>
                </div>
                <div class="panel-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <input type="text" name="location" value="{{ old('location', $user->location) }}"
                                       class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="is_active" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea name="bio" rows="3" class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600">
                                <i class="fas fa-save mr-2"></i>Update User
                            </button>
                        </div>
                    </form>
                    
                    <div class="flex gap-2 mt-3 pt-3 border-t border-gray-200">
                        @if($user->is_active)
                            <form action="{{ route('admin.users.ban', $user->id) }}" method="POST" class="inline"
                                  data-confirm-title="Ban User" 
                                  data-confirm-message="Are you sure you want to ban user '{{ $user->name }}'? They will not be able to access their account.">
                                @csrf
                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600">
                                    <i class="fas fa-ban mr-2"></i>Ban User
                                </button>
                            </form>
                        @else
                            <form action="{{ route('admin.users.unban', $user->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                                    <i class="fas fa-check mr-2"></i>Unban User
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" class="inline"
                              data-confirm-title="Delete User" 
                              data-confirm-message="Are you sure you want to delete user '{{ $user->name }}'? This action cannot be undone and will also delete all their reviews and data.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                                <i class="fas fa-trash mr-2"></i>Delete User
                            </button>
                        </form>
                    </div>
                </div>
            </section>

            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-star text-orange-500"></i> User Reviews ({{ $user->reviews_count }})</h2>
                </div>
                <div class="panel-body divide-y divide-gray-200">
                    @forelse($user->reviews->take(10) as $review)
                        <div class="py-3">
                            <div class="flex items-center justify-between mb-2">
                                <a href="{{ route('restaurants.show', $review->restaurant_id) }}" class="font-semibold text-gray-800 hover:text-orange-500">
                                    {{ $review->restaurant->name }}
                                </a>
                                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-gray-600">{{ Str::limit($review->content, 100) }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                Status: 
                                <span class="badge {{ $review->status == 'approved' ? 'badge-success' : ($review->status == 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                    {{ ucfirst($review->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 py-4">No reviews yet</p>
                    @endforelse
                </div>
            </section>
        </div>

        <div>
            <section class="panel">
                <div class="panel-header">
                    <h2 class="panel-title"><i class="fas fa-info-circle text-orange-500"></i> User Stats</h2>
                </div>
                <div class="panel-body space-y-4">
                    <div>
                        <div class="text-sm text-gray-600">Total Reviews</div>
                        <div class="text-2xl font-bold text-orange-500">{{ $user->reviews_count }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Favorites</div>
                        <div class="text-2xl font-bold text-orange-500">{{ $user->favorite_restaurants_count }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600">Member Since</div>
                        <div class="text-sm font-medium">{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    @if($user->last_login_at)
                    <div>
                        <div class="text-sm text-gray-600">Last Login</div>
                        <div class="text-sm font-medium">{{ $user->last_login_at->diffForHumans() }}</div>
                    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection

