/**
 * Review Moderation JavaScript
 */
class ReviewModeration {
    constructor() {
        this.api = adminAPI;
        this.currentFilter = 'pending';
        this.init();
    }

    async init() {
        this.loadReviews();
    }

    async filterReviews(status) {
        this.currentFilter = status;
        document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
        event.target.classList.add('active');
        this.loadReviews();
    }

    async loadReviews() {
        try {
            const params = this.currentFilter !== 'all' ? { status: this.currentFilter } : {};
            const response = await this.api.getReviews(params);
            if (response.success) {
                this.displayReviews(response.data || []);
            }
        } catch (error) {
            console.error('Failed to load reviews:', error);
            this.displayError();
        }
    }

    displayReviews(reviews) {
        const tbody = document.getElementById('reviews-table-body');
        if (!reviews || reviews.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-500">No reviews found</td></tr>';
            return;
        }
        tbody.innerHTML = reviews.map(r => {
            const statusBadge = r.status === 'approved' ? 'badge-success' : 
                               r.status === 'rejected' ? 'badge-danger' : 'badge-pending';
            return `
                <tr>
                    <td>
                        <div class="max-w-xs">
                            <p class="font-medium text-gray-900">${r.title || 'No title'}</p>
                            <p class="text-sm text-gray-600 truncate">${r.comment || 'No comment'}</p>
                        </div>
                    </td>
                    <td>${r.restaurant?.name || 'N/A'}</td>
                    <td>${r.user?.name || 'N/A'}</td>
                    <td>
                        <div class="flex items-center">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            <span>${r.overall_rating || 0}</span>
                        </div>
                    </td>
                    <td><span class="badge ${statusBadge}">${r.status || 'pending'}</span></td>
                    <td class="text-sm text-gray-600">${new Date(r.created_at).toLocaleDateString()}</td>
                    <td>
                        <div class="action-buttons">
                            ${r.status === 'pending' ? `
                                <button onclick="reviewModeration.approveReview(${r.id})" class="action-btn action-btn-success" title="Approve">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="reviewModeration.rejectReview(${r.id})" class="action-btn action-btn-delete" title="Reject">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                            <button onclick="reviewModeration.viewReview(${r.id})" class="action-btn action-btn-view">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async approveReview(id) {
        if (await adminApp?.showConfirm('Approve this review?')) {
            try {
                await this.api.approveReview(id);
                adminApp?.showNotification('Review approved', 'success');
                this.loadReviews();
            } catch (error) {
                adminApp?.showNotification('Failed to approve', 'error');
            }
        }
    }

    async rejectReview(id) {
        const reason = prompt('Reason for rejection (optional):');
        if (await adminApp?.showConfirm('Reject this review?')) {
            try {
                await this.api.rejectReview(id, reason);
                adminApp?.showNotification('Review rejected', 'success');
                this.loadReviews();
            } catch (error) {
                adminApp?.showNotification('Failed to reject', 'error');
            }
        }
    }

    viewReview(id) {
        window.location.href = `review-detail.html?id=${id}`;
    }

    displayError() {
        document.getElementById('reviews-table-body').innerHTML = 
            '<tr><td colspan="7" class="text-center py-8 text-red-600">Failed to load reviews</td></tr>';
    }
}

let reviewModeration;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('reviews-table-body')) {
        reviewModeration = new ReviewModeration();
    }
});

