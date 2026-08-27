#!/usr/bin/env bash
set -e

echo "تثبيت مكتبات المشروع (composer install)..."
composer install --no-dev --optimize-autoloader --working-dir=/var/www/html

echo "تنظيف وتجهيز الكاش..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "تشغيل المايجريشن على قاعدة البيانات..."
php artisan migrate --force

echo "ربط مجلد التخزين..."
php artisan storage:link || true

echo "تشغيل السيرفر..."
/start.sh