#!/bin/bash

# Script Deploy Tự Động Cho Laravel trên Server Linux/Ubuntu
set -e

echo "🚀 Bắt đầu quá trình Deploy ứng dụng Quán Mới..."

# 1. Chuyển vào thư mục dự án
cd /var/www/quanmoi.com

# 2. Bật chế độ bảo trì tạm thời
php artisan down || true

# 3. Kéo mã nguồn mới nhất từ Git
echo "📥 Đang kéo code mới từ Git origin TrongTin/admin-dashboard..."
git fetch origin
git checkout TrongTin/admin-dashboard
git pull origin TrongTin/admin-dashboard

# 4. Cài đặt vendor composer tối ưu
echo "📦 Đang cài đặt thư viện PHP Composer..."
composer install --no-dev --optimize-autoloader

# 5. Biên dịch file tĩnh Frontend
echo "🎨 Đang biên dịch Asset Vite CSS/JS..."
npm ci
npm run build

# 6. Chạy Migration cơ sở dữ liệu
echo "🗄️ Đang nâng cấp CSDL Database..."
php artisan migrate --force

# 7. Xóa & Cấu hình lại bộ nhớ đệm Cache
echo "⚡ Đang tối ưu hóa bộ nhớ Cache Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 8. Tắt chế độ bảo trì, bật lại hệ thống
php artisan up

echo "✅ ĐÃ DEPLOY TỰ ĐỘNG THÀNH CÔNG!"
