# 🍽️ FoodieHub - Restaurant Review Platform

A full-featured restaurant review platform built as a Laravel monolith. Users can discover restaurants, write detailed reviews with photos, and manage their profiles. Features a two-tier management system: platform admins manage everything, while restaurant owners can claim and manage their own restaurants. Complete admin panel and restaurant owner dashboard integrated with the main application.

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat-square&logo=tailwind-css)
![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)

## ✨ Features

### 👥 User Features
- 🔐 **Authentication** - User registration, login, and session management with error handling
- 🏪 **Restaurant Discovery** - Browse restaurants with advanced search and filtering
- ⭐ **Multi-dimensional Ratings** - Rate restaurants on food, service, ambiance, and value
- 📝 **Rich Reviews** - Write detailed reviews with photos and recommendations
- 📸 **Photo Uploads** - Upload multiple images per review
- 👍 **Helpful Votes** - Mark reviews as helpful to improve review quality
- 💬 **Review Responses** - Restaurant owners can respond to reviews
- ✅ **Verified Reviews** - Verified purchase badges for authentic reviews
- 📸 **Photo Galleries** - Click-to-zoom photo galleries for restaurants and reviews
- ❤️ **Favorites** - Save favorite restaurants for quick access
- 👤 **User Profiles** - Manage profile with avatar upload, bio, and privacy settings
- 📊 **Personal Stats** - View review history and statistics
- 🗺️ **Location Features** - Location-based search with interactive maps
- ⏰ **Restaurant Details** - View working hours, features, and contact information
- 🔔 **Notifications** - Dropdown notification center with real-time updates
- 🎯 **Recommendations** - Personalized restaurant recommendations based on preferences
- 💰 **Price Range Filtering** - Filter by Budget, Standard, Exclusive, or Premium

### 🔧 Admin Features
- 📊 **Dashboard** - Real-time statistics and analytics with activity feed
- 👥 **User Management** - View, edit, ban/unban, and delete users
- 🏪 **Restaurant Management** - Create, edit, delete restaurants and assign owners
  - Interactive map picker for precise location selection
  - Multiple image uploads with preview
  - Simplified working hours input
  - Geocoding from address to coordinates
- 📝 **Review Moderation** - Approve or reject pending reviews (notifications sent only after approval)
- ⚙️ **System Settings** - Configure platform settings
- 📈 **Activity Feed** - Monitor recent user activity and reviews
- 🎯 **Restaurant Ownership** - Assign restaurant owners to manage their listings
- 📱 **Responsive Admin Panel** - Mobile-friendly sidebar with slide-in menu

### 🏪 Restaurant Owner Features
- 🎫 **Claim Restaurant** - Claim and manage your restaurant listing
- 📝 **Edit Restaurant** - Update restaurant details (name, cuisine, hours, features, etc.)
  - Interactive map picker for location updates
  - Multiple image uploads with delete/replace options
  - Simplified working hours management
- 📊 **Dashboard** - View restaurant statistics, reviews, and performance
  - Rating distribution charts
  - Category ratings breakdown (food, service, ambiance, value)
  - Review trends and analytics
- 👀 **View Reviews** - See all reviews for your restaurant with filtering
- 💬 **Respond to Reviews** - Respond to customer reviews to engage with feedback
- ⏰ **Working Hours** - Display and manage restaurant operating hours
- ✨ **Features Management** - Add and manage restaurant features
- 🔔 **Notifications** - Receive notifications when new reviews are approved

### 🛠️ Technical Features
- 🔒 **Session-based Authentication** - Secure session management with improved error handling
- 🖼️ **Polymorphic Images** - Support for images on restaurants and reviews with external URL support
- 🏗️ **Laravel Monolith** - Traditional Laravel application with Blade templates
- 📱 **Responsive Design** - Mobile-first approach with Tailwind CSS
  - Mobile-friendly navigation with slide-in menus
  - Responsive admin panel sidebar
  - Touch-optimized interactions
- 🎨 **Modern UI** - Clean, professional interface with smooth animations
- 🔍 **Advanced Search** - Search by name, cuisine, location, and description
- 🗂️ **Smart Filtering** - Filter by cuisine, price range (Budget/Standard/Exclusive/Premium), rating, location, features, and "open now"
- 🔔 **Notification System** - Real-time notifications with dropdown menu (database + optional email)
- 📊 **Analytics** - Restaurant owner analytics with Chart.js visualizations
- 🎯 **Recommendations Engine** - Personalized restaurant recommendations
- 🗺️ **Interactive Maps** - OpenStreetMap integration with Leaflet.js (free, no API key required)
- ⏰ **Working Hours Management** - Simplified hours input with quick-apply options

