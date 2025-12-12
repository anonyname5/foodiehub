// Profile page functionality

let userReviews = [];
let favoriteRestaurants = [];
let allRestaurants = [];
let currentReviewPage = 1;
let reviewsPerPage = 5;

// DOM Elements
let mobileMenuBtn, mobileMenu;
let profileNavBtns, profileSections;
let userName, userEmail;
let totalReviews, averageRating, favoriteCount;
let recentActivity, topCuisines;
let userReviewsContainer, reviewFilter, loadMoreUserReviewsBtn, loadMoreUserReviewsElement;
let favoriteRestaurantsContainer, favoriteFilter;
let profileForm;

// Initialize the profile page
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    setupEventListeners();
    setupAuthEventListeners();
    initializeAuth();
    loadUserData();
    loadRestaurants();
    
    // Ensure avatars are initialized after everything loads
    setTimeout(() => {
        console.log('Final avatar initialization...');
        initializeUserAvatars();
    }, 1000);
});

// Initialize DOM elements
function initializeElements() {
    // Mobile menu
    mobileMenuBtn = document.getElementById('mobile-menu-btn');
    mobileMenu = document.getElementById('mobile-menu');
    
    // Navigation
    profileNavBtns = document.querySelectorAll('.profile-nav-btn');
    profileSections = document.querySelectorAll('.profile-section');
    
    // Debug: Check if elements were found
    console.log('Profile nav buttons found:', profileNavBtns.length);
    console.log('Profile sections found:', profileSections.length);
    
    // User info
    userName = document.getElementById('user-name');
    userEmail = document.getElementById('user-email');
    
    // Stats
    totalReviews = document.getElementById('total-reviews');
    averageRating = document.getElementById('average-rating');
    favoriteCount = document.getElementById('favorite-count');
    
    // Overview section
    recentActivity = document.getElementById('recent-activity');
    topCuisines = document.getElementById('top-cuisines');
    
    // Reviews section
    userReviewsContainer = document.getElementById('user-reviews');
    reviewFilter = document.getElementById('review-filter');
    loadMoreUserReviewsBtn = document.getElementById('load-more-user-reviews-btn');
    loadMoreUserReviewsElement = document.getElementById('load-more-user-reviews');
    
    // Favorites section
    favoriteRestaurantsContainer = document.getElementById('favorite-restaurants');
    favoriteFilter = document.getElementById('favorite-filter');
    
    // Settings section
    profileForm = document.getElementById('profile-form');
    
    // Initialize profile form enhancements
    initializeProfileForm();
    
    // Initialize location autocomplete
    initializeLocationAutocomplete();
    
    // Initialize avatars after a short delay to ensure DOM is ready
    setTimeout(() => {
        initializeUserAvatars();
    }, 100);
}

// Setup event listeners
function setupEventListeners() {
    console.log('Setting up profile event listeners...');
    console.log('profileNavBtns:', profileNavBtns);
    
    // Mobile menu toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Profile navigation
    if (profileNavBtns && profileNavBtns.length > 0) {
        profileNavBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const section = btn.dataset.section;
                switchSection(section);
            });
        });
    }
    
    // Review filter
    if (reviewFilter) {
        reviewFilter.addEventListener('change', filterUserReviews);
    }
    
    // Favorite filter
    if (favoriteFilter) {
        favoriteFilter.addEventListener('change', filterFavorites);
    }
    
    // Load more reviews
    if (loadMoreUserReviewsBtn) {
        loadMoreUserReviewsBtn.addEventListener('click', loadMoreUserReviews);
    }
    
    // Profile form
    if (profileForm) {
        profileForm.addEventListener('submit', handleProfileUpdate);
        
        // Reset button
        const resetBtn = profileForm.querySelector('button[type="button"]');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetProfileForm);
        }
    }
}

// Load user data
async function loadUserData() {
    try {
        // Check if user is authenticated
        if (!window.api || !window.api.isAuthenticated()) {
            // Redirect to login if not authenticated
            window.location.href = 'index.html';
            return;
        }
        
        const currentUser = window.api.getCurrentUser();
        if (!currentUser) {
            window.location.href = 'index.html';
            return;
        }
        
        // Load user reviews from API
        await loadUserReviewsFromAPI(currentUser.id);
        
        // Load favorite restaurants from API
        await loadFavoriteRestaurantsFromAPI(currentUser.id);
        
        // Update user profile information
        updateUserProfile(currentUser);
        
        // Force update avatars after user data is loaded
        setTimeout(() => {
            initializeUserAvatars();
        }, 200);
        
        // Update user stats
        updateUserStats();
        
        // Load overview data
        loadRecentActivity();
        loadTopCuisines();
        
        // Load user reviews
        displayUserReviews();
        
        // Load favorite restaurants
        displayFavoriteRestaurants();
        
    } catch (error) {
        console.error('Error loading user data:', error);
        showNotification('Failed to load user data', 'error');
    }
}

// Load user reviews from API
async function loadUserReviewsFromAPI(userId) {
    try {
        const response = await window.api.getUserReviews(userId);
        if (response.success) {
            userReviews = response.data;
            console.log('Loaded user reviews:', userReviews);
        } else {
            throw new Error('Failed to load user reviews');
        }
    } catch (error) {
        console.error('Error loading user reviews:', error);
        userReviews = [];
    }
}

// Load favorite restaurants from API
async function loadFavoriteRestaurantsFromAPI(userId) {
    try {
        const response = await window.api.getUserFavorites(userId);
        if (response.success) {
            favoriteRestaurants = response.data;
            console.log('Loaded favorite restaurants:', favoriteRestaurants);
        } else {
            throw new Error('Failed to load favorite restaurants');
        }
    } catch (error) {
        console.error('Error loading favorite restaurants:', error);
        favoriteRestaurants = [];
    }
}

