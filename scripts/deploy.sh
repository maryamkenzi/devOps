#!/bin/bash

# Akaunting Deployment Script

set -e

echo "🚀 Starting Akaunting Deployment"
echo "================================"
date

# Check requirements
echo "🔍 Checking requirements..."
php --version || { echo "❌ PHP not found"; exit 1; }
composer --version || { echo "❌ Composer not found"; exit 1; }
mysql --version || echo "⚠️  MySQL not found (database might be remote)"

# Install dependencies
echo "📦 Installing dependencies..."
composer install --no-dev --optimize-autoloader

# Set permissions
echo "🔒 Setting permissions..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Setup environment
if [ ! -f ".env" ]; then
    echo "⚙️  Creating .env file..."
    cp .env.example .env
    php artisan key:generate
    echo "✅ Please edit .env with your database credentials"
else
    echo "✅ .env file already exists"
fi

# Database setup
echo "🗄️  Setting up database..."
read -p "Run database migrations? (y/n): " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    php artisan migrate --force
    echo "✅ Database migrations completed"
fi

# Cache clear
echo "🧹 Clearing cache..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo ""
echo "🎉 Akaunting deployment completed!"
echo ""
echo "📋 Next steps:"
echo "1. Configure your web server (Apache/Nginx)"
echo "2. Set up SSL certificate"
echo "3. Configure cron jobs for scheduled tasks"
echo "4. Set up backups"
echo ""
echo "🌐 Access your Akaunting installation at:"
echo "   http://your-domain"
echo ""
echo "🔧 Admin credentials:"
echo "   Email: admin@example.com"
echo "   Password: admin"