## 🚀 Technology Stack

- **Backend & Frontend** (Laravel Monolith): 
  - Laravel 10.x
  - PHP 8.1+
  - MySQL
  - Blade Templates (server-side rendering)
  - Intervention Image (image processing)
  - Session-based authentication
- **Frontend Assets**: 
  - Vanilla JavaScript (ES6+)
  - Tailwind CSS 3.x
  - Font Awesome 6
  - OpenStreetMap with Leaflet.js (free, no API key required)
  - Nominatim API (OpenStreetMap geocoding service)
  - Chart.js (for analytics)
- **Database**: MySQL
- **Image Storage**: Local filesystem with polymorphic relationships (supports external URLs)
- **Notifications**: Laravel Notification system (database + optional email)

## 📋 Requirements

- PHP >= 8.1
- Composer
- MySQL 5.7+ or MariaDB 10.3+
- Python 3.x (for static file serving) OR Node.js OR PHP built-in server
- Modern web browser

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/anonyname5/foodiehub.git
   cd foodiehub
   ```

2. **Install PHP dependencies**
   ```bash
   cd backend
   composer install
   ```

3. **Environment setup**
   ```bash
   cp env.example .env
   php artisan key:generate
   ```

4. **Configure database**
   
   Edit `backend/.env` file and set your database configuration:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=foodiehub
   DB_USERNAME=root
   DB_PASSWORD=your_password
   ```

5. **Create database**
   ```bash
   # Option 1: Using SQL file
   mysql -u root -p < create-database.sql
   
   # Option 2: Using Laravel
   php artisan db:create  # If available, or create manually
   ```

6. **Run migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed database** (optional - creates sample data and admin user)
   ```bash
   php artisan db:seed
   ```

8. **Create storage link**
   ```bash
   php artisan storage:link
   ```

9. **Map Configuration (No Setup Required)**
   
   The application uses **OpenStreetMap with Leaflet.js**, which is completely free and requires no API key. Map features work out of the box without any configuration.
   
   **Note**: The application uses OpenStreetMap's Nominatim service for geocoding (converting addresses to coordinates). This is free but has usage limits for high-volume applications. For production use with high traffic, consider using a commercial geocoding service.

10. **Configure Mail Settings (Optional)**
    
    For email notifications to work, configure mail settings in `backend/.env`:
    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=your-smtp-host
    MAIL_PORT=587
    MAIL_USERNAME=your-username
    MAIL_PASSWORD=your-password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=noreply@yourdomain.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```
    
    **Note**: If mail is not configured, notifications will still work via the in-app dropdown, but email notifications will be disabled automatically.

11. **Start the Laravel server**
    ```bash
    php artisan serve
    ```
    Application runs on `http://127.0.0.1:8000`

    - **Main Site**: `http://127.0.0.1:8000`
    - **Admin Panel**: `http://127.0.0.1:8000/admin` (admin users only)
    - **Restaurant Owner Dashboard**: `http://127.0.0.1:8000/restaurant-owner/dashboard` (restaurant owners only)

### 🚀 Quick Start (Windows)

Simply start the Laravel server:
```bash
cd backend
php artisan serve
```

This will start:
- **Main Application** on `http://127.0.0.1:8000`
- **Admin Panel** on `http://127.0.0.1:8000/admin` (admin users only)
- **Restaurant Owner Dashboard** on `http://127.0.0.1:8000/restaurant-owner/dashboard` (restaurant owners only)

## 🎯 Usage

### Default Admin Credentials

After running the seeder, you can log in with:
- **Email**: `admin@foodiehub.com`
- **Password**: `admin123`

### User Flow

1. **Register/Login** - Create an account or log in (with improved error handling)
2. **Browse Restaurants** - Explore restaurants with advanced search and filters
   - Filter by cuisine, price range (Budget/Standard/Exclusive/Premium), rating, location, features
   - Sort by rating, review count, or newest
   - "Open now" filter for currently open restaurants
3. **View Details** - Click on a restaurant to see:
   - Full details, working hours, features, and contact information
   - Photo gallery with click-to-zoom
   - Interactive map with location
   - Reviews with sorting and filtering options
   - Helpful votes and restaurant owner responses
4. **Write Review** - Share your dining experience with:
   - Multi-dimensional ratings (food, service, ambiance, value)
   - Photos and recommendations
   - Review is submitted as "pending" and requires admin approval
