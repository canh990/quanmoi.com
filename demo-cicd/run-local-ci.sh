#!/bin/bash
# ==============================================================================
# Script chạy Local CI Check cho môi trường Linux/macOS/Git Bash
# ==============================================================================

set -e

echo "========================================================"
echo "🔍 [LOCAL CI] Đang tự động kiểm tra dự án Quán Mới..."
echo "========================================================"

# 1. Kiểm tra Laravel Pint
echo -e "\n🧹 [1/3] Kiểm tra định dạng Code (Laravel Pint)..."
vendor/bin/pint --test

# 2. Chạy Test
echo -e "\n🧪 [2/3] Đang chạy Automated Tests..."
php artisan test

# 3. Build Vite Frontend
echo -e "\n📦 [3/3] Đang biên dịch Frontend (Vite)..."
npm run build

echo -e "\n========================================================"
echo "✅ [THÀNH CÔNG] Tất cả kiểm tra Local CI đều ĐẠT!"
echo "========================================================"
