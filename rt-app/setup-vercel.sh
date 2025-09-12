#!/bin/bash

# SIMRT Vercel Deployment Setup Script
# This script prepares the SIMRT application for Vercel deployment

echo "🚀 Setting up SIMRT for Vercel deployment..."

# 1. Generate application key if not exists
if [ -z "$APP_KEY" ]; then
    echo "⚠️  Generating new application key..."
    php artisan key:generate --show
    echo "📝 Add this APP_KEY to your Vercel environment variables"
fi

# 2. Create required directories
mkdir -p storage/{app,framework/{cache,sessions,views},logs}
mkdir -p bootstrap/cache
mkdir -p api

# 3. Set proper permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# 4. Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 5. Install dependencies
echo "📦 Installing dependencies..."
composer install --optimize-autoloader --no-dev --prefer-dist

# 6. Build assets
echo "🎨 Building frontend assets..."
npm ci
npm run build

# 7. Cache configurations for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Setup complete! Ready for Vercel deployment."
echo ""
echo "Next steps:"
echo "1. Push your code to GitHub"
echo "2. Connect your GitHub repo to Vercel"
echo "3. Add environment variables in Vercel dashboard"
echo "4. Deploy!"
echo ""
echo "📋 Required environment variables for Vercel:"
echo "- APP_KEY (generate with: php artisan key:generate --show)"
echo "- APP_URL (your Vercel domain)"
echo "- Database credentials (use PlanetScale, Railway, etc.)"
echo "- GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET (optional)"
