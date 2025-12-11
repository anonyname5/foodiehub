/**
 * Restaurant Management JavaScript
 */
class RestaurantManagement {
    constructor() {
        this.api = adminAPI;
        this.init();
    }

    async init() {
        document.getElementById('search-input')?.addEventListener('input', (e) => {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.loadRestaurants(e.target.value), 500);
        });
        this.loadRestaurants();
    }

    async loadRestaurants(search = '') {
        try {
            const params = search ? { search } : {};
            const response = await this.api.getRestaurants(params);
            if (response.success) {
                this.displayRestaurants(response.data || []);
            }
        } catch (error) {
            console.error('Failed to load restaurants:', error);
            this.displayError();
        }
    }

    displayRestaurants(restaurants) {
        const tbody = document.getElementById('restaurants-table-body');
        if (!restaurants || restaurants.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">No restaurants found</td></tr>';
            return;
        }
        tbody.innerHTML = restaurants.map(r => `
            <tr>
                <td>
                    <div class="flex items-center space-x-3">
                        <img src="${r.main_image || r.images?.[0] || 'assets/images/placeholder.jpg'}" 
                             alt="${r.name}" class="w-12 h-12 rounded object-cover">
                        <div>
                            <p class="font-medium">${r.name}</p>
                            <p class="text-sm text-gray-500">${r.price_range || 'N/A'}</p>
                        </div>
                    </div>
                </td>
                <td>${r.cuisine}</td>
                <td>${r.location}</td>
                <td>
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span>${r.average_rating || 0}</span>
                    </div>
                </td>
                <td>${r.review_count || 0}</td>
                <td>${r.is_active ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>'}</td>
                <td>
                    <div class="action-buttons">
                        <button onclick="restaurantManagement.viewRestaurant(${r.id})" class="action-btn action-btn-view">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="restaurantManagement.deleteRestaurant(${r.id})" class="action-btn action-btn-delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    viewRestaurant(id) {
        window.location.href = `restaurant-detail.html?id=${id}`;
    }

    async deleteRestaurant(id) {
        if (await adminApp?.showConfirm('Delete this restaurant?')) {
            try {
                await this.api.deleteRestaurant(id);
                adminApp?.showNotification('Restaurant deleted', 'success');
                this.loadRestaurants();
            } catch (error) {
                adminApp?.showNotification('Failed to delete', 'error');
            }
        }
    }

    displayError() {
        document.getElementById('restaurants-table-body').innerHTML = 
            '<tr><td colspan="7" class="text-center py-8 text-red-600">Failed to load restaurants</td></tr>';
    }
}

let restaurantManagement;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('restaurants-table-body')) {
        restaurantManagement = new RestaurantManagement();
    }
});