// Update user profile information
function updateUserProfile(user) {
    // Update user name and email in profile header
    if (userName) {
        userName.textContent = user.name || 'User';
    }
    
    if (userEmail) {
        userEmail.textContent = user.email || '';
    }
    
    // Update avatar display using the centralized avatar management
    initializeUserAvatars();
    
    // Update profile form with current user data
    if (profileForm) {
        const firstNameInput = profileForm.querySelector('input[name="firstName"]');
        const lastNameInput = profileForm.querySelector('input[name="lastName"]');
        const emailInput = profileForm.querySelector('input[name="email"]');
        const bioInput = profileForm.querySelector('textarea[name="bio"]');
        const locationInput = profileForm.querySelector('input[name="location"]');
        
        if (firstNameInput) firstNameInput.value = user.firstName || user.name?.split(' ')[0] || '';
        if (lastNameInput) lastNameInput.value = user.lastName || user.name?.split(' ').slice(1).join(' ') || '';
        if (emailInput) emailInput.value = user.email || '';
        if (bioInput) bioInput.value = user.bio || '';
        if (locationInput) locationInput.value = user.location || '';
    }
}

// Load restaurants data
async function loadRestaurants() {
    try {
        // Use sample data from main.js or load from JSON
        allRestaurants = window.FoodieHub ? window.FoodieHub.sampleRestaurants : [];
        
        // If no data available, fetch from JSON
        if (allRestaurants.length === 0) {
            const response = await fetch('data/restaurants.json');
            const data = await response.json();
            allRestaurants = data.restaurants;
        }
        
    } catch (error) {
        console.error('Error loading restaurants:', error);
    }
}

// Update user stats
function updateUserStats() {
    // Total reviews
    if (totalReviews) {
        totalReviews.textContent = userReviews ? userReviews.length : 0;
    }
    
    // Average rating
    if (averageRating) {
        if (userReviews && userReviews.length > 0) {
            const validRatings = userReviews.filter(review => {
                const rating = review.overall_rating || review.rating;
                return rating !== null && rating !== undefined && !isNaN(parseFloat(rating));
            });
            
            if (validRatings.length > 0) {
                const totalRating = validRatings.reduce((sum, review) => {
                    const rating = parseFloat(review.overall_rating || review.rating);
                    return sum + rating;
                }, 0);
                const avg = (totalRating / validRatings.length).toFixed(1);
                averageRating.textContent = avg;
            } else {
                averageRating.textContent = '0.0';
            }
        } else {
            averageRating.textContent = '0.0';
        }
    }
    
    // Favorite count
    if (favoriteCount) {
        favoriteCount.textContent = favoriteRestaurants ? favoriteRestaurants.length : 0;
    }
}

// Load recent activity
function loadRecentActivity() {
    if (!recentActivity) return;
    
    const activities = [];
    
    // Add recent reviews
    if (userReviews && userReviews.length > 0) {
        userReviews.slice(0, 3).forEach(review => {
            const restaurantName = review.restaurant?.name || review.restaurantName || 'Unknown Restaurant';
            const reviewDate = review.created_at || review.date || new Date().toISOString().split('T')[0];
            const rating = review.overall_rating || review.rating || 0;
            
            activities.push({
                type: 'review',
                text: `Reviewed ${restaurantName}`,
                date: reviewDate,
                rating: rating
            });
        });
    }
    
    // Add recent favorites
    if (favoriteRestaurants && favoriteRestaurants.length > 0) {
        favoriteRestaurants.slice(0, 2).forEach(restaurant => {
            const restaurantName = restaurant.name || 'Unknown Restaurant';
            const dateAdded = restaurant.created_at || restaurant.dateAdded || new Date().toISOString().split('T')[0];
            
            activities.push({
                type: 'favorite',
                text: `Added ${restaurantName} to favorites`,
                date: dateAdded
            });
        });
    }
    
    // Sort by date
    activities.sort((a, b) => new Date(b.date) - new Date(a.date));
    
    if (activities.length === 0) {
        recentActivity.innerHTML = '<p class="text-gray-500 text-center py-4">No recent activity</p>';
        return;
    }
    
    recentActivity.innerHTML = activities.slice(0, 5).map(activity => `
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-${activity.type === 'review' ? 'star' : 'heart'} text-orange-500 text-sm"></i>
            </div>
            <div class="flex-1">
                <p class="text-sm text-gray-800">${activity.text}</p>
                <p class="text-xs text-gray-500">${window.FoodieHub.formatDate(activity.date)}</p>
            </div>
            ${activity.rating ? `
                <div class="star-rating text-xs">
                    ${window.FoodieHub.generateStars(activity.rating)}
                </div>
            ` : ''}
        </div>
    `).join('');
}

// Load top cuisines
function loadTopCuisines() {
    if (!topCuisines) return;
    
    const cuisineCounts = {};
    
    // Count cuisines from reviews
    userReviews.forEach(review => {
        const restaurant = allRestaurants.find(r => r.id === review.restaurantId);
        if (restaurant) {
            cuisineCounts[restaurant.cuisine] = (cuisineCounts[restaurant.cuisine] || 0) + 1;
        }
    });
    
    // Count cuisines from favorites
    favoriteRestaurants.forEach(restaurant => {
        cuisineCounts[restaurant.cuisine] = (cuisineCounts[restaurant.cuisine] || 0) + 1;
    });
    
    const sortedCuisines = Object.entries(cuisineCounts)
        .sort(([,a], [,b]) => b - a)
        .slice(0, 5);
    
    if (sortedCuisines.length === 0) {
        topCuisines.innerHTML = '<p class="text-gray-500 text-center py-4">No cuisine preferences yet</p>';
        return;
    }
    
    topCuisines.innerHTML = sortedCuisines.map(([cuisine, count]) => `
        <div class="flex items-center justify-between">
            <span class="text-gray-800">${cuisine}</span>
            <div class="flex items-center">
                <div class="w-16 bg-gray-200 rounded-full h-2 mr-3">
                    <div class="bg-orange-500 h-2 rounded-full" style="width: ${(count / Math.max(...Object.values(cuisineCounts))) * 100}%"></div>
                </div>
                <span class="text-sm text-gray-600">${count}</span>
            </div>
        </div>
    `).join('');
}

