// Restaurant detail page functionality

let currentRestaurant = null;
let restaurantReviews = [];
let allRestaurants = [];
let currentReviewPage = 1;
let reviewsPerPage = 5;

// DOM Elements
let loading, restaurantNotFound, restaurantContent;
let mobileMenuBtn, mobileMenu;
let restaurantMainImage, viewGalleryBtn, galleryModal, closeGalleryBtn, galleryImages;
let restaurantName, restaurantCuisine, restaurantLocation, restaurantPrice;
let restaurantRating, restaurantRatingNumber, restaurantReviewCount;
let restaurantDescription, writeReviewBtn, shareBtn, favoriteBtn;
let restaurantAddress, restaurantPhone, restaurantHours, restaurantFeatures;
let ratingBreakdown, reviewsList, reviewSort, loadMoreReviewsBtn, loadMoreReviewsElement;
let relatedRestaurants;

// Initialize the restaurant detail page
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    setupEventListeners();
    initializeAuth();
    loadRestaurantDetails();
});

// Initialize DOM elements
function initializeElements() {
    // Main containers
    loading = document.getElementById('loading');
    restaurantNotFound = document.getElementById('restaurant-not-found');
    restaurantContent = document.getElementById('restaurant-content');
    
    // Mobile menu
    mobileMenuBtn = document.getElementById('mobile-menu-btn');
    mobileMenu = document.getElementById('mobile-menu');
    
    // Restaurant info elements
    restaurantMainImage = document.getElementById('restaurant-main-image');
    viewGalleryBtn = document.getElementById('view-gallery');
    restaurantName = document.getElementById('restaurant-name');
    restaurantCuisine = document.getElementById('restaurant-cuisine');
    restaurantLocation = document.getElementById('restaurant-location');
    restaurantPrice = document.getElementById('restaurant-price');
    restaurantRating = document.getElementById('restaurant-rating');
    restaurantRatingNumber = document.getElementById('restaurant-rating-number');
    restaurantReviewCount = document.getElementById('restaurant-review-count');
    restaurantDescription = document.getElementById('restaurant-description');
    
    // Action buttons
    writeReviewBtn = document.getElementById('write-review-btn');
    shareBtn = document.getElementById('share-btn');
    favoriteBtn = document.getElementById('favorite-btn');
    
    // Contact info
    restaurantAddress = document.getElementById('restaurant-address');
    restaurantPhone = document.getElementById('restaurant-phone');
    restaurantHours = document.getElementById('restaurant-hours');
    restaurantFeatures = document.getElementById('restaurant-features');
    
    // Reviews section
    ratingBreakdown = document.getElementById('rating-breakdown');
    reviewsList = document.getElementById('reviews-list');
    reviewSort = document.getElementById('review-sort');
    loadMoreReviewsBtn = document.getElementById('load-more-reviews-btn');
    loadMoreReviewsElement = document.getElementById('load-more-reviews');
    
    // Gallery modal
    galleryModal = document.getElementById('gallery-modal');
    closeGalleryBtn = document.getElementById('close-gallery');
    galleryImages = document.getElementById('gallery-images');
    
    // Related restaurants
    relatedRestaurants = document.getElementById('related-restaurants');
}

// Setup event listeners
function setupEventListeners() {
    // Mobile menu toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Gallery functionality
    if (viewGalleryBtn) {
        viewGalleryBtn.addEventListener('click', openGallery);
    }
    if (closeGalleryBtn) {
        closeGalleryBtn.addEventListener('click', closeGallery);
    }
    if (galleryModal) {
        galleryModal.addEventListener('click', function(e) {
            if (e.target === galleryModal) {
                closeGallery();
            }
        });
    }
    
    // Action buttons
    if (writeReviewBtn) {
        writeReviewBtn.addEventListener('click', function(e) {
            e.preventDefault();
            // Store restaurant ID for review form
            const restaurantId = getRestaurantIdFromURL();
            localStorage.setItem('reviewRestaurantId', restaurantId);
            window.location.href = 'write-review.html';
        });
    }
    
    if (shareBtn) {
        shareBtn.addEventListener('click', shareRestaurant);
    }
    
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', toggleFavorite);
    }
    
    // Review sorting
    if (reviewSort) {
        reviewSort.addEventListener('change', sortReviews);
    }
    
    // Load more reviews
    if (loadMoreReviewsBtn) {
        loadMoreReviewsBtn.addEventListener('click', loadMoreReviews);
    }
    
    // Authentication event listeners
    setupAuthEventListeners();
}

