/**
 * Main JavaScript for Restaurant Review Platform
 * Integrates with Laravel API backend
 */

// Global variables
let currentUser = null;
let restaurants = [];
let reviews = [];
let autocompleteData = {
    restaurants: [],
    cuisines: [],
    locations: []
};

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    // Always setup event listeners (for search functionality)
    setupEventListeners();
    
    // Load autocomplete data
    loadAutocompleteData();
    
    // Only run full app initialization on the homepage
    if (document.getElementById('featured-restaurants') || document.getElementById('recent-reviews')) {
        initializeApp();
    }
    
    // Initialize location features if on restaurants page
    if (document.getElementById('location-filter')) {
        initializeLocationFilter();
    }
});

/**
 * Initialize the application
 */
async function initializeApp() {
    try {
        // Check authentication status
        await checkAuthStatus();

        // Load initial data
        await loadStatistics();
        await loadRestaurants();
        await loadRecentReviews();

        console.log('Application initialized successfully');

    } catch (error) {
        console.error('Failed to initialize app:', error);
        showNotification('Failed to load application data', 'error');
    }
}

/**
 * Check authentication status
 */
async function checkAuthStatus() {
    try {
        if (window.api) {
            const response = await window.api.checkAuth();
            if (response.success && response.authenticated) {
                currentUser = response.user;
                window.api.setCurrentUser(currentUser);
            }
        }
        updateAuthUI();
    } catch (error) {
        console.error('Failed to check auth status:', error);
        // Continue without authentication
    }
}

/**
 * Load dashboard statistics from API
 */
async function loadStatistics() {
    try {
        console.log('Loading dashboard statistics...');
        
        if (window.api) {
            const response = await window.api.getStatistics();
            console.log('Statistics API response:', response);
            
            if (response.success) {
                updateStatisticsDisplay(response.data);
                return;
            }
        }
        
        // Fallback to loading state indicators
        updateStatisticsDisplay({
            restaurants: '-',
            reviews: '-', 
            users: '-',
            cities: '-'
        });
        
    } catch (error) {
        console.error('Failed to load statistics:', error);
        
        // Show loading indicators on error
        updateStatisticsDisplay({
            restaurants: '-',
            reviews: '-',
            users: '-', 
            cities: '-'
        });
    }
}

/**
 * Update statistics display on homepage
 */
function updateStatisticsDisplay(stats) {
    console.log('Updating statistics display with:', stats);
    
    // Add animation helper function
    function animateCount(element, targetNumber) {
        if (typeof targetNumber !== 'number') {
            element.textContent = targetNumber;
            return;
        }
        
        const duration = 1500; // 1.5 seconds
        const startTime = performance.now();
        const startNumber = 0;
        
        function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
            const easeOutQuad = 1 - (1 - progress) * (1 - progress);
            const currentNumber = Math.floor(startNumber + (targetNumber - startNumber) * easeOutQuad);
            
            if (targetNumber > 999) {
                element.textContent = (currentNumber / 1000).toFixed(1) + 'K+';
            } else {
                element.textContent = currentNumber + '+';
            }
            
            if (progress < 1) {
                requestAnimationFrame(updateCount);
            } else {
                // Final display with proper formatting
                if (targetNumber > 999) {
                    element.textContent = (targetNumber / 1000).toFixed(1) + 'K+';
                } else {
                    element.textContent = targetNumber + '+';
                }
            }
        }
        
        requestAnimationFrame(updateCount);
    }
    
    // Update restaurant count
    const restaurantCountElement = document.getElementById('restaurant-count');
    if (restaurantCountElement && stats.restaurants) {
        animateCount(restaurantCountElement, parseInt(stats.restaurants));
    }
    
    // Update review count  
    const reviewCountElement = document.getElementById('review-count');
    if (reviewCountElement && stats.reviews !== undefined) {
        animateCount(reviewCountElement, parseInt(stats.reviews) || 0);
    }
    
    // Update user count (Food Lovers)
    const userCountElement = document.getElementById('user-count');
    if (userCountElement && stats.users) {
        animateCount(userCountElement, parseInt(stats.users));
    }
    
    // Update city count
    const cityCountElement = document.getElementById('city-count');
    if (cityCountElement && stats.cities) {
        animateCount(cityCountElement, parseInt(stats.cities));
    }
}

/**
 * Setup event listeners
 */
