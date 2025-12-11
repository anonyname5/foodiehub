# FoodieHub Admin Panel

Complete admin panel frontend for managing the FoodieHub restaurant review platform.

## Features

- 🔐 **Admin Authentication** - Secure login with session-based authentication
- 📊 **Dashboard** - Real-time statistics and activity feed
- 👥 **User Management** - View, edit, ban/unban, and delete users
- 🏪 **Restaurant Management** - Manage restaurant listings
- 📝 **Review Moderation** - Approve or reject pending reviews
- ⚙️ **System Settings** - Configure platform settings

## Structure

```
admin/public/
├── index.html                    # Admin login page
├── pages/
│   ├── dashboard/
│   │   └── dashboard.html        # Main dashboard
│   ├── users/
│   │   ├── users.html            # User list
│   │   └── user-detail.html      # User details
│   ├── restaurants/
│   │   ├── restaurants.html      # Restaurant list
│   │   └── restaurant-detail.html # Restaurant details
│   ├── reviews/
│   │   ├── reviews.html          # Review moderation queue
│   │   └── review-detail.html    # Review details
│   └── settings/
│       └── settings.html         # System settings
└── assets/
    ├── css/
    │   ├── admin.css             # Main admin styles
    │   └── components.css        # Component styles
    └── js/
        ├── admin-api.js          # API integration
        ├── admin-main.js          # Main admin logic
        ├── dashboard.js           # Dashboard functionality
        ├── user-management.js    # User management
        ├── restaurant-management.js # Restaurant management
        ├── review-moderation.js   # Review moderation
        └── settings.js            # Settings management
```

## Getting Started

1. **Start the admin panel server:**
   ```bash
   cd admin/public
   python -m http.server 3001
   ```

2. **Access the admin panel:**
   - URL: `http://localhost:3001`
   - Email: `admin@foodiehub.com`
   - Password: `admin123`

## API Integration

The admin panel connects to the Laravel backend API at `http://127.0.0.1:8000/api`.

All API calls use session-based authentication with cookies.

## Pages Overview

### Dashboard
- Platform statistics (restaurants, users, reviews, cities)
- Monthly metrics
- Recent activity feed
- Quick action buttons

### User Management
- List all users with search and filtering
- View user details and review history
- Ban/unban users
- Delete users
- Pagination support

### Restaurant Management
- List all restaurants
- Search and filter by cuisine/location
- View restaurant details
- Manage restaurant status

### Review Moderation
- Filter by status (pending, approved, rejected)
- Approve or reject reviews
- View review details
- Track moderation history

### Settings
- Site configuration
- Registration settings
- Review settings (min/max length)
- System preferences

## Development

The admin panel uses:
- **Vanilla JavaScript** (ES6+)
- **Tailwind CSS** (via CDN)
- **Font Awesome** icons
- **No build process** - direct file serving

## Notes

- All pages require admin authentication
- Session-based authentication (cookies)
- Responsive design for mobile devices
- Real-time updates on dashboard

