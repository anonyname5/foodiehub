class AdminAPI {
    constructor() {
        this.baseURL = 'http://127.0.0.1:8000/api';
    }

    isAdminUser(user) {
        if (!user) return false;
        const role = (user.role || '').toLowerCase();
        return user.is_admin === true || role === 'admin' || role === 'super_admin';
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            credentials: 'include',
            ...options
        };

        const response = await fetch(url, config);
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            const message = data?.message || `Request failed (${response.status})`;
            throw new Error(message);
        }
        return data;
    }

    // Auth
    async checkAuth() {
        return this.request('/auth/check');
    }

    async logout() {
        return this.request('/auth/logout', { method: 'POST' });
    }

    // Dashboard
    async getDashboardStats() {
        return this.request('/admin/dashboard/stats');
    }

    async getDashboardActivity() {
        return this.request('/admin/dashboard/activity');
    }

    // Users
    async getUsers(params = {}) {
        const qs = new URLSearchParams(params).toString();
        const endpoint = qs ? `/admin/users?${qs}` : '/admin/users';
        return this.request(endpoint);
    }

    // Reviews
    async getPendingReviews(params = {}) {
        const qs = new URLSearchParams({ status: 'pending', ...params }).toString();
        return this.request(`/reviews?${qs}`);
    }
}

window.adminApi = new AdminAPI();

