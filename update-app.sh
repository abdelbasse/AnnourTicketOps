#!/bin/bash

# Navigate to the Laravel project directory inside the container
cd /var/www

# Run git pull to update the codebase from GitHub
echo "Pulling latest changes from GitHub..."
git pull origin main

# Check if there are any changes in the Composer dependencies
if [ -f "composer.json" ]; then
    echo "Checking for Composer dependencies update..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Clear cache and regenerate application keys and config caches
echo "Clearing and refreshing cache..."
php artisan cache:clear
php artisan config:clear
php artisan config:cache
php artisan route:cache

echo "Application updated successfully!"
