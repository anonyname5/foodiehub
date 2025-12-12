document.addEventListener('DOMContentLoaded', async () => {
    await loadReviews();
    wireReviewFilters();
});

function wireReviewFilters() {
    const status = document.getElementById('review-status');
    const sort = document.getElementById('review-sort');
    const search = document.getElementById('review-search');
    [status, sort, search].forEach(el => {
        el?.addEventListener('change', debounce(loadReviews, 0));
        el?.addEventListener('input', debounce(loadReviews, 300));
    });
}

async function loadReviews() {
    const container = document.getElementById('reviews-list');
    if (!container) return;
    container.innerHTML = '<p class="text-gray-500">Loading reviews...</p>';
    try {
        const params = {
            status: document.getElementById('review-status')?.value || 'pending',
        };
        const sort = document.getElementById('review-sort')?.value || 'newest';
        if (sort === 'oldest') params.sort_by = 'oldest';
        if (sort === 'highest_rated') params.sort_by = 'highest';
        if (sort === 'lowest_rated') params.sort_by = 'lowest';
        const search = document.getElementById('review-search')?.value || '';
        if (search) params.search = search;

        const qs = new URLSearchParams(params).toString();
        const res = await adminApi.request(`/reviews?${qs}`);
        if (!res.success || !res.data?.length) {
            container.innerHTML = '<p class="text-gray-500">No reviews found.</p>';
            return;
        }
        container.innerHTML = '';
        res.data.forEach(r => container.appendChild(renderReview(r)));
    } catch (e) {
        container.innerHTML = '<p class="text-red-500">Failed to load reviews.</p>';
    }
}

function renderReview(r) {
    const el = document.createElement('div');
    el.className = 'py-3 flex flex-col gap-1';
    const statusColor = r.status === 'approved' ? 'text-green-600' : r.status === 'rejected' ? 'text-red-600' : 'text-orange-600';
    el.innerHTML = `
        <div class="flex justify-between items-center">
            <div class="text-sm font-semibold text-gray-900">${r.title || 'Review'}</div>
            <div class="text-xs ${statusColor} font-semibold capitalize">${r.status || 'pending'}</div>
        </div>
        <div class="text-xs text-gray-600">${r.user?.name || 'User'} • ${r.restaurant?.name || 'Restaurant'}</div>
        <div class="text-xs text-gray-500">Rating: ${(r.overall_rating ?? 0).toFixed?.(1) || r.overall_rating || '-'}</div>
        <div class="flex gap-2 mt-2">
            <button class="px-3 py-1 text-xs rounded bg-green-100 text-green-700 approve-btn" data-id="${r.id}">Approve</button>
            <button class="px-3 py-1 text-xs rounded bg-red-100 text-red-700 reject-btn" data-id="${r.id}">Reject</button>
        </div>
    `;
    el.querySelector('.approve-btn')?.addEventListener('click', () => handleReviewAction(r.id, 'approve'));
    el.querySelector('.reject-btn')?.addEventListener('click', () => handleReviewAction(r.id, 'reject'));
    return el;
}

async function handleReviewAction(id, action) {
    const container = document.getElementById('reviews-list');
    try {
        if (action === 'approve') {
            await adminApi.request(`/admin/reviews/${id}/approve`, { method: 'POST' });
        } else {
            await adminApi.request(`/admin/reviews/${id}/reject`, { method: 'POST' });
        }
        await loadReviews();
    } catch (e) {
        if (container) container.insertAdjacentHTML('afterbegin', `<p class="text-red-500">Action failed: ${e.message || 'error'}</p>`);
    }
}

function debounce(fn, delay) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

