// Review form functionality

let allRestaurants = [];
let selectedRestaurant = null;
let currentRatings = {
    overall: 0,
    food: 0,
    service: 0,
    ambiance: 0,
    value: 0
};

// DOM Elements
let mobileMenuBtn, mobileMenu;
let restaurantSelect, restaurantInfo, restaurantDetails;
let overallRating, foodRating, serviceRating, ambianceRating, valueRating;
let reviewTitle, reviewText, visitDate, photoUpload, uploadBtn, photoPreview;
let reviewForm, submitReviewBtn, saveDraftBtn;
let successModal, viewRestaurantBtn, writeAnotherBtn;
let recentReviews;

// Initialize the review form
document.addEventListener('DOMContentLoaded', function() {
    initializeElements();
    setupEventListeners();
    initializeAuth();
    loadRestaurants();
    loadRecentReviews();
    checkForPreSelectedRestaurant();
});

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

// Initialize DOM elements
function initializeElements() {
    // Mobile menu
    mobileMenuBtn = document.getElementById('mobile-menu-btn');
    mobileMenu = document.getElementById('mobile-menu');
    
    // Restaurant selection
    restaurantSelect = document.getElementById('restaurant-select');
    restaurantInfo = document.getElementById('restaurant-info');
    restaurantDetails = document.getElementById('restaurant-details');
    
    // Rating elements
    overallRating = document.getElementById('overall-rating');
    foodRating = document.getElementById('food-rating');
    serviceRating = document.getElementById('service-rating');
    ambianceRating = document.getElementById('ambiance-rating');
    valueRating = document.getElementById('value-rating');
    
    // Form elements
    reviewTitle = document.getElementById('review-title');
    reviewText = document.getElementById('review-text');
    visitDate = document.getElementById('visit-date');
    photoUpload = document.getElementById('photo-upload');
    uploadBtn = document.getElementById('upload-btn');
    photoPreview = document.getElementById('photo-preview');
    
    // Form and buttons
    reviewForm = document.getElementById('review-form');
    submitReviewBtn = document.getElementById('submit-review');
    saveDraftBtn = document.getElementById('save-draft');
    
    // Success modal
    successModal = document.getElementById('success-modal');
    viewRestaurantBtn = document.getElementById('view-restaurant');
    writeAnotherBtn = document.getElementById('write-another');
    
    // Recent reviews
    recentReviews = document.getElementById('recent-reviews');
}

// Setup event listeners
function setupEventListeners() {
    // Mobile menu toggle
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', toggleMobileMenu);
    }
    
    // Restaurant selection
    if (restaurantSelect) {
        restaurantSelect.addEventListener('change', handleRestaurantSelection);
    }
    
    // Rating interactions
    setupRatingListeners();
    
    // Photo upload
    if (uploadBtn) {
        uploadBtn.addEventListener('click', () => photoUpload.click());
    }
    if (photoUpload) {
        photoUpload.addEventListener('change', handlePhotoUpload);
    }
    
    // Form submission
    if (reviewForm) {
        reviewForm.addEventListener('submit', handleFormSubmission);
    }
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', saveDraft);
    }
    
    // Authentication event listeners
    setupAuthEventListeners();
    
    // Success modal
    if (viewRestaurantBtn) {
        viewRestaurantBtn.addEventListener('click', viewSelectedRestaurant);
    }
    if (writeAnotherBtn) {
        writeAnotherBtn.addEventListener('click', resetForm);
    }
    
    // Set default visit date to today
    if (visitDate) {
        visitDate.value = new Date().toISOString().split('T')[0];
    }
}

// Setup rating event listeners
function setupRatingListeners() {
    const ratingElements = [
        { element: overallRating, type: 'overall' },
        { element: foodRating, type: 'food' },
        { element: serviceRating, type: 'service' },
        { element: ambianceRating, type: 'ambiance' },
        { element: valueRating, type: 'value' }
    ];
    
    ratingElements.forEach(({ element, type }) => {
        if (element) {
            const stars = element.querySelectorAll('.star');
            stars.forEach((star, index) => {
                star.addEventListener('click', () => setRating(type, index + 1));
                star.addEventListener('mouseenter', () => highlightStars(stars, index + 1));
            });
            
            element.addEventListener('mouseleave', () => {
                highlightStars(stars, currentRatings[type]);
            });
        }
    });
}

// Set rating
function setRating(type, rating) {
    currentRatings[type] = rating;
    
    // Update hidden input
    const hiddenInput = document.getElementById(`${type}-rating-value`);
    if (hiddenInput) {
        hiddenInput.value = rating;
    }
    
    // Update star display
    const ratingElement = document.getElementById(`${type}-rating`);
    if (ratingElement) {
        const stars = ratingElement.querySelectorAll('.star');
        highlightStars(stars, rating);
    }
    
    // Auto-fill overall rating if other ratings are set
    if (type !== 'overall' && currentRatings.food > 0 && currentRatings.service > 0 && currentRatings.ambiance > 0 && currentRatings.value > 0) {
        const avgRating = Math.round((currentRatings.food + currentRatings.service + currentRatings.ambiance + currentRatings.value) / 4);
        setRating('overall', avgRating);
    }
}

