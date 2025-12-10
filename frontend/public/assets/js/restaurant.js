// Restaurant listing page functionality

let allRestaurants = [];
let filteredRestaurants = [];
let currentView = 'grid';
let currentPage = 1;
let restaurantsPerPage = 6;
// Restaurant page autocomplete data
let restaurantAutocompleteData = {
    restaurants: [],
    cuisines: [],
    locations: []
};

// DOM Elements
let searchInput, searchBtn, mobileMenuBtn, mobileMenu;
let cuisineFilter, priceFilter, ratingFilter, locationFilter, clearFilters;
let sortOption, gridViewBtn, listViewBtn;
let restaurantsGrid, loading, noResults, resultsCount, loadMoreBtn, loadMoreContainer;

// Initialize the restaurant listing page
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    setupEventListeners();
    initializeAuth();
    loadRestaurants();
});

// Initialize DOM elements
function initializeElements() {
    // Search elements
    searchInput = document.getElementById('search-input');
    searchBtn = document.getElementById('search-btn');
    
    // Mobile menu elements
    mobileMenuBtn = document.getElementById('mobile-menu-btn');
    mobileMenu = document.getElementById('mobile-menu');
    
    // Filter elements
    cuisineFilter = document.getElementById('cuisine-filter');
    priceFilter = document.getElementById('price-filter');
    ratingFilter = document.getElementById('rating-filter');
    locationFilter = document.getElementById('location-filter');
    clearFilters = document.getElementById('clear-filters');
    
    // Sort and view elements
    sortOption = document.getElementById('sort-option');
    gridViewBtn = document.getElementById('grid-view');
    listViewBtn = document.getElementById('list-view');
    
    // Content elements
    restaurantsGrid = document.getElementById('restaurants-grid');
    loading = document.getElementById('loading');
    noResults = document.getElementById('no-results');
    resultsCount = document.getElementById('results-count');
    loadMoreBtn = document.getElementById('load-more');
    loadMoreContainer = document.getElementById('load-more-container');
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    if (searchBtn) {
        searchBtn.addEventListener('click', handleSearch);
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                handleSearch();
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
        
        // Load search query from localStorage (from homepage)
        const savedQuery = localStorage.getItem('searchQuery');
        if (savedQuery) {
            searchInput.value = savedQuery;
            localStorage.removeItem('searchQuery');
            handleSearch();
        }
    }
    
    // Mobile menu toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Filter event listeners
    if (cuisineFilter) {
        cuisineFilter.addEventListener('change', applyFilters);
    }
    if (priceFilter) {
        priceFilter.addEventListener('change', applyFilters);
    }
    if (ratingFilter) {
        ratingFilter.addEventListener('change', applyFilters);
    }
    if (locationFilter) {
        locationFilter.addEventListener('change', applyFilters);
    }
    if (clearFilters) {
        clearFilters.addEventListener('click', clearAllFilters);
    }
    
    // Sort and view event listeners
    if (sortOption) {
        sortOption.addEventListener('change', applySorting);
    }
    if (gridViewBtn) {
        gridViewBtn.addEventListener('click', () => switchView('grid'));
    }
    if (listViewBtn) {
        listViewBtn.addEventListener('click', () => switchView('list'));
    }
    
    // Load more button
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', loadMoreRestaurants);
    }
    
    // Reset search button
    const resetSearchBtn = document.getElementById('reset-search');
    if (resetSearchBtn) {
        resetSearchBtn.addEventListener('click', resetSearch);
    }
    
    // Setup authentication event listeners
    setupAuthEventListeners();
}

// Load restaurants data
async function loadRestaurants() {
    try {
        showLoading();
        
        // Load restaurants from API
        const response = await window.api.getRestaurants();
        
        if (response.success) {
            allRestaurants = response.data;
            filteredRestaurants = [...allRestaurants];
            displayRestaurants();
            
            // Load autocomplete data after restaurants are loaded
            loadAutocompleteDataFromRestaurants();
        } else {
            throw new Error('Failed to load restaurants');
        }
        
        hideLoading();
    } catch (error) {
        console.error('Error loading restaurants:', error);
        hideLoading();
        showNoResults();
    }
}