// Display user reviews
function displayUserReviews() {
    if (!userReviewsContainer) return;
    
    if (userReviews.length === 0) {
        userReviewsContainer.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-star text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No reviews yet</h3>
                <p class="text-gray-600 mb-6">Start sharing your dining experiences with the community!</p>
                <a href="write-review.html" class="btn-primary">Write Your First Review</a>
            </div>
        `;
        return;
    }
    
    const reviewsToShow = userReviews.slice(0, currentReviewPage * reviewsPerPage);
    
    userReviewsContainer.innerHTML = reviewsToShow.map(review => `
        <div class="border-b border-gray-200 pb-6 last:border-b-0">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?w=50&h=50&fit=crop&crop=face" alt="You" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">You</h4>
                        <p class="text-sm text-gray-600">${review.restaurant?.name || 'Unknown Restaurant'}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">${window.FoodieHub ? window.FoodieHub.formatDate(review.created_at || review.date) : new Date(review.created_at || review.date).toLocaleDateString()}</p>
                    <div class="flex items-center space-x-2 mt-1">
                        <button class="text-sm text-gray-500 hover:text-orange-500" onclick="editReview(${review.id})">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="text-sm text-gray-500 hover:text-red-500" onclick="deleteReview(${review.id})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="star-rating mb-3">
                ${window.FoodieHub ? window.FoodieHub.generateStars(review.overall_rating || review.rating || 0) : '★'.repeat(Math.floor(review.overall_rating || review.rating || 0))}
            </div>
            ${review.title ? `<h5 class="font-semibold text-gray-800 mb-2">${review.title}</h5>` : ''}
            <p class="text-gray-700 mb-3">${review.comment || review.review_text || 'No comment'}</p>
            ${review.food_rating || review.service_rating || review.ambiance_rating || review.value_rating ? `
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    ${review.food_rating ? `
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Food:</span>
                        <div class="star-rating text-xs">
                            ${window.FoodieHub ? window.FoodieHub.generateStars(review.food_rating) : '★'.repeat(Math.floor(review.food_rating))}
                        </div>
                    </div>
                    ` : ''}
                    ${review.service_rating ? `
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Service:</span>
                        <div class="star-rating text-xs">
                            ${window.FoodieHub ? window.FoodieHub.generateStars(review.service_rating) : '★'.repeat(Math.floor(review.service_rating))}
                        </div>
                    </div>
                    ` : ''}
                    ${review.ambiance_rating ? `
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Ambiance:</span>
                        <div class="star-rating text-xs">
                            ${window.FoodieHub ? window.FoodieHub.generateStars(review.ambiance_rating) : '★'.repeat(Math.floor(review.ambiance_rating))}
                        </div>
                    </div>
                    ` : ''}
                    ${review.value_rating ? `
                    <div class="flex items-center">
                        <span class="text-gray-600 mr-2">Value:</span>
                        <div class="star-rating text-xs">
                            ${window.FoodieHub ? window.FoodieHub.generateStars(review.value_rating) : '★'.repeat(Math.floor(review.value_rating))}
                        </div>
                    </div>
                    ` : ''}
                </div>
            ` : ''}
        </div>
    `).join('');
    
    updateLoadMoreUserReviewsButton();
}

// Display favorite restaurants
function displayFavoriteRestaurants() {
    if (!favoriteRestaurantsContainer) return;
    
    if (favoriteRestaurants.length === 0) {
        favoriteRestaurantsContainer.innerHTML = `
            <div class="col-span-2 text-center py-12">
                <i class="fas fa-heart text-4xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No favorites yet</h3>
                <p class="text-gray-600 mb-6">Start adding restaurants to your favorites!</p>
                <a href="restaurants.html" class="btn-primary">Browse Restaurants</a>
            </div>
        `;
        return;
    }
    
    favoriteRestaurantsContainer.innerHTML = favoriteRestaurants.map(restaurant => {
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
                    <button class="absolute top-4 right-4 bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition" onclick="removeFavorite(${restaurant.id})">
                        <i class="fas fa-heart"></i>
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

// Switch profile section
function switchSection(section) {
    // Update navigation buttons
    if (profileNavBtns && profileNavBtns.length > 0) {
        profileNavBtns.forEach(btn => {
            btn.classList.remove('active', 'bg-orange-50', 'text-orange-600');
            btn.classList.add('text-gray-700');
        });
    }
    
    const activeBtn = document.querySelector(`[data-section="${section}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-orange-50', 'text-orange-600');
        activeBtn.classList.remove('text-gray-700');
    }
    
    // Update sections
    if (profileSections && profileSections.length > 0) {
        profileSections.forEach(section => {
            section.classList.add('hidden');
        });
    }
    
    const activeSection = document.getElementById(`${section}-section`);
    if (activeSection) {
        activeSection.classList.remove('hidden');
    }
}

// Filter user reviews
function filterUserReviews() {
    const filter = reviewFilter.value;
    let filteredReviews = [...userReviews];
    
    switch (filter) {
        case 'recent':
            filteredReviews.sort((a, b) => new Date(b.date) - new Date(a.date));
            break;
        case 'highest':
            filteredReviews.sort((a, b) => b.rating - a.rating);
            break;
        case 'lowest':
            filteredReviews.sort((a, b) => a.rating - b.rating);
            break;
    }
    
    userReviews = filteredReviews;
    currentReviewPage = 1;
    displayUserReviews();
}

// Filter favorites
function filterFavorites() {
    const filter = favoriteFilter.value;
    let filteredFavorites = [...favoriteRestaurants];
    
    switch (filter) {
        case 'recent':
            filteredFavorites.sort((a, b) => new Date(b.dateAdded) - new Date(a.dateAdded));
            break;
        case 'rating':
            filteredFavorites.sort((a, b) => b.rating - a.rating);
            break;
    }
    
    favoriteRestaurants = filteredFavorites;
    displayFavoriteRestaurants();
}

// Load more user reviews
function loadMoreUserReviews() {
    currentReviewPage++;
    displayUserReviews();
}

// Update load more user reviews button
function updateLoadMoreUserReviewsButton() {
    const totalPages = Math.ceil(userReviews.length / reviewsPerPage);
    
    if (currentReviewPage >= totalPages) {
        loadMoreUserReviewsElement.style.display = 'none';
    } else {
        loadMoreUserReviewsElement.style.display = 'block';
    }
}

// Initialize profile form enhancements
function initializeProfileForm() {
    // Bio character counter
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    
    if (bioTextarea && bioCounter) {
        bioTextarea.addEventListener('input', function() {
            const length = this.value.length;
            bioCounter.textContent = `${length}/500`;
            
            if (length > 500) {
                bioCounter.classList.add('text-red-500');
                bioCounter.classList.remove('text-gray-400');
            } else {
                bioCounter.classList.remove('text-red-500');
                bioCounter.classList.add('text-gray-400');
            }
        });
        
        // Initialize counter
        bioCounter.textContent = `${bioTextarea.value.length}/500`;
    }
    
    // Avatar upload functionality
    const avatarUpload = document.getElementById('avatar-upload');
    const profileAvatar = document.getElementById('profile-avatar');
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    
    if (avatarUpload && profileAvatar) {
        // Initialize avatars based on user data or localStorage
        initializeUserAvatars();
        
        avatarUpload.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type and size
                if (!file.type.startsWith('image/')) {
                    showNotification('Please select a valid image file', 'error');
                    return;
                }
                
                try {
                    // Compress image if it's too large
                    let fileToUpload = file;
                    if (file.size > 2 * 1024 * 1024) { // 2MB limit (PHP constraint)
                        console.log('Image is too large, compressing...');
                        fileToUpload = await compressImage(file, 2 * 1024 * 1024); // Compress to under 2MB
                        console.log('Compressed image size:', fileToUpload.size);
                    }
                    // Show loading state
                    const uploadBtn = document.querySelector('button[onclick*="avatar-upload"]');
                    if (uploadBtn) {
                        uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
                        uploadBtn.disabled = true;
                    }
                    
                    // First, preview the image locally
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        updateBothAvatars(e.target.result);
                    };
                    reader.readAsDataURL(file);
                    
                    // Upload to server
                    const currentUser = window.api.getCurrentUser();
                    if (currentUser) {
                        const response = await window.api.uploadImages('user', currentUser.id, [fileToUpload], 0);
                        if (response.success) {
                            // Update current user data with new avatar URL
                            currentUser.avatar = response.images[0].url;
                            window.api.setCurrentUser(currentUser);
                            
                            // Make sure avatar URL is absolute
                            const avatarUrl = response.images[0].url.startsWith('/storage/') 
                                ? `http://127.0.0.1:8000${response.images[0].url}`
                                : response.images[0].url;
                            
                            // Update both avatars with server URL
                            updateBothAvatars(avatarUrl);
                            
                            // Update all avatars across the app if available
                            if (typeof updateAllUserAvatars === 'function') {
                                updateAllUserAvatars(currentUser);
                            }
                            
                            // Save to localStorage as backup
                            localStorage.setItem('userAvatar', avatarUrl);
                            
                            showNotification('Profile photo updated successfully!', 'success');
                        } else {
                            throw new Error(response.message || 'Upload failed');
                        }
                    }
                } catch (error) {
                    console.error('Avatar upload error:', error);
                    showNotification('Failed to upload profile photo. Please try again.', 'error');
                    
                    // Reset to previous avatar
                    initializeUserAvatars();
                } finally {
                    // Reset upload button
                    const uploadBtn = document.querySelector('button[onclick*="avatar-upload"]');
                    if (uploadBtn) {
                        uploadBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Change Photo';
                        uploadBtn.disabled = false;
                    }
                }
            }
        });
        
        // Remove avatar button
        const removeAvatarBtn = document.getElementById('remove-avatar-btn');
        if (removeAvatarBtn) {
            removeAvatarBtn.addEventListener('click', async function() {
                try {
                    // Update current user data to remove avatar
                    const currentUser = window.api.getCurrentUser();
                    if (currentUser) {
                        currentUser.avatar = null;
                        window.api.setCurrentUser(currentUser);
                        
                        // Update all avatars to use generated avatar instead
                        if (typeof updateAllUserAvatars === 'function') {
                            updateAllUserAvatars(currentUser);
                        } else {
                            // Fallback to local update
                            if (profileAvatar) setDefaultAvatar(profileAvatar);
                            if (sidebarAvatar) setDefaultAvatar(sidebarAvatar);
                        }
                        
                        // Clear saved avatar
                        localStorage.removeItem('userAvatar');
                        // Clear file input
                        avatarUpload.value = '';
                        
                        showNotification('Profile photo removed successfully!', 'success');
                    }
                } catch (error) {
                    console.error('Error removing avatar:', error);
                    showNotification('Failed to remove profile photo', 'error');
                }
            });
        }
    }
    
    // Form validation
    const form = document.getElementById('profile-form');
    if (form) {
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        inputs.forEach(input => {
            input.addEventListener('blur', validateField);
            input.addEventListener('input', clearFieldError);
        });
    }
}

// Validate individual form field
function validateField(event) {
    const field = event.target;
    const value = field.value.trim();
    
    // Remove existing error styling
    field.classList.remove('border-red-500', 'ring-red-500');
    field.classList.add('border-gray-300');
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    let isValid = true;
    let errorMessage = '';
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'This field is required';
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Bio length validation
    if (field.id === 'bio' && value.length > 500) {
        isValid = false;
        errorMessage = 'Bio must be 500 characters or less';
    }
    
    if (!isValid) {
        field.classList.add('border-red-500', 'ring-red-500');
        field.classList.remove('border-gray-300');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'field-error text-red-500 text-xs mt-1';
        errorDiv.textContent = errorMessage;
        field.parentNode.appendChild(errorDiv);
    }
    
    return isValid;
}

// Clear field error on input
function clearFieldError(event) {
    const field = event.target;
    field.classList.remove('border-red-500', 'ring-red-500');
    field.classList.add('border-gray-300');
    
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Initialize user avatars based on current user data
function initializeUserAvatars() {
    console.log('Initializing user avatars...');
    
    // Get current user data
    const currentUser = window.api ? window.api.getCurrentUser() : null;
    
    console.log('Avatar data:', {
        currentUser: currentUser,
        userAvatar: currentUser?.avatar
    });
    
    // Use the centralized avatar update function if available, otherwise use local logic
    if (currentUser && typeof updateAllUserAvatars === 'function') {
        console.log('Using centralized avatar update function');
        updateAllUserAvatars(currentUser);
        return;
    }
    
    // Fallback to local avatar management
    const profileAvatar = document.getElementById('profile-avatar');
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    const savedAvatar = localStorage.getItem('userAvatar');
    
    // Priority: API user avatar > localStorage avatar > generated avatar > default avatar
    let avatarToUse = null;
    
    if (currentUser && currentUser.avatar && 
        currentUser.avatar !== 'default' && 
        currentUser.avatar !== null && 
        currentUser.avatar !== '') {
        // Use API avatar if it exists and is valid
        // If it's a relative path, make it absolute to the Laravel backend
        avatarToUse = currentUser.avatar.startsWith('/storage/') 
            ? `http://127.0.0.1:8000${currentUser.avatar}` 
            : currentUser.avatar;
        console.log('Using API user avatar:', avatarToUse);
    } else if (savedAvatar && 
               !savedAvatar.includes('data:image/svg') && 
               savedAvatar.startsWith('http')) {
        // Use saved avatar if it's a valid URL
        avatarToUse = savedAvatar;
        console.log('Using saved avatar from localStorage');
    } else if (currentUser && currentUser.name) {
        // Generate a nice avatar using the user's name
        const name = encodeURIComponent(currentUser.name);
        avatarToUse = `https://ui-avatars.com/api/?name=${name}&background=f97316&color=ffffff&size=200&bold=true`;
        console.log('Using generated avatar for user:', currentUser.name);
    } else {
        console.log('Using default avatar');
    }
    
    if (avatarToUse) {
        // Use custom avatar
        if (profileAvatar) {
            profileAvatar.src = avatarToUse;
            console.log('Set profile avatar src to:', avatarToUse);
            
            // Force visibility
            profileAvatar.style.display = 'block';
            profileAvatar.style.visibility = 'visible';
            profileAvatar.style.width = '80px';
            profileAvatar.style.height = '80px';
            
            // Add error handling for profile avatar with timeout
            let imageLoaded = false;
            
            profileAvatar.onload = function() {
                imageLoaded = true;
                console.log('Profile avatar loaded successfully');
            };
            
            profileAvatar.onerror = function() {
                console.error('Profile avatar failed to load, using default');
                setDefaultAvatar(profileAvatar);
            };
            
            // Set a timeout to use default if image doesn't load within 3 seconds
            setTimeout(() => {
                if (!imageLoaded) {
                    console.log('Profile avatar timeout, using default');
                    setDefaultAvatar(profileAvatar);
                }
            }, 3000);
        }
        if (sidebarAvatar) {
            sidebarAvatar.src = avatarToUse;
            console.log('Set sidebar avatar src to:', avatarToUse);
            
            // Force visibility
            sidebarAvatar.style.display = 'block';
            sidebarAvatar.style.visibility = 'visible';
            sidebarAvatar.style.width = '80px';
            sidebarAvatar.style.height = '80px';
            
            // Add error handling for sidebar avatar with timeout
            let imageLoaded = false;
            
            sidebarAvatar.onload = function() {
                imageLoaded = true;
                console.log('Sidebar avatar loaded successfully');
            };
            
            sidebarAvatar.onerror = function() {
                console.error('Sidebar avatar failed to load, using default');
                setDefaultAvatar(sidebarAvatar);
            };
            
            // Set a timeout to use default if image doesn't load within 3 seconds
            setTimeout(() => {
                if (!imageLoaded) {
                    console.log('Sidebar avatar timeout, using default');
                    setDefaultAvatar(sidebarAvatar);
                }
            }, 3000);
        }
    } else {
        // Use default avatar
        if (profileAvatar) {
            setDefaultAvatar(profileAvatar);
            console.log('Set default profile avatar');
        }
        if (sidebarAvatar) {
            setDefaultAvatar(sidebarAvatar);
            console.log('Set default sidebar avatar');
        }
    }
}

// Update both profile and sidebar avatars
function updateBothAvatars(avatarSrc) {
    const profileAvatar = document.getElementById('profile-avatar');
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    
    console.log('Updating both avatars with src:', avatarSrc);
    
    if (profileAvatar) {
        profileAvatar.src = avatarSrc;
        profileAvatar.style.display = 'block';
        profileAvatar.style.visibility = 'visible';
        
        profileAvatar.onerror = function() {
            console.error('Profile avatar failed to load, using fallback');
            setDefaultAvatar(profileAvatar);
        };
    }
    
    if (sidebarAvatar) {
        sidebarAvatar.src = avatarSrc;
        sidebarAvatar.style.display = 'block';
        sidebarAvatar.style.visibility = 'visible';
        
        sidebarAvatar.onerror = function() {
            console.error('Sidebar avatar failed to load, using fallback');
            setDefaultAvatar(sidebarAvatar);
        };
    }
    
    // Also update any navigation avatars
    const navAvatars = document.querySelectorAll('#user-avatar, #mobile-user-avatar, #user-menu-avatar');
    navAvatars.forEach(avatar => {
        if (avatar) {
            avatar.src = avatarSrc;
            avatar.onerror = function() {
                console.error('Navigation avatar failed to load, using fallback');
                setDefaultAvatar(avatar);
            };
        }
    });
}

// Set default avatar (user icon)
function setDefaultAvatar(avatarElement) {
    if (avatarElement) {
        console.log('Setting default avatar for element:', avatarElement);
        
        // Use a very simple default avatar - just a colored circle with user icon
        const defaultAvatarDataUrl = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iNDAiIGN5PSI0MCIgcj0iNDAiIGZpbGw9IiNmOTczMTYiLz4KPHN2ZyB4PSIyMCIgeT0iMjAiIHdpZHRoPSI0MCIgaGVpZ2h0PSI0MCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIj4KPHBhdGggZD0iTTEyIDEyQzE0LjIwOTEgMTIgMTYgMTAuMjA5MSAxNiA4QzE2IDUuNzkwODYgMTQuMjA5MSA0IDEyIDRDOS43OTA4NiA0IDggNS43OTA4NiA4IDhDOCAxMC4yMDkxIDkuNzkwODYgMTIgMTIgMTJaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMTIgMTRDOC42ODYyOSAxNCA2IDE2LjY4NjMgNiAyMEgxOEMxOCAxNi42ODYzIDE1LjMxMzcgMTQgMTIgMTRaIiBmaWxsPSJ3aGl0ZSIvPgo8L3N2Zz4KPC9zdmc+';
        
        console.log('Using base64 default avatar');
        
        // Set as image source
        avatarElement.src = defaultAvatarDataUrl;
        
        // Force visibility
        avatarElement.style.display = 'block';
        avatarElement.style.visibility = 'visible';
        avatarElement.style.width = '80px';
        avatarElement.style.height = '80px';
        
        // Add error handling
        avatarElement.onload = function() {
            console.log('Default avatar loaded successfully');
        };
        
        avatarElement.onerror = function() {
            console.error('Failed to load default avatar');
            // Fallback to a simple colored div
            avatarElement.style.display = 'none';
            const fallbackDiv = document.createElement('div');
            fallbackDiv.className = 'w-20 h-20 bg-orange-500 rounded-full flex items-center justify-center text-white text-2xl font-bold';
            fallbackDiv.innerHTML = '👤';
            avatarElement.parentNode.insertBefore(fallbackDiv, avatarElement);
        };
    } else {
        console.error('Avatar element not found');
    }
}

// Handle profile update
function handleProfileUpdate(event) {
    event.preventDefault();
    
    const form = event.target;
    const formData = new FormData(form);
    
    // Validate all fields
    const inputs = form.querySelectorAll('input[required], textarea[required]');
    let isFormValid = true;
    
    inputs.forEach(input => {
        if (!validateField({ target: input })) {
            isFormValid = false;
        }
    });
    
    if (!isFormValid) {
        showNotification('Please fix the errors in the form', 'error');
        return;
    }
    
    const profileData = {
        firstName: formData.get('firstName'),
        lastName: formData.get('lastName'),
        email: formData.get('email'),
        bio: formData.get('bio'),
        location: formData.get('location')
    };
    
    // Show loading state
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
    submitBtn.disabled = true;
    
    // Simulate API call (replace with actual API call)
    setTimeout(() => {
        // Update display
        if (userName) {
            userName.textContent = `${profileData.firstName} ${profileData.lastName}`;
        }
        if (userEmail) {
            userEmail.textContent = profileData.email;
        }
        
        // Save to localStorage
        localStorage.setItem('userProfile', JSON.stringify(profileData));
        
        // Update avatars to ensure they're in sync
        initializeUserAvatars();
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        showNotification('Profile updated successfully!', 'success');
    }, 1000);
}

// Reset profile form
function resetProfileForm() {
    const form = document.getElementById('profile-form');
    if (!form) return;
    
    // Reset form fields to original values
    const firstName = form.querySelector('#first-name');
    const lastName = form.querySelector('#last-name');
    const email = form.querySelector('#email');
    const bio = form.querySelector('#bio');
    const location = form.querySelector('#location');
    
    if (firstName) firstName.value = 'John';
    if (lastName) lastName.value = 'Doe';
    if (email) email.value = 'user@example.com';
    if (bio) {
        bio.value = 'Food enthusiast and restaurant explorer. Love trying new cuisines and sharing my experiences with fellow food lovers!';
        // Update bio counter
        const bioCounter = document.getElementById('bio-counter');
        if (bioCounter) {
            bioCounter.textContent = `${bio.value.length}/500`;
        }
    }
    if (location) location.value = 'New York, NY';
    
    // Clear any validation errors
    const errorMessages = form.querySelectorAll('.field-error');
    errorMessages.forEach(error => error.remove());
    
    const inputs = form.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.classList.remove('border-red-500', 'ring-red-500');
        input.classList.add('border-gray-300');
    });
    
    // Update avatars to ensure they're in sync
    initializeUserAvatars();
    
    showNotification('Form reset to original values', 'info');
}

