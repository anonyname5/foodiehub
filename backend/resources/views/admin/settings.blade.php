@extends('layouts.admin')

@section('title', 'Settings - FoodieHub')
@section('page-title', 'Settings')
@section('page-description', 'Configure system settings')

@section('content')
    <section class="panel">
        <div class="panel-header">
            <h2 class="panel-title"><i class="fas fa-cog text-orange-500"></i> System Settings</h2>
        </div>
        <div class="panel-body">
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" 
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Reviews Per Day</label>
                        <input type="number" name="max_reviews_per_day" value="{{ old('max_reviews_per_day', $settings['max_reviews_per_day']) }}" 
                               min="1" max="100"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Site Description</label>
                    <textarea name="site_description" rows="3" 
                              class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Min Review Length</label>
                        <input type="number" name="min_review_length" value="{{ old('min_review_length', $settings['min_review_length']) }}" 
                               min="1" max="500"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Max Review Length</label>
                        <input type="number" name="max_review_length" value="{{ old('max_review_length', $settings['max_review_length']) }}" 
                               min="10" max="5000"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="allow_registration" value="1" 
                               {{ old('allow_registration', $settings['allow_registration']) ? 'checked' : '' }}
                               class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Allow User Registration</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="require_email_verification" value="1" 
                               {{ old('require_email_verification', $settings['require_email_verification']) ? 'checked' : '' }}
                               class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-700">Require Email Verification</span>
                    </label>
                </div>

                <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg hover:bg-orange-600">
                    <i class="fas fa-save mr-2"></i>Save Settings
                </button>
            </form>
        </div>
    </section>
@endsection

