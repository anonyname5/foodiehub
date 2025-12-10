#!/bin/bash

echo "Starting FoodieHub Development Servers..."
echo

echo "1. Starting Laravel Backend..."
gnome-terminal --title="Laravel Backend" -- bash -c "cd backend && php artisan serve; exec bash" &
sleep 3

echo "2. Starting User Frontend (Port 3000)..."
gnome-terminal --title="User Frontend" -- bash -c "cd frontend/public && python3 -m http.server 3000; exec bash" &
sleep 2

echo "3. Starting Admin Panel (Port 3001)..."
gnome-terminal --title="Admin Panel" -- bash -c "cd admin/public && python3 -m http.server 3001; exec bash" &
sleep 2

echo
echo "✅ All servers started!"
echo
echo "📱 User Frontend: http://localhost:3000"
echo "🔧 Admin Panel:   http://localhost:3001"
echo "🚀 Backend API:   http://127.0.0.1:8000"
echo
echo "Admin Login: admin@foodiehub.com / admin123"
echo
