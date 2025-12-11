/**
 * Admin API Integration
 * Handles all admin API calls to Laravel backend
 */

class AdminAPI {
    constructor() {
        this.baseURL = 'http://127.0.0.1:8000/api';
    }

    /**
     * Make API request with session-based authentication
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

        try {
            const response = await fetch(url, config);
            const data = await response.json();

            // Handle 401 Unauthorized (not logged in or not admin)
            if (response.status === 401) {
                this.handleUnauthorized();
                throw new Error('Unauthorized access');
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            return data;
        } catch (error) {
            console.error('Admin API request failed:', error);
            throw error;
        }
    }

    /**
     * Handle unauthorized access
     */
    handleUnauthorized() {
        // Redirect to login if not authenticated
        if (window.location.pathname !== '/index.html' && 
            !window.location.pathname.includes('index.html')) {
            window.location.href = 'index.html';
        }
    }

    /**
     * Admin Authentication
     */
    async login(email, password) {
        const response = await this.request('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });

        if (response.success && response.user && response.user.is_admin) {
            return response;
        } else {
            throw new Error('Invalid admin credentials');
        }
    }

    async logout() {
        try {
            await this.request('/auth/logout', { method: 'POST' });
        } catch (error) {
            console.error('Logout error:', error);
        }
    }

    async checkAuth() {
        try {
            const response = await this.request('/auth/check');
            if (response.success && response.user && response.user.is_admin) {
                return response.user;
            }
            return null;
        } catch (error) {
            return null;
        }
    }

    /**
     * Dashboard
     */
    async getDashboardStats() {
        return await this.request('/admin/dashboard/stats');
    }

    async getRecentActivity() {
        return await this.request('/admin/dashboard/activity');
    }

    /**
     * User Management
     */
    async getUsers(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = `/admin/users${queryString ? '?' + queryString : ''}`;
        return await this.request(endpoint);
    }

    async getUser(id) {
        return await this.request(`/admin/users/${id}`);
    }

    async updateUser(id, data) {
        return await this.request(`/admin/users/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async banUser(id) {
        return await this.request(`/admin/users/${id}/ban`, {
            method: 'POST'
        });
    }

    async unbanUser(id) {
        return await this.request(`/admin/users/${id}/unban`, {
            method: 'POST'
        });
    }

    async deleteUser(id) {
        return await this.request(`/admin/users/${id}`, {
            method: 'DELETE'
        });
    }

    /**
     * Review Management
     */
    async getReviews(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = `/reviews${queryString ? '?' + queryString : ''}`;
        return await this.request(endpoint);
    }

    async getReview(id) {
        return await this.request(`/reviews/${id}`);
    }

    async approveReview(id) {
        return await this.request(`/admin/reviews/${id}/approve`, {
            method: 'POST'
        });
    }

    async rejectReview(id, reason = '') {
        return await this.request(`/admin/reviews/${id}/reject`, {
            method: 'POST',
            body: JSON.stringify({ reason })
        });
    }

    /**
     * Restaurant Management
     */
    async getRestaurants(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const endpoint = `/restaurants${queryString ? '?' + queryString : ''}`;
        return await this.request(endpoint);
    }

    async getRestaurant(id) {
        return await this.request(`/restaurants/${id}`);
    }

    async updateRestaurant(id, data) {
        return await this.request(`/restaurants/${id}`, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    async deleteRestaurant(id) {
        return await this.request(`/restaurants/${id}`, {
            method: 'DELETE'
        });
    }

    /**
     * Settings
     */
    async getSettings() {
        return await this.request('/admin/settings');
    }

    async updateSettings(data) {
        return await this.request('/admin/settings', {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }
}

// Create global instance
const adminAPI = new AdminAPI();