function setupEventListeners() {
    // Search functionality
    const searchForm = document.getElementById('search-form');
    const searchBtn = document.getElementById('search-btn');
    const searchInput = document.getElementById('search-input');
    
    if (searchForm) {
        searchForm.addEventListener('submit', handleSearch);
    } else if (searchBtn) {
        searchBtn.addEventListener('click', handleSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch(e);
            }
        });
        
        // Autocomplete functionality
        searchInput.addEventListener('input', handleAutocomplete);
        searchInput.addEventListener('focus', handleAutocomplete);
        searchInput.addEventListener('keydown', handleAutocompleteKeydown);
        searchInput.addEventListener('blur', function() {
            // Delay hiding to allow clicking on suggestions
            setTimeout(() => {
                hideAutocomplete();
            }, 200);
        });
    }

    // Mobile menu toggle
    const mobileMenuButton = document.getElementById('mobile-menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Authentication buttons
    setupAuthEventListeners();
}

/**
 * Setup authentication event listeners
 */
function setupAuthEventListeners() {
    // Login buttons
    const loginBtn = document.getElementById('login-btn');
    const mobileLoginBtn = document.getElementById('mobile-login-btn');
    if (loginBtn) loginBtn.addEventListener('click', () => showLoginModal());
    if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', () => showLoginModal());

    // Register buttons
    const registerBtn = document.getElementById('register-btn');
    const mobileRegisterBtn = document.getElementById('mobile-register-btn');
    if (registerBtn) registerBtn.addEventListener('click', () => showRegisterModal());
    if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', () => showRegisterModal());

    // Logout buttons
    const logoutBtn = document.getElementById('logout-btn');
    const mobileLogoutBtn = document.getElementById('mobile-logout-btn');
    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);
    if (mobileLogoutBtn) mobileLogoutBtn.addEventListener('click', handleLogout);

    // User dropdown toggle
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    if (userMenuToggle && userDropdown) {
        userMenuToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
        });
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (userDropdown && !userDropdown.contains(e.target) && !userMenuToggle?.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });

    // Modal close buttons
    const closeLoginModal = document.getElementById('close-login-modal');
    const closeRegisterModal = document.getElementById('close-register-modal');
    if (closeLoginModal) closeLoginModal.addEventListener('click', () => hideLoginModal());
    if (closeRegisterModal) closeRegisterModal.addEventListener('click', () => hideRegisterModal());

    // Modal switch buttons
    const showRegisterModalBtn = document.getElementById('show-register-modal');
    const showLoginModalBtn = document.getElementById('show-login-modal');
    if (showRegisterModalBtn) showRegisterModalBtn.addEventListener('click', () => {
        hideLoginModal();
        showRegisterModal();
    });
    if (showLoginModalBtn) showLoginModalBtn.addEventListener('click', () => {
        hideRegisterModal();
        showLoginModal();
    });

    // Form submissions
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
}

/**
 * Load restaurants from API
 */
async function loadRestaurants() {
    try {
        console.log('Loading restaurants...');
        
        if (window.api) {
            const response = await window.api.getRestaurants();
            console.log('API response:', response);
            
            if (response.success) {
                restaurants = response.data;
                console.log('Restaurants loaded:', restaurants);
                renderRestaurants();
                return;
            }
        }
        
        // Fallback to sample data
        restaurants = [
            {
                id: 1,
                name: "Mario's Italian Bistro",
                description: "Authentic Italian cuisine in the heart of the city",
                cuisine: "Italian",
                location: "Downtown",
                average_rating: 4.5,
                reviews_count: 128,
                image: "https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&h=300&fit=crop"
            },
            {
                id: 2,
                name: "Sakura Sushi",
                description: "Fresh sushi and Japanese cuisine",
                cuisine: "Japanese",
                location: "Midtown",
                average_rating: 4.8,
                reviews_count: 95,
                image: "https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=400&h=300&fit=crop"
            }
        ];
        renderRestaurants();
        
    } catch (error) {
        console.error('Failed to load restaurants:', error);
        showNotification('Failed to load restaurants', 'error');
    }
}

/**
 * Load recent reviews from API
 */
async function loadRecentReviews() {
    try {
        console.log('Loading recent reviews...');
        
        if (window.api) {
            const response = await window.api.getReviews({ sort_by: 'newest', limit: 3 });
            console.log('Reviews API response:', response);
            
            if (response.success) {
                reviews = response.data;
                console.log('Reviews loaded:', reviews);
                renderRecentReviews();
                return;
            }
        }
        
        // Fallback to sample data
        reviews = [
            {
                id: 1,
                user: { name: "Sarah M." },
                restaurant: { name: "Mario's Italian Bistro" },
                overall_rating: 5,
                comment: "Amazing pasta and great service!",
                created_at: "2024-01-15T10:00:00Z"
            },
            {
                id: 2,
                user: { name: "Mike T." },
                restaurant: { name: "Sakura Sushi" },
                overall_rating: 4,
                comment: "Fresh sushi and friendly staff.",
                created_at: "2024-01-14T15:30:00Z"
            }
        ];
        renderRecentReviews();
        
    } catch (error) {
        console.error('Failed to load reviews:', error);
        showNotification('Failed to load recent reviews', 'error');
    }
}

