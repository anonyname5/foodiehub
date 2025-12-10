#!/bin/bash

echo "========================================"
echo "FoodieHub Backend Setup for XAMPP"
echo "========================================"
echo

echo "Step 1: Checking if Composer is installed..."
if ! command -v composer &> /dev/null; then
    echo "ERROR: Composer is not installed or not in PATH"
    echo "Please install Composer from https://getcomposer.org/"
    exit 1
fi
echo "Composer found!"

echo
echo "Step 2: Installing PHP dependencies..."
composer install
if [ $? -ne 0 ]; then
    echo "ERROR: Failed to install dependencies"
    exit 1
fi
echo "Dependencies installed successfully!"

echo
echo "Step 3: Setting up environment file..."
if [ ! -f .env ]; then
    cp env.example .env
    echo "Environment file created from example"
else
    echo "Environment file already exists"
fi

echo
echo "Step 4: Generating application key..."
php artisan key:generate
if [ $? -ne 0 ]; then
    echo "ERROR: Failed to generate application key"
    exit 1
fi
echo "Application key generated!"

echo
echo "========================================"
echo "IMPORTANT: Manual Steps Required"
echo "========================================"
echo
echo "1. Start XAMPP Control Panel"
echo "2. Start Apache and MySQL services"
echo "3. Open phpMyAdmin: http://localhost/phpmyadmin"
echo "4. Create database named 'foodiehub'"
echo "5. Update .env file with your database credentials"
echo
echo "Database settings for XAMPP:"
echo "DB_HOST=127.0.0.1"
echo "DB_PORT=3306"
echo "DB_DATABASE=foodiehub"
echo "DB_USERNAME=root"
echo "DB_PASSWORD="
echo
echo "After completing these steps, run:"
echo "php artisan migrate"
echo "php artisan db:seed"
echo "php artisan serve"
echo
echo "========================================"
echo "Setup completed!"
echo "========================================"
