FROM php:8.4-apache

# نصب پیش‌نیازها
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# غیرفعال کردن MPMهای اضافی و فعال‌سازی prefork
RUN a2dismod mpm_event
RUN a2enmod mpm_prefork
RUN a2enmod rewrite

# تنظیم DocumentRoot به پوشه public لاراول
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# کپی فایل‌های پروژه
COPY . /var/www/html

# تنظیم مالکیت و دسترسی
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# نصب composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# نصب وابستگی‌ها
RUN composer install --no-dev --optimize-autoloader

# پاک‌سازی کش لاراول
RUN php artisan config:clear
