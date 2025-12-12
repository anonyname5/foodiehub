# Project Structure

## Restaurant Review Platform - Laravel Monolith

This document describes the structure of the Restaurant Review application after conversion to a traditional Laravel monolith.

---

## 📁 Directory Structure

```
Restaurant Review/
├── backend/                          # Laravel application (main directory)
│   ├── app/
│   │   ├── Console/
│   │   │   └── Kernel.php
│   │   ├── Exceptions/
│   │   │   └── Handler.php
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/
│   │   │   │   │   └── AdminController.php      # Admin panel controller
│   │   │   │   ├── Api/                          # Legacy API controllers (not used)
│   │   │   │   │   ├── AdminController.php
│   │   │   │   │   ├── AuthController.php
│   │   │   │   │   ├── ImageController.php
│   │   │   │   │   ├── RestaurantController.php
│   │   │   │   │   ├── ReviewController.php
│   │   │   │   │   └── UserController.php
│   │   │   │   ├── AuthController.php            # Web authentication
│   │   │   │   ├── HomeController.php             # Homepage
│   │   │   │   ├── ProfileController.php          # User profile
│   │   │   │   ├── RestaurantController.php       # Restaurant pages
│   │   │   │   ├── ReviewController.php           # Review management
│   │   │   │   └── Controller.php                # Base controller
│   │   │   ├── Kernel.php                         # HTTP kernel
│   │   │   └── Middleware/
│   │   │       ├── AdminMiddleware.php            # Admin access control
│   │   │       ├── Authenticate.php
│   │   │       ├── EncryptCookies.php
│   │   │       ├── PreventRequestsDuringMaintenance.php
│   │   │       ├── RedirectIfAuthenticated.php
│   │   │       ├── TrimStrings.php
│   │   │       ├── TrustHosts.php
│   │   │       ├── TrustProxies.php
│   │   │       └── VerifyCsrfToken.php
│   │   ├── Models/
│   │   │   ├── Image.php                          # Image model (polymorphic)
│   │   │   ├── Restaurant.php                     # Restaurant model
│   │   │   ├── Review.php                         # Review model
│   │   │   └── User.php                           # User model
│   │   ├── Providers/
│   │   │   ├── AppServiceProvider.php             # Loads helpers.php
│   │   │   ├── AuthServiceProvider.php
│   │   │   ├── EventServiceProvider.php
│   │   │   └── RouteServiceProvider.php
│   │   └── helpers.php                            # image_url() helper function
│   │
│   ├── bootstrap/
│   │   ├── app.php
│   │   └── cache/
│   │
│   ├── config/                                     # Configuration files
│   │   ├── app.php
│   │   ├── auth.php
│   │   ├── cache.php
│   │   ├── cors.php
│   │   ├── database.php
│   │   ├── filesystems.php
│   │   ├── logging.php
│   │   ├── queue.php
│   │   ├── session.php
│   │   └── view.php
│   │
│   ├── database/
│   │   ├── migrations/
│   │   │   ├── 2024_01_01_000001_create_users_table.php
│   │   │   ├── 2024_01_01_000002_create_restaurants_table.php
│   │   │   ├── 2024_01_01_000003_create_reviews_table.php
│   │   │   ├── 2024_01_01_000004_create_favorites_table.php
│   │   │   ├── 2024_01_01_000005_create_images_table.php
│   │   │   ├── 2025_09_12_145907_add_admin_fields_to_users_table.php
│   │   │   └── 2025_09_12_150000_add_status_to_reviews_table.php
│   │   └── seeders/
│   │       ├── AdminUserSeeder.php
│   │       ├── DatabaseSeeder.php
│   │       ├── RestaurantSeeder.php
│   │       ├── ReviewSeeder.php
│   │       └── UserSeeder.php
│   │
│   ├── public/                                     # Public web root
│   │   ├── admin-assets/                           # Admin panel assets
│   │   │   └── assets/
│   │   │       ├── css/
│   │   │       │   └── admin.css
│   │   │       └── js/
│   │   │           ├── admin-api.js
│   │   │           ├── admin-main.js
│   │   │           ├── dashboard.js
│   │   │           ├── restaurant-management.js
│   │   │           ├── review-moderation.js
│   │   │           ├── settings.js
│   │   │           └── user-management.js
│   │   ├── assets/                                 # Frontend assets
│   │   │   ├── css/
│   │   │   │   └── style.css
│   │   │   ├── js/
│   │   │   │   ├── api.js                          # Legacy (not used)
│   │   │   │   ├── main.js                         # Main JavaScript
│   │   │   │   ├── profile.js
│   │   │   │   ├── restaurant.js
│   │   │   │   ├── restaurant-detail.js
│   │   │   │   └── review.js
│   │   │   └── images/
│   │   │       └── icons/
│   │   │           └── Default User Icon.png
│   │   ├── index.php                               # Laravel entry point
│   │   └── storage/                                # Storage symlink
│   │
│   ├── resources/
│   │   └── views/                                  # Blade templates
│   │       ├── admin/                              # Admin panel views
│   │       │   ├── dashboard.blade.php
│   │       │   ├── restaurants.blade.php
│   │       │   ├── reviews.blade.php
│   │       │   ├── settings.blade.php
│   │       │   ├── user-show.blade.php
│   │       │   └── users.blade.php
│   │       ├── layouts/                            # Layout templates
│   │       │   ├── admin.blade.php                 # Admin layout
│   │       │   └── app.blade.php                   # Main layout
│   │       ├── home.blade.php                      # Homepage
│   │       ├── profile/
│   │       │   └── show.blade.php                 # User profile
│   │       ├── restaurants/
│   │       │   ├── index.blade.php                # Restaurant listing
│   │       │   └── show.blade.php                 # Restaurant detail
│   │       └── reviews/
│   │           ├── create.blade.php               # Create review
│   │           └── edit.blade.php                # Edit review
│   │
│   ├── routes/
│   │   ├── api.php                                 # API routes (cleaned/empty)
│   │   ├── console.php                            # Artisan commands
│   │   └── web.php                                # All web routes
│   │
│   ├── storage/                                    # Storage directory
│   │   ├── app/
│   │   │   └── public/                            # Public storage (images)
│   │   ├── framework/
│   │   │   ├── cache/
│   │   │   ├── sessions/
│   │   │   └── views/
│   │   └── logs/
│   │       └── laravel.log
│   │
│   ├── vendor/                                     # Composer dependencies
│   │
│   ├── artisan                                     # Artisan CLI
│   ├── composer.json
│   ├── composer.lock
│   └── .env                                        # Environment config
│
├── frontend/                                       # Legacy frontend (can be deleted)
│   └── public/                                     # Old HTML files (not used)
│
├── CONVERSION_CHECKLIST.md                         # Conversion tracking
├── PROJECT_STRUCTURE.md                            # This file
└── README.md                                       # Project documentation
```

