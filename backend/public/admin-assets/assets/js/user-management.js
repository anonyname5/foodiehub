document.addEventListener('DOMContentLoaded', async () => {
    await loadUsers();
    wireFilters();
});

function wireFilters() {
    const search = document.getElementById('user-search');
    const status = document.getElementById('user-status');
    const sort = document.getElementById('user-sort');
    [search, status, sort].forEach(el => {
        el?.addEventListener('input', debounce(loadUsers, 300));
        el?.addEventListener('change', debounce(loadUsers, 0));
    });
}

async function loadUsers() {
    const container = document.getElementById('users-list');
    if (!container) return;
    container.innerHTML = '<p class="text-gray-500">Loading users...</p>';

    try {
        const params = {
            search: document.getElementById('user-search')?.value || '',
            status: document.getElementById('user-status')?.value || '',
            sort_by: document.getElementById('user-sort')?.value || 'newest',
        };
        const res = await adminApi.getUsers(params);
        if (!res.success || !res.data || !res.data.data?.length) {
            container.innerHTML = '<p class="text-gray-500">No users found.</p>';
            return;
        }
        const users = res.data.data || res.data; // handle paginated vs non-paginated
        container.innerHTML = '';
        users.forEach(user => container.appendChild(renderUser(user)));
    } catch (e) {
        container.innerHTML = '<p class="text-red-500">Failed to load users.</p>';
    }
}

function renderUser(user) {
    const el = document.createElement('div');
    el.className = 'py-3 flex items-center justify-between';
    el.innerHTML = `
        <div>
            <div class="text-sm font-semibold text-gray-900">${user.name || 'User'}</div>
            <div class="text-xs text-gray-500">${user.email || ''}</div>
            <div class="text-xs text-gray-500">${user.location || ''}</div>
        </div>
        <div class="text-right">
            <div class="text-xs text-gray-500">Reviews: ${user.reviews_count ?? '-'}</div>
            <div class="text-xs text-gray-500">Favorites: ${user.favorite_restaurants_count ?? '-'}</div>
            <div class="text-xs font-semibold ${user.is_active ? 'text-green-600' : 'text-red-600'}">${user.is_active ? 'Active' : 'Inactive'}</div>
        </div>
    `;
    return el;
}

function debounce(fn, delay) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