// Handle search
function handleSearch() {
    const query = searchInput.value.trim().toLowerCase();
    
    if (query) {
        filteredRestaurants = allRestaurants.filter(restaurant => 
            restaurant.name.toLowerCase().includes(query) ||
            restaurant.cuisine.toLowerCase().includes(query) ||
            restaurant.location.toLowerCase().includes(query) ||
            restaurant.description.toLowerCase().includes(query)
        );
    } else {
        filteredRestaurants = [...allRestaurants];
    }
    
    currentPage = 1;
    applyFilters();
}

// Apply filters
function applyFilters() {
    let filtered = [...allRestaurants];
    
    // Search filter
    const searchQuery = searchInput.value.trim().toLowerCase();
    if (searchQuery) {
        filtered = filtered.filter(restaurant => 
            restaurant.name.toLowerCase().includes(searchQuery) ||
            (restaurant.cuisine && restaurant.cuisine.toLowerCase().includes(searchQuery)) ||
            (restaurant.location && restaurant.location.toLowerCase().includes(searchQuery)) ||
            (restaurant.description && restaurant.description.toLowerCase().includes(searchQuery))
        );
    }
    
    // Cuisine filter
    const cuisine = cuisineFilter.value;
    if (cuisine) {
        filtered = filtered.filter(restaurant => restaurant.cuisine === cuisine);
    }
    
    // Price filter
    const price = priceFilter.value;
    if (price) {
        filtered = filtered.filter(restaurant => restaurant.price_range === price);
    }
    
    // Rating filter
    const rating = parseFloat(ratingFilter.value);
    if (rating) {
        filtered = filtered.filter(restaurant => {
            const restaurantRating = parseFloat(restaurant.average_rating) || 0;
            return restaurantRating >= rating;
        });
    }
    
    // Location filter
    const location = locationFilter.value;
    if (location) {
        filtered = filtered.filter(restaurant => restaurant.location === location);
    }
    
    filteredRestaurants = filtered;
    currentPage = 1;
    applySorting();
}

// Apply sorting
function applySorting() {
    const sortBy = sortOption.value;
    
    switch (sortBy) {
        case 'rating':
            filteredRestaurants.sort((a, b) => {
                const ratingA = parseFloat(a.average_rating) || 0;
                const ratingB = parseFloat(b.average_rating) || 0;
                return ratingB - ratingA;
            });
            break;
        case 'reviews':
            filteredRestaurants.sort((a, b) => {
                const reviewsA = a.review_count || 0;
                const reviewsB = b.review_count || 0;
                return reviewsB - reviewsA;
            });
            break;
        case 'name':
            filteredRestaurants.sort((a, b) => a.name.localeCompare(b.name));
            break;
        case 'price-low':
            filteredRestaurants.sort((a, b) => {
                const priceA = (a.price_range || '$$').length;
                const priceB = (b.price_range || '$$').length;
                return priceA - priceB;
            });
            break;
        case 'price-high':
            filteredRestaurants.sort((a, b) => {
                const priceA = (a.price_range || '$$').length;
                const priceB = (b.price_range || '$$').length;
                return priceB - priceA;
            });
            break;
    }
    
    displayRestaurants();
}

// Clear all filters
function clearAllFilters() {
    searchInput.value = '';
    cuisineFilter.value = '';
    priceFilter.value = '';
    ratingFilter.value = '';
    locationFilter.value = '';
    
    filteredRestaurants = [...allRestaurants];
    currentPage = 1;
    applySorting();
}

// Reset search
function resetSearch() {
    clearAllFilters();
    displayRestaurants();
}