/**
 * Handle search form submission
 */
async function handleSearch(event) {
    if (event) {
        event.preventDefault();
    }
    
    const searchInput = document.getElementById('search-input');
    const locationInput = document.getElementById('location-input');
    
    const searchTerm = searchInput ? searchInput.value.trim() : '';
    const location = locationInput ? locationInput.value.trim() : '';
    
    // If no search term, just redirect to restaurants page
    if (!searchTerm && !location) {
        window.location.href = 'restaurants.html';
        return;
    }
    
    // Show search results on homepage instead of redirecting
    await performHomepageSearch(searchTerm, location);
}

/**
 * Perform search and show results on homepage
 */
async function performHomepageSearch(searchTerm, location) {
    try {
        // Show loading state
        showLoading();
        
        // Get all restaurants from API
        const response = await window.api.getRestaurants();
        if (response.success) {
            let filteredRestaurants = response.data;
            
            // Apply search filter
            if (searchTerm) {
                filteredRestaurants = filteredRestaurants.filter(restaurant => 
                    restaurant.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                    (restaurant.cuisine && restaurant.cuisine.toLowerCase().includes(searchTerm.toLowerCase())) ||
                    (restaurant.location && restaurant.location.toLowerCase().includes(searchTerm.toLowerCase())) ||
                    (restaurant.description && restaurant.description.toLowerCase().includes(searchTerm.toLowerCase()))
                );
            }
            
            // Apply location filter
            if (location) {
                filteredRestaurants = filteredRestaurants.filter(restaurant => 
                    restaurant.location && restaurant.location.toLowerCase().includes(location.toLowerCase())
                );
            }
            
            // Update the restaurants array with filtered results
            restaurants = filteredRestaurants;
            
            // Update the page title to show search results
            updatePageTitleForSearch(searchTerm, location, filteredRestaurants.length);
            
            // Re-render the restaurants section
            renderRestaurants();
            
            // Scroll to results section
            const featuredSection = document.getElementById('featured-restaurants');
            if (featuredSection) {
                featuredSection.scrollIntoView({ behavior: 'smooth' });
            }
        } else {
            throw new Error('Failed to load restaurants');
        }
        
        hideLoading();
    } catch (error) {
        console.error('Search error:', error);
        hideLoading();
        showNotification('Search failed. Please try again.', 'error');
    }
}

/**
 * Update page title to show search results
 */
function updatePageTitleForSearch(searchTerm, location, resultCount) {
    const heroTitle = document.querySelector('h1');
    const heroSubtitle = document.querySelector('p');
    
    if (heroTitle && heroSubtitle) {
        if (searchTerm && location) {
            heroTitle.textContent = `Search Results for "${searchTerm}" in ${location}`;
            heroSubtitle.textContent = `Found ${resultCount} restaurant${resultCount !== 1 ? 's' : ''} matching your search`;
        } else if (searchTerm) {
            heroTitle.textContent = `Search Results for "${searchTerm}"`;
            heroSubtitle.textContent = `Found ${resultCount} restaurant${resultCount !== 1 ? 's' : ''} matching your search`;
        } else if (location) {
            heroTitle.textContent = `Restaurants in ${location}`;
            heroSubtitle.textContent = `Found ${resultCount} restaurant${resultCount !== 1 ? 's' : ''} in this area`;
        }
        
        // Add a "Clear Search" button
        addClearSearchButton();
    }
}

/**
 * Add clear search button to reset the search
 */
function addClearSearchButton() {
    // Remove existing clear button if any
    const existingButton = document.getElementById('clear-search-btn');
    if (existingButton) {
        existingButton.remove();
    }
    
    // Add new clear button
    const heroSection = document.querySelector('.bg-gradient-to-r.from-orange-500');
    if (heroSection) {
        const clearButton = document.createElement('button');
        clearButton.id = 'clear-search-btn';
        clearButton.className = 'mt-4 bg-white text-orange-500 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition border border-orange-200';
        clearButton.innerHTML = '<i class="fas fa-times mr-2"></i>Clear Search';
        clearButton.onclick = clearSearch;
        
        const searchContainer = heroSection.querySelector('.max-w-2xl');
        if (searchContainer) {
            searchContainer.appendChild(clearButton);
        }
    }
}

