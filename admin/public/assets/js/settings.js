/**
 * Settings JavaScript
 */
class Settings {
    constructor() {
        this.api = adminAPI;
        this.init();
    }

    async init() {
        document.getElementById('settings-form')?.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveSettings();
        });
        this.loadSettings();
    }

    async loadSettings() {
        try {
            const response = await this.api.getSettings();
            if (response.success && response.data) {
                this.displaySettings(response.data);
            }
        } catch (error) {
            console.error('Failed to load settings:', error);
        }
    }

    displaySettings(data) {
        if (data.site_name) document.getElementById('site-name').value = data.site_name;
        if (data.site_description) document.getElementById('site-description').value = data.site_description;
        if (data.allow_registration !== undefined) {
            document.getElementById('allow-registration').checked = data.allow_registration;
        }
        if (data.min_review_length) document.getElementById('min-review-length').value = data.min_review_length;
        if (data.max_review_length) document.getElementById('max-review-length').value = data.max_review_length;
    }

    async saveSettings() {
        const data = {
            site_name: document.getElementById('site-name').value,
            site_description: document.getElementById('site-description').value,
            allow_registration: document.getElementById('allow-registration').checked,
            min_review_length: parseInt(document.getElementById('min-review-length').value),
            max_review_length: parseInt(document.getElementById('max-review-length').value)
        };

        try {
            const response = await this.api.updateSettings(data);
            if (response.success) {
                adminApp?.showNotification('Settings saved successfully', 'success');
            }
        } catch (error) {
            adminApp?.showNotification('Failed to save settings', 'error');
        }
    }
}

let settings;
document.addEventListener('DOMContentLoaded', () => {
    if (document.getElementById('settings-form')) {
        settings = new Settings();
    }
});

// Global function for reset button
async function loadSettings() {
    if (settings) {
        await settings.loadSettings();
    }
}