// Get restaurant ID from URL
function getRestaurantIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Load restaurant details
async function loadRestaurantDetails() {
    const restaurantId = getRestaurantIdFromURL();
    
    if (!restaurantId) {
        showRestaurantNotFound();
        return;
    }
    
    try {
        showLoading();
        
        // Load restaurant details from API
        const restaurantResponse = await window.api.getRestaurant(restaurantId);
        if (restaurantResponse.success) {
            currentRestaurant = restaurantResponse.data;
        } else {
            throw new Error('Restaurant not found');
        }
        
        // Load restaurant reviews from API
        await loadRestaurantReviews();
        
        // Load all restaurants for related restaurants
        const restaurantsResponse = await window.api.getRestaurants();
        if (restaurantsResponse.success) {
            allRestaurants = restaurantsResponse.data;
        }
        
        // Display restaurant details
        displayRestaurantDetails();
        displayRestaurantReviews();
        displayRelatedRestaurants();
        
        // Initialize map
        initializeMap();
        
        hideLoading();
        showRestaurantContent();
        
    } catch (error) {
        console.error('Error loading restaurant details:', error);
        showRestaurantNotFound();
    }
}

// Load restaurant reviews
async function loadRestaurantReviews() {
    try {
        const restaurantId = getRestaurantIdFromURL();
        const response = await window.api.getReviews({ restaurant_id: restaurantId });
        
        if (response.success) {
            restaurantReviews = response.data;
        } else {
            // Fallback to empty array if no reviews found
            restaurantReviews = [];
        }
    } catch (error) {
        console.error('Error loading restaurant reviews:', error);
        restaurantReviews = [];
    }
}

