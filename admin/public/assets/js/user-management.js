/**
 * User Management JavaScript
 * Handles user listing, filtering, and management actions
 */

class UserManagement {
    constructor() {
        this.api = adminAPI;
        this.currentPage = 1;
        this.filters = {
            search: '',
            status: '',
            location: '',
            sort_by: 'newest'
        };
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.loadUsers();
    }

    setupEventListeners() {
        // Search input with debounce
        let searchTimeout;
        document.getElementById('search-input')?.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.filters.search = e.target.value;
                this.currentPage = 1;
                this.loadUsers();
            }, 500);
        });

        // Status filter
        document.getElementById('status-filter')?.addEventListener('change', (e) => {
            this.filters.status = e.target.value;
            this.currentPage = 1;
            this.loadUsers();
        });

        // Sort by
        document.getElementById('sort-by')?.addEventListener('change', (e) => {
            this.filters.sort_by = e.target.value;
            this.currentPage = 1;
            this.loadUsers();
        });
    }

    async loadUsers() {
        try {
            const params = {
                page: this.currentPage,
                ...this.filters
            };

            const response = await this.api.getUsers(params);
            
            if (response.success) {
                this.displayUsers(response.data);
                this.displayPagination(response.pagination);
            }
        } catch (error) {
            console.error('Failed to load users:', error);
            adminApp?.showNotification('Failed to load users', 'error');
            this.displayError();
        }
    }

    displayUsers(users) {
        const tbody = document.getElementById('users-table-body');
        
        if (!users || users.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">
                        <div class="empty-state">
                            <i class="fas fa-users empty-state-icon"></i>
                            <p>No users found</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = users.map(user => {
            const statusBadge = user.is_active 
                ? '<span class="badge badge-success">Active</span>'
                : '<span class="badge badge-danger">Inactive</span>';
            
            const avatar = user.avatar 
                ? `<img src="${user.avatar}" alt="${user.name}" class="avatar">`
                : `<div class="avatar bg-gray-300 flex items-center justify-center"><i class="fas fa-user text-gray-600"></i></div>`;
            
            const joinedDate = new Date(user.created_at).toLocaleDateString();
            
            return `
                <tr>
                    <td>
                        <div class="flex items-center space-x-3">
                            ${avatar}
                            <div>
                                <p class="font-medium text-gray-900">${this.escapeHtml(user.name)}</p>
                                <p class="text-sm text-gray-500">${user.reviews_count || 0} reviews</p>
                            </div>
                        </div>
                    </td>
                    <td class="text-gray-700">${this.escapeHtml(user.email)}</td>
                    <td class="text-gray-700">${user.location || 'N/A'}</td>
                    <td class="text-gray-700">${user.reviews_count || 0}</td>
                    <td>${statusBadge}</td>
                    <td class="text-gray-600 text-sm">${joinedDate}</td>
                    <td>
                        <div class="action-buttons">
                            <button 
                                onclick="userManagement.viewUser(${user.id})" 
                                class="action-btn action-btn-view"
                                title="View Details"
                            >
                                <i class="fas fa-eye"></i>
                            </button>
                            <button 
                                onclick="userManagement.editUser(${user.id})" 
                                class="action-btn action-btn-edit"
                                title="Edit"
                            >
                                <i class="fas fa-edit"></i>
                            </button>
                            ${user.is_active 
                                ? `<button onclick="userManagement.banUser(${user.id})" class="action-btn action-btn-delete" title="Ban User"><i class="fas fa-ban"></i></button>`
                                : `<button onclick="userManagement.unbanUser(${user.id})" class="action-btn action-btn-success" title="Unban User"><i class="fas fa-check"></i></button>`
                            }
                            <button 
                                onclick="userManagement.deleteUser(${user.id})" 
                                class="action-btn action-btn-delete"
                                title="Delete"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    displayPagination(pagination) {
        const container = document.getElementById('pagination');
        
        if (!pagination || pagination.total_pages <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '<div class="pagination">';
        
        // Previous button
        html += `
            <button 
                class="pagination-btn" 
                ${pagination.current_page === 1 ? 'disabled' : ''}
                onclick="userManagement.goToPage(${pagination.current_page - 1})"
            >
                <i class="fas fa-chevron-left"></i>
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= pagination.total_pages; i++) {
            if (i === 1 || i === pagination.total_pages || 
                (i >= pagination.current_page - 2 && i <= pagination.current_page + 2)) {
                html += `
                    <button 
                        class="pagination-btn ${i === pagination.current_page ? 'active' : ''}"
                        onclick="userManagement.goToPage(${i})"
                    >
                        ${i}
                    </button>
                `;
            } else if (i === pagination.current_page - 3 || i === pagination.current_page + 3) {
                html += '<span class="px-2">...</span>';
            }
        }

        // Next button
        html += `
            <button 
                class="pagination-btn" 
                ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}
                onclick="userManagement.goToPage(${pagination.current_page + 1})"
            >
                <i class="fas fa-chevron-right"></i>
            </button>
        `;

        html += '</div>';
        html += `<div class="text-sm text-gray-600 mt-2 text-center">
            Showing ${((pagination.current_page - 1) * pagination.per_page) + 1} to 
            ${Math.min(pagination.current_page * pagination.per_page, pagination.total_items)} of 
            ${pagination.total_items} users
        </div>`;

        container.innerHTML = html;
    }

    goToPage(page) {
        this.currentPage = page;
        this.loadUsers();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    async viewUser(id) {
        window.location.href = `user-detail.html?id=${id}`;
    }

    async editUser(id) {
        window.location.href = `user-detail.html?id=${id}&edit=true`;
    }

    async banUser(id) {
        const confirmed = await adminApp?.showConfirm('Are you sure you want to ban this user?');
        if (!confirmed) return;

        try {
            const response = await this.api.banUser(id);
            if (response.success) {
                adminApp?.showNotification('User banned successfully', 'success');
                this.loadUsers();
            }
        } catch (error) {
            adminApp?.showNotification('Failed to ban user', 'error');
        }
    }

    async unbanUser(id) {
        const confirmed = await adminApp?.showConfirm('Are you sure you want to unban this user?');
        if (!confirmed) return;

        try {
            const response = await this.api.unbanUser(id);
            if (response.success) {
                adminApp?.showNotification('User unbanned successfully', 'success');
                this.loadUsers();
            }
        } catch (error) {
            adminApp?.showNotification('Failed to unban user', 'error');
        }
    }

    async deleteUser(id) {
        const confirmed = await adminApp?.showConfirm('Are you sure you want to delete this user? This action cannot be undone.');
        if (!confirmed) return;

        try {
            const response = await this.api.deleteUser(id);
            if (response.success) {
                adminApp?.showNotification('User deleted successfully', 'success');
                this.loadUsers();
            }
        } catch (error) {
            adminApp?.showNotification('Failed to delete user', 'error');
        }
    }

    displayError() {
        const tbody = document.getElementById('users-table-body');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center py-8 text-red-600">
                    <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                    <p>Failed to load users. Please try again.</p>
                </td>
            </tr>
        `;
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize user management
let userManagement;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('users-table-body')) {
        userManagement = new UserManagement();
    }
});

