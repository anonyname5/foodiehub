<?php

echo "Testing Laravel setup...\n";

// Test 1: Check if composer autoload exists
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✅ Composer autoload found\n";
} else {
    echo "❌ Composer autoload not found. Run: composer install\n";
    exit(1);
}

// Test 2: Check if .env exists
if (file_exists(__DIR__ . '/.env')) {
    echo "✅ .env file found\n";
} else {
    echo "❌ .env file not found. Run: copy env.example .env\n";
    exit(1);
}

// Test 3: Check if APP_KEY is set
$envContent = file_get_contents(__DIR__ . '/.env');
if (strpos($envContent, 'APP_KEY=') !== false && strpos($envContent, 'APP_KEY=') < strpos($envContent, 'APP_KEY=') + 50) {
    echo "✅ APP_KEY appears to be set\n";
} else {
    echo "❌ APP_KEY not set. Run: php artisan key:generate\n";
    exit(1);
}

// Test 4: Check if bootstrap/app.php exists
if (file_exists(__DIR__ . '/bootstrap/app.php')) {
    echo "✅ Laravel bootstrap file found\n";
} else {
    echo "❌ Laravel bootstrap file not found\n";
    exit(1);
}

echo "\n🎉 All tests passed! Laravel setup looks good.\n";
echo "You can now run: php artisan serve\n";