/**
 * Clear search and reset to original state
 */
function clearSearch() {
    // Clear search input
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Reset page title
    const heroTitle = document.querySelector('h1');
    const heroSubtitle = document.querySelector('p');
    if (heroTitle && heroSubtitle) {
        heroTitle.textContent = 'Discover Amazing Restaurants';
        heroSubtitle.textContent = 'Share your dining experiences and find your next favorite meal';
    }
    
    // Remove clear button
    const clearButton = document.getElementById('clear-search-btn');
    if (clearButton) {
        clearButton.remove();
    }
    
    // Reload all restaurants
    loadRestaurants();
}

/**
 * Toggle favorite status for a restaurant
 */
async function toggleFavorite(restaurantId) {
    try {
        // Check if user is authenticated
        if (!window.api || !window.api.isAuthenticated()) {
            showNotification('Please log in to add favorites', 'error');
            return;
        }
        
        const currentUser = window.api.getCurrentUser();
        if (!currentUser) {
            showNotification('Please log in to add favorites', 'error');
            return;
        }
        
        // Check if restaurant is already favorited
        const favoritesResponse = await window.api.getUserFavorites(currentUser.id);
        const isFavorited = favoritesResponse.success && 
            favoritesResponse.data.some(restaurant => restaurant.id === restaurantId);
        
        if (isFavorited) {
            // Remove from favorites
            const response = await window.api.removeFavorite(currentUser.id, restaurantId);
            if (response.success) {
                showNotification('Restaurant removed from favorites', 'success');
                updateFavoriteButton(restaurantId, false);
            } else {
                throw new Error(response.message || 'Failed to remove favorite');
            }
        } else {
            // Add to favorites
            const response = await window.api.addFavorite(currentUser.id, restaurantId);
            if (response.success) {
                showNotification('Restaurant added to favorites', 'success');
                updateFavoriteButton(restaurantId, true);
            } else {
                throw new Error(response.message || 'Failed to add favorite');
            }
        }
    } catch (error) {
        console.error('Error toggling favorite:', error);
        showNotification('Failed to update favorites', 'error');
    }
}

/**
 * Update favorite button appearance
 */
function updateFavoriteButton(restaurantId, isFavorited) {
    const button = document.querySelector(`[data-restaurant-id="${restaurantId}"]`);
    if (button) {
        const icon = button.querySelector('i');
        if (icon) {
            if (isFavorited) {
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-red-500');
            } else {
                icon.classList.remove('text-red-500');
                icon.classList.add('text-gray-400');
            }
        }
    }
}

/**
 * Render restaurants
 */
