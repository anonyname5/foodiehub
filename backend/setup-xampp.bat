@echo off
echo ========================================
echo FoodieHub Backend Setup for XAMPP
echo ========================================
echo.

echo Step 1: Checking if Composer is installed...
composer --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Composer is not installed or not in PATH
    echo Please install Composer from https://getcomposer.org/
    pause
    exit /b 1
)
echo Composer found!

echo.
echo Step 2: Installing PHP dependencies...
composer install
if %errorlevel% neq 0 (
    echo ERROR: Failed to install dependencies
    pause
    exit /b 1
)
echo Dependencies installed successfully!

echo.
echo Step 3: Setting up environment file...
if not exist .env (
    copy env.example .env
    echo Environment file created from example
) else (
    echo Environment file already exists
)

echo.
echo Step 4: Generating application key...
php artisan key:generate
if %errorlevel% neq 0 (
    echo ERROR: Failed to generate application key
    pause
    exit /b 1
)
echo Application key generated!

echo.
echo ========================================
echo IMPORTANT: Manual Steps Required
echo ========================================
echo.
echo 1. Start XAMPP Control Panel
echo 2. Start Apache and MySQL services
echo 3. Open phpMyAdmin: http://localhost/phpmyadmin
echo 4. Create database named 'foodiehub'
echo 5. Update .env file with your database credentials
echo.
echo Database settings for XAMPP:
echo DB_HOST=127.0.0.1
echo DB_PORT=3306
echo DB_DATABASE=foodiehub
echo DB_USERNAME=root
echo DB_PASSWORD=
echo.
echo After completing these steps, run:
echo php artisan migrate
echo php artisan db:seed
echo php artisan serve
echo.
echo ========================================
echo Setup completed! Press any key to exit.
echo ========================================
pause
