# FoodieHub Development Guide

## 🚀 **Unified Development Setup**

This guide shows how to run both the user frontend and admin panel using the **same technology stack** for consistency.

## 📋 **Prerequisites**

Choose **ONE** of these options (all work the same way):

### Option 1: Python (Recommended - Built-in)
```bash
# Check if Python is installed
python --version
# or
python3 --version
```

### Option 2: Node.js
```bash
# Install Node.js from https://nodejs.org
npm install -g http-server
```

### Option 3: PHP
```bash
# Check if PHP is installed
php --version
```

## 🔧 **Starting All Servers**

### **Method 1: Manual Start (Recommended)**

**Terminal 1 - Laravel Backend:**
```bash
cd backend
php artisan serve
# Backend runs on: http://127.0.0.1:8000
```

**Terminal 2 - User Frontend:**
```bash
cd frontend/public

# Using Python
python -m http.server 3000

# OR using Node.js
http-server -p 3000

# OR using PHP
php -S localhost:3000

# User frontend runs on: http://localhost:3000
```

**Terminal 3 - Admin Panel:**
```bash
cd admin/public

# Using Python
python -m http.server 3001

# OR using Node.js
http-server -p 3001

# OR using PHP
php -S localhost:3001

# Admin panel runs on: http://localhost:3001
```

### **Method 2: Automated Scripts**

**Windows:**
```bash
# Double-click or run:
start-dev-servers.bat
```

**Linux/Mac:**
```bash
# Make executable and run:
chmod +x start-dev-servers.sh
./start-dev-servers.sh
```

## 🌐 **Access URLs**

| Service | URL | Description |
|---------|-----|-------------|
| **User Frontend** | http://localhost:3000 | Public user interface |
| **Admin Panel** | http://localhost:3001 | Admin management interface |
| **Backend API** | http://127.0.0.1:8000 | Laravel API endpoints |

## 🔐 **Admin Login**

- **URL**: http://localhost:3001
- **Email**: admin@foodiehub.com
- **Password**: admin123

## 📁 **Project Structure**

```
foodiehub/
├── backend/           # Laravel API (Port 8000)
├── frontend/public/   # User Frontend (Port 3000)
├── admin/public/      # Admin Panel (Port 3001)
└── start-dev-servers.* # Development scripts
```

## 🔄 **Technology Stack**

### **Both Frontends Use Same Technology:**
- **HTML5** - Semantic markup
- **CSS3** - Styling (Tailwind CSS)
- **Vanilla JavaScript** - No frameworks
- **Static File Serving** - Python/Node.js/PHP HTTP server

### **Backend:**
- **Laravel 10** - PHP framework
- **MySQL** - Database
- **RESTful API** - JSON endpoints

## 🛠️ **Development Workflow**

1. **Start all three servers** (backend + 2 frontends)
2. **Develop features** in your preferred editor
3. **Test in browser** - changes are live (no build process)
4. **API calls** go from frontend to backend automatically

## 🚨 **Troubleshooting**

### **Port Already in Use:**
```bash
# Find process using port
netstat -ano | findstr :3000
netstat -ano | findstr :3001
netstat -ano | findstr :8000

# Kill process (Windows)
taskkill /PID <process_id> /F

# Kill process (Linux/Mac)
kill -9 <process_id>
```

### **Python Not Found:**
```bash
# Install Python from https://python.org
# Make sure to check "Add to PATH" during installation
```

### **Node.js Alternative:**
```bash
# Install Node.js from https://nodejs.org
npm install -g http-server
```

### **PHP Alternative:**
```bash
# Install PHP from https://php.net/downloads.php
# Or use XAMPP: https://www.apachefriends.org/
```

## 📝 **Why This Architecture?**

### **Consistency:**
- Both frontends use the **same technology stack**
- Both use the **same serving method**
- Easy to maintain and understand

### **Simplicity:**
- No build processes or compilation
- Direct file serving for development
- Hot reloading (just refresh browser)

### **Flexibility:**
- Can switch between Python/Node.js/PHP servers
- Same functionality regardless of server choice
- Easy to deploy to any static hosting

## 🎯 **Production Deployment**

For production, deploy static files to:
- **CDN** (Cloudflare, AWS CloudFront)
- **Static Hosting** (Netlify, Vercel, GitHub Pages)
- **Web Server** (Nginx, Apache)

The frontend code remains the same - only the serving method changes!
