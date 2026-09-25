# ==============================================================================
# Dockerfile tối ưu hóa cho ứng dụng Laravel Quán Mới System
# ==============================================================================

# Giai đoạn 1: Build Frontend Assets (Vite)
FROM node:20-alpine AS frontend_builder
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm ci
COPY resources ./resources
COPY public ./public
RUN npm run build

# Giai đoạn 2: Khởi tạo PHP 8.3-FPM Production
FROM php:8.3-fpm-alpine

# Cài đặt các extension PHP cần thiết cho Laravel
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    zip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    curl \
    mysql-client \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring gd zip bcmath intl opcache

# Cài đặt Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Coppy mã nguồn ứng dụng
COPY . .

# Coppy các file Frontend đã build ở Giai đoạn 1
COPY --from=frontend_builder /app/public/build ./public/build

# Cài đặt Composer Dependencies cho môi trường Production
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Thắt chặt quyền truy cập thư mục storage & cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