// Highlight stars
function highlightStars(stars, rating) {
    stars.forEach((star, index) => {
        if (index < rating) {
            star.className = 'fas fa-star star active';
        } else {
            star.className = 'far fa-star star';
        }
    });
}

// Load restaurants
async function loadRestaurants() {
    try {
        // Try to load from API first
        if (window.api) {
            const response = await window.api.getRestaurants();
            if (response.success) {
                allRestaurants = response.data;
                populateRestaurantSelect();
                return;
            }
        }
        
        // Fallback to sample data or JSON
        allRestaurants = window.FoodieHub ? window.FoodieHub.sampleRestaurants : [];
        
        // If no data available, fetch from JSON
        if (allRestaurants.length === 0) {
            const response = await fetch('data/restaurants.json');
            const data = await response.json();
            allRestaurants = data.restaurants;
        }
        
        populateRestaurantSelect();
        
    } catch (error) {
        console.error('Error loading restaurants:', error);
        // Show error notification
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Failed to load restaurants', 'error');
        }
    }
}

// Populate restaurant select dropdown
function populateRestaurantSelect() {
    if (!restaurantSelect) return;
    
    restaurantSelect.innerHTML = '<option value="">Choose a restaurant...</option>';
    
    allRestaurants.forEach(restaurant => {
        const option = document.createElement('option');
        option.value = restaurant.id;
        option.textContent = `${restaurant.name} - ${restaurant.cuisine} (${restaurant.location})`;
        restaurantSelect.appendChild(option);
    });
}

// Handle restaurant selection
function handleRestaurantSelection() {
    const restaurantId = restaurantSelect.value;
    
    if (restaurantId) {
        selectedRestaurant = allRestaurants.find(r => r.id === parseInt(restaurantId));
        displayRestaurantInfo();
    } else {
        hideRestaurantInfo();
    }
}

// Display restaurant info
function displayRestaurantInfo() {
    if (!selectedRestaurant || !restaurantInfo || !restaurantDetails) return;
    
    restaurantDetails.innerHTML = `
        <div class="flex items-center space-x-3 mb-4">
            <img src="${selectedRestaurant.image}" alt="${selectedRestaurant.name}" class="w-16 h-16 object-cover rounded-lg">
            <div>
                <h4 class="font-semibold text-gray-800">${selectedRestaurant.name}</h4>
                <p class="text-sm text-gray-600">${selectedRestaurant.cuisine} • ${selectedRestaurant.location}</p>
                <div class="flex items-center mt-1">
                    <div class="star-rating text-sm">
                        ${window.FoodieHub.generateStars(selectedRestaurant.rating)}
                    </div>
                    <span class="text-sm text-gray-500 ml-1">${selectedRestaurant.rating}</span>
                </div>
            </div>
        </div>
        <p class="text-sm text-gray-700">${selectedRestaurant.description}</p>
    `;
    
    restaurantInfo.classList.remove('hidden');
}

// Hide restaurant info
function hideRestaurantInfo() {
    if (restaurantInfo) {
        restaurantInfo.classList.add('hidden');
    }
}

// Handle photo upload
function handlePhotoUpload(event) {
    const files = Array.from(event.target.files);
    
    if (files.length === 0) return;
    
    // Validate files
    const validFiles = files.filter(file => {
        if (file.size > 10 * 1024 * 1024) { // 10MB limit
            alert(`File ${file.name} is too large. Maximum size is 10MB.`);
            return false;
        }
        if (!file.type.startsWith('image/')) {
            alert(`File ${file.name} is not an image.`);
            return false;
        }
        return true;
    });
    
    if (validFiles.length === 0) return;
    
    // Display previews
    displayPhotoPreviews(validFiles);
}

// Display photo previews
function displayPhotoPreviews(files) {
    if (!photoPreview) return;
    
    photoPreview.innerHTML = '';
    
    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.createElement('div');
            preview.className = 'relative';
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview ${index + 1}" class="w-full h-24 object-cover rounded-lg">
                <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600" onclick="removePhoto(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            photoPreview.appendChild(preview);
        };
        reader.readAsDataURL(file);
    });
    
    photoPreview.classList.remove('hidden');
}

// Remove photo
function removePhoto(index) {
    // This would need to be implemented with proper file management
    // For now, just hide the preview
    const previews = photoPreview.querySelectorAll('div');
    if (previews[index]) {
        previews[index].remove();
    }
    
    if (photoPreview.children.length === 0) {
        photoPreview.classList.add('hidden');
    }
}