5. **Manage Profile** - Update profile with:
   - Profile picture upload
   - Bio and privacy settings
   - View review history and statistics
   - Manage favorites
6. **Notifications** - Click the bell icon to view:
   - Recent notifications in dropdown menu
   - Mark notifications as read
   - Navigate to related content
7. **Claim Restaurant** (Optional) - Claim a restaurant to become its owner and manage it

### Admin Flow

1. **Login** - Access admin panel with admin credentials (check "Login as admin" checkbox)
2. **Dashboard** - View platform statistics and recent activity
3. **Manage Users** - View, edit, ban/unban, or delete users
4. **Manage Restaurants** - Create, edit, delete restaurants and assign owners
   - Interactive map picker for location selection
   - Geocoding from address to coordinates
   - Multiple image uploads with preview
   - Simplified working hours input
5. **Moderate Reviews** - Approve or reject pending reviews
   - **Important**: Notifications are sent to restaurant owners only after approval
   - Restaurant statistics update only for approved reviews
6. **Settings** - Configure system-wide settings

### Restaurant Owner Flow

1. **Claim Restaurant** - Select and claim an available restaurant
2. **Dashboard** - View restaurant statistics, recent reviews, and performance metrics
   - Rating distribution charts
   - Category ratings breakdown (food, service, ambiance, value)
   - Review trends and analytics
3. **Edit Restaurant** - Update restaurant details (name, cuisine, hours, features, contact info)
   - Interactive map picker for location updates
   - Multiple image uploads with delete/replace options
   - Simplified working hours management
4. **View Reviews** - See all reviews for your restaurant with filtering options
5. **Respond to Reviews** - Engage with customers by responding to their reviews
6. **Notifications** - Receive notifications when:
   - New reviews are approved (not sent for pending reviews)
   - Users respond to your review responses
7. **Monitor Performance** - Track ratings, review counts, and favorites

**Note**: Restaurant owners can only edit their own restaurant. They cannot delete restaurants or change active status (admin only).

## 🔔 Notification System

### How Notifications Work

1. **Review Submission Flow**:
   - User submits a review → Status: `pending`
   - **No notification sent** to restaurant owner at this stage
   - Admin reviews the pending review
   - Admin approves review → Status: `approved`
   - **Notification sent** to restaurant owner (in-app + optional email)
   - Restaurant statistics updated (only approved reviews count)

2. **Review Response Flow**:
   - Restaurant owner responds to a review
   - **Notification sent** to the review author (in-app + optional email)

3. **Notification Display**:
   - Click the bell icon in the navbar to open dropdown menu
   - View recent notifications without leaving the current page
   - Mark individual notifications as read
   - Mark all notifications as read
   - Click "View all notifications" for full page view

4. **Email Notifications**:
   - Automatically enabled if mail is configured in `.env`
   - Gracefully disabled if mail is not configured (no errors)
   - Users can control email preferences in profile settings

## 📝 Review Approval Workflow

All reviews require admin approval before being published:

1. **User submits review** → Status: `pending`
2. **Admin reviews** → Approve or reject
3. **If approved**:
   - Review status: `approved`
   - Notification sent to restaurant owner
   - Restaurant statistics updated
   - Review appears on restaurant page
4. **If rejected**:
   - Review status: `rejected`
   - Review hidden from public view
   - No notification sent
   - Statistics remain unchanged

## 📁 Project Structure

