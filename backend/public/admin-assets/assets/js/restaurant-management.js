document.addEventListener('DOMContentLoaded', async () => {
    await loadRestaurants();
    wireRestaurantFilters();
});

function wireRestaurantFilters() {
    const search = document.getElementById('restaurant-search');
    const cuisine = document.getElementById('restaurant-cuisine');
    const status = document.getElementById('restaurant-status');
    [search, cuisine, status].forEach(el => {
        el?.addEventListener('input', debounce(loadRestaurants, 300));
        el?.addEventListener('change', debounce(loadRestaurants, 0));
    });
}

async function loadRestaurants() {
    const container = document.getElementById('restaurants-list');
    if (!container) return;
    container.innerHTML = '<p class="text-gray-500">Loading restaurants...</p>';
    try {
        const params = {};
        const searchVal = document.getElementById('restaurant-search')?.value || '';
        const cuisineVal = document.getElementById('restaurant-cuisine')?.value || '';
        const statusVal = document.getElementById('restaurant-status')?.value || '';
        if (searchVal) params.search = searchVal;
        if (cuisineVal) params.cuisine = cuisineVal;
        if (statusVal) params.is_active = statusVal === 'active' ? 1 : 0;

        // Reuse public restaurants endpoint for now
        const res = await adminApi.request('/restaurants?' + new URLSearchParams(params).toString());
        if (!res.success || !res.data?.length) {
            container.innerHTML = '<p class="text-gray-500">No restaurants found.</p>';
            return;
        }
        container.innerHTML = '';
        const cuisines = new Set();
        res.data.forEach(r => cuisines.add(r.cuisine));
        fillCuisineSelect([...cuisines]);
        res.data.forEach(r => container.appendChild(renderRestaurant(r)));
    } catch (e) {
        container.innerHTML = '<p class="text-red-500">Failed to load restaurants.</p>';
    }
}

function renderRestaurant(r) {
    const el = document.createElement('div');
    el.className = 'py-3 flex items-center justify-between';
    el.innerHTML = `
        <div>
            <div class="text-sm font-semibold text-gray-900">${r.name || 'Restaurant'}</div>
            <div class="text-xs text-gray-500">${r.location || ''}</div>
            <div class="text-xs text-gray-500">${r.cuisine || ''}</div>
        </div>
        <div class="text-right">
            <div class="text-xs text-gray-500">Rating: ${(r.average_rating ?? 0).toFixed?.(1) || r.average_rating || '-'}</div>
            <div class="text-xs text-gray-500">Reviews: ${r.review_count ?? '-'}</div>
            <div class="text-xs font-semibold ${r.is_active ? 'text-green-600' : 'text-red-600'}">${r.is_active ? 'Active' : 'Inactive'}</div>
        </div>
    `;
    return el;
}

function fillCuisineSelect(list) {
    const select = document.getElementById('restaurant-cuisine');
    if (!select || select.dataset.filled === '1') return;
    list.filter(Boolean).forEach(c => {
        const opt = document.createElement('option');
        opt.value = c;
        opt.textContent = c;
        select.appendChild(opt);
    });
    select.dataset.filled = '1';
}

function debounce(fn, delay) {
    let t;
    return (...args) => {
        clearTimeout(t);
        t = setTimeout(() => fn.apply(this, args), delay);
    };
}

