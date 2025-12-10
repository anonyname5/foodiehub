/**
 * API Integration for Restaurant Review Platform
 * Connects frontend to Laravel backend
 */

class RestaurantAPI {
    constructor() {
        this.baseURL = 'http://127.0.0.1:8000/api';
        this.authToken = localStorage.getItem('auth_token');
    }

    /**
     * Set authentication token
     */
    setAuthToken(token) {
        this.authToken = token;
        localStorage.setItem('auth_token', token);
    }

    /**
     * Clear authentication token
     */
    clearAuthToken() {
        this.authToken = null;
        localStorage.removeItem('auth_token');
    }

    /**
     * Make API request
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            credentials: 'include', // Include cookies for session-based auth
            ...options
        };

        // Add auth token if available
        if (this.authToken) {
            config.headers['Authorization'] = `Bearer ${this.authToken}`;
        }

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    /**
     * Authentication endpoints
     */
    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        // For session-based authentication, we don't need to store tokens
        // The session is automatically managed by the browser cookies
        if (response.success && response.user) {
            this.setCurrentUser(response.user);
        }

        return response;
    }

    async register(userData) {
        const response = await this.request('/auth/register', {
            method: 'POST',
            body: JSON.stringify(userData)
        });

        // For session-based authentication, we don't need to store tokens
        // The session is automatically managed by the browser cookies
        if (response.success && response.user) {
            this.setCurrentUser(response.user);
        }

        return response;
    }

    async logout() {
        try {
            await this.request('/auth/logout', { method: 'POST' });
        } finally {
            this.clearCurrentUser();
        }
    }

    async checkAuth() {
        return await this.request('/auth/check');
    }

    /**
     * Restaurant endpoints
     */
    async getRestaurants(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = queryString ? `/restaurants?${queryString}` : '/restaurants';
        return await this.request(endpoint);
    }

    async getRestaurant(id) {
        return await this.request(`/restaurants/${id}`);
    }

    async getRestaurantReviews(id, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = queryString ? `/restaurants/${id}/reviews?${queryString}` : `/restaurants/${id}/reviews`;
        return await this.request(endpoint);
    }

    /**
     * Get dashboard statistics
     */
    async getStatistics() {
        return await this.request('/statistics');
    }

    /**
     * Review endpoints
     */
    async getReviews(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = queryString ? `/reviews?${queryString}` : '/reviews';
        return await this.request(endpoint);
    }

    async getReview(id) {
        return await this.request(`/reviews/${id}`);
    }

    async createReview(reviewData) {
        return await this.request('/reviews', {
            method: 'POST',
            body: JSON.stringify(reviewData)
        });
    }

    async updateReview(id, reviewData) {
        return await this.request(`/reviews/${id}`, {
            method: 'PUT',
            body: JSON.stringify(reviewData)
        });
    }

    async deleteReview(id) {
        return await this.request(`/reviews/${id}`, {
            method: 'DELETE'
        });
    }

    async getUserReviews() {
        return await this.request('/reviews/my/reviews');
    }

    /**
     * User endpoints
     */
    async getUser(id) {
        return await this.request(`/users/${id}`);
    }

    async updateUser(id, userData) {
        return await this.request(`/users/${id}`, {
            method: 'PUT',
            body: JSON.stringify(userData)
        });
    }

    async getUserReviews(id) {
        return await this.request(`/users/${id}/reviews`);
    }

    async getUserFavorites(id) {
        return await this.request(`/users/${id}/favorites`);
    }

    async addFavorite(userId, restaurantId) {
        return await this.request(`/users/${userId}/favorites`, {
            method: 'POST',
            body: JSON.stringify({ restaurant_id: restaurantId })
        });
    }

    async removeFavorite(userId, restaurantId) {
        return await this.request(`/users/${userId}/favorites/${restaurantId}`, {
            method: 'DELETE'
        });
    }

    /**
     * Image upload endpoints
     */
    async uploadImages(type, id, images, primaryIndex = 0) {
        const formData = new FormData();
        formData.append('type', type);
        formData.append('id', id);
        formData.append('primary_index', primaryIndex);

        // Add images to form data
        images.forEach((image, index) => {
            formData.append(`images[${index}]`, image);
        });

        const response = await fetch(`${this.baseURL}/images/upload`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json'
            },
            credentials: 'include', // Include cookies for session-based auth
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `HTTP error! status: ${response.status}`);
        }

        return data;
    }

    async deleteImage(imageId) {
        return await this.request(`/images/${imageId}`, {
            method: 'DELETE'
        });
    }

    async setPrimaryImage(imageId) {
        return await this.request(`/images/${imageId}/primary`, {
            method: 'PUT'
        });
    }

    async reorderImages(images) {
        return await this.request('/images/reorder', {
            method: 'PUT',
            body: JSON.stringify({ images })
        });
    }

    /**
     * Utility methods
     */
    isAuthenticated() {
        return !!this.getCurrentUser();
    }

    getCurrentUser() {
        const userData = localStorage.getItem('current_user');
        return userData ? JSON.parse(userData) : null;
    }

    setCurrentUser(user) {
        localStorage.setItem('current_user', JSON.stringify(user));
    }

    clearCurrentUser() {
        localStorage.removeItem('current_user');
    }
}