---

## 🎯 Key Components

### Controllers

#### Web Controllers (Active)
- **HomeController** - Homepage with statistics and featured content
- **RestaurantController** - Restaurant listing and detail pages
- **ReviewController** - Review creation, editing, deletion
- **ProfileController** - User profile display and update
- **AuthController** - Login, register, logout (web routes)
- **Admin\AdminController** - Admin panel functionality

#### API Controllers (Legacy - Not Used)
- Located in `app/Http/Controllers/Api/`
- Kept for reference but not used in monolith

### Models

- **User** - User accounts with admin support
- **Restaurant** - Restaurant listings
- **Review** - Restaurant reviews
- **Image** - Polymorphic image model (for restaurants, reviews, users)

### Views (Blade Templates)

#### Main Application
- `layouts/app.blade.php` - Main layout with navigation
- `home.blade.php` - Homepage
- `restaurants/index.blade.php` - Restaurant listing
- `restaurants/show.blade.php` - Restaurant detail
- `reviews/create.blade.php` - Create review form
- `reviews/edit.blade.php` - Edit review form
- `profile/show.blade.php` - User profile

#### Admin Panel
- `layouts/admin.blade.php` - Admin layout
- `admin/dashboard.blade.php` - Admin dashboard
- `admin/users.blade.php` - User management
- `admin/user-show.blade.php` - User details
- `admin/restaurants.blade.php` - Restaurant management
- `admin/reviews.blade.php` - Review moderation
- `admin/settings.blade.php` - System settings

### Routes