// Display restaurant details
function displayRestaurantDetails() {
    if (!currentRestaurant) return;
    
    // Basic info - handle image properly
    if (currentRestaurant.primary_image && currentRestaurant.primary_image.length > 0) {
        restaurantMainImage.src = currentRestaurant.primary_image[0].url || currentRestaurant.primary_image[0].full_url || currentRestaurant.primary_image[0].path;
    } else if (currentRestaurant.images && currentRestaurant.images.length > 0) {
        // Find primary image first, otherwise use first image
        const primaryImage = currentRestaurant.images.find(img => img.is_primary) || currentRestaurant.images[0];
        restaurantMainImage.src = primaryImage.url || primaryImage.full_url || primaryImage.path;
    } else if (currentRestaurant.main_image) {
        restaurantMainImage.src = currentRestaurant.main_image;
    } else {
        // Fallback to a default image
        restaurantMainImage.src = 'https://via.placeholder.com/600x400/ff6b35/ffffff?text=No+Image';
    }
    restaurantMainImage.alt = currentRestaurant.name;
    restaurantName.textContent = currentRestaurant.name;
    restaurantCuisine.textContent = currentRestaurant.cuisine;
    restaurantLocation.textContent = currentRestaurant.location;
    restaurantPrice.textContent = currentRestaurant.priceRange;
    restaurantDescription.textContent = currentRestaurant.description;
    
    // Rating
    const rating = parseFloat(currentRestaurant.average_rating) || 0;
    restaurantRating.innerHTML = window.FoodieHub.generateStars(rating);
    restaurantRatingNumber.textContent = rating > 0 ? rating.toFixed(1) : 'No rating';
    restaurantReviewCount.textContent = currentRestaurant.review_count || 0;
    
    // Contact info
    restaurantAddress.textContent = currentRestaurant.address || 'Address not available';
    restaurantPhone.textContent = currentRestaurant.phone || 'Phone not available';
    
    // Hours
    if (currentRestaurant.hours) {
        const hoursHTML = Object.entries(currentRestaurant.hours).map(([day, hours]) => `
            <div class="flex justify-between items-center py-3 px-4 rounded-lg hover:bg-gray-50 transition-colors border-b border-gray-100 last:border-b-0">
                <span class="font-medium capitalize text-gray-800 flex items-center">
                    <span class="w-2 h-2 bg-orange-500 rounded-full mr-3"></span>
                    ${day}
                </span>
                <span class="text-gray-600 font-mono text-sm bg-gray-100 px-3 py-1 rounded-md">${hours}</span>
            </div>
        `).join('');
        restaurantHours.innerHTML = `
            <div class="bg-gray-50 rounded-lg overflow-hidden">
                ${hoursHTML}
            </div>
        `;
    }
    
    // Features
    if (currentRestaurant.features) {
        const featuresHTML = currentRestaurant.features.map(feature => `
            <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm">
                ${feature}
            </span>
        `).join('');
        restaurantFeatures.innerHTML = featuresHTML;
    }
    
    // Update page title
    document.title = `${currentRestaurant.name} - FoodieHub`;
    
    // Update sidebar address and directions link
    const addressSidebar = document.getElementById('restaurant-address-sidebar');
    const directionsLink = document.getElementById('directions-link');
    
    if (addressSidebar) {
        addressSidebar.textContent = currentRestaurant.address || 'Address not available';
    }
    
    if (directionsLink) {
        // Use restaurant name and location for more accurate results
        const restaurantQuery = encodeURIComponent(`${currentRestaurant.name}, ${currentRestaurant.location || currentRestaurant.address}`);
        const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${restaurantQuery}`;
        
        directionsLink.href = googleMapsUrl;
        directionsLink.title = `Get directions to ${currentRestaurant.name}`;
    }
}

// Initialize Google Maps embed
function initializeMap() {
    if (!currentRestaurant || !currentRestaurant.latitude || !currentRestaurant.longitude) {
        // Show fallback message if no coordinates
        const mapContainer = document.getElementById('restaurant-map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-48 bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-map-marker-alt text-3xl mb-2"></i>
                        <p>Location coordinates not available</p>
                    </div>
                </div>
            `;
        }
        return;
    }

    try {
        // Create Google Maps embed iframe
        const mapContainer = document.getElementById('restaurant-map');
        if (mapContainer) {
            // Use restaurant name and location for better accuracy
            const restaurantQuery = encodeURIComponent(`${currentRestaurant.name}, ${currentRestaurant.location || currentRestaurant.address}`);
            const mapUrl = `https://maps.google.com/maps?q=${restaurantQuery}&output=embed`;
            
            mapContainer.innerHTML = `
                <iframe
                    width="100%"
                    height="100%"
                    style="border:0; border-radius: 0.5rem;"
                    loading="lazy"
                    allowfullscreen
                    referrerpolicy="no-referrer-when-downgrade"
                    src="${mapUrl}">
                </iframe>
            `;
        }

    } catch (error) {
        console.error('Error initializing Google Maps:', error);
        
        // Show fallback message on error
        const mapContainer = document.getElementById('restaurant-map');
        if (mapContainer) {
            mapContainer.innerHTML = `
                <div class="flex items-center justify-center h-48 bg-gray-100 rounded-lg">
                    <div class="text-center text-gray-500">
                        <i class="fas fa-exclamation-triangle text-3xl mb-2"></i>
                        <p>Unable to load Google Maps</p>
                        <p class="text-sm">Please check your internet connection</p>
                    </div>
                </div>
            `;
        }
    }
}

