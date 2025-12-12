# 🍽️ FoodieHub - Restaurant Review Platform

A full-featured restaurant review platform built as a Laravel monolith. Users can discover restaurants, write detailed reviews with photos, and manage their profiles. Features a two-tier management system: platform admins manage everything, while restaurant owners can claim and manage their own restaurants. Complete admin panel and restaurant owner dashboard integrated with the main application.

![Laravel](https://img.shields.io/badge/Laravel-10.x-FF2D20?style=flat-square&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php)
![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat-square&logo=javascript)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat-square&logo=tailwind-css)
![License](https://img.shields.io/badge/license-MIT-blue?style=flat-square)

## ✨ Features

### 👥 User Features
- 🔐 **Authentication** - User registration, login, and session management
- 🏪 **Restaurant Discovery** - Browse restaurants with advanced search and filtering
- ⭐ **Multi-dimensional Ratings** - Rate restaurants on food, service, ambiance, and value
- 📝 **Rich Reviews** - Write detailed reviews with photos and recommendations
- 📸 **Photo Uploads** - Upload multiple images per review
- ❤️ **Favorites** - Save favorite restaurants for quick access
- 👤 **User Profiles** - Manage profile with avatar, bio, and privacy settings
- 📊 **Personal Stats** - View review history and statistics
- 🗺️ **Location Features** - Location-based search with Google Maps integration
- ⏰ **Restaurant Details** - View working hours, features, and contact information

### 🔧 Admin Features
- 📊 **Dashboard** - Real-time statistics and analytics
- 👥 **User Management** - View, edit, ban/unban, and delete users
- 🏪 **Restaurant Management** - Create, edit, delete restaurants and assign owners
- 📝 **Review Moderation** - Approve or reject pending reviews
- ⚙️ **System Settings** - Configure platform settings
- 📈 **Activity Feed** - Monitor recent user activity and reviews
- 🎯 **Restaurant Ownership** - Assign restaurant owners to manage their listings

### 🏪 Restaurant Owner Features
- 🎫 **Claim Restaurant** - Claim and manage your restaurant listing
- 📝 **Edit Restaurant** - Update restaurant details (name, cuisine, hours, features, etc.)
- 📊 **Dashboard** - View restaurant statistics, reviews, and performance
- 👀 **View Reviews** - See all reviews for your restaurant
- ⏰ **Working Hours** - Display and manage restaurant operating hours
- ✨ **Features Management** - Add and manage restaurant features

### 🛠️ Technical Features
- 🔒 **Session-based Authentication** - Secure session management
- 🖼️ **Polymorphic Images** - Support for images on restaurants and reviews
- 🏗️ **Laravel Monolith** - Traditional Laravel application with Blade templates
- 📱 **Responsive Design** - Mobile-first approach with Tailwind CSS
- 🎨 **Modern UI** - Clean, professional interface with smooth animations
- 🔍 **Advanced Search** - Search by name, cuisine, location, and description
- 🗂️ **Smart Filtering** - Filter by cuisine, price range, rating, and location

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
  - Google Maps API
- **Database**: MySQL
- **Image Storage**: Local filesystem with polymorphic relationships

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

9. **Start the Laravel server**
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

1. **Register/Login** - Create an account or log in
2. **Browse Restaurants** - Explore restaurants with search and filters
3. **View Details** - Click on a restaurant to see full details, working hours, features, photos, and reviews
4. **Write Review** - Share your dining experience with ratings and photos
5. **Manage Profile** - Update your profile, view your reviews, and manage favorites
6. **Claim Restaurant** (Optional) - Claim a restaurant to become its owner and manage it

### Admin Flow

1. **Login** - Access admin panel with admin credentials (check "Login as admin" checkbox)
2. **Dashboard** - View platform statistics and recent activity
3. **Manage Users** - View, edit, ban/unban, or delete users
4. **Manage Restaurants** - Create, edit, delete restaurants and assign owners
5. **Moderate Reviews** - Approve or reject pending reviews
6. **Settings** - Configure system-wide settings

### Restaurant Owner Flow

1. **Claim Restaurant** - Select and claim an available restaurant
2. **Dashboard** - View restaurant statistics, recent reviews, and performance metrics
3. **Edit Restaurant** - Update restaurant details (name, cuisine, hours, features, contact info)
4. **View Reviews** - See all reviews for your restaurant with filtering options
5. **Monitor Performance** - Track ratings, review counts, and favorites

**Note**: Restaurant owners can only edit their own restaurant. They cannot delete restaurants or change active status (admin only).

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
│   │   │   │   ├── HomeController.php    # Homepage
│   │   │   │   ├── ProfileController.php # User profile
│   │   │   │   ├── RestaurantController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── RestaurantOwnerController.php  # Restaurant owner management
│   │   │   │   ├── ImageController.php            # Image uploads
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
│   │   └── Models/                 # Eloquent models
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
│   │       └── restaurant-owner/  # Restaurant owner views
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
- `PUT /profile` - Update profile
- `GET /reviews/create` - Create review form
- `POST /reviews` - Submit review
- `GET /reviews/{id}/edit` - Edit review form
- `PUT /reviews/{id}` - Update review
- `DELETE /reviews/{id}` - Delete review
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
- **Review** - User reviews with multi-dimensional ratings
- **Image** - Polymorphic image storage (restaurants & reviews)
- **Favorite** - User-restaurant favorites relationship

### User Roles
- **Regular User** - Can browse, review, and manage profile
- **Restaurant Owner** - Can claim and manage their restaurant
- **Admin** - Full platform management access

### Key Relationships
- User has many Reviews
- User belongs to many Restaurants (favorites)
- User belongs to one Restaurant (as owner) - `restaurant_id`
- Restaurant belongs to one User (owner) - `owner_id`
- Restaurant has many Reviews
- Restaurant has many Images (polymorphic)
- Review belongs to User and Restaurant
- Review has many Images (polymorphic)

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
- Authentication System (Session-based)
- **Two-Tier Restaurant Management System**
  - Admin: Full control (create, edit, delete, assign owners)
  - Restaurant Owners: Manage their own restaurant
- Restaurant Claiming System
- Restaurant Owner Dashboard
- Working Hours Display
- Restaurant Features Display
- Review System
- Image Upload System
- Admin Panel Integration

### 🔮 Planned
- Unit and Integration Tests
- Image Optimization
- SEO Improvements
- Progressive Web App (PWA)
- Real-time Notifications
- Advanced Analytics

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

For support, email support@foodiehub.com or open an issue in the repository.

## 🙏 Acknowledgments

- [Laravel](https://laravel.com) - The PHP Framework
- [Tailwind CSS](https://tailwindcss.com) - Utility-first CSS framework
- [Font Awesome](https://fontawesome.com) - Icon library
- [Intervention Image](https://image.intervention.io) - Image processing library
- [Google Maps](https://developers.google.com/maps) - Maps integration

---

⭐ If you find this project helpful, please consider giving it a star!
