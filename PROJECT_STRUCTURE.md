# FoodieHub Project Structure

This document outlines the complete project structure for the FoodieHub restaurant review platform.

## Overview

```
foodiehub/
├── backend/                    # Laravel API Backend
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AdminController.php    # Admin dashboard & management
│   │   │   ├── AuthController.php     # Authentication (user & admin)
│   │   │   ├── RestaurantController.php
│   │   │   ├── ReviewController.php
│   │   │   └── ...
│   │   ├── Http/Middleware/
│   │   │   ├── AdminMiddleware.php    # Admin access control
│   │   │   └── ...
│   │   ├── Models/
│   │   │   ├── User.php               # Extended with admin roles
│   │   │   ├── Restaurant.php
│   │   │   ├── Review.php
│   │   │   └── Image.php
│   │   └── ...
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── 2025_09_12_145907_add_admin_fields_to_users_table.php
│   │   │   └── ...
│   │   └── seeders/
│   │       ├── AdminUserSeeder.php    # Create admin users
│   │       └── ...
│   ├── routes/api.php                 # Admin routes included
│   └── ...
├── admin/                      # Admin Panel Frontend
│   ├── public/                 # Web-accessible files
│   │   ├── index.html         # Admin dashboard
│   │   ├── pages/             # Admin pages
│   │   │   ├── dashboard/     # Dashboard & analytics
│   │   │   │   └── dashboard.html
│   │   │   ├── users/         # User management
│   │   │   │   ├── users.html
│   │   │   │   └── user-detail.html
│   │   │   ├── restaurants/   # Restaurant management
│   │   │   │   ├── restaurants.html
│   │   │   │   └── restaurant-detail.html
│   │   │   ├── reviews/       # Review moderation
│   │   │   │   ├── reviews.html
│   │   │   │   └── review-detail.html
│   │   │   └── settings/      # System settings
│   │   │       └── settings.html
│   │   └── assets/            # Admin static assets
│   │       ├── css/           # Admin stylesheets
│   │       │   ├── admin.css
│   │       │   └── components.css
│   │       ├── js/            # Admin JavaScript modules
│   │       │   ├── admin-api.js
│   │       │   ├── admin-main.js
│   │       │   ├── dashboard.js
│   │       │   ├── user-management.js
│   │       │   ├── restaurant-management.js
│   │       │   ├── review-moderation.js
│   │       │   └── settings.js
│   │       └── images/        # Admin icons & images
│   ├── src/                   # Source files (future)
│   ├── docs/                  # Admin documentation
│   └── README.md             # Admin panel documentation
├── frontend/                   # User-facing Frontend
│   ├── public/                 # Web-accessible files
│   │   ├── index.html         # Landing page
│   │   ├── pages/             # Application pages
│   │   │   ├── restaurants/   # Restaurant pages
│   │   │   │   ├── restaurants.html
│   │   │   │   └── restaurant-detail.html
│   │   │   ├── profile/       # User profile
│   │   │   │   └── profile.html
│   │   │   └── reviews/       # Review management
│   │   │       └── write-review.html
│   │   └── assets/            # Static assets
│   │       ├── css/           # Stylesheets
│   │       │   └── style.css
│   │       ├── js/            # JavaScript modules
│   │       │   ├── api.js
│   │       │   ├── main.js
│   │       │   ├── restaurant.js
│   │       │   ├── restaurant-detail.js
│   │       │   ├── profile.js
│   │       │   └── review.js
│   │       ├── images/        # Images and icons
│   │       └── data/          # Static data files
│   ├── src/                   # Source files (future)
│   ├── docs/                  # Documentation
│   ├── package.json           # Package configuration
│   ├── .gitignore            # Git ignore rules
│   └── README.md             # Frontend documentation
├── docs/                      # Project documentation
├── .gitignore                # Global git ignore
└── README.md                 # Main project README
```

## Technology Stack

### Backend (Laravel)
- **Framework**: Laravel 10
- **Language**: PHP 8.1+
- **Database**: MySQL
- **Authentication**: Session-based
- **API**: RESTful API endpoints
- **Image Storage**: Polymorphic image handling

### Frontend (Vanilla JS)
- **Language**: Vanilla JavaScript (ES6+)
- **Styling**: Tailwind CSS
- **Icons**: Font Awesome 6
- **Maps**: Google Maps API
- **Location**: Nominatim OpenStreetMap API
- **Build**: No build process (direct serving)

### Data & APIs
- **Restaurant Data**: 8 authentic Malaysian restaurants
- **Location Services**: Free geocoding and autocomplete
- **Image Hosting**: Unsplash for sample images
- **Maps**: Google Maps embed and directions

## Key Features Implemented

### ✅ User Management
- User registration and authentication
- Profile management with avatar upload
- Session-based login/logout
- Dynamic navigation based on auth state

### ✅ Restaurant System
- Browse 8 Malaysian restaurants with real location data
- Interactive Google Maps with restaurant locations
- Image galleries with modal viewing
- Restaurant details and information

