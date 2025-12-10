# FoodieHub Backend Setup Guide

This guide will help you set up the Laravel backend for the FoodieHub restaurant review platform.

## Prerequisites

Before you begin, make sure you have the following installed on your system:

- **PHP 8.1 or higher**
- **Composer** (PHP dependency manager)
- **MySQL 5.7 or higher**
- **Node.js** (for frontend assets, optional)

## Installation Steps

### 1. Install Dependencies

```bash
# Navigate to the backend directory
cd backend

# Install PHP dependencies
composer install
```

### 2. Environment Configuration

```bash
# Copy the environment file
cp env.example .env

# Generate application key
php artisan key:generate
```

### 3. Database Setup (XAMPP)

1. **Start XAMPP:**
   - Open XAMPP Control Panel
   - Start **Apache** and **MySQL** services

2. **Create Database:**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Click "New" to create a new database
   - Database name: `foodiehub`
   - Collation: `utf8mb4_unicode_ci`
   - Click "Create"

3. **Update .env file** with XAMPP database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=foodiehub
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

5. **Seed the Database:**
   ```bash
   php artisan db:seed
   ```

### 4. Start the Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

## API Endpoints

### Authentication
- `POST /api/auth/register` - Register new user
- `POST /api/auth/login` - Login user
- `POST /api/auth/logout` - Logout user
- `GET /api/auth/profile` - Get user profile
- `PUT /api/auth/profile` - Update user profile
- `GET /api/auth/check` - Check authentication status

### Restaurants
- `GET /api/restaurants` - List restaurants (with filters)
- `GET /api/restaurants/{id}` - Get restaurant details
- `GET /api/restaurants/{id}/reviews` - Get restaurant reviews
- `GET /api/restaurants/{id}/rating-breakdown` - Get rating breakdown
- `GET /api/restaurants/{id}/related` - Get related restaurants
- `GET /api/restaurants/filter-options` - Get filter options

### Reviews
- `GET /api/reviews` - List reviews
- `GET /api/reviews/{id}` - Get review details
- `POST /api/reviews` - Create review (authenticated)
- `PUT /api/reviews/{id}` - Update review (authenticated)
- `DELETE /api/reviews/{id}` - Delete review (authenticated)
- `GET /api/reviews/my/reviews` - Get user's reviews (authenticated)

### Users
- `GET /api/users/{id}` - Get user profile
- `PUT /api/users/{id}` - Update user profile (authenticated)
- `GET /api/users/{id}/reviews` - Get user's reviews
- `GET /api/users/{id}/favorites` - Get user's favorites
- `POST /api/users/{id}/favorites` - Add favorite (authenticated)
- `DELETE /api/users/{id}/favorites/{restaurant_id}` - Remove favorite (authenticated)

## Testing the API

### Using cURL

1. **Register a new user:**
   ```bash
   curl -X POST http://localhost:8000/api/auth/register \
     -H "Content-Type: application/json" \
     -d '{
       "name": "John Doe",
       "email": "john@example.com",
       "password": "password123",
       "password_confirmation": "password123"
     }'
   ```

2. **Login:**
   ```bash
   curl -X POST http://localhost:8000/api/auth/login \
     -H "Content-Type: application/json" \
     -d '{
       "email": "john@example.com",
       "password": "password123"
     }'
   ```

3. **Get restaurants:**
   ```bash
   curl http://localhost:8000/api/restaurants
   ```

### Using Postman

1. Import the API collection (if available)
2. Set the base URL to `http://localhost:8000/api`
3. Test the endpoints

## Database Structure

### Tables Created:
- `users` - User accounts and profiles
- `restaurants` - Restaurant information
- `reviews` - User reviews and ratings
- `favorites` - User favorite restaurants

### Sample Data:
The seeder creates 6 sample restaurants with different cuisines and locations.

## Configuration

### File Upload Settings
Update these settings in your `.env` file:
```env
MAX_FILE_SIZE=10240
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
```

### Session Configuration
The API uses session-based authentication. Sessions are stored in files by default.

## Troubleshooting

### Common Issues:

1. **Database Connection Error:**
   - Check your database credentials in `.env`
   - Ensure MySQL is running
   - Verify the database exists

2. **Permission Errors:**
   - Make sure the `storage` and `bootstrap/cache` directories are writable
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

3. **Composer Issues:**
   - Update Composer: `composer self-update`
   - Clear cache: `composer clear-cache`

4. **Migration Errors:**
   - Reset database: `php artisan migrate:fresh --seed`

## Development Tips

1. **Enable Debug Mode:**
   Set `APP_DEBUG=true` in `.env` for detailed error messages

2. **View Routes:**
   ```bash
   php artisan route:list
   ```

3. **Clear Cache:**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   ```

4. **Database Seeding:**
   ```bash
   php artisan db:seed --class=RestaurantSeeder
   ```

## Next Steps

1. **Frontend Integration:** Connect your frontend to these API endpoints
2. **File Upload:** Implement image upload functionality
3. **Email Verification:** Add email verification for user registration
4. **Rate Limiting:** Implement API rate limiting
5. **Caching:** Add Redis caching for better performance

## Support

If you encounter any issues, check the Laravel documentation or create an issue in the project repository.