// Display restaurant reviews
function displayRestaurantReviews() {
    if (!restaurantReviews.length) {
        reviewsList.innerHTML = '<p class="text-gray-500 text-center py-8">No reviews yet. Be the first to review this restaurant!</p>';
        return;
    }
    
    // Display rating breakdown
    displayRatingBreakdown();
    
    // Display reviews
    const reviewsToShow = restaurantReviews.slice(0, currentReviewPage * reviewsPerPage);
    
    reviewsList.innerHTML = reviewsToShow.map(review => `
        <div class="border-b border-gray-200 pb-6 last:border-b-0">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <img src="${review.user?.avatar || 'https://ui-avatars.com/api/?name=' + encodeURIComponent(review.user?.name || 'User') + '&background=ff6b35&color=fff&size=100'}" alt="${review.user?.name || 'User'}" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">${review.user?.name || 'Anonymous'}</h4>
                        <div class="star-rating">
                            ${window.FoodieHub.generateStars(review.overall_rating || review.rating)}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">${window.FoodieHub.formatDate(review.created_at || review.date)}</p>
                    <button class="text-sm text-gray-500 hover:text-orange-500 mt-1">
                        <i class="far fa-thumbs-up mr-1"></i>${review.helpful_count || 0}
                    </button>
                </div>
            </div>
            <p class="text-gray-700">${review.comment || review.review_text || 'No comment'}</p>
        </div>
    `).join('');
    
    // Update load more button
    updateLoadMoreReviewsButton();
}

// Display rating breakdown
function displayRatingBreakdown() {
    const totalReviews = restaurantReviews.length;
    const ratingCounts = {5: 0, 4: 0, 3: 0, 2: 0, 1: 0};
    
    restaurantReviews.forEach(review => {
        const rating = Math.round(review.overall_rating || review.rating || 0);
        if (rating >= 1 && rating <= 5) {
            ratingCounts[rating]++;
        }
    });
    
    const breakdownHTML = Object.entries(ratingCounts).reverse().map(([rating, count]) => {
        const percentage = totalReviews > 0 ? (count / totalReviews) * 100 : 0;
        return `
            <div class="flex items-center mb-2">
                <span class="w-8 text-sm">${rating}★</span>
                <div class="flex-1 mx-3 bg-gray-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: ${percentage}%"></div>
                </div>
                <span class="w-8 text-sm text-gray-600">${count}</span>
            </div>
        `;
    }).join('');
    
    const overallRating = parseFloat(currentRestaurant.average_rating) || 0;
    ratingBreakdown.innerHTML = `
        <div class="flex items-center mb-4">
            <div class="text-3xl font-bold text-gray-800 mr-4">${overallRating > 0 ? overallRating.toFixed(1) : 'No rating'}</div>
            <div>
                <div class="star-rating mb-1">
                    ${window.FoodieHub.generateStars(overallRating)}
                </div>
                <div class="text-sm text-gray-600">${totalReviews} reviews</div>
            </div>
        </div>
        ${breakdownHTML}
    `;
}

// Display related restaurants
function displayRelatedRestaurants() {
    if (!allRestaurants.length) return;
    
    // Find restaurants with same cuisine or location
    const related = allRestaurants
        .filter(r => r.id !== currentRestaurant.id && 
                    (r.cuisine === currentRestaurant.cuisine || r.location === currentRestaurant.location))
        .slice(0, 3);
    
    if (related.length === 0) {
        relatedRestaurants.innerHTML = '<p class="text-gray-500 text-center py-4">No related restaurants found.</p>';
        return;
    }
    
    relatedRestaurants.innerHTML = related.map(restaurant => `
        <div class="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition">
            <img src="${restaurant.primary_image && restaurant.primary_image.length > 0 ? restaurant.primary_image[0].full_url : (restaurant.images && restaurant.images.length > 0 ? restaurant.images[0].full_url : (restaurant.main_image || 'https://via.placeholder.com/64x64/ff6b35/ffffff?text=No+Image'))}" alt="${restaurant.name}" class="w-16 h-16 object-cover rounded-lg">
            <div class="flex-1">
                <h4 class="font-semibold text-gray-800 text-sm">${restaurant.name}</h4>
                <p class="text-xs text-gray-600">${restaurant.cuisine} • ${restaurant.location}</p>
                <div class="flex items-center mt-1">
                    <div class="star-rating text-xs">
                        ${window.FoodieHub.generateStars(parseFloat(restaurant.average_rating) || 0)}
                    </div>
                    <span class="text-xs text-gray-500 ml-1">${(parseFloat(restaurant.average_rating) || 0).toFixed(1)}</span>
                </div>
            </div>
            <a href="restaurant-detail.html?id=${restaurant.id}" class="text-orange-500 hover:text-orange-600">
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    `).join('');
}

