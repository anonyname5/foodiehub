<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FoodieHub - Discover & Review Restaurants')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-white shadow-lg sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <i class="fas fa-utensils text-orange-500 text-2xl mr-2"></i>
                    <a href="{{ route('home') }}" class="text-2xl font-bold text-gray-800">FoodieHub</a>
                </div>
                
                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500 transition' }}">Home</a>
                    <a href="{{ route('restaurants.index') }}" class="{{ request()->routeIs('restaurants.*') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500 transition' }}">Restaurants</a>
                    @auth
                    <a href="{{ route('reviews.create') }}" class="{{ request()->routeIs('reviews.create') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500 transition' }}">Write Review</a>
                    @endauth
                </div>
                
                <!-- Right side: Authentication Buttons or User Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    @guest
                    <!-- Authentication Buttons -->
                    <div class="auth-buttons flex items-center space-x-4">
                        <button id="login-btn" class="text-gray-600 hover:text-orange-500 transition">Login</button>
                        <button id="register-btn" class="bg-orange-500 text-white px-4 py-2 rounded-lg hover:bg-orange-600 transition">Register</button>
                    </div>
                    @else
                    <!-- Notifications Bell -->
                    <div class="notifications-menu relative">
                        <button id="notification-toggle" class="relative p-2 text-gray-600 hover:text-orange-500 transition">
                            <i class="fas fa-bell text-xl"></i>
                            <span id="notification-badge" class="absolute top-0 right-0 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">
                                <span id="notification-count">0</span>
                            </span>
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div id="notifications-dropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 hidden z-50 max-h-96 overflow-hidden flex flex-col">
                            <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-orange-50">
                                <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                <button id="mark-all-read-btn" class="text-xs text-orange-600 hover:text-orange-800 hidden">Mark all read</button>
                            </div>
                            <div id="notifications-list" class="overflow-y-auto flex-1">
                                <div class="p-4 text-center text-gray-500">
                                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                                    <p class="text-sm">Loading notifications...</p>
                                </div>
                            </div>
                            <div class="px-4 py-2 border-t border-gray-200 bg-gray-50 text-center">
                                <a href="{{ route('notifications.index') }}" class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- User Menu -->
                    <div class="user-menu relative">
                        <button id="user-menu-toggle" class="flex items-center space-x-3 hover:bg-gray-50 rounded-lg px-3 py-2 transition">
                            <div class="flex flex-col text-left">
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-gray-500">{{ Auth::user()->email }}</span>
                            </div>
                        @php
                            $avatarPath = Auth::user()->avatar;
                            $avatarUrl = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])
                                ? $avatarPath
                                : image_url($avatarPath);
                        @endphp
                        <img src="{{ $avatarUrl }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-orange-500">
                            <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div id="user-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 hidden z-50">
                            <div class="py-2">
                                <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-user mr-3 text-gray-400"></i>
                                    Profile
                                </a>
                                <a href="{{ route('reviews.create') }}" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                    <i class="fas fa-edit mr-3 text-gray-400"></i>
                                    Write Review
                                </a>
                                @if(Auth::user()->isAdmin())
                                <hr class="my-1">
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-orange-600 hover:bg-orange-50 transition">
                                    <i class="fas fa-shield-alt mr-3 text-orange-500"></i>
                                    Admin Panel
                                </a>
                                @endif
                                @if(Auth::user()->isRestaurantOwner())
                                <hr class="my-1">
                                <a href="{{ route('restaurant-owner.dashboard') }}" class="flex items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-store mr-3 text-blue-500"></i>
                                    My Restaurant
                                </a>
                                @elseif(!Auth::user()->isAdmin())
                                <hr class="my-1">
                                <a href="{{ route('restaurant-owner.claim') }}" class="flex items-center px-4 py-2 text-sm text-blue-600 hover:bg-blue-50 transition">
                                    <i class="fas fa-store mr-3 text-blue-500"></i>
                                    Claim Restaurant
                                </a>
                                @endif
                                <hr class="my-1">
                                <form action="{{ route('logout') }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition">
                                        <i class="fas fa-sign-out-alt mr-3 text-gray-400"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endguest
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-orange-500 transition-colors p-2 rounded-lg hover:bg-gray-100">
                        <i id="mobile-menu-icon" class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu overlay -->
        <div id="mobile-menu-overlay" class="mobile-menu-overlay hidden"></div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="mobile-menu md:hidden bg-white hidden">
            <div class="mobile-menu-header">
                <span>FoodieHub</span>
                <button id="mobile-menu-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div>
                <a href="{{ route('home') }}" class="block px-3 py-2 {{ request()->routeIs('home') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' }}">Home</a>
                <a href="{{ route('restaurants.index') }}" class="block px-3 py-2 {{ request()->routeIs('restaurants.*') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' }}">Restaurants</a>
                @auth
                <a href="{{ route('reviews.create') }}" class="block px-3 py-2 {{ request()->routeIs('reviews.create') ? 'text-orange-500 font-semibold' : 'text-gray-600 hover:text-orange-500' }}">Write Review</a>
                @endauth
                
                @guest
                <!-- Mobile Auth Buttons -->
                <div class="auth-buttons px-3 py-2 space-y-2">
                    <button id="mobile-login-btn" class="block w-full text-left text-gray-600 hover:text-orange-500">Login</button>
                    <button id="mobile-register-btn" class="block w-full text-left bg-orange-500 text-white px-3 py-2 rounded-lg hover:bg-orange-600">Register</button>
                </div>
                @else
                <!-- Mobile User Menu -->
                <div class="user-menu px-3 py-2">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-gray-500">{{ Auth::user()->email }}</span>
                        </div>
                        @php
                            $avatarPath = Auth::user()->avatar;
                            $avatarUrl = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://'])
                                ? $avatarPath
                                : image_url($avatarPath);
                        @endphp
                        <img src="{{ $avatarUrl }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-orange-500">
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="block text-gray-600 hover:text-orange-500 text-sm">Profile</a>
                        <a href="{{ route('reviews.create') }}" class="block text-gray-600 hover:text-orange-500 text-sm">Write Review</a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block text-orange-600 hover:text-orange-700 text-sm font-semibold">
                            <i class="fas fa-shield-alt mr-2"></i>Admin Panel
                        </a>
                        @endif
                        @if(Auth::user()->isRestaurantOwner())
                        <a href="{{ route('restaurant-owner.dashboard') }}" class="block text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            <i class="fas fa-store mr-2"></i>My Restaurant
                        </a>
                        @elseif(!Auth::user()->isAdmin())
                        <a href="{{ route('restaurant-owner.claim') }}" class="block text-blue-600 hover:text-blue-700 text-sm font-semibold">
                            <i class="fas fa-store mr-2"></i>Claim Restaurant
                        </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="block text-gray-600 hover:text-orange-500 text-sm">Logout</button>
                        </form>
                    </div>
                </div>
                @endguest
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <i class="fas fa-utensils text-orange-500 text-2xl mr-2"></i>
                        <span class="text-2xl font-bold">FoodieHub</span>
                    </div>
                    <p class="text-gray-400">Discover, review, and share your favorite restaurants with fellow food lovers.</p>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('restaurants.index') }}" class="text-gray-400 hover:text-orange-500 transition">Browse Restaurants</a></li>
                        @auth
                        <li><a href="{{ route('reviews.create') }}" class="text-gray-400 hover:text-orange-500 transition">Write Review</a></li>
                        @endauth
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition">Privacy Policy</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Follow Us</h3>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-orange-500 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; {{ date('Y') }} FoodieHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    @guest
    <!-- Login Modal -->
    <div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 w-full max-w-md mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Login</h2>
                <button id="close-login-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Error Alert -->
            @if($errors->has('email') || $errors->has('password'))
            <div id="login-error-alert" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-start">
                <i class="fas fa-exclamation-circle mt-0.5 mr-2"></i>
                <div class="flex-1">
                    @if($errors->has('email'))
                        <p class="font-medium">{{ $errors->first('email') }}</p>
                    @elseif($errors->has('password'))
                        <p class="font-medium">{{ $errors->first('password') }}</p>
                    @endif
                </div>
                <button type="button" onclick="document.getElementById('login-error-alert').classList.add('hidden')" class="text-red-700 hover:text-red-900 ml-2">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @endif
            
            <form id="login-form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="login-email" name="email" value="{{ old('email') }}" required
                           class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="login-password" name="password" required
                           class="w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 flex items-center space-x-2">
                    <input type="checkbox" id="admin-login" name="admin_login" value="1" {{ old('admin_login') ? 'checked' : '' }}
                           class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="admin-login" class="text-sm text-gray-700">Login as admin</label>
                </div>
                
                <button type="submit" id="login-submit-btn" class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition">
                    Login
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <p class="text-gray-600">Don't have an account? 
                    <button id="show-register-modal" class="text-orange-500 hover:text-orange-600 font-medium">Register here</button>
                </p>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="register-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Register</h2>
                <button id="close-register-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="register-form" action="{{ route('register') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="register-name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="register-name" name="name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div class="mb-4">
                    <label for="register-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="register-email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div class="mb-4">
                    <label for="register-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="register-password" name="password" required minlength="8"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div class="mb-6">
                    <label for="register-password-confirm" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                    <input type="password" id="register-password-confirm" name="password_confirmation" required minlength="8"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <button type="submit" class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition">
                    Register
                </button>
            </form>
            
            <div class="mt-4 text-center">
                <p class="text-gray-600">Already have an account? 
                    <button id="show-login-modal" class="text-orange-500 hover:text-orange-600 font-medium">Login here</button>
                </p>
            </div>
        </div>
    </div>
    @endguest

    {{-- Confirmation Modal --}}
    <div id="confirm-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" style="display: none;">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4 transform transition-all">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="flex-shrink-0 mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2 text-center" id="confirm-title">Confirm Delete</h3>
                <p class="text-sm text-gray-600 mb-6 text-center" id="confirm-message">Are you sure you want to delete this item? This action cannot be undone.</p>
                <div class="flex gap-3">
                    <button type="button" id="confirm-cancel" class="flex-1 bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition font-medium">
                        Cancel
                    </button>
                    <button type="button" id="confirm-ok" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition font-medium">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- API.js removed - using monolith approach, no API calls needed --}}
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script>
        // Show login modal if there are errors
        @if($errors->has('email') || $errors->has('password'))
        document.addEventListener('DOMContentLoaded', function() {
            const loginModal = document.getElementById('login-modal');
            if (loginModal) {
                loginModal.classList.remove('hidden');
                // Show notification
                showLoginError('{{ $errors->first('email') ?: $errors->first('password') }}');
            }
        });
        
        function showLoginError(message) {
            // Create or update error notification
            let errorDiv = document.getElementById('login-error-notification');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'login-error-notification';
                errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-3';
                document.body.appendChild(errorDiv);
            }
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv && errorDiv.parentElement) {
                    errorDiv.remove();
                }
            }, 5000);
        }
        @endif
        
        // Mobile Menu Toggle
        const mobileMenuButton = document.getElementById('mobile-menu-btn');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuIcon = document.getElementById('mobile-menu-icon');

        function toggleMobileMenu() {
            const isOpen = !mobileMenu.classList.contains('hidden');
            if (isOpen) {
                closeMobileMenu();
            } else {
                openMobileMenu();
            }
        }

        function openMobileMenu() {
            // Force reflow to ensure initial state is applied
            mobileMenu.style.display = 'flex';
            mobileMenuOverlay.style.display = 'block';
            
            // Small delay to trigger animation
            requestAnimationFrame(() => {
                mobileMenu.classList.remove('hidden');
                mobileMenuOverlay.classList.remove('hidden');
                document.body.classList.add('mobile-menu-open');
                if (mobileMenuIcon) {
                    mobileMenuIcon.classList.remove('fa-bars');
                    mobileMenuIcon.classList.add('fa-times');
                }
            });
        }

        function closeMobileMenu() {
            mobileMenu.classList.add('hidden');
            mobileMenuOverlay.classList.add('hidden');
            document.body.classList.remove('mobile-menu-open');
            if (mobileMenuIcon) {
                mobileMenuIcon.classList.remove('fa-times');
                mobileMenuIcon.classList.add('fa-bars');
            }
            
            // Clean up after animation completes
            setTimeout(() => {
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenu.style.display = '';
                    mobileMenuOverlay.style.display = '';
                }
            }, 400);
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', toggleMobileMenu);
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', closeMobileMenu);
        }

        if (mobileMenuOverlay) {
            mobileMenuOverlay.addEventListener('click', closeMobileMenu);
        }

        // Close mobile menu when clicking on a link
        const mobileMenuLinks = mobileMenu ? mobileMenu.querySelectorAll('a') : [];
        mobileMenuLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    closeMobileMenu();
                }
            });
        });

        // Confirmation Modal Handler
        let confirmCallback = null;
        const confirmModal = document.getElementById('confirm-modal');
        const confirmTitle = document.getElementById('confirm-title');
        const confirmMessage = document.getElementById('confirm-message');
        const confirmOk = document.getElementById('confirm-ok');
        const confirmCancel = document.getElementById('confirm-cancel');

        function showConfirmModal(title, message, callback) {
            confirmTitle.textContent = title || 'Confirm Delete';
            confirmMessage.textContent = message || 'Are you sure you want to delete this item? This action cannot be undone.';
            confirmCallback = callback;
            confirmModal.classList.remove('hidden');
            confirmModal.style.display = 'flex';
        }

        function hideConfirmModal() {
            confirmModal.classList.add('hidden');
            confirmModal.style.display = 'none';
            confirmCallback = null;
        }

        confirmOk.addEventListener('click', function() {
            if (confirmCallback) {
                confirmCallback();
            }
            hideConfirmModal();
        });

        confirmCancel.addEventListener('click', hideConfirmModal);
        
        // Close modal when clicking outside
        confirmModal.addEventListener('click', function(e) {
            if (e.target === confirmModal) {
                hideConfirmModal();
            }
        });

        // Handle delete forms
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form.method === 'POST' && form.querySelector('input[name="_method"][value="DELETE"]')) {
                e.preventDefault();
                const action = form.action;
                const title = form.dataset.confirmTitle || 'Confirm Delete';
                const message = form.dataset.confirmMessage || 'Are you sure you want to delete this item? This action cannot be undone.';
                
                showConfirmModal(title, message, function() {
                    form.submit();
                });
            }
        });

        // Login form error handling
        const loginForm = document.getElementById('login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"]');
                const emailInput = this.querySelector('#login-email');
                const passwordInput = this.querySelector('#login-password');
                
                // Clear previous error states
                if (emailInput) {
                    emailInput.classList.remove('border-red-500');
                }
                if (passwordInput) {
                    passwordInput.classList.remove('border-red-500');
                }
                
                // Remove error alert if exists
                const errorAlert = document.getElementById('login-error-alert');
                if (errorAlert) {
                    errorAlert.classList.add('hidden');
                }
                
                // Basic client-side validation
                let hasError = false;
                if (!emailInput || !emailInput.value.trim()) {
                    if (emailInput) emailInput.classList.add('border-red-500');
                    hasError = true;
                }
                if (!passwordInput || !passwordInput.value) {
                    if (passwordInput) passwordInput.classList.add('border-red-500');
                    hasError = true;
                }
                
                if (hasError) {
                    e.preventDefault();
                    showLoginError('Please fill in all required fields.');
                    return false;
                }
                
                // Don't prevent default - let form submit normally to Laravel route
                // Just add loading state
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';
                }
            });
        }

        // Helper function to show login errors
        function showLoginError(message) {
            // Create or update error notification
            let errorDiv = document.getElementById('login-error-notification');
            if (!errorDiv) {
                errorDiv = document.createElement('div');
                errorDiv.id = 'login-error-notification';
                errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[60] flex items-center space-x-3 animate-slide-in';
                document.body.appendChild(errorDiv);
            }
            errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 hover:text-red-200">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv && errorDiv.parentElement) {
                    errorDiv.style.opacity = '0';
                    errorDiv.style.transform = 'translateX(100%)';
                    setTimeout(() => errorDiv.remove(), 300);
                }
            }, 5000);
        }

        // Notification Dropdown Toggle
        @auth
        const notificationToggle = document.getElementById('notification-toggle');
        const notificationsDropdown = document.getElementById('notifications-dropdown');
        const notificationsList = document.getElementById('notifications-list');
        const markAllReadBtn = document.getElementById('mark-all-read-btn');

        // Toggle dropdown
        if (notificationToggle && notificationsDropdown) {
            notificationToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const isHidden = notificationsDropdown.classList.contains('hidden');
                
                if (isHidden) {
                    notificationsDropdown.classList.remove('hidden');
                    loadNotifications();
                } else {
                    notificationsDropdown.classList.add('hidden');
                }
            });
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (notificationsDropdown && 
                !notificationsDropdown.contains(e.target) && 
                !notificationToggle.contains(e.target)) {
                notificationsDropdown.classList.add('hidden');
            }
        });

        // Load notifications into dropdown
        function loadNotifications() {
            if (!notificationsList) return;
            
            notificationsList.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                    <p class="text-sm">Loading notifications...</p>
                </div>
            `;

            fetch('{{ route("notifications.recent") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                renderNotifications(data.notifications);
                updateNotificationCount(data.unread_count);
                
                // Show/hide mark all read button
                if (data.unread_count > 0) {
                    markAllReadBtn.classList.remove('hidden');
                } else {
                    markAllReadBtn.classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                notificationsList.innerHTML = `
                    <div class="p-4 text-center text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                        <p class="text-sm">Failed to load notifications</p>
                    </div>
                `;
            });
        }

        // Render notifications in dropdown
        function renderNotifications(notifications) {
            if (!notificationsList) return;
            
            if (notifications.length === 0) {
                notificationsList.innerHTML = `
                    <div class="p-8 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                        <p class="text-sm">No notifications</p>
                        <p class="text-xs text-gray-400 mt-1">You're all caught up!</p>
                    </div>
                `;
                return;
            }

            const notificationsHTML = notifications.map(notif => {
                const isRead = notif.read_at !== null;
                const iconClass = notif.type === 'new_review' 
                    ? 'fa-comment text-orange-500' 
                    : notif.type === 'review_response' 
                    ? 'fa-reply text-blue-500' 
                    : 'fa-bell text-gray-400';
                
                return `
                    <div class="notification-item px-4 py-3 border-b border-gray-100 hover:bg-gray-50 transition ${!isRead ? 'bg-orange-50' : ''}" 
                         data-notification-id="${notif.id}" 
                         data-url="${notif.url || ''}"
                         onclick="handleNotificationClick('${notif.id}', '${notif.url || ''}', ${isRead}, this)">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fas ${iconClass}"></i>
                            </div>
                            <div class="ml-3 flex-1 min-w-0">
                                <p class="text-sm text-gray-900 ${!isRead ? 'font-semibold' : ''}">${escapeHtml(notif.message)}</p>
                                <p class="text-xs text-gray-500 mt-1">${notif.created_at_human}</p>
                                ${notif.url ? `<span class="text-xs text-orange-600 mt-1 inline-block">View details →</span>` : ''}
                            </div>
                            ${!isRead ? `
                                <button onclick="event.stopPropagation(); markNotificationAsRead('${notif.id}', this)" 
                                        class="ml-2 text-gray-400 hover:text-gray-600 p-1 rounded hover:bg-gray-200" 
                                        title="Mark as read">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                            ` : ''}
                        </div>
                    </div>
                `;
            }).join('');

            notificationsList.innerHTML = notificationsHTML;
        }

        // Handle notification click
        function handleNotificationClick(notificationId, url, isRead, element) {
            // Mark as read if unread
            if (!isRead) {
                markNotificationAsRead(notificationId, null, false);
            }
            
            // Navigate to URL if available
            if (url) {
                window.location.href = url;
            }
        }

        // Mark notification as read
        function markNotificationAsRead(notificationId, buttonElement, reload = true) {
            fetch(`{{ route('notifications.read', '') }}/${notificationId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the notification item
                    const notificationItem = buttonElement ? buttonElement.closest('.notification-item') : document.querySelector(`[data-notification-id="${notificationId}"]`);
                    if (notificationItem) {
                        notificationItem.classList.remove('bg-orange-50');
                        const messageElement = notificationItem.querySelector('p.text-gray-900');
                        if (messageElement) {
                            messageElement.classList.remove('font-semibold');
                        }
                        const checkButton = notificationItem.querySelector('button');
                        if (checkButton) {
                            checkButton.remove();
                        }
                    }
                    // Reload notifications to update count
                    if (reload) {
                        loadNotifications();
                    } else {
                        updateNotificationCount();
                    }
                }
            })
            .catch(error => {
                console.error('Error marking notification as read:', error);
            });
        }

        // Mark all as read
        if (markAllReadBtn) {
            markAllReadBtn.addEventListener('click', function() {
                fetch('{{ route("notifications.read-all") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadNotifications();
                    }
                })
                .catch(error => {
                    console.error('Error marking all as read:', error);
                });
            });
        }

        // Helper function to escape HTML
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Update notification count
        function updateNotificationCount(count) {
            const badge = document.getElementById('notification-badge');
            const countElement = document.getElementById('notification-count');
            
            if (!badge || !countElement) return;
            
            // If count is provided, use it; otherwise fetch from server
            if (count !== undefined) {
                if (count > 0) {
                    countElement.textContent = count > 99 ? '99+' : count;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            } else {
                // Fetch count from server
                fetch('{{ route("notifications.unread-count") }}', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.count > 0) {
                        countElement.textContent = data.count > 99 ? '99+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error fetching notification count:', error);
                });
            }
        }

        // Update notification count on page load
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route("notifications.unread-count") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.count);
            })
            .catch(error => {
                console.error('Error fetching notification count:', error);
            });
        });

        // Make functions available globally
        window.updateNotificationCount = updateNotificationCount;
        window.markNotificationAsRead = markNotificationAsRead;
        window.handleNotificationClick = handleNotificationClick;
        @endauth
    </script>
    @stack('scripts')
</body>
</html>

