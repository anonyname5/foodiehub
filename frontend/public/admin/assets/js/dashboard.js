document.addEventListener('DOMContentLoaded', async () => {
    await loadStats();
    await loadActivity();
    await loadPendingReviews();
});

async function loadStats() {
    try {
        const res = await adminApi.getDashboardStats();
        if (!res.success) throw new Error('Failed stats');
        const stats = res.data;
        setValue('stat-restaurants', stats.restaurants);
        setValue('stat-users', stats.users);
        setValue('stat-reviews', stats.reviews);
        setValue('stat-pending', stats.pending_reviews);
        setText('stat-restaurants-sub', 'Total restaurants');
        setText('stat-users-sub', `${stats.active_users} active users`);
        setText('stat-reviews-sub', 'Total reviews');
        setText('stat-pending-sub', 'Pending reviews');
    } catch (e) {
        setValue('stat-restaurants', '-');
        setValue('stat-users', '-');
        setValue('stat-reviews', '-');
        setValue('stat-pending', '-');
    }
}

async function loadActivity() {
    const container = document.getElementById('recent-activity');
    if (!container) return;
    container.innerHTML = '<p class="text-gray-500">Loading activity...</p>';
    try {
        const res = await adminApi.getDashboardActivity();
        if (!res.success || !res.data || !res.data.activity?.length) {
            container.innerHTML = '<p class="text-gray-500">No recent activity.</p>';
            return;
        }
        container.innerHTML = '';
        res.data.activity.forEach(item => {
            const el = document.createElement('div');
            el.className = 'flex items-start space-x-3';
            el.innerHTML = `
                <div class="h-8 w-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <div class="text-sm font-semibold text-gray-800">${item.title || 'Activity'}</div>
                    <div class="text-sm text-gray-600">${item.description || ''}</div>
                </div>
            `;
            container.appendChild(el);
        });
    } catch (e) {
        container.innerHTML = '<p class="text-red-500">Failed to load activity.</p>';
    }
}

async function loadPendingReviews() {
    const container = document.getElementById('pending-reviews');
    if (!container) return;
    container.innerHTML = '<p class="text-gray-500">Loading pending reviews...</p>';
    try {
        const res = await adminApi.getPendingReviews({ limit: 5 });
        if (!res.success || !res.data || !res.data.length) {
            container.innerHTML = '<p class="text-gray-500">No pending reviews.</p>';
            return;
        }
        container.innerHTML = '';
        res.data.forEach(review => {
            const el = document.createElement('div');
            el.className = 'p-3 border rounded-lg bg-gray-50';
            el.innerHTML = `
                <div class="flex justify-between text-sm text-gray-700">
                    <span class="font-semibold">${review.user?.name || 'User'}</span>
                    <span class="text-orange-500 font-semibold">${Number(review.overall_rating || 0).toFixed(1)} ★</span>
                </div>
                <div class="text-sm text-gray-600">${review.restaurant?.name || 'Restaurant'}</div>
                <div class="text-xs text-gray-500 mt-1">${review.title || ''}</div>
            `;
            container.appendChild(el);
        });
    } catch (e) {
        container.innerHTML = '<p class="text-red-500">Failed to load pending reviews.</p>';
    }
}

function setValue(id, value) {
    const el = document.getElementById(id);
    if (el) el.textContent = value ?? '-';
}

function setText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