### ✅ Location Features
- Location autocomplete using free APIs
- Current location detection
- Google Maps integration for directions
- City-based filtering

### ✅ Review System
- Write and submit restaurant reviews
- Rating system with star display
- Review listing and management

### ✅ Dashboard
- Dynamic statistics from real database
- Restaurant, user, review, and city counts
- Animated counter displays

### ✅ Image Management
- Polymorphic image storage
- Gallery functionality
- Avatar upload with compression
- Primary image designation

### ✅ Admin Panel
- Secure admin authentication system
- Dashboard with real-time statistics
- User management (view, edit, ban/unban, delete)
- Restaurant management and moderation
- Review moderation (approve/reject pending reviews)
- System settings configuration
- Role-based access control (admin/super_admin)

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/auth/check` - Check authentication
- `POST /api/auth/logout` - User logout

### Restaurants
- `GET /api/restaurants` - List restaurants
- `GET /api/restaurants/{id}` - Restaurant details
- `GET /api/restaurants/filter-options` - Filter options
- `GET /api/statistics` - Dashboard statistics

### Reviews
- `GET /api/reviews` - List reviews
- `POST /api/reviews` - Create review
- `GET /api/restaurants/{id}/reviews` - Restaurant reviews

### Images
- `POST /api/images/upload` - Upload images
- `DELETE /api/images/{id}` - Delete image

### Admin Endpoints
- `GET /api/admin/dashboard/stats` - Dashboard statistics
- `GET /api/admin/dashboard/activity` - Recent activity
- `GET /api/admin/users` - List users with filtering
- `GET /api/admin/users/{id}` - Get user details
- `PUT /api/admin/users/{id}` - Update user
- `POST /api/admin/users/{id}/ban` - Ban user
- `POST /api/admin/users/{id}/unban` - Unban user
- `DELETE /api/admin/users/{id}` - Delete user
- `POST /api/admin/reviews/{id}/approve` - Approve review
- `POST /api/admin/reviews/{id}/reject` - Reject review
- `GET /api/admin/settings` - Get system settings
- `PUT /api/admin/settings` - Update system settings

## Development Workflow

1. **Backend**: Laravel serves API on `http://127.0.0.1:8000`
2. **Frontend**: Static server serves frontend on `http://localhost:3000`
3. **Admin Panel**: Static server serves admin on `http://localhost:3001`
4. **Database**: MySQL with seeded Malaysian restaurant data
5. **Testing**: Manual testing through frontend and admin interfaces

## Deployment Considerations

### Frontend
- Static hosting (Netlify, Vercel, GitHub Pages)
- CDN for assets
- Environment-specific API URLs

### Admin Panel
- Separate static hosting or subdomain
- Secure access with admin authentication
- Environment-specific API URLs
- CDN for assets

### Backend
- PHP hosting (shared hosting, VPS, cloud)
- MySQL database
- File storage for uploads
- Environment configuration

## Data Sources

- **Restaurant Data**: Authentic Malaysian restaurants
- **Images**: High-quality Unsplash food photography
- **Locations**: Real GPS coordinates and addresses
- **Reviews**: User-generated content system

## Admin Panel Features

### 🔐 Authentication & Authorization
- **Admin Login**: Separate admin authentication system
- **Role-based Access**: Support for `admin` and `super_admin` roles
- **Session Management**: Secure admin sessions with middleware protection
- **Access Control**: AdminMiddleware ensures only authorized users access admin routes

### 📊 Dashboard & Analytics
- **Real-time Statistics**: Live counts of users, restaurants, reviews, cities
- **Monthly Metrics**: New users, restaurants, and reviews this month
- **Activity Feed**: Recent user registrations and reviews
- **Pending Reviews**: Count of reviews awaiting moderation

### 👥 User Management
- **User Listing**: Paginated list with search and filtering
- **User Details**: Complete user profile with review history
- **User Actions**: Edit profiles, ban/unban users, soft delete
- **Advanced Filtering**: Search by name, email, location, status
- **User Statistics**: Review count, favorite restaurants count

### 🏪 Restaurant Management
- **Restaurant Listing**: Admin view of all restaurants (including inactive)
- **Restaurant Details**: Full restaurant information and images
- **Status Management**: Activate/deactivate restaurants
- **Image Management**: Upload and manage restaurant images

### 📝 Review Moderation
- **Review Queue**: List of pending reviews awaiting approval
- **Review Actions**: Approve or reject reviews with reason tracking
- **Review Details**: Full review content with user and restaurant context
- **Status Tracking**: Track approval/rejection timestamps

### ⚙️ System Settings
- **Site Configuration**: Site name, description, featured cities
- **Registration Settings**: Enable/disable user registration
- **Review Settings**: Min/max review length, daily limits
- **Notification Settings**: Email verification requirements

This structure provides a clean separation between frontend, backend, and admin panel, making the project maintainable and scalable while providing comprehensive administrative capabilities.