// Edit review
function editReview(reviewId) {
    const review = userReviews.find(r => r.id === reviewId);
    if (review) {
        // Store review data for editing
        localStorage.setItem('editingReview', JSON.stringify(review));
        window.location.href = 'write-review.html';
    }
}

// Delete review
function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review?')) {
        userReviews = userReviews.filter(r => r.id !== reviewId);
        localStorage.setItem('userReviews', JSON.stringify(userReviews));
        updateUserStats();
        displayUserReviews();
    }
}

// Remove favorite
async function removeFavorite(restaurantId) {
    if (confirm('Remove this restaurant from your favorites?')) {
        try {
            const currentUser = window.api.getCurrentUser();
            if (!currentUser) {
                showNotification('Please log in to manage favorites', 'error');
                return;
            }
            
            const response = await window.api.removeFavorite(currentUser.id, restaurantId);
            if (response.success) {
                // Remove from local array
                favoriteRestaurants = favoriteRestaurants.filter(r => r.id !== restaurantId);
                updateUserStats();
                displayFavoriteRestaurants();
                showNotification('Restaurant removed from favorites', 'success');
            } else {
                throw new Error(response.message || 'Failed to remove favorite');
            }
        } catch (error) {
            console.error('Error removing favorite:', error);
            showNotification('Failed to remove favorite', 'error');
        }
    }
}

