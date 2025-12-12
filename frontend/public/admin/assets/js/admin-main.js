// Shared admin bootstrap: auth check, logout, nav active state

function isAdminUser(user) {
    if (!user) return false;
    const role = (user.role || '').toLowerCase();
    return user.is_admin === true || role === 'admin' || role === 'super_admin';
}

async function ensureAdminSession() {
    const sessionStatus = document.getElementById('session-status');
    try {
        const res = await adminApi.checkAuth();
        if (!res.success || !res.authenticated || !isAdminUser(res.user)) {
            if (sessionStatus) sessionStatus.textContent = 'Not authorized. Redirecting...';
            window.location.href = '/';
            return;
        }
        if (sessionStatus) sessionStatus.textContent = 'Authenticated as admin';
        const nameEl = document.getElementById('admin-name');
        if (nameEl) nameEl.textContent = res.user.name || 'Admin';
        return res.user;
    } catch (err) {
        if (sessionStatus) sessionStatus.textContent = 'Session check failed. Redirecting...';
        window.location.href = '/';
    }
}

async function checkApiHealth() {
    const apiStatus = document.getElementById('api-status');
    try {
        const res = await fetch(`${adminApi.baseURL}/health`);
        const data = await res.json();
        if (apiStatus) apiStatus.textContent = data.success ? 'API reachable' : 'API check failed';
    } catch (e) {
        if (apiStatus) apiStatus.textContent = 'API not reachable';
    }
}

function setupLogout() {
    const btn = document.getElementById('admin-logout');
    if (btn) {
        btn.addEventListener('click', async () => {
            try {
                await adminApi.logout();
            } finally {
                window.location.href = '/';
            }
        });
    }
}

function setActiveNav() {
    const links = document.querySelectorAll('.nav-link');
    const path = window.location.pathname;
    links.forEach(link => {
        if (link.getAttribute('href') === path) {
            link.classList.add('active');
        } else {
            link.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    // Force consistent host to match backend cookie domain (127.0.0.1)
    if (window.location.hostname === 'localhost') {
        const url = window.location.href.replace('://localhost', '://127.0.0.1');
        window.location.replace(url);
        return;
    }

    setActiveNav();
    setupLogout();
    ensureAdminSession();
    checkApiHealth();
});