#### Web Routes (`routes/web.php`)
- **Home**: `/` → HomeController@index
- **Restaurants**: `/restaurants` → RestaurantController@index
- **Restaurant Detail**: `/restaurants/{id}` → RestaurantController@show
- **Reviews**: `/reviews/create`, `/reviews/{id}/edit` → ReviewController
- **Profile**: `/profile` → ProfileController@show
- **Auth**: `/login`, `/register`, `/logout` → AuthController
- **Admin**: `/admin/*` → Admin\AdminController (protected by admin middleware)

#### API Routes (`routes/api.php`)
- Cleaned/empty - no longer used in monolith

### Middleware

- **auth** - Authentication check
- **admin** - Admin access control (redirects for web, JSON for API)
- **guest** - Redirect authenticated users
- Standard Laravel middleware (CSRF, sessions, etc.)

### Helpers

- **`image_url($path)`** - Handles both external URLs and local storage paths
  - Returns URL as-is if starts with `http://` or `https://`
  - Prepends `storage/` for local paths
  - Returns default icon for empty/null paths

---

## 🔄 Request Flow

### User Request Flow
1. User visits URL → `routes/web.php`
2. Route matches → Controller method
3. Controller fetches data from Models
4. Controller returns Blade view with data
5. Blade template renders HTML
6. Response sent to browser

### Admin Request Flow
1. Admin visits `/admin/*` → Admin routes
2. `auth` middleware checks authentication
3. `admin` middleware checks admin privileges
4. AdminController handles request
5. Admin Blade template renders
6. Response sent to browser

---

## 📦 Assets

### Frontend Assets
- Location: `public/assets/`
- CSS: `public/assets/css/style.css`
- JavaScript: `public/assets/js/main.js` (and others)
- Images: `public/assets/images/`

### Admin Assets
- Location: `public/admin-assets/`
- CSS: `public/admin-assets/assets/css/admin.css`
- JavaScript: `public/admin-assets/assets/js/*.js`

### Storage
- Location: `storage/app/public/`
- Used for: User avatars, restaurant images, review images
- Accessible via: `storage/` symlink in `public/`

---

## 🗄️ Database Structure

### Tables
- **users** - User accounts (with admin fields)
- **restaurants** - Restaurant listings
- **reviews** - Restaurant reviews (with status field)
- **favorites** - User favorite restaurants (pivot table)
- **images** - Polymorphic images table

### Relationships
- User → Reviews (hasMany)
- User → FavoriteRestaurants (belongsToMany)
- Restaurant → Reviews (hasMany)
- Restaurant → Images (morphMany)
- Review → User (belongsTo)
- Review → Restaurant (belongsTo)
- Review → Images (morphMany)

---

## 🔐 Authentication & Authorization

### Authentication
- Session-based (Laravel default)
- Login/Register forms submit to web routes
- CSRF protection enabled

### Authorization
- **Admin Middleware** - Checks `is_admin` or `role` field
- Admin routes protected by `['auth', 'admin']` middleware
- Admin panel accessible at `/admin`

---

## 🎨 Frontend Architecture

### Server-Side Rendering
- All pages rendered server-side with Blade
- Data passed from controllers to views
- No client-side API calls for initial page load

### JavaScript
- `main.js` - Main application JavaScript
  - Form handling (non-blocking)
  - UI interactions
  - No API calls (removed)
- Admin JavaScript files in `admin-assets/` (legacy, not actively used)

### Styling
- Tailwind CSS (via CDN)
- Custom CSS in `assets/css/style.css`
- Admin CSS in `admin-assets/assets/css/admin.css`

---

## 🚀 Running the Application

### Development Server
```bash
cd backend
php artisan serve
```
Access at: `http://127.0.0.1:8000`

### Admin Panel
Access at: `http://127.0.0.1:8000/admin` (admin users only)

---

## 📝 Notes

- **Monolith Architecture**: Everything runs from a single Laravel application
- **No CORS Issues**: All requests are same-origin
- **No API Dependencies**: All data is server-rendered
- **Legacy Code**: API controllers and old frontend files exist but are not used
- **Image Handling**: Supports both external URLs and local storage paths
- **Admin Panel**: Fully integrated with main application

---

**Last Updated**: After monolith conversion
**Status**: ✅ Fully converted to Laravel monolith
