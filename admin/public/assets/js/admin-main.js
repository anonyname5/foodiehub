/**
 * Admin Main JavaScript
 * Handles navigation, authentication, and common admin functionality
 */

class AdminApp {
    constructor() {
        this.currentUser = null;
        this.api = adminAPI;
        this.init();
    }

    async init() {
        // Check if we're on login page
        if (this.isLoginPage()) {
            this.initLogin();
            return;
        }

        // Check authentication
        await this.checkAuth();
        this.initNavigation();
        this.initCommonFeatures();
    }

    isLoginPage() {
        const path = window.location.pathname;
        return path.includes('index.html') || path === '/' || path.endsWith('/admin/public/');
    }

    async checkAuth() {
        try {
            this.currentUser = await this.api.checkAuth();
            if (!this.currentUser) {
                window.location.href = 'index.html';
                return;
            }
            this.updateUserDisplay();
        } catch (error) {
            console.error('Auth check failed:', error);
            window.location.href = 'index.html';
        }
    }

    initLogin() {
        const loginForm = document.getElementById('admin-login-form');
        if (loginForm) {
            loginForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleLogin();
            });
        }
    }

    async handleLogin() {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const errorDiv = document.getElementById('login-error');
        const submitBtn = document.getElementById('login-submit');

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Logging in...';
        errorDiv.classList.add('hidden');

        try {
            const response = await this.api.login(email, password);
            if (response.success) {
                // Redirect to dashboard
                window.location.href = 'pages/dashboard/dashboard.html';
            }
        } catch (error) {
            errorDiv.textContent = error.message || 'Invalid email or password';
            errorDiv.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-sign-in-alt mr-2"></i>Login';
        }
    }

    initNavigation() {
        // Highlight active menu item
        this.highlightActiveMenu();
        
        // Handle logout
        const logoutBtn = document.getElementById('admin-logout');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', async () => {
                await this.handleLogout();
            });
        }

        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        if (mobileMenuBtn && mobileMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        }
    }

    highlightActiveMenu() {
        const currentPath = window.location.pathname;
        const menuItems = document.querySelectorAll('.admin-nav-link');
        
        menuItems.forEach(item => {
            const href = item.getAttribute('href');
            if (currentPath.includes(href)) {
                item.classList.add('bg-blue-600', 'text-white');
                item.classList.remove('text-gray-700', 'hover:bg-gray-100');
            }
        });
    }

    async handleLogout() {
        try {
            await this.api.logout();
            window.location.href = 'index.html';
        } catch (error) {
            console.error('Logout error:', error);
            // Still redirect even if logout fails
            window.location.href = 'index.html';
        }
    }

    updateUserDisplay() {
        const userNameEl = document.getElementById('admin-user-name');
        const userEmailEl = document.getElementById('admin-user-email');
        
        if (userNameEl && this.currentUser) {
            userNameEl.textContent = this.currentUser.name;
        }
        if (userEmailEl && this.currentUser) {
            userEmailEl.textContent = this.currentUser.email;
        }
    }

    initCommonFeatures() {
        // Initialize tooltips, modals, etc.
        this.initModals();
        this.initNotifications();
    }

    initModals() {
        // Close modals on outside click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-overlay')) {
                e.target.closest('.modal').classList.add('hidden');
            }
        });

        // Close modals on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal').forEach(modal => {
                    modal.classList.add('hidden');
                });
            }
        });
    }

    initNotifications() {
        // Auto-hide notifications after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.notification').forEach(notification => {
                notification.classList.add('hidden');
            });
        }, 5000);
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            'bg-blue-500'
        } text-white`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    /**
     * Show confirmation dialog
     */
    async showConfirm(message) {
        return new Promise((resolve) => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center modal-overlay bg-black bg-opacity-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold mb-4">Confirm Action</h3>
                    <p class="text-gray-700 mb-6">${message}</p>
                    <div class="flex justify-end space-x-3">
                        <button class="cancel-btn px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
                            Cancel
                        </button>
                        <button class="confirm-btn px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                            Confirm
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            modal.querySelector('.cancel-btn').addEventListener('click', () => {
                modal.remove();
                resolve(false);
            });

            modal.querySelector('.confirm-btn').addEventListener('click', () => {
                modal.remove();
                resolve(true);
            });
        });
    }
}

// Initialize admin app when DOM is ready
let adminApp;
document.addEventListener('DOMContentLoaded', () => {
    adminApp = new AdminApp();
});