// Handle form submission
async function handleFormSubmission(event) {
    event.preventDefault();
    
    // Check if user is authenticated
    if (!window.api || !window.api.isAuthenticated()) {
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Please login to submit a review', 'error');
        } else {
            alert('Please login to submit a review');
        }
        return;
    }
    
    // Validate form
    if (!validateForm()) {
        return;
    }
    
    // Show loading state
    submitReviewBtn.disabled = true;
    submitReviewBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
    
    try {
        // Prepare review data
        const reviewData = {
            restaurant_id: parseInt(restaurantSelect.value),
            overall_rating: currentRatings.overall,
            food_rating: currentRatings.food,
            service_rating: currentRatings.service,
            ambiance_rating: currentRatings.ambiance,
            value_rating: currentRatings.value,
            title: reviewTitle.value.trim(),
            comment: reviewText.value.trim(),
            visit_date: visitDate.value,
            recommend: document.querySelector('input[name="recommend"]:checked')?.value === 'yes'
        };
        
        // Submit review via API
        const response = await window.api.createReview(reviewData);
        
        if (response.success) {
            // Upload images if any were selected
            const selectedFiles = Array.from(photoUpload.files);
            if (selectedFiles.length > 0) {
                try {
                    await window.api.uploadImages('review', response.data.id, selectedFiles, 0);
                    console.log('Images uploaded successfully');
                } catch (imageError) {
                    console.error('Failed to upload images:', imageError);
                    // Don't fail the entire review submission if image upload fails
                    if (window.restaurantApp) {
                        window.restaurantApp.showNotification('Review submitted but image upload failed', 'warning');
                    }
                }
            }
            
            // Show success modal
            showSuccessModal();
            
            // Reset form
            resetForm();
            
            // Show success notification
            if (window.restaurantApp) {
                window.restaurantApp.showNotification('Review submitted successfully!', 'success');
            }
        } else {
            throw new Error(response.message || 'Failed to submit review');
        }
        
    } catch (error) {
        console.error('Review submission error:', error);
        
        // Show error notification
        if (window.restaurantApp) {
            window.restaurantApp.showNotification('Failed to submit review. Please try again.', 'error');
        } else {
            alert('Failed to submit review. Please try again.');
        }
    } finally {
        // Reset button
        submitReviewBtn.disabled = false;
        submitReviewBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Submit Review';
    }
}

// Validate form
function validateForm() {
    const errors = [];
    
    // Check restaurant selection
    if (!restaurantSelect.value) {
        errors.push('Please select a restaurant');
    }
    
    // Check overall rating
    if (currentRatings.overall === 0) {
        errors.push('Please provide an overall rating');
    }
    
    // Check review text
    if (!reviewText.value.trim()) {
        errors.push('Please write a review');
    } else if (reviewText.value.trim().length < 50) {
        errors.push('Review must be at least 50 characters long');
    }
    
    if (errors.length > 0) {
        alert('Please fix the following errors:\n' + errors.join('\n'));
        return false;
    }
    
    return true;
}

// Save review to localStorage (simulation)
function saveReviewToLocalStorage() {
    const review = {
        id: Date.now(),
        restaurantId: parseInt(restaurantSelect.value),
        restaurantName: selectedRestaurant.name,
        userName: 'You', // In a real app, this would come from user authentication
        rating: currentRatings.overall,
        title: reviewTitle.value,
        comment: reviewText.value,
        date: new Date().toISOString().split('T')[0],
        ratings: {
            food: currentRatings.food,
            service: currentRatings.service,
            ambiance: currentRatings.ambiance,
            value: currentRatings.value
        },
        recommend: document.querySelector('input[name="recommend"]:checked')?.value,
        visitDate: visitDate.value,
        photos: [] // In a real app, photos would be uploaded to a server
    };
    
    // Get existing reviews from localStorage
    const existingReviews = JSON.parse(localStorage.getItem('userReviews') || '[]');
    existingReviews.push(review);
    localStorage.setItem('userReviews', JSON.stringify(existingReviews));
}

// Save draft
function saveDraft() {
    const draft = {
        restaurantId: restaurantSelect.value,
        ratings: currentRatings,
        title: reviewTitle.value,
        text: reviewText.value,
        visitDate: visitDate.value,
        recommend: document.querySelector('input[name="recommend"]:checked')?.value
    };
    
    localStorage.setItem('reviewDraft', JSON.stringify(draft));
    alert('Draft saved successfully!');
}