// Switch view between grid and list
function switchView(view) {
    currentView = view;
    
    if (view === 'grid') {
        gridViewBtn.className = 'p-2 bg-orange-500 text-white rounded';
        listViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300';
    } else {
        listViewBtn.className = 'p-2 bg-orange-500 text-white rounded';
        gridViewBtn.className = 'p-2 bg-gray-200 text-gray-600 rounded hover:bg-gray-300';
    }
    
    displayRestaurants();
}

// Display restaurants
function displayRestaurants() {
    if (filteredRestaurants.length === 0) {
        showNoResults();
        return;
    }
    
    hideNoResults();
    
    const startIndex = 0;
    const endIndex = currentPage * restaurantsPerPage;
    const restaurantsToShow = filteredRestaurants.slice(startIndex, endIndex);
    
    if (currentView === 'grid') {
        displayGridView(restaurantsToShow);
    } else {
        displayListView(restaurantsToShow);
    }
    
    updateResultsCount();
    updateLoadMoreButton();
}

// Display grid view
function displayGridView(restaurants) {
    restaurantsGrid.className = 'grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6';
    
    restaurantsGrid.innerHTML = restaurants.map(restaurant => {
        // Handle image URL - prioritize primary_image, then images, then main_image
        let imageUrl = 'https://via.placeholder.com/400x300?text=No+Image';
        if (restaurant.primary_image && restaurant.primary_image.length > 0 && restaurant.primary_image[0].full_url) {
            imageUrl = restaurant.primary_image[0].full_url;
        } else if (restaurant.images && restaurant.images.length > 0 && restaurant.images[0].full_url) {
            imageUrl = restaurant.images[0].full_url;
        } else if (restaurant.main_image) {
            imageUrl = restaurant.main_image;
        }
        
        // Handle rating
        const rating = parseFloat(restaurant.average_rating) || 0;
        const reviewCount = restaurant.review_count || 0;
        
        return `
            <div class="restaurant-card bg-white rounded-lg shadow-md overflow-hidden">
                <div class="relative">
                    <img src="${imageUrl}" alt="${restaurant.name}" class="w-full h-48 object-cover">
                    <div class="absolute top-4 right-4 bg-white px-2 py-1 rounded-full text-sm font-semibold text-orange-500">
                        ${restaurant.price_range || '$$'}
                    </div>
                    <button class="absolute top-4 left-4 bg-white p-2 rounded-full shadow-md hover:shadow-lg transition-all duration-200 favorite-btn" 
                            data-restaurant-id="${restaurant.id}" onclick="toggleFavorite(${restaurant.id})">
                        <i class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors"></i>
                    </button>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${restaurant.name}</h3>
                    <p class="text-gray-600 mb-3">${restaurant.cuisine || 'Restaurant'} • ${restaurant.location || 'Unknown'}</p>
                    <p class="text-gray-700 mb-4 text-sm">${restaurant.description || 'No description available'}</p>
                    <div class="flex items-center justify-between">
                        <div class="rating-display">
                            <div class="star-rating">
                                ${window.FoodieHub ? window.FoodieHub.generateStars(rating) : '★'.repeat(Math.floor(rating))}
                            </div>
                            <span class="rating-number ml-2">${rating > 0 ? rating.toFixed(1) : 'No rating'}</span>
                            <span class="text-gray-500 text-sm ml-1">(${reviewCount})</span>
                        </div>
                        <a href="restaurant-detail.html?id=${restaurant.id}" class="text-orange-500 hover:text-orange-600 font-semibold">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Display list view
function displayListView(restaurants) {
    restaurantsGrid.className = 'space-y-4';
    
    restaurantsGrid.innerHTML = restaurants.map(restaurant => {
        // Handle image URL - prioritize primary_image, then images, then main_image
        let imageUrl = 'https://via.placeholder.com/400x300?text=No+Image';
        if (restaurant.primary_image && restaurant.primary_image.length > 0 && restaurant.primary_image[0].full_url) {
            imageUrl = restaurant.primary_image[0].full_url;
        } else if (restaurant.images && restaurant.images.length > 0 && restaurant.images[0].full_url) {
            imageUrl = restaurant.images[0].full_url;
        } else if (restaurant.main_image) {
            imageUrl = restaurant.main_image;
        }
        
        // Handle rating
        const rating = parseFloat(restaurant.average_rating) || 0;
        const reviewCount = restaurant.review_count || 0;
        
        return `
            <div class="restaurant-card bg-white rounded-lg shadow-md overflow-hidden">
                <div class="flex flex-col md:flex-row">
                    <div class="md:w-1/3 relative">
                        <img src="${imageUrl}" alt="${restaurant.name}" class="w-full h-48 md:h-full object-cover">
                        <button class="absolute top-4 left-4 bg-white p-2 rounded-full shadow-md hover:shadow-lg transition-all duration-200 favorite-btn" 
                                data-restaurant-id="${restaurant.id}" onclick="toggleFavorite(${restaurant.id})">
                            <i class="fas fa-heart text-gray-400 hover:text-red-500 transition-colors"></i>
                        </button>
                    </div>
                    <div class="md:w-2/3 p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">${restaurant.name}</h3>
                                <p class="text-gray-600 mb-2">${restaurant.cuisine || 'Restaurant'} • ${restaurant.location || 'Unknown'}</p>
                                <p class="text-gray-700 text-sm">${restaurant.description || 'No description available'}</p>
                            </div>
                            <div class="bg-white px-2 py-1 rounded-full text-sm font-semibold text-orange-500">
                                ${restaurant.price_range || '$$'}
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="rating-display">
                                <div class="star-rating">
                                    ${window.FoodieHub ? window.FoodieHub.generateStars(rating) : '★'.repeat(Math.floor(rating))}
                                </div>
                                <span class="rating-number ml-2">${rating > 0 ? rating.toFixed(1) : 'No rating'}</span>
                                <span class="text-gray-500 text-sm ml-1">(${reviewCount} reviews)</span>
                            </div>
                            <a href="restaurant-detail.html?id=${restaurant.id}" class="btn-primary">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Load more restaurants
function loadMoreRestaurants() {
    currentPage++;
    displayRestaurants();
}

// Update results count
function updateResultsCount() {
    if (resultsCount) {
        resultsCount.textContent = filteredRestaurants.length;
    }
}

// Update load more button
function updateLoadMoreButton() {
    const totalPages = Math.ceil(filteredRestaurants.length / restaurantsPerPage);
    
    if (currentPage >= totalPages) {
        loadMoreContainer.style.display = 'none';
    } else {
        loadMoreContainer.style.display = 'block';
    }
}

// Show loading state
function showLoading() {
    if (loading) {
        loading.classList.remove('hidden');
    }
    if (restaurantsGrid) {
        restaurantsGrid.innerHTML = '';
    }
}

// Hide loading state
function hideLoading() {
    if (loading) {
        loading.classList.add('hidden');
    }
}

// Show no results
function showNoResults() {
    if (noResults) {
        noResults.classList.remove('hidden');
    }
    if (restaurantsGrid) {
        restaurantsGrid.innerHTML = '';
    }
    if (loadMoreContainer) {
        loadMoreContainer.style.display = 'none';
    }
}

// Hide no results
function hideNoResults() {
    if (noResults) {
        noResults.classList.add('hidden');
    }
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

// Toggle mobile menu
function toggleMobileMenu() {
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
}

// Initialize authentication
async function initializeAuth() {
    try {
        // Check if user is authenticated
        if (window.api) {
            await window.api.checkAuth();
            updateAuthUI();
        }
    } catch (error) {
        console.error('Failed to check auth status:', error);
    }
}

// Update authentication UI
function updateAuthUI() {
    const authButtons = document.querySelectorAll('.auth-buttons');
    const userMenus = document.querySelectorAll('.user-menu');
    
    if (window.api && window.api.isAuthenticated()) {
        // User is logged in - show user menu, hide auth buttons
        authButtons.forEach(btn => btn.classList.add('hidden'));
        userMenus.forEach(menu => menu.classList.remove('hidden'));
        
        // Update user info
        const user = window.api.getCurrentUser();
        if (user) {
            document.querySelectorAll('.user-name').forEach(el => el.textContent = user.name);
            document.querySelectorAll('.user-email').forEach(el => el.textContent = user.email);
            
            // Update avatars
            const avatarElements = document.querySelectorAll('#user-avatar, #mobile-user-avatar');
            avatarElements.forEach(avatar => {
                if (user.avatar) {
                    avatar.src = user.avatar;
                    avatar.onerror = function() {
                        // Fallback to UI Avatars if image fails to load
                        this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=ff6b35&color=fff&size=100`;
                    };
                } else {
                    // Use UI Avatars as default
                    avatar.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=ff6b35&color=fff&size=100`;
                }
            });
        }
    } else {
        // User is not logged in - show auth buttons, hide user menu
        authButtons.forEach(btn => btn.classList.remove('hidden'));
        userMenus.forEach(menu => menu.classList.add('hidden'));
    }
}

// Setup authentication event listeners
function setupAuthEventListeners() {
    // Login/Register buttons
    const loginBtn = document.getElementById('login-btn');
    const registerBtn = document.getElementById('register-btn');
    const mobileLoginBtn = document.getElementById('mobile-login-btn');
    const mobileRegisterBtn = document.getElementById('mobile-register-btn');
    
    if (loginBtn) loginBtn.addEventListener('click', showLoginModal);
    if (registerBtn) registerBtn.addEventListener('click', showRegisterModal);
    if (mobileLoginBtn) mobileLoginBtn.addEventListener('click', showLoginModal);
    if (mobileRegisterBtn) mobileRegisterBtn.addEventListener('click', showRegisterModal);
    
    // Modal close buttons
    const closeLoginModal = document.getElementById('close-login-modal');
    const closeRegisterModal = document.getElementById('close-register-modal');
    const showLoginModalBtn = document.getElementById('show-login-modal');
    const showRegisterModalBtn = document.getElementById('show-register-modal');
    
    if (closeLoginModal) closeLoginModal.addEventListener('click', hideLoginModal);
    if (closeRegisterModal) closeRegisterModal.addEventListener('click', hideRegisterModal);
    if (showLoginModalBtn) showLoginModalBtn.addEventListener('click', showLoginModal);
    if (showRegisterModalBtn) showRegisterModalBtn.addEventListener('click', showRegisterModal);
    
    // Form submissions
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    
    if (loginForm) loginForm.addEventListener('submit', handleLogin);
    if (registerForm) registerForm.addEventListener('submit', handleRegister);
    
    // User menu
    const userMenuToggle = document.getElementById('user-menu-toggle');
    const userDropdown = document.getElementById('user-dropdown');
    const logoutBtn = document.getElementById('logout-btn');
    const mobileLogoutBtn = document.getElementById('mobile-logout-btn');
    
    if (userMenuToggle && userDropdown) {
        userMenuToggle.addEventListener('click', () => {
            userDropdown.classList.toggle('hidden');
        });
    }
    
    if (logoutBtn) logoutBtn.addEventListener('click', handleLogout);
    if (mobileLogoutBtn) mobileLogoutBtn.addEventListener('click', handleLogout);
    
    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (userDropdown && !userMenuToggle.contains(e.target) && !userDropdown.contains(e.target)) {
            userDropdown.classList.add('hidden');
        }
    });
}

// Authentication handler functions
function showLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function hideLoginModal() {
    const modal = document.getElementById('login-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function showRegisterModal() {
    const modal = document.getElementById('register-modal');
    if (modal) {
        modal.classList.remove('hidden');
    }
}

function hideRegisterModal() {
    const modal = document.getElementById('register-modal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

async function handleLogin(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const email = formData.get('email');
    const password = formData.get('password');
    
    try {
        const response = await window.api.login(email, password);
        if (response.success) {
            hideLoginModal();
            updateAuthUI();
            // Show success message
            if (window.restaurantApp) {
                window.restaurantApp.showNotification('Login successful!', 'success');
            }
        } else {
            throw new Error(response.message || 'Login failed');
        }
    } catch (error) {
        console.error('Login error:', error);
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Login failed. Please try again.', 'error');
        } else {
            alert('Login failed. Please try again.');
        }
    }
}

async function handleRegister(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    const name = formData.get('name');
    const email = formData.get('email');
    const password = formData.get('password');
    const passwordConfirmation = formData.get('password_confirmation');
    
    if (password !== passwordConfirmation) {
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Passwords do not match', 'error');
        } else {
            alert('Passwords do not match');
        }
        return;
    }
    
    try {
        const response = await window.api.register(name, email, password, passwordConfirmation);
        if (response.success) {
            hideRegisterModal();
            updateAuthUI();
            // Show success message
            if (window.restaurantApp) {
                window.restaurantApp.showNotification('Registration successful!', 'success');
            }
        } else {
            throw new Error(response.message || 'Registration failed');
        }
    } catch (error) {
        console.error('Registration error:', error);
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Registration failed. Please try again.', 'error');
        } else {
            alert('Registration failed. Please try again.');
        }
    }
}

async function handleLogout() {
    try {
        await window.api.logout();
        updateAuthUI();
        // Show success message
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Logged out successfully', 'success');
        }
    } catch (error) {
        console.error('Logout error:', error);
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Logout failed', 'error');
        } else {
            alert('Logout failed');
        }
    }
}

/**
 * Autocomplete functionality for restaurants page
 */

// Load autocomplete data from restaurants
function loadAutocompleteDataFromRestaurants() {
    if (allRestaurants.length > 0) {
        // Extract unique data for autocomplete
        const restaurantNames = [...new Set(allRestaurants.map(r => r.name))];
        const cuisines = [...new Set(allRestaurants.map(r => r.cuisine).filter(Boolean))];
        const locations = [...new Set(allRestaurants.map(r => r.location).filter(Boolean))];
        
        restaurantAutocompleteData = {
            restaurants: restaurantNames,
            cuisines: cuisines,
            locations: locations
        };
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
    restaurantAutocompleteData.restaurants.forEach(name => {
        if (name.toLowerCase().includes(query)) {
            suggestions.push({
                text: name,
                type: 'restaurant',
                icon: 'fas fa-utensils'
            });
        }
    });
    
    // Search cuisines
    restaurantAutocompleteData.cuisines.forEach(cuisine => {
        if (cuisine.toLowerCase().includes(query)) {
            suggestions.push({
                text: cuisine,
                type: 'cuisine',
                icon: 'fas fa-pizza-slice'
            });
        }
    });
    
    // Search locations
    restaurantAutocompleteData.locations.forEach(location => {
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
        <div class="autocomplete-item px-4 py-3 hover:bg-orange-50 cursor-pointer flex items-center transition-colors duration-150 border-b border-gray-100 last:border-b-0" 
             data-suggestion="${suggestion.text}" data-index="${index}">
            <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-full mr-3">
                <i class="${suggestion.icon} text-orange-500 text-sm"></i>
            </div>
            <div class="flex-1">
                <span class="text-gray-800 font-medium">${suggestion.text}</span>
                <span class="block text-xs text-gray-500 capitalize">${suggestion.type}</span>
            </div>
            <div class="ml-2">
                <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
            </div>
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
            items.forEach(i => i.classList.remove('bg-orange-50', 'shadow-sm'));
            // Add highlight to current item
            this.classList.add('bg-orange-50', 'shadow-sm');
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
        // Trigger search for restaurants page
        handleSearch();
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
                handleSearch();
            }
            return;
        case 'Escape':
            hideAutocomplete();
            return;
        default:
            return;
    }
    
    // Update active item
    items.forEach(item => item.classList.remove('active', 'bg-orange-50', 'shadow-sm'));
    if (activeIndex >= 0 && items[activeIndex]) {
        items[activeIndex].classList.add('active', 'bg-orange-50', 'shadow-sm');
    }
}
