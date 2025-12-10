Write-Host "Creating required directories and fixing permissions..." -ForegroundColor Yellow

# Create bootstrap/cache directory
if (!(Test-Path "bootstrap\cache")) {
    New-Item -ItemType Directory -Path "bootstrap\cache" -Force
    Write-Host "✅ Created bootstrap/cache directory" -ForegroundColor Green
}

# Create storage directories
$storageDirs = @(
    "storage\app",
    "storage\app\public",
    "storage\framework\cache", 
    "storage\framework\cache\data",
    "storage\framework\sessions", 
    "storage\framework\views", 
    "storage\logs"
)

foreach ($dir in $storageDirs) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force
        Write-Host "✅ Created $dir directory" -ForegroundColor Green
    }
}

# Create cache files
if (!(Test-Path "bootstrap\cache\packages.php")) {
    Set-Content -Path "bootstrap\cache\packages.php" -Value "<?php`n`nreturn [];"
    Write-Host "✅ Created packages.php cache file" -ForegroundColor Green
}

if (!(Test-Path "bootstrap\cache\services.php")) {
    Set-Content -Path "bootstrap\cache\services.php" -Value "<?php`n`nreturn [];"
    Write-Host "✅ Created services.php cache file" -ForegroundColor Green
}

Write-Host ""
Write-Host "🎉 Directory setup completed!" -ForegroundColor Cyan
Write-Host "Now you can run: php artisan key:generate" -ForegroundColor White
