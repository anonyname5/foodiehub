# FoodieHub Backend Setup for XAMPP

This guide is specifically designed for users running XAMPP on Windows, macOS, or Linux.

## Prerequisites

- **XAMPP** installed and running
- **PHP 8.1+** (included with XAMPP)
- **Composer** installed globally
- **Command Line/Terminal** access

## Quick Setup (Windows)

### Option 1: Automated Setup
1. **Run the setup script:**
   ```cmd
   setup-xampp.bat
   ```
2. **Follow the manual steps** shown in the script output

### Option 2: Manual Setup
Follow the detailed steps below.

## Detailed Setup Steps

### 1. Start XAMPP Services

1. **Open XAMPP Control Panel**
2. **Start the following services:**
   - ✅ **Apache** (for web server)
   - ✅ **MySQL** (for database)

### 2. Create Database

#### Method A: Using phpMyAdmin (Recommended)
1. **Open phpMyAdmin:** `http://localhost/phpmyadmin`
2. **Click "New"** in the left sidebar
3. **Database name:** `foodiehub`
4. **Collation:** `utf8mb4_unicode_ci`
5. **Click "Create"**

#### Method B: Using SQL File
1. **Open phpMyAdmin:** `http://localhost/phpmyadmin`
2. **Click "Import"** tab
3. **Choose file:** Select `database-setup.sql` from the backend folder
4. **Click "Go"**

#### Method C: Using Command Line
```bash
# Open MySQL command line (if available)
mysql -u root -p

# Run the SQL commands
CREATE DATABASE foodiehub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE foodiehub;
```

### 3. Install Backend Dependencies

```bash
# Navigate to backend directory
cd backend

# Install PHP dependencies
composer install
```

### 4. Configure Environment

```bash
# Copy environment file
copy env.example .env

# Generate application key
php artisan key:generate
```

### 5. Update Database Configuration

Edit the `.env` file and ensure these settings:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=foodiehub
DB_USERNAME=root
DB_PASSWORD=
```

**Note:** Leave `DB_PASSWORD` empty for default XAMPP MySQL setup.

### 6. Run Database Migrations

```bash
# Create database tables
php artisan migrate

# Seed with sample data
php artisan db:seed
```

### 7. Start the Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000`

## Testing the Setup

### 1. Check API Health
Open in browser: `http://localhost:8000/api/health`

Expected response:
```json
{
    "success": true,
    "message": "API is running",
    "timestamp": "2024-01-15T10:30:00.000000Z"
}
```

### 2. Test Restaurant Endpoint
Open in browser: `http://localhost:8000/api/restaurants`

You should see a list of 6 sample restaurants.

### 3. Test Registration
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

## XAMPP-Specific Notes

### Default XAMPP Configuration
- **MySQL Port:** 3306
- **MySQL Username:** root
- **MySQL Password:** (empty)
- **phpMyAdmin:** `http://localhost/phpmyadmin`

### Common XAMPP Issues

#### 1. Port Conflicts
If port 8000 is busy, use a different port:
```bash
php artisan serve --port=8080
```

#### 2. MySQL Won't Start
- Check if another MySQL service is running
- Change MySQL port in XAMPP if needed
- Restart XAMPP services

#### 3. Permission Issues (Windows)
Run Command Prompt as Administrator if you encounter permission errors.

#### 4. Composer Not Found
- Add Composer to your system PATH
- Or use full path: `C:\xampp\php\composer.phar install`

### XAMPP File Locations

**Windows:**
- XAMPP: `C:\xampp\`
- MySQL: `C:\xampp\mysql\`
- PHP: `C:\xampp\php\`

**macOS:**
- XAMPP: `/Applications/XAMPP/`
- MySQL: `/Applications/XAMPP/xamppfiles/var/mysql/`

**Linux:**
- XAMPP: `/opt/lampp/`
- MySQL: `/opt/lampp/var/mysql/`

## Development Workflow

### Daily Development
1. **Start XAMPP** (Apache + MySQL)
2. **Navigate to backend folder**
3. **Start Laravel server:** `php artisan serve`
4. **Access API:** `http://localhost:8000/api`

### Making Changes
1. **Edit code** in your IDE
2. **Clear cache** if needed: `php artisan cache:clear`
3. **Test endpoints** in browser or Postman

### Database Changes
1. **Create migration:** `php artisan make:migration add_new_column`
2. **Edit migration file**
3. **Run migration:** `php artisan migrate`
4. **Rollback if needed:** `php artisan migrate:rollback`

## Troubleshooting

### Database Connection Issues
```bash
# Test database connection
php artisan tinker
# Then run: DB::connection()->getPdo();
```

### Clear All Caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Check Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

## Next Steps

1. **Test all API endpoints** using Postman or browser
2. **Connect your frontend** to the API
3. **Add file upload functionality** for images
4. **Implement additional features** as needed

## Support

If you encounter issues:
1. Check XAMPP error logs
2. Verify all services are running
3. Check Laravel logs in `storage/logs/`
4. Ensure database exists and is accessible

The backend is now ready to power your FoodieHub restaurant review platform!
