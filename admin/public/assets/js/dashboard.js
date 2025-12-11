/**
 * Dashboard JavaScript
 * Handles dashboard statistics and activity
 */

class Dashboard {
    constructor() {
        this.api = adminAPI;
        this.init();
    }

    async init() {
        await this.loadStats();
        await this.loadRecentActivity();
        
        // Refresh stats every 30 seconds
        setInterval(() => {
            this.loadStats();
        }, 30000);
    }

    async loadStats() {
        try {
            const response = await this.api.getDashboardStats();
            if (response.success && response.data) {
                this.updateStats(response.data);
            }
        } catch (error) {
            console.error('Failed to load dashboard stats:', error);
            adminApp?.showNotification('Failed to load dashboard statistics', 'error');
        }
    }

    updateStats(data) {
        // Update main stats
        document.getElementById('stat-restaurants').textContent = data.restaurants || 0;
        document.getElementById('stat-users').textContent = data.users || 0;
        document.getElementById('stat-reviews').textContent = data.reviews || 0;
        document.getElementById('stat-pending').textContent = data.pending_reviews || 0;
        document.getElementById('stat-cities').textContent = data.cities || 0;
        document.getElementById('active-users').textContent = data.active_users || 0;

        // Update monthly stats
        if (data.this_month) {
            document.getElementById('month-users').textContent = data.this_month.new_users || 0;
            document.getElementById('month-restaurants').textContent = data.this_month.new_restaurants || 0;
            document.getElementById('month-reviews').textContent = data.this_month.new_reviews || 0;
        }
    }

    async loadRecentActivity() {
        try {
            const response = await this.api.getRecentActivity();
            if (response.success && response.data) {
                this.displayActivity(response.data);
            } else {
                this.displayEmptyActivity();
            }
        } catch (error) {
            console.error('Failed to load recent activity:', error);
            this.displayEmptyActivity();
        }
    }

    displayActivity(activities) {
        const container = document.getElementById('recent-activity');
        
        if (!activities || activities.length === 0) {
            this.displayEmptyActivity();
            return;
        }

        container.innerHTML = activities.map(activity => {
            const icon = this.getActivityIcon(activity.type);
            const color = this.getActivityColor(activity.type);
            const time = this.formatTime(activity.created_at);
            
            return `
                <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-shrink-0 w-10 h-10 ${color} rounded-full flex items-center justify-center">
                        <i class="${icon} text-white"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900">${activity.description || 'Activity'}</p>
                        <p class="text-xs text-gray-500 mt-1">${time}</p>
                    </div>
                </div>
            `;
        }).join('');
    }

    displayEmptyActivity() {
        const container = document.getElementById('recent-activity');
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <p class="text-gray-600">No recent activity</p>
            </div>
        `;
    }

    getActivityIcon(type) {
        const icons = {
            'user_registered': 'fas fa-user-plus',
            'review_created': 'fas fa-star',
            'review_approved': 'fas fa-check-circle',
            'review_rejected': 'fas fa-times-circle',
            'user_banned': 'fas fa-ban',
            'user_unbanned': 'fas fa-user-check',
            'default': 'fas fa-bell'
        };
        return icons[type] || icons.default;
    }

    getActivityColor(type) {
        const colors = {
            'user_registered': 'bg-blue-500',
            'review_created': 'bg-orange-500',
            'review_approved': 'bg-green-500',
            'review_rejected': 'bg-red-500',
            'user_banned': 'bg-red-500',
            'user_unbanned': 'bg-green-500',
            'default': 'bg-gray-500'
        };
        return colors[type] || colors.default;
    }

    formatTime(dateString) {
        if (!dateString) return 'Just now';
        
        const date = new Date(dateString);
        const now = new Date();
        const diff = now - date;
        
        const seconds = Math.floor(diff / 1000);
        const minutes = Math.floor(seconds / 60);
        const hours = Math.floor(minutes / 60);
        const days = Math.floor(hours / 24);
        
        if (days > 0) return `${days} day${days > 1 ? 's' : ''} ago`;
        if (hours > 0) return `${hours} hour${hours > 1 ? 's' : ''} ago`;
        if (minutes > 0) return `${minutes} minute${minutes > 1 ? 's' : ''} ago`;
        return 'Just now';
    }
}

// Initialize dashboard when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('stat-restaurants')) {
        new Dashboard();
    }
});