// Load draft
function loadDraft() {
    const draft = JSON.parse(localStorage.getItem('reviewDraft') || '{}');
    
    if (draft.restaurantId) {
        restaurantSelect.value = draft.restaurantId;
        handleRestaurantSelection();
    }
    
    if (draft.ratings) {
        Object.keys(draft.ratings).forEach(type => {
            if (draft.ratings[type] > 0) {
                setRating(type, draft.ratings[type]);
            }
        });
    }
    
    if (draft.title) reviewTitle.value = draft.title;
    if (draft.text) reviewText.value = draft.text;
    if (draft.visitDate) visitDate.value = draft.visitDate;
    if (draft.recommend) {
        const radio = document.querySelector(`input[name="recommend"][value="${draft.recommend}"]`);
        if (radio) radio.checked = true;
    }
}

// Show success modal
function showSuccessModal() {
    if (successModal) {
        successModal.classList.remove('hidden');
    }
}

// Hide success modal
function hideSuccessModal() {
    if (successModal) {
        successModal.classList.add('hidden');
    }
}

// View selected restaurant
function viewSelectedRestaurant() {
    if (selectedRestaurant) {
        window.location.href = `restaurant-detail.html?id=${selectedRestaurant.id}`;
    }
}

// Reset form
function resetForm() {
    reviewForm.reset();
    currentRatings = { overall: 0, food: 0, service: 0, ambiance: 0, value: 0 };
    
    // Reset all star ratings
    const ratingElements = [overallRating, foodRating, serviceRating, ambianceRating, valueRating];
    ratingElements.forEach(element => {
        if (element) {
            const stars = element.querySelectorAll('.star');
            stars.forEach(star => {
                star.className = 'far fa-star star';
            });
        }
    });
    
    // Reset hidden inputs
    document.querySelectorAll('input[type="hidden"]').forEach(input => {
        input.value = '';
    });
    
    hideRestaurantInfo();
    photoPreview.classList.add('hidden');
    hideSuccessModal();
    
    // Set default visit date
    visitDate.value = new Date().toISOString().split('T')[0];
}

// Load recent reviews
async function loadRecentReviews() {
    if (!recentReviews) return;
    
    try {
        // Try to load from API first
        if (window.api) {
            const response = await window.api.getReviews({ sort_by: 'newest', limit: 3 });
            if (response.success && response.data.length > 0) {
                recentReviews.innerHTML = response.data.map(review => `
                    <div class="border-b border-gray-200 pb-3 last:border-b-0">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-800 text-sm">${review.user.name}</h4>
                            <div class="star-rating text-xs">
                                ${generateStars(review.overall_rating)}
                            </div>
                        </div>
                        <p class="text-xs text-gray-600 mb-1">${review.restaurant.name}</p>
                        <p class="text-xs text-gray-700">${review.comment.substring(0, 100)}${review.comment.length > 100 ? '...' : ''}</p>
                    </div>
                `).join('');
                return;
            }
        }
        
        // Fallback to sample data
        const sampleReviews = [
            {
                userName: "Sarah M.",
                restaurantName: "Bella Vista",
                rating: 5,
                date: "2024-01-15",
                comment: "Amazing pasta and great service!"
            },
            {
                userName: "Mike T.",
                restaurantName: "Sakura Sushi",
                rating: 4,
                date: "2024-01-14",
                comment: "Fresh sushi and friendly staff."
            },
            {
                userName: "Jessica L.",
                restaurantName: "El Mariachi",
                rating: 5,
                date: "2024-01-13",
                comment: "Best tacos in town! Highly recommend."
            }
        ];
        
        recentReviews.innerHTML = sampleReviews.map(review => `
            <div class="border-b border-gray-200 pb-3 last:border-b-0">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-semibold text-gray-800 text-sm">${review.userName}</h4>
                    <div class="star-rating text-xs">
                        ${generateStars(review.rating)}
                    </div>
                </div>
                <p class="text-xs text-gray-600 mb-1">${review.restaurantName}</p>
                <p class="text-xs text-gray-700">${review.comment}</p>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Error loading recent reviews:', error);
    }
}

// Generate stars HTML
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    const emptyStars = 5 - fullStars - (hasHalfStar ? 1 : 0);
    
    let stars = '';
    
    // Full stars
    for (let i = 0; i < fullStars; i++) {
        stars += '<i class="fas fa-star text-yellow-400"></i>';
    }
    
    // Half star
    if (hasHalfStar) {
        stars += '<i class="fas fa-star-half-alt text-yellow-400"></i>';
    }
    
    // Empty stars
    for (let i = 0; i < emptyStars; i++) {
        stars += '<i class="far fa-star text-gray-300"></i>';
    }
    
    return stars;
}

// Check for pre-selected restaurant
function checkForPreSelectedRestaurant() {
    const restaurantId = localStorage.getItem('reviewRestaurantId');
    if (restaurantId) {
        localStorage.removeItem('reviewRestaurantId');
        restaurantSelect.value = restaurantId;
        handleRestaurantSelection();
    }
}

// Toggle mobile menu
function toggleMobileMenu() {
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
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
