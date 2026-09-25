#!/bin/sh
# ==============================================================================
# File: demo-cicd/git-hook-pre-push.sh
# Hướng dẫn: Coppy file này vào thư mục .git/hooks/ và đổi tên thành "pre-push"
# (Ví dụ đường dẫn: .git/hooks/pre-push)
# ==============================================================================

echo "========================================================"
echo "🔍 [LOCAL CI] Đang tự động kiểm tra code trước khi push..."
echo "========================================================"

# Step 1: Kiểm tra format code bằng Laravel Pint
echo "\n🧹 [1/3] Kiểm tra định dạng code (Laravel Pint)..."
vendor/bin/pint --test
if [ $? -ne 0 ]; then
    echo "❌ CODE KHÔNG ĐÚNG ĐỊNH DẠNG!"
    echo "💡 Hãy chạy lệnh: vendor/bin/pint để tự động sửa format, sau đó git commit lại."
    exit 1
fi

# Step 2: Chạy Automated Tests (PHPUnit / Pest)
echo "\n🧪 [2/3] Đang chạy Unit & Feature Tests..."
php artisan test
if [ $? -ne 0 ]; then
    echo "❌ AUTOMATED TESTS BỊ LỖI!"
    echo "💡 Vui lòng sửa lại code/test bị lỗi trước khi push."
    exit 1
fi

# Step 3: Thử biên dịch Frontend (Vite)
echo "\n📦 [3/3] Đang biên dịch thử Frontend (Vite)..."
npm run build
if [ $? -ne 0 ]; then
    echo "❌ BIÊN DỊCH FRONTEND BỊ LỖI!"
    echo "💡 Vui lòng kiểm tra lại các file JS/CSS/Vue/Blade."
    exit 1
fi

echo "\n========================================================"
echo "✅ [LOCAL CI PASSED] Tất cả kiểm tra thành công!"
echo "🚀 Đang tiến hành Push code lên GitHub..."
echo "========================================================"
exit 0
