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
                    <!-- User Menu -->
                    <div class="user-menu relative">
                        <button id="user-menu-toggle" class="flex items-center space-x-3 hover:bg-gray-50 rounded-lg px-3 py-2 transition">
                            <div class="flex flex-col text-left">
                                <span class="text-sm font-semibold text-gray-800">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-gray-500">{{ Auth::user()->email }}</span>
                            </div>
                            <img src="{{ image_url(Auth::user()->avatar) }}" alt="User Avatar" class="w-8 h-8 rounded-full object-cover border-2 border-orange-500">
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
                    <button id="mobile-menu-btn" class="text-gray-600 hover:text-orange-500">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="md:hidden bg-white border-t hidden">
            <div class="px-2 pt-2 pb-3 space-y-1">
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
                        <img src="{{ image_url(Auth::user()->avatar) }}" alt="User Avatar" class="w-10 h-10 rounded-full object-cover border-2 border-orange-500">
                    </div>
                    <div class="space-y-2">
                        <a href="{{ route('profile.show') }}" class="block text-gray-600 hover:text-orange-500 text-sm">Profile</a>
                        <a href="{{ route('reviews.create') }}" class="block text-gray-600 hover:text-orange-500 text-sm">Write Review</a>
                        @if(Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block text-orange-600 hover:text-orange-700 text-sm font-semibold">
                            <i class="fas fa-shield-alt mr-2"></i>Admin Panel
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
        <div class="bg-white rounded-lg p-8 w-full max-w-md mx-4">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Login</h2>
                <button id="close-login-modal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="login-form" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="login-email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="login-email" name="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                
                <div class="mb-6">
                    <label for="login-password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="login-password" name="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="mb-6 flex items-center space-x-2">
                    <input type="checkbox" id="admin-login" name="admin_login" value="1"
                           class="h-4 w-4 text-orange-500 focus:ring-orange-500 border-gray-300 rounded">
                    <label for="admin-login" class="text-sm text-gray-700">Login as admin</label>
                </div>
                
                <button type="submit" class="w-full bg-orange-500 text-white py-2 px-4 rounded-lg hover:bg-orange-600 transition">
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

    {{-- API.js removed - using monolith approach, no API calls needed --}}
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @stack('scripts')
</body>
</html>