// Create global API instance
window.api = new RestaurantAPI();

/**
 * Location Service using free APIs
 */
class LocationService {
    constructor() {
        this.nominatimBaseURL = 'https://nominatim.openstreetmap.org';
        this.cache = new Map();
        this.debounceTimer = null;
    }

    /**
     * Get current location using browser geolocation
     */
    async getCurrentLocation() {
        return new Promise((resolve, reject) => {
            if (!navigator.geolocation) {
                reject(new Error('Geolocation is not supported by this browser'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const { latitude, longitude } = position.coords;
                    try {
                        const location = await this.reverseGeocode(latitude, longitude);
                        resolve({
                            latitude,
                            longitude,
                            formatted: location.display_name,
                            city: location.address?.city || location.address?.town || location.address?.village,
                            state: location.address?.state,
                            country: location.address?.country,
                            full: location
                        });
                    } catch (error) {
                        resolve({
                            latitude,
                            longitude,
                            formatted: `${latitude.toFixed(4)}, ${longitude.toFixed(4)}`,
                            city: null,
                            state: null,
                            country: null
                        });
                    }
                },
                (error) => {
                    reject(new Error('Unable to retrieve your location: ' + error.message));
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 300000 // 5 minutes
                }
            );
        });
    }

    /**
     * Reverse geocode coordinates to address
     */
    async reverseGeocode(lat, lon) {
        const cacheKey = `reverse_${lat}_${lon}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        const url = `${this.nominatimBaseURL}/reverse?format=json&lat=${lat}&lon=${lon}&addressdetails=1&accept-language=en`;
        
        try {
            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'FoodieHub-WebApp/1.0'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Geocoding failed: ${response.status}`);
            }
            
            const data = await response.json();
            this.cache.set(cacheKey, data);
            return data;
        } catch (error) {
            console.error('Reverse geocoding error:', error);
            throw error;
        }
    }

    /**
     * Search for locations with autocomplete
     */
    async searchLocations(query, limit = 5) {
        if (!query || query.length < 2) {
            return [];
        }

        const cacheKey = `search_${query}_${limit}`;
        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        const url = `${this.nominatimBaseURL}/search?format=json&q=${encodeURIComponent(query)}&limit=${limit}&addressdetails=1&accept-language=en`;
        
        try {
            const response = await fetch(url, {
                headers: {
                    'User-Agent': 'FoodieHub-WebApp/1.0'
                }
            });
            
            if (!response.ok) {
                throw new Error(`Location search failed: ${response.status}`);
            }
            
            const data = await response.json();
            const formattedResults = data.map(item => ({
                display_name: item.display_name,
                formatted: this.formatAddress(item),
                latitude: parseFloat(item.lat),
                longitude: parseFloat(item.lon),
                city: item.address?.city || item.address?.town || item.address?.village,
                state: item.address?.state,
                country: item.address?.country,
                type: item.type,
                importance: item.importance
            }));

            this.cache.set(cacheKey, formattedResults);
            return formattedResults;
        } catch (error) {
            console.error('Location search error:', error);
            return [];
        }
    }

    /**
     * Format address for display
     */
    formatAddress(item) {
        const parts = [];
        const addr = item.address;
        
        if (addr?.city || addr?.town || addr?.village) {
            parts.push(addr.city || addr.town || addr.village);
        }
        
        if (addr?.state) {
            parts.push(addr.state);
        }
        
        if (addr?.country) {
            parts.push(addr.country);
        }
        
        return parts.join(', ') || item.display_name;
    }

    /**
     * Debounced search for autocomplete
     */
    searchWithDebounce(query, callback, delay = 300) {
        clearTimeout(this.debounceTimer);
        this.debounceTimer = setTimeout(async () => {
            const results = await this.searchLocations(query);
            callback(results);
        }, delay);
    }
}

// Create global location service instance
window.locationService = new LocationService();

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RestaurantAPI;
}