// Toggle mobile menu
function toggleMobileMenu() {
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
}

// Authentication functions
function initializeAuth() {
    updateAuthUI();
}

function setupAuthEventListeners() {
    // Login modal
    const loginModal = document.getElementById('login-modal');
    const registerModal = document.getElementById('register-modal');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    
    if (loginForm) {
        loginForm.addEventListener('submit', handleLogin);
    }
    
    if (registerForm) {
        registerForm.addEventListener('submit', handleRegister);
    }
    
    // Close modal buttons
    const closeLoginBtn = document.getElementById('close-login-modal');
    const closeRegisterBtn = document.getElementById('close-register-modal');
    
    if (closeLoginBtn) {
        closeLoginBtn.addEventListener('click', hideLoginModal);
    }
    
    if (closeRegisterBtn) {
        closeRegisterBtn.addEventListener('click', hideRegisterModal);
    }
    
    // Logout button
    const logoutBtn = document.getElementById('logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
}

function updateAuthUI() {
    const loginBtn = document.getElementById('login-btn');
    const registerBtn = document.getElementById('register-btn');
    const userMenu = document.getElementById('user-menu');
    const userMenuMobile = document.getElementById('user-menu-mobile');
    
    if (window.api && window.api.isAuthenticated()) {
        const currentUser = window.api.getCurrentUser();
        
        // Hide login/register buttons
        if (loginBtn) loginBtn.style.display = 'none';
        if (registerBtn) registerBtn.style.display = 'none';
        
        // Show user menu
        if (userMenu) userMenu.style.display = 'block';
        if (userMenuMobile) userMenuMobile.style.display = 'block';
        
        // Update user info in menu
        const userNameElement = document.getElementById('user-menu-name');
        const userEmailElement = document.getElementById('user-menu-email');
        const userAvatarElement = document.getElementById('user-menu-avatar');
        
        if (userNameElement && currentUser) {
            userNameElement.textContent = currentUser.name;
        }
        
        if (userEmailElement && currentUser) {
            userEmailElement.textContent = currentUser.email;
        }
        
        if (userAvatarElement && currentUser) {
            if (currentUser.avatar) {
                userAvatarElement.src = currentUser.avatar;
            } else {
                // Use UI Avatars as fallback
                const initials = currentUser.name ? currentUser.name.split(' ').map(n => n[0]).join('').toUpperCase() : 'U';
                userAvatarElement.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(currentUser.name || 'User')}&background=orange&color=white&size=100`;
            }
        }
    } else {
        // Show login/register buttons
        if (loginBtn) loginBtn.style.display = 'block';
        if (registerBtn) registerBtn.style.display = 'block';
        
        // Hide user menu
        if (userMenu) userMenu.style.display = 'none';
        if (userMenuMobile) userMenuMobile.style.display = 'none';
    }
}

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
    
    const formData = new FormData(event.target);
    const email = formData.get('email');
    const password = formData.get('password');
    
    try {
        showLoading();
        
        const response = await window.api.login(email, password);
        
        if (response.success) {
            hideLoginModal();
            updateAuthUI();
            showNotification('Login successful!', 'success');
            
            // Reload user data after login
            await loadUserData();
        } else {
            throw new Error(response.message || 'Login failed');
        }
        
        hideLoading();
    } catch (error) {
        console.error('Login error:', error);
        hideLoading();
        showNotification(error.message || 'Login failed', 'error');
    }
}

async function handleRegister(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const name = formData.get('name');
    const email = formData.get('email');
    const password = formData.get('password');
    const confirmPassword = formData.get('confirm_password');
    
    if (password !== confirmPassword) {
        showNotification('Passwords do not match', 'error');
        return;
    }
    
    try {
        showLoading();
        
        const response = await window.api.register(name, email, password);
        
        if (response.success) {
            hideRegisterModal();
            updateAuthUI();
            showNotification('Registration successful!', 'success');
            
            // Reload user data after registration
            await loadUserData();
        } else {
            throw new Error(response.message || 'Registration failed');
        }
        
        hideLoading();
    } catch (error) {
        console.error('Registration error:', error);
        hideLoading();
        showNotification(error.message || 'Registration failed', 'error');
    }
}

async function handleLogout() {
    try {
        await window.api.logout();
        updateAuthUI();
        showNotification('Logged out successfully', 'success');
        
        // Redirect to homepage after logout
        window.location.href = 'index.html';
    } catch (error) {
        console.error('Logout error:', error);
        showNotification('Logout failed', 'error');
    }
}

function showLoading() {
    // Add loading state if needed
    console.log('Loading...');
}

function hideLoading() {
    // Remove loading state if needed
    console.log('Loading complete');
}

function showNotification(message, type = 'info') {
    // Simple notification system
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// Image compression function
async function compressImage(file, maxSizeBytes, quality = 0.8) {
    return new Promise((resolve) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Calculate new dimensions (keep aspect ratio, max 800px width/height)
            let { width, height } = img;
            const maxDimension = 800;
            
            if (width > height) {
                if (width > maxDimension) {
                    height = (height * maxDimension) / width;
                    width = maxDimension;
                }
            } else {
                if (height > maxDimension) {
                    width = (width * maxDimension) / height;
                    height = maxDimension;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // Draw and compress
            ctx.drawImage(img, 0, 0, width, height);
            
            // Try different quality levels to get under size limit
            let currentQuality = quality;
            const tryCompress = () => {
                canvas.toBlob((blob) => {
                    if (blob.size <= maxSizeBytes || currentQuality <= 0.1) {
                        // Convert blob to file
                        const compressedFile = new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        });
                        resolve(compressedFile);
                    } else {
                        currentQuality -= 0.1;
                        tryCompress();
                    }
                }, file.type, currentQuality);
            };
            
            tryCompress();
        };
        
        img.src = URL.createObjectURL(file);
    });
}

// Location autocomplete functionality
function initializeLocationAutocomplete() {
    const locationInput = document.getElementById('location');
    const currentLocationBtn = document.getElementById('current-location-btn');
    const locationDropdown = document.getElementById('location-dropdown');
    const locationSuggestions = document.getElementById('location-suggestions');
    
    if (!locationInput || !window.locationService) return;
    
    let activeIndex = -1;
    let currentSuggestions = [];
    
    // Handle current location button
    if (currentLocationBtn) {
        currentLocationBtn.addEventListener('click', async function() {
            const button = this;
            const originalText = button.innerHTML;
            
            try {
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Getting...';
                button.disabled = true;
                
                const location = await window.locationService.getCurrentLocation();
                locationInput.value = location.formatted;
                hideLocationDropdown();
                
                showNotification('Current location detected!', 'success');
            } catch (error) {
                console.error('Error getting current location:', error);
                showNotification(error.message, 'error');
            } finally {
                button.innerHTML = originalText;
                button.disabled = false;
            }
        });
    }
    
    // Handle input changes for autocomplete
    locationInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideLocationDropdown();
            return;
        }
        
        // Use debounced search
        window.locationService.searchWithDebounce(query, (results) => {
            currentSuggestions = results;
            displayLocationSuggestions(results);
        });
    });
    
    // Handle keyboard navigation
    locationInput.addEventListener('keydown', function(e) {
        if (!locationDropdown.classList.contains('hidden')) {
            switch (e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    activeIndex = Math.min(activeIndex + 1, currentSuggestions.length - 1);
                    updateActiveItem();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    activeIndex = Math.max(activeIndex - 1, -1);
                    updateActiveItem();
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (activeIndex >= 0 && currentSuggestions[activeIndex]) {
                        selectLocation(currentSuggestions[activeIndex]);
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
        if (!locationInput.contains(e.target) && !locationDropdown.contains(e.target)) {
            hideLocationDropdown();
        }
    });
    
    // Display location suggestions
    function displayLocationSuggestions(results) {
        locationSuggestions.innerHTML = '';
        activeIndex = -1;
        
        if (results.length === 0) {
            locationSuggestions.innerHTML = '<div class="px-4 py-2 text-gray-500 text-sm">No locations found</div>';
        } else {
            results.forEach((location, index) => {
                const item = document.createElement('div');
                item.className = 'px-4 py-2 cursor-pointer hover:bg-gray-50 flex items-center';
                item.innerHTML = `
                    <i class="fas fa-map-marker-alt text-orange-500 mr-3"></i>
                    <div>
                        <div class="font-medium text-gray-800">${location.formatted}</div>
                        <div class="text-xs text-gray-500">${location.display_name}</div>
                    </div>
                `;
                
                item.addEventListener('click', () => selectLocation(location));
                locationSuggestions.appendChild(item);
            });
        }
        
        locationDropdown.classList.remove('hidden');
    }
    
    // Update active item styling
    function updateActiveItem() {
        const items = locationSuggestions.querySelectorAll('div.cursor-pointer');
        items.forEach((item, index) => {
            if (index === activeIndex) {
                item.classList.add('bg-orange-50', 'border-l-4', 'border-orange-500');
            } else {
                item.classList.remove('bg-orange-50', 'border-l-4', 'border-orange-500');
            }
        });
    }
    
    // Select a location
    function selectLocation(location) {
        locationInput.value = location.formatted;
        hideLocationDropdown();
        
        // Store additional location data as data attributes
        locationInput.dataset.latitude = location.latitude;
        locationInput.dataset.longitude = location.longitude;
        locationInput.dataset.city = location.city || '';
        locationInput.dataset.state = location.state || '';
        locationInput.dataset.country = location.country || '';
    }
    
    // Hide location dropdown
    function hideLocationDropdown() {
        locationDropdown.classList.add('hidden');
        activeIndex = -1;
        currentSuggestions = [];
    }
}
