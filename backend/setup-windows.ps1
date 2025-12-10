Write-Host "========================================" -ForegroundColor Cyan
Write-Host "FoodieHub Backend Setup for Windows" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Step 1: Creating required directories..." -ForegroundColor Yellow
# Create bootstrap/cache directory if it doesn't exist
if (!(Test-Path "bootstrap\cache")) {
    New-Item -ItemType Directory -Path "bootstrap\cache" -Force
    Write-Host "Created bootstrap/cache directory" -ForegroundColor Green
}

# Create storage directories if they don't exist
$storageDirs = @("storage\app", "storage\framework\cache", "storage\framework\sessions", "storage\framework\views", "storage\logs")
foreach ($dir in $storageDirs) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force
        Write-Host "Created $dir directory" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "Step 2: Copying environment file..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "Environment file already exists" -ForegroundColor Green
} else {
    Copy-Item "env.example" ".env"
    Write-Host "Environment file created" -ForegroundColor Green
}

Write-Host ""
Write-Host "Step 3: Generating application key..." -ForegroundColor Yellow
php artisan key:generate
if ($LASTEXITCODE -eq 0) {
    Write-Host "Application key generated successfully" -ForegroundColor Green
} else {
    Write-Host "Failed to generate application key" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "IMPORTANT: Manual Steps Required" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Start XAMPP Control Panel" -ForegroundColor White
Write-Host "2. Start Apache and MySQL services" -ForegroundColor White
Write-Host "3. Open phpMyAdmin: http://localhost/phpmyadmin" -ForegroundColor White
Write-Host "4. Create database named 'foodiehub'" -ForegroundColor White
Write-Host ""
Write-Host "After creating the database, run:" -ForegroundColor Yellow
Write-Host "php artisan migrate" -ForegroundColor White
Write-Host "php artisan db:seed" -ForegroundColor White
Write-Host "php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Setup completed! Press any key to exit." -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Read-Host