```
Restaurant Review/
├── backend/                    # Laravel Monolith Application
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   ├── Admin/            # Admin panel controllers
│   │   │   │   │   └── AdminController.php
│   │   │   │   ├── AuthController.php    # Web authentication
│   │   │   │   ├── HomeController.php    # Homepage with recommendations
│   │   │   │   ├── ProfileController.php # User profile with avatar upload
│   │   │   │   ├── RestaurantController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── RestaurantOwnerController.php  # Restaurant owner management
│   │   │   │   ├── ImageController.php            # Image uploads
│   │   │   │   ├── NotificationController.php    # Notification management
│   │   │   │   ├── HelpfulVoteController.php      # Helpful votes
│   │   │   │   ├── ReviewResponseController.php   # Review responses
│   │   │   │   └── Api/            # Legacy API controllers (not used)
│   │   │   │       ├── AdminController.php
│   │   │   │       ├── AuthController.php
│   │   │   │       ├── RestaurantController.php
│   │   │   │       ├── ReviewController.php
│   │   │   │       ├── UserController.php
│   │   │   │       └── ImageController.php
│   │   │   └── Middleware/         # Auth, Admin & Restaurant Owner middleware
│   │   │       ├── AdminMiddleware.php
│   │   │       └── RestaurantOwnerMiddleware.php
│   │   ├── Models/                 # Eloquent models
│   │   │   ├── User.php
│   │   │   ├── Restaurant.php
│   │   │   ├── Review.php
│   │   │   ├── Image.php
│   │   │   ├── HelpfulVote.php
│   │   │   └── ReviewResponse.php
│   │   └── Notifications/          # Notification classes
│   │       ├── NewReviewNotification.php
│   │       └── ReviewResponseNotification.php
│   ├── database/
│   │   ├── migrations/            # Database migrations
│   │   └── seeders/               # Database seeders
│   ├── resources/
│   │   └── views/                 # Blade templates
│   │       ├── layouts/           # Layout templates
│   │       ├── layouts/           # Layout templates
│   │       ├── admin/             # Admin panel views
│   │       ├── restaurants/       # Restaurant views
│   │       ├── reviews/           # Review views
│   │       ├── profile/           # Profile views
│   │       ├── restaurant-owner/  # Restaurant owner views
│   │       └── notifications/     # Notification views
│   ├── routes/
│   │   ├── web.php                # All web routes
│   │   └── api.php                # Legacy (cleaned/empty)
│   ├── public/
│   │   ├── assets/                # Frontend assets
│   │   ├── admin-assets/          # Admin panel assets
│   │   └── index.php              # Laravel entry point
│   └── storage/                   # File storage
├── frontend/                      # Legacy frontend (can be deleted)
└── README.md                      # This file
```

## 🔐 Web Routes

### Public Routes
- `GET /` - Homepage
- `GET /restaurants` - Restaurant listing
- `GET /restaurants/{id}` - Restaurant detail
- `POST /login` - User login
- `POST /register` - User registration

### Authenticated Routes
- `GET /profile` - User profile
- `PUT /profile` - Update profile (including avatar upload)
- `GET /reviews/create` - Create review form
- `POST /reviews` - Submit review (status: pending, requires admin approval)
- `GET /reviews/{id}/edit` - Edit review form
- `PUT /reviews/{id}` - Update review
- `DELETE /reviews/{id}` - Delete review
- `POST /reviews/{id}/helpful` - Toggle helpful vote on review
- `POST /reviews/{id}/response` - Restaurant owner responds to review
- `PUT /reviews/{id}/response` - Update review response
- `DELETE /reviews/{id}/response` - Delete review response
- `GET /notifications` - View all notifications (full page)
- `GET /notifications/unread-count` - Get unread notification count (AJAX)
- `GET /notifications/recent` - Get recent notifications for dropdown (AJAX)
- `POST /notifications/{id}/read` - Mark notification as read
- `POST /notifications/read-all` - Mark all notifications as read
- `POST /logout` - Logout

### Admin Routes (Admin Only)
- `GET /admin` - Admin dashboard
- `GET /admin/users` - User management
- `GET /admin/users/{id}` - User details
- `PUT /admin/users/{id}` - Update user
- `POST /admin/users/{id}/ban` - Ban user
- `POST /admin/users/{id}/unban` - Unban user
- `DELETE /admin/users/{id}` - Delete user
- `GET /admin/restaurants` - Restaurant management
- `GET /admin/restaurants/create` - Create restaurant form
- `POST /admin/restaurants` - Store new restaurant
- `GET /admin/restaurants/{id}/edit` - Edit restaurant form
- `PUT /admin/restaurants/{id}` - Update restaurant
- `DELETE /admin/restaurants/{id}` - Delete restaurant
- `POST /admin/restaurants/{id}/toggle-status` - Toggle active/inactive status
- `GET /admin/reviews` - Review moderation
- `POST /admin/reviews/{id}/approve` - Approve review
- `POST /admin/reviews/{id}/reject` - Reject review
- `GET /admin/settings` - System settings
- `PUT /admin/settings` - Update settings

### Restaurant Owner Routes (Restaurant Owners Only)
- `GET /restaurant-owner/claim` - Claim restaurant page
- `POST /restaurant-owner/claim` - Claim a restaurant
- `GET /restaurant-owner/dashboard` - Restaurant owner dashboard
- `GET /restaurant-owner/edit` - Edit restaurant form
- `PUT /restaurant-owner/update` - Update restaurant details
- `GET /restaurant-owner/reviews` - View restaurant reviews


## 🗄️ Database Schema

