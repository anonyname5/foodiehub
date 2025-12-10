@echo off
echo Starting FoodieHub Development Servers...
echo.

echo 1. Starting Laravel Backend...
start "Laravel Backend" cmd /k "cd backend && php artisan serve"
timeout /t 3 /nobreak >nul

echo 2. Starting User Frontend (Port 3000)...
start "User Frontend" cmd /k "cd frontend/public && python -m http.server 3000"
timeout /t 2 /nobreak >nul

echo 3. Starting Admin Panel (Port 3001)...
start "Admin Panel" cmd /k "cd admin/public && python -m http.server 3001"
timeout /t 2 /nobreak >nul

echo.
echo ✅ All servers started!
echo.
echo 📱 User Frontend: http://localhost:3000
echo 🔧 Admin Panel:   http://localhost:3001
echo 🚀 Backend API:   http://127.0.0.1:8000
echo.
echo Admin Login: admin@foodiehub.com / admin123
echo.
pause
