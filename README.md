# 🍽️ FoodieHub - Restaurant Review Platform

A full-featured restaurant review platform built with Laravel 10 backend API and vanilla JavaScript frontend. Users can discover restaurants, write detailed reviews with photos, and manage their profiles. Complete admin panel backend ready (frontend in development).

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

### 🔧 Admin Features (Backend Ready)
- 📊 **Dashboard** - Real-time statistics and analytics
- 👥 **User Management** - View, edit, ban/unban, and delete users
- 🏪 **Restaurant Management** - Manage restaurant listings and details
- 📝 **Review Moderation** - Approve or reject pending reviews
- ⚙️ **System Settings** - Configure platform settings
- 📈 **Activity Feed** - Monitor recent user activity and reviews

### 🛠️ Technical Features
- 🔒 **Session-based Authentication** - Secure session management
- 🖼️ **Polymorphic Images** - Support for images on restaurants and reviews
- 📦 **RESTful API** - Clean, well-documented API endpoints
- 📱 **Responsive Design** - Mobile-first approach with Tailwind CSS
- 🎨 **Modern UI** - Clean, professional interface with smooth animations
- 🔍 **Advanced Search** - Search by name, cuisine, location, and description
- 🗂️ **Smart Filtering** - Filter by cuisine, price range, rating, and location

## 🚀 Technology Stack

- **Backend**: 
  - Laravel 10.x
  - PHP 8.1+
  - MySQL
  - Intervention Image (image processing)
  - Laravel Sanctum (session-based auth)
- **Frontend**: 
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

9. **Start the backend server**
   ```bash
   php artisan serve
   ```
   Backend API runs on `http://127.0.0.1:8000`

10. **Start the frontend server** (in a new terminal)
    ```bash
    cd frontend/public
    
    # Using Python (recommended)
    python -m http.server 3000
    
    # OR using Node.js
    npx http-server -p 3000
    
    # OR using PHP
    php -S localhost:3000
    ```
    Frontend runs on `http://localhost:3000`

11. **Start admin panel** (when available)
    ```bash
    cd admin/public
    python -m http.server 3001
    ```
    Admin panel runs on `http://localhost:3001`

### 🚀 Quick Start (Windows)

Use the automated script:
```bash
start-dev-servers.bat
```

This will start:
- Backend API on `http://127.0.0.1:8000`
- User Frontend on `http://localhost:3000`
- Admin Panel on `http://localhost:3001` (when created)

## 🎯 Usage

### Default Admin Credentials

After running the seeder, you can log in with:
- **Email**: `admin@foodiehub.com`
- **Password**: `admin123`

### User Flow

1. **Register/Login** - Create an account or log in
2. **Browse Restaurants** - Explore restaurants with search and filters
3. **View Details** - Click on a restaurant to see full details, photos, and reviews
4. **Write Review** - Share your dining experience with ratings and photos
5. **Manage Profile** - Update your profile, view your reviews, and manage favorites

### Admin Flow

1. **Login** - Access admin panel with admin credentials
2. **Dashboard** - View platform statistics and recent activity
3. **Manage Users** - View, edit, ban/unban, or delete users
4. **Moderate Reviews** - Approve or reject pending reviews
5. **Manage Restaurants** - Update restaurant information and status
6. **Settings** - Configure system-wide settings

## 📁 Project Structure

```
foodiehub/
├── backend/                    # Laravel API Backend
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/Api/    # API Controllers
│   │   │   │   ├── AdminController.php
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── RestaurantController.php
│   │   │   │   ├── ReviewController.php
│   │   │   │   ├── UserController.php
│   │   │   │   └── ImageController.php
│   │   │   └── Middleware/         # Auth & Admin middleware
│   │   └── Models/                 # Eloquent models
│   ├── database/
│   │   ├── migrations/            # Database migrations
│   │   └── seeders/               # Database seeders
│   ├── routes/
│   │   └── api.php                # API routes
│   └── storage/                   # File storage
├── frontend/                      # User-facing Frontend
│   └── public/
│       ├── index.html             # Homepage
│       ├── pages/                 # Application pages
│       │   ├── restaurants/       # Restaurant pages
│       │   ├── profile/            # User profile
│       │   └── reviews/            # Review management
│       └── assets/
│           ├── css/                # Stylesheets
│           ├── js/                 # JavaScript modules
│           └── images/             # Images and icons
├── admin/                         # Admin Panel (in development)
│   └── public/                    # Admin frontend files
├── start-dev-servers.bat          # Windows dev script
├── start-dev-servers.sh            # Linux/Mac dev script
└── README.md                      # This file
```

## 🔐 API Routes

### Authentication Routes
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/auth/check` - Check authentication status
- `POST /api/auth/logout` - User logout
- `GET /api/auth/profile` - Get user profile
- `PUT /api/auth/profile` - Update user profile

### Restaurant Routes
- `GET /api/restaurants` - List restaurants (with filtering)
- `GET /api/restaurants/{id}` - Get restaurant details
- `GET /api/restaurants/filter-options` - Get filter options
- `GET /api/restaurants/{id}/reviews` - Get restaurant reviews
- `GET /api/restaurants/{id}/rating-breakdown` - Get rating breakdown
- `GET /api/restaurants/{id}/related` - Get related restaurants
- `GET /api/statistics` - Get platform statistics

### Review Routes
- `GET /api/reviews` - List reviews
- `POST /api/reviews` - Create review (authenticated)
- `GET /api/reviews/{id}` - Get review details
- `PUT /api/reviews/{id}` - Update review (authenticated)
- `DELETE /api/reviews/{id}` - Delete review (authenticated)
- `GET /api/reviews/my/reviews` - Get user's reviews (authenticated)

### User Routes
- `GET /api/users/{id}` - Get user profile
- `PUT /api/users/{id}` - Update user (authenticated)
- `GET /api/users/{id}/reviews` - Get user's reviews
- `GET /api/users/{id}/favorites` - Get user's favorites
- `POST /api/users/{id}/favorites` - Add favorite (authenticated)
- `DELETE /api/users/{id}/favorites/{restaurant_id}` - Remove favorite (authenticated)

### Image Routes
- `POST /api/images/upload` - Upload image (authenticated)
- `DELETE /api/images/{id}` - Delete image (authenticated)
- `PUT /api/images/{id}/primary` - Set primary image (authenticated)
- `PUT /api/images/reorder` - Reorder images (authenticated)

### Admin Routes (Requires Admin Authentication)
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

## 🗄️ Database Schema

### Main Models
- **User** - Users with admin roles and profile information
- **Restaurant** - Restaurant listings with location and ratings
- **Review** - User reviews with multi-dimensional ratings
- **Image** - Polymorphic image storage (restaurants & reviews)
- **Favorite** - User-restaurant favorites relationship

### Key Relationships
- User has many Reviews
- User belongs to many Restaurants (favorites)
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

### API Base URL
Edit `frontend/public/assets/js/api.js`:
```javascript
this.baseURL = 'http://127.0.0.1:8000/api';
```

### Styling
- Main styles: `frontend/public/assets/css/style.css`
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
- Backend API (100%)
- User Frontend (100%)
- Authentication System
- Restaurant Management
- Review System
- Image Upload System
- Admin Backend API

### 🚧 In Development
- Admin Panel Frontend (0%)

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