### Main Models
- **User** - Users with admin roles, restaurant owner roles, and profile information
- **Restaurant** - Restaurant listings with location, ratings, working hours, and features
  - Price Range: `Budget`, `Standard`, `Exclusive`, or `Premium`
- **Review** - User reviews with multi-dimensional ratings, status (pending/approved/rejected), and helpful votes
- **Image** - Polymorphic image storage (restaurants & reviews, supports external URLs)
- **Favorite** - User-restaurant favorites relationship
- **HelpfulVote** - User votes on helpful reviews
- **ReviewResponse** - Restaurant owner responses to reviews
- **Notification** - Laravel notifications (database table)

### User Roles
- **Regular User** - Can browse, review, and manage profile
- **Restaurant Owner** - Can claim and manage their restaurant
- **Admin** - Full platform management access

### Key Relationships
- User has many Reviews
- User belongs to many Restaurants (favorites)
- User belongs to one Restaurant (as owner) - `restaurant_id`
- User has many Notifications
- Restaurant belongs to one User (owner) - `owner_id`
- Restaurant has many Reviews
- Restaurant has many Images (polymorphic)
- Review belongs to User and Restaurant
- Review has many Images (polymorphic)
- Review has many HelpfulVotes
- Review has one ReviewResponse (from restaurant owner)
- ReviewResponse belongs to Review and User (owner)

## 🎨 Customization

### Changing App Name
Edit `backend/.env`:
```env
APP_NAME="Your App Name"
```

### Image Upload Settings
Edit `backend/.env`:
```env
MAX_FILE_SIZE=10240  # Max file size in KB
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
```

### Styling
- Main styles: `backend/public/assets/css/style.css`
- Admin styles: `backend/public/admin-assets/assets/css/admin.css`
- Uses Tailwind CSS via CDN
- Custom animations and components in style.css

## 🧪 Testing

```bash
# Run backend tests (when implemented)
cd backend
php artisan test
```

## 📝 Development Status

### ✅ Completed
- Laravel Monolith Application (100%)
- All User-Facing Pages (Blade Templates)
- Admin Panel (Blade Templates)
- Authentication System (Session-based with improved error handling)
- **Two-Tier Restaurant Management System**
  - Admin: Full control (create, edit, delete, assign owners)
  - Restaurant Owners: Manage their own restaurant
- Restaurant Claiming System
- Restaurant Owner Dashboard with Analytics
- Working Hours Display and Management
- Restaurant Features Display
- Review System with Approval Workflow
- Image Upload System (restaurants and reviews)
- Admin Panel Integration
- **Notification System**
  - Dropdown notification center
  - Real-time unread count badge
  - Notifications sent only after review approval
  - Email notifications (optional, requires mail configuration)
- **Review Features**
  - Helpful votes on reviews
  - Restaurant owner responses to reviews
  - Verified review badges
  - Review sorting and filtering
- **Photo Galleries**
  - Click-to-zoom modal for restaurant photos
  - Multiple image uploads for restaurants
  - Image preview and management
- **Interactive Maps**
  - Google Maps integration
  - Interactive location picker for admin
  - Geocoding from address to coordinates
- **User Profile**
  - Profile picture upload
  - Bio and privacy settings
- **Responsive Design**
  - Mobile-friendly navigation (slide-in menu)
  - Responsive admin panel sidebar
  - Touch-optimized interactions
- **Price Range System**
  - Budget, Standard, Exclusive, Premium categories
- **Recommendations Engine**
  - Personalized restaurant recommendations
  - Based on user preferences and top-rated restaurants

### 🔮 Planned
- Unit and Integration Tests
- Image Optimization
- SEO Improvements
- Progressive Web App (PWA)
- Real-time Notifications (WebSocket/Pusher)
- Advanced Analytics
- Social Sharing Features
- Review Photo Moderation

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

### Development Guidelines
- Follow Laravel coding standards for backend
- Use Tailwind CSS for styling
- Keep JavaScript vanilla (no frameworks)
- Write clear commit messages
- Update documentation as needed

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📧 Support

For support, email ahmdsyukri09@gmail.com or open an issue in the repository.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Font Awesome](https://fontawesome.com) - Icon library
- [Intervention Image](https://image.intervention.io) - Image processing library
- [OpenStreetMap](https://www.openstreetmap.org) - Free and open-source mapping data
- [Leaflet.js](https://leafletjs.com) - Interactive maps library
- [Nominatim](https://nominatim.org) - OpenStreetMap geocoding service

---

⭐ If you find this project helpful, please consider giving it a star!