function renderRestaurants() {
    const container = document.getElementById('featured-restaurants');
    if (!container) {
        console.log('Featured restaurants container not found');
        return;
    }
    
    console.log('Rendering restaurants in container:', container);
    console.log('Restaurants to render:', restaurants);
    
    // Debug: Log the first restaurant structure
    if (restaurants.length > 0) {
        console.log('First restaurant structure:', restaurants[0]);
        console.log('Average rating type:', typeof restaurants[0].average_rating);
        console.log('Average rating value:', restaurants[0].average_rating);
    }
    
    if (restaurants.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8 col-span-full">No restaurants found.</p>';
        return;
    }
    
    container.innerHTML = restaurants.map(restaurant => `
        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
            <div class="relative">
                ${restaurant.primary_image && restaurant.primary_image.length > 0 && restaurant.primary_image[0].full_url ? 
                    `<img src="${restaurant.primary_image[0].full_url}" alt="${restaurant.name}" class="w-full h-48 object-cover">` :
                    restaurant.main_image ?
                    `<img src="${restaurant.main_image}" alt="${restaurant.name}" class="w-full h-48 object-cover">` :
                    `<div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">No Image</span>
                    </div>`
                }
                <div class="absolute top-2 right-2">
                    <span class="bg-orange-500 text-white px-2 py-1 rounded-full text-sm font-medium">
                        ${restaurant.average_rating ? parseFloat(restaurant.average_rating).toFixed(1) : 'N/A'}
                    </span>
                </div>
                <button class="absolute top-2 left-2 bg-white p-2 rounded-full shadow-md hover:shadow-lg transition-all duration-200 favorite-btn" 
                        data-restaurant-id="${restaurant.id}" onclick="toggleFavorite(${restaurant.id})">
                    <i class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors"></i>
                </button>
            </div>
            <div class="p-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">${restaurant.name}</h3>
                <p class="text-gray-600 text-sm mb-2">${restaurant.cuisine}</p>
                <p class="text-gray-500 text-sm mb-3">${restaurant.location}</p>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">${restaurant.review_count || 0} reviews</span>
                    <a href="restaurant-detail.html?id=${restaurant.id}" 
                       class="text-orange-500 hover:text-orange-600 font-medium text-sm">
                        View Details
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

/**
 * Render recent reviews
 */
function renderRecentReviews() {
    const container = document.getElementById('recent-reviews');
    if (!container) {
        console.log('Recent reviews container not found');
        return;
    }
    
    console.log('Rendering reviews in container:', container);
    console.log('Reviews to render:', reviews);
    
    if (reviews.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-4 col-span-full">No reviews yet.</p>';
        return;
    }
    
    container.innerHTML = reviews.map(review => `
        <div class="bg-white rounded-lg shadow-md p-4">
            <div class="flex items-start justify-between mb-2">
                <div>
                    <h4 class="font-semibold text-gray-800">${review.title || 'Review'}</h4>
                    <p class="text-sm text-gray-600">by ${review.user.name}</p>
                </div>
                <div class="flex items-center">
                    ${renderStars(review.overall_rating)}
                </div>
            </div>
            <p class="text-gray-700 text-sm mb-2">${review.comment.substring(0, 100)}${review.comment.length > 100 ? '...' : ''}</p>
            <div class="flex items-center justify-between text-xs text-gray-500">
                <span>${review.restaurant.name}</span>
                <span>${new Date(review.created_at).toLocaleDateString()}</span>
            </div>
        </div>
    `).join('');
}

/**
 * Render star rating
 */
function renderStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let stars = '';
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        stars += '<span class="text-yellow-400">★</span>';
    }
    
    // Half star
    if (hasHalfStar) {
        stars += '<span class="text-yellow-400">☆</span>';
    }
    
    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        stars += '<span class="text-gray-300">☆</span>';
    }
    
    return stars;
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

/**
 * Show loading state
 */
function showLoading(message = 'Loading...') {
    const loading = document.createElement('div');
    loading.id = 'loading-overlay';
    loading.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    loading.innerHTML = `
        <div class="bg-white rounded-lg p-6 flex items-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-orange-500 mr-3"></div>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(loading);
}

/**
 * Hide loading state
 */
function hideLoading() {
    const loading = document.getElementById('loading-overlay');
    if (loading) {
        loading.remove();
    }
}

/**
 * Show login modal
 */
function showLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

/**
 * Hide login modal
 */
function hideLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Show register modal
 */
function showRegisterModal() {
    const modal = document.getElementById('register-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

/**
 * Hide register modal
 */
function hideRegisterModal() {
    const modal = document.getElementById('register-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Handle login form submission
 */
async function handleLogin(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const email = formData.get('email');
    const password = formData.get('password');
    
    try {
        showLoading('Logging in...');
        
        const response = await window.api.login(email, password);
        
        if (response.success) {
            currentUser = response.user;
            window.api.setCurrentUser(currentUser);
            updateAuthUI();
            showNotification('Login successful!', 'success');
            hideLoginModal();
        } else {
            showNotification(response.message || 'Login failed', 'error');
        }
    } catch (error) {
        console.error('Login error:', error);
        showNotification('Login failed. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Handle register form submission
 */
async function handleRegister(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const userData = {
        name: formData.get('name'),
        email: formData.get('email'),
        password: formData.get('password'),
        password_confirmation: formData.get('password_confirmation')
    };
    
    try {
        showLoading('Creating account...');
        
        const response = await window.api.register(userData);
        
        if (response.success) {
            currentUser = response.user;
            window.api.setCurrentUser(currentUser);
            updateAuthUI();
            showNotification('Account created successfully!', 'success');
            hideRegisterModal();
        } else {
            showNotification(response.message || 'Registration failed', 'error');
        }
    } catch (error) {
        console.error('Registration error:', error);
        showNotification('Registration failed. Please try again.', 'error');
    } finally {
        hideLoading();
    }
}

/**
 * Handle logout
 */
async function handleLogout() {
    try {
        await window.api.logout();
        currentUser = null;
        window.api.clearCurrentUser();
        updateAuthUI();
        showNotification('Logged out successfully', 'success');
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Logout failed', 'error');
    }
}

/**
 * Update all user avatars across the application
 */
function updateAllUserAvatars(user) {
    if (!user) return;
    
    // Get saved avatar from localStorage as backup
    const savedAvatar = localStorage.getItem('userAvatar');
    
    // Determine which avatar to use
    let avatarSrc = null;
    
    if (user.avatar && 
        user.avatar !== 'default' && 
        user.avatar !== null && 
        user.avatar !== '') {
        // Use API avatar if it exists and is valid
        // If it's a relative path, make it absolute to the Laravel backend
        avatarSrc = user.avatar.startsWith('/storage/') 
            ? `http://127.0.0.1:8000${user.avatar}` 
            : user.avatar;
    } else if (savedAvatar && 
               !savedAvatar.includes('data:image/svg') && 
               savedAvatar.startsWith('http')) {
        // Use saved avatar if it's a valid URL
        avatarSrc = savedAvatar;
    } else if (user.name) {
        // Generate a nice avatar using the user's name
        const name = encodeURIComponent(user.name);
        avatarSrc = `https://ui-avatars.com/api/?name=${name}&background=f97316&color=ffffff&size=200&bold=true`;
    } else {
        // Final fallback
        avatarSrc = 'https://ui-avatars.com/api/?name=User&background=f97316&color=ffffff&size=200&bold=true';
    }
    
    // Update all avatar elements
    const avatarSelectors = [
        '#user-avatar',
        '#mobile-user-avatar', 
        '#sidebar-avatar',
        '#profile-avatar',
        '.user-avatar'
    ];
    
    avatarSelectors.forEach(selector => {
        const elements = document.querySelectorAll(selector);
        elements.forEach(element => {
            if (element) {
                element.src = avatarSrc;
                element.alt = `${user.name}'s Avatar`;
                element.style.display = 'block';
                element.style.visibility = 'visible';
                
                // Add loading state
                element.classList.add('opacity-50');
                
                element.onload = () => {
                    element.classList.remove('opacity-50');
                };
                
                element.onerror = () => {
                    // Fallback to generated avatar if current fails
                    if (!avatarSrc.includes('ui-avatars.com')) {
                        const fallbackName = encodeURIComponent(user.name || 'User');
                        element.src = `https://ui-avatars.com/api/?name=${fallbackName}&background=f97316&color=ffffff&size=200&bold=true`;
                    }
                    element.classList.remove('opacity-50');
                };
            }
        });
    });
}

/**
 * Update authentication UI
 */
function updateAuthUI() {
    const authButtons = document.querySelectorAll('.auth-buttons');
    const userMenu = document.querySelectorAll('.user-menu');
    
    
    if (currentUser) {
        // Show user menu, hide auth buttons
        authButtons.forEach(button => button.classList.add('hidden'));
        userMenu.forEach(menu => {
            menu.classList.remove('hidden');
        });
        
        // Update user info
        const userNameElements = document.querySelectorAll('.user-name');
        userNameElements.forEach(element => {
            element.textContent = currentUser.name;
        });
        
        // Update user email
        const userEmailElements = document.querySelectorAll('.user-email');
        userEmailElements.forEach(element => {
            element.textContent = currentUser.email;
        });
        
        // Update user avatar across all elements
        updateAllUserAvatars(currentUser);
    } else {
        // Show auth buttons, hide user menu
        authButtons.forEach(button => button.classList.remove('hidden'));
        userMenu.forEach(menu => menu.classList.add('hidden'));
    }
}

/**
 * Autocomplete functionality
 */

// Load autocomplete data from API
async function loadAutocompleteData() {
    try {
        if (window.api) {
            const response = await window.api.getRestaurants();
            if (response.success && response.data) {
                // Extract unique data for autocomplete
                const restaurantNames = [...new Set(response.data.map(r => r.name))];
                const cuisines = [...new Set(response.data.map(r => r.cuisine).filter(Boolean))];
                const locations = [...new Set(response.data.map(r => r.location).filter(Boolean))];
                
                autocompleteData = {
                    restaurants: restaurantNames,
                    cuisines: cuisines,
                    locations: locations
                };
            }
        }
    } catch (error) {
        console.error('Failed to load autocomplete data:', error);
    }
}

// Handle autocomplete input
function handleAutocomplete() {
    const searchInput = document.getElementById('search-input');
    const query = searchInput.value.trim().toLowerCase();
    
    if (query.length < 2) {
        hideAutocomplete();
        return;
    }
    
    const suggestions = getSuggestions(query);
    showAutocomplete(suggestions);
}

// Get suggestions based on query
function getSuggestions(query) {
    const suggestions = [];
    
    // Search restaurant names
    autocompleteData.restaurants.forEach(name => {
        if (name.toLowerCase().includes(query)) {
            suggestions.push({
                text: name,
                type: 'restaurant',
                icon: 'fas fa-utensils'
            });
        }
    });
    
    // Search cuisines
    autocompleteData.cuisines.forEach(cuisine => {
        if (cuisine.toLowerCase().includes(query)) {
            suggestions.push({
                text: cuisine,
                type: 'cuisine',
                icon: 'fas fa-pizza-slice'
            });
        }
    });
    
    // Add location suggestion (async search will be handled separately)
    if (query.length >= 2) {
        suggestions.push({
            text: `Search locations for "${query}"`,
            type: 'location-search',
            icon: 'fas fa-map-marker-alt',
            query: query
        });
    }
    
    // Search locations
    autocompleteData.locations.forEach(location => {
        if (location.toLowerCase().includes(query)) {
            suggestions.push({
                text: location,
                type: 'location',
                icon: 'fas fa-map-marker-alt'
            });
        }
    });
    
    // Remove duplicates and limit to 8 suggestions
    const uniqueSuggestions = suggestions.filter((suggestion, index, self) => 
        index === self.findIndex(s => s.text === suggestion.text)
    ).slice(0, 8);
    
    return uniqueSuggestions;
}

// Show autocomplete dropdown
function showAutocomplete(suggestions) {
    const dropdown = document.getElementById('autocomplete-dropdown');
    const suggestionsContainer = document.getElementById('autocomplete-suggestions');
    
    if (!dropdown || !suggestionsContainer || suggestions.length === 0) {
        hideAutocomplete();
        return;
    }
    
    // Generate HTML for suggestions
    const suggestionsHTML = suggestions.map((suggestion, index) => `
        <div class="autocomplete-item px-4 py-3 hover:bg-gray-100 cursor-pointer flex items-center" 
             data-suggestion="${suggestion.text}" data-index="${index}">
            <i class="${suggestion.icon} text-orange-500 mr-3 w-4"></i>
            <span class="text-gray-800">${suggestion.text}</span>
            <span class="ml-auto text-xs text-gray-500 capitalize">${suggestion.type}</span>
        </div>
    `).join('');
    
    suggestionsContainer.innerHTML = suggestionsHTML;
    dropdown.classList.remove('hidden');
    
    // Add click event listeners to suggestions
    const items = suggestionsContainer.querySelectorAll('.autocomplete-item');
    items.forEach(item => {
        item.addEventListener('click', function() {
            const suggestion = this.getAttribute('data-suggestion');
            selectSuggestion(suggestion);
        });
        
        item.addEventListener('mouseenter', function() {
            // Remove highlight from other items
            items.forEach(i => i.classList.remove('bg-orange-50'));
            // Add highlight to current item
            this.classList.add('bg-orange-50');
        });
    });
}

// Hide autocomplete dropdown
function hideAutocomplete() {
    const dropdown = document.getElementById('autocomplete-dropdown');
    if (dropdown) {
        dropdown.classList.add('hidden');
    }
}

// Select a suggestion
function selectSuggestion(suggestion) {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.value = suggestion;
        hideAutocomplete();
        // Don't automatically trigger search - let user click search button
    }
}

// Handle keyboard navigation in autocomplete
function handleAutocompleteKeydown(event) {
    const dropdown = document.getElementById('autocomplete-dropdown');
    if (!dropdown || dropdown.classList.contains('hidden')) {
        return;
    }
    
    const items = dropdown.querySelectorAll('.autocomplete-item');
    const currentActive = dropdown.querySelector('.autocomplete-item.active');
    let activeIndex = -1;
    
    if (currentActive) {
        activeIndex = parseInt(currentActive.getAttribute('data-index'));
    }
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            activeIndex = Math.min(activeIndex + 1, items.length - 1);
            break;
        case 'ArrowUp':
            event.preventDefault();
            activeIndex = Math.max(activeIndex - 1, -1);
            break;
        case 'Enter':
            event.preventDefault();
            if (activeIndex >= 0 && items[activeIndex]) {
                const suggestion = items[activeIndex].getAttribute('data-suggestion');
                selectSuggestion(suggestion);
            } else {
                handleSearch(event);
            }
            return;
        case 'Escape':
            hideAutocomplete();
            return;
        default:
            return;
    }
    
    // Update active item
    items.forEach(item => item.classList.remove('active', 'bg-orange-50'));
    if (activeIndex >= 0 && items[activeIndex]) {
        items[activeIndex].classList.add('active', 'bg-orange-50');
    }
}

// Create FoodieHub global object for compatibility with other JS files
window.FoodieHub = {
    generateStars: renderStars,
    formatDate: function(dateString) {
        return new Date(dateString).toLocaleDateString();
    },
    sampleRestaurants: [] // Will be populated by other files if needed
};

/**
 * Initialize location filter on restaurants page
 */
function initializeLocationFilter() {
    const locationFilter = document.getElementById('location-filter');
    const locationDetectBtn = document.getElementById('location-detect-btn');
    const locationDropdown = document.getElementById('location-filter-dropdown');
    const locationSuggestions = document.getElementById('location-filter-suggestions');
    
    if (!locationFilter || !window.locationService) return;
    
    let activeIndex = -1;
    let currentSuggestions = [];
    
    // Handle location detection button
    if (locationDetectBtn) {
        locationDetectBtn.addEventListener('click', async function() {
            const button = this;
            const originalHTML = button.innerHTML;
            
            try {
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                button.disabled = true;
                
                const location = await window.locationService.getCurrentLocation();
                locationFilter.value = location.city || location.formatted;
                hideLocationDropdown();
                
                // Trigger filter update
                const event = new Event('input');
                locationFilter.dispatchEvent(event);
                
                showNotification('Location detected and filter applied!', 'success');
            } catch (error) {
                console.error('Error detecting location:', error);
                showNotification(error.message || 'Could not detect location', 'error');
            } finally {
                button.innerHTML = originalHTML;
                button.disabled = false;
            }
        });
    }
    
    // Handle input for autocomplete
    locationFilter.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideLocationDropdown();
            return;
        }
        
        // Use debounced search
        window.locationService.searchWithDebounce(query, (results) => {
            currentSuggestions = results.slice(0, 5); // Limit to 5 suggestions
            displayLocationFilterSuggestions(currentSuggestions);
        });
    });
    
    // Handle keyboard navigation
    locationFilter.addEventListener('keydown', function(e) {
        if (!locationDropdown.classList.contains('hidden')) {
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, currentSuggestions.length - 1);
                    updateLocationActiveItem();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, -1);
                    updateLocationActiveItem();
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (activeIndex >= 0 && currentSuggestions[activeIndex]) {
                        selectLocationFilter(currentSuggestions[activeIndex]);
                    }
                    break;
                case 'Escape':
                    hideLocationDropdown();
                    break;
            }
        }
    });
    
    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!locationFilter.contains(e.target) && !locationDropdown.contains(e.target)) {
            hideLocationDropdown();
        }
    });
    
    function displayLocationFilterSuggestions(results) {
        locationSuggestions.innerHTML = '';
        activeIndex = -1;
        
        if (results.length === 0) {
            locationSuggestions.innerHTML = '<div class="px-3 py-2 text-gray-500 text-sm">No locations found</div>';
        } else {
            results.forEach((location, index) => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 cursor-pointer hover:bg-gray-50 flex items-center text-sm';
                item.innerHTML = `
                    <i class="fas fa-map-marker-alt text-orange-500 mr-2"></i>
                    <div class="font-medium text-gray-800">${location.formatted}</div>
                `;
                
                item.addEventListener('click', () => selectLocationFilter(location));
                locationSuggestions.appendChild(item);
            });
        }
        
        locationDropdown.classList.remove('hidden');
    }
    
    function updateLocationActiveItem() {
        const items = locationSuggestions.querySelectorAll('div.cursor-pointer');
        items.forEach((item, index) => {
            if (index === activeIndex) {
                item.classList.add('bg-orange-50', 'border-l-2', 'border-orange-500');
            } else {
                item.classList.remove('bg-orange-50', 'border-l-2', 'border-orange-500');
            }
        });
    }
    
    function selectLocationFilter(location) {
        locationFilter.value = location.city || location.formatted;
        hideLocationDropdown();
        
        // Trigger restaurant filtering if available
        if (typeof filterRestaurants === 'function') {
            filterRestaurants();
        }
    }
    
    function hideLocationDropdown() {
        locationDropdown.classList.add('hidden');
        activeIndex = -1;
        currentSuggestions = [];
    }
}

// Export functions for use in other files
window.restaurantApp = {
    loadRestaurants,
    loadRecentReviews,
    renderRestaurants,
    renderRecentReviews,
    showNotification,
    showLoading,
    hideLoading
};