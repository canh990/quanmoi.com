FROM php:8.4-apache

# =====================================
# 1. Install system dependencies
# =====================================

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    curl \
    default-mysql-client \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*


# =====================================
# 2. Configure PHP extensions
# =====================================

RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache


# =====================================
# 3. Disable MySQL client SSL
#    Laravel schema loader cần mysql client
# =====================================

RUN printf '[client]\nssl=0\n' > /etc/mysql/my.cnf


# =====================================
# 4. Enable Apache mod_rewrite
# =====================================

RUN a2enmod rewrite


# =====================================
# 5. Install Composer
# =====================================

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


# =====================================
# 6. Configure Apache Document Root
#    Laravel public/
# =====================================

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri \
    -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf


# =====================================
# 7. Set working directory
# =====================================

WORKDIR /var/www/html


# =====================================
# 8. Copy Laravel project
# =====================================

COPY . .


# =====================================
# 9. Install PHP dependencies
# =====================================

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader


# =====================================
# 10. Set permissions
# =====================================

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache


# =====================================
# 11. Expose Apache
# =====================================

EXPOSE 80


# =====================================
# 12. Start Apache
# =====================================

CMD ["apache2-foreground"]