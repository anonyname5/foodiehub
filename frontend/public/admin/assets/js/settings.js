document.addEventListener('DOMContentLoaded', async () => {
    await loadSettings();
    wireSave();
});

async function loadSettings() {
    const statusEl = document.getElementById('settings-status');
    try {
        const res = await adminApi.request('/admin/settings');
        if (!res.success || !res.data) throw new Error('Failed to load settings');
        const s = res.data;
        setValue('setting-site-name', s.site_name || '');
        setValue('setting-site-description', s.site_description || '');
        setChecked('setting-registration', s.enable_registration);
        setChecked('setting-review-approval', s.require_review_approval);
        if (statusEl) statusEl.textContent = 'Loaded settings';
    } catch (e) {
        if (statusEl) statusEl.textContent = 'Failed to load settings';
    }
}

function wireSave() {
    const btn = document.getElementById('save-settings');
    const statusEl = document.getElementById('settings-status');
    btn?.addEventListener('click', async () => {
        try {
            if (statusEl) statusEl.textContent = 'Saving...';
            const payload = {
                site_name: getValue('setting-site-name'),
                site_description: getValue('setting-site-description'),
                enable_registration: isChecked('setting-registration'),
                require_review_approval: isChecked('setting-review-approval'),
            };
            const res = await adminApi.request('/admin/settings', {
                method: 'PUT',
                body: JSON.stringify(payload)
            });
            if (!res.success) throw new Error('Save failed');
            if (statusEl) statusEl.textContent = 'Settings saved';
        } catch (e) {
            if (statusEl) statusEl.textContent = 'Failed to save settings';
        }
    });
}

function setValue(id, val) {
    const el = document.getElementById(id);
    if (el) el.value = val;
}
function getValue(id) {
    return document.getElementById(id)?.value || '';
}
function setChecked(id, val) {
    const el = document.getElementById(id);
    if (el) el.checked = !!val;
}
function isChecked(id) {
    return document.getElementById(id)?.checked || false;
}