// Sort reviews
function sortReviews() {
    const sortBy = reviewSort.value;
    
    switch (sortBy) {
        case 'newest':
            restaurantReviews.sort((a, b) => new Date(b.date) - new Date(a.date));
            break;
        case 'oldest':
            restaurantReviews.sort((a, b) => new Date(a.date) - new Date(b.date));
            break;
        case 'highest':
            restaurantReviews.sort((a, b) => b.rating - a.rating);
            break;
        case 'lowest':
            restaurantReviews.sort((a, b) => a.rating - b.rating);
            break;
    }
    
    currentReviewPage = 1;
    displayRestaurantReviews();
}

// Load more reviews
function loadMoreReviews() {
    currentReviewPage++;
    displayRestaurantReviews();
}

// Update load more reviews button
function updateLoadMoreReviewsButton() {
    const totalPages = Math.ceil(restaurantReviews.length / reviewsPerPage);
    
    if (currentReviewPage >= totalPages) {
        loadMoreReviewsElement.style.display = 'none';
    } else {
        loadMoreReviewsElement.style.display = 'block';
    }
}

// Open gallery
function openGallery() {
    if (!currentRestaurant) return;
    
    let images = [];
    
    // Check for polymorphic images (from Image model relationship)
    if (currentRestaurant.images && currentRestaurant.images.length > 0) {
        images = currentRestaurant.images.map(img => img.url || img.full_url || img.path);
    } 
    // Check for primary images (from Image model relationship) 
    else if (currentRestaurant.primary_image && currentRestaurant.primary_image.length > 0) {
        images = currentRestaurant.primary_image.map(img => img.full_url || img.url);
    }
    // Check for images array field (from seeder data)
    else if (Array.isArray(currentRestaurant.images_array)) {
        images = currentRestaurant.images_array;
    }
    // Use main_image as fallback and create array
    else if (currentRestaurant.main_image) {
        images = [currentRestaurant.main_image];
    }
    
    if (images.length === 0) {
        // Show message if no images available
        galleryImages.innerHTML = `
            <div class="col-span-full text-center py-8 text-gray-500">
                <i class="fas fa-image text-4xl mb-2"></i>
                <p>No additional images available</p>
            </div>
        `;
        galleryModal.classList.remove('hidden');
        return;
    }
    
    galleryImages.innerHTML = images.map((imageUrl, index) => `
        <div class="gallery-item">
            <img src="${imageUrl}" alt="${currentRestaurant.name} - Image ${index + 1}" class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-80 transition">
        </div>
    `).join('');
    
    galleryModal.classList.remove('hidden');
}

// Close gallery
function closeGallery() {
    galleryModal.classList.add('hidden');
}

// Share restaurant
function shareRestaurant() {
    if (navigator.share) {
        navigator.share({
            title: currentRestaurant.name,
            text: `Check out ${currentRestaurant.name} on FoodieHub!`,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copied to clipboard!');
        });
    }
}

// Toggle favorite
function toggleFavorite() {
    const isFavorited = favoriteBtn.classList.contains('favorited');
    
    if (isFavorited) {
        favoriteBtn.classList.remove('favorited');
        favoriteBtn.innerHTML = '<i class="far fa-heart mr-2"></i>Save';
    } else {
        favoriteBtn.classList.add('favorited');
        favoriteBtn.innerHTML = '<i class="fas fa-heart mr-2"></i>Saved';
    }
}

// Show loading state
function showLoading() {
    if (loading) {
        loading.classList.remove('hidden');
    }
    if (restaurantContent) {
        restaurantContent.classList.add('hidden');
    }
    if (restaurantNotFound) {
        restaurantNotFound.classList.add('hidden');
    }
}

// Hide loading state
function hideLoading() {
    if (loading) {
        loading.classList.add('hidden');
    }
}

// Show restaurant content
function showRestaurantContent() {
    if (restaurantContent) {
        restaurantContent.classList.remove('hidden');
    }
}

// Show restaurant not found
function showRestaurantNotFound() {
    if (restaurantNotFound) {
        restaurantNotFound.classList.remove('hidden');
    }
    if (loading) {
        loading.classList.add('hidden');
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
