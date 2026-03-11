# پروژه Laravel - PHP 8.2
# هر RUN جدا است تا در صورت خطا مشخص شود مرحلهٔ مشکل‌دار کدام است
FROM php:8.2-cli

# ─── مرحله ۱: به‌روزرسانی لیست پکیج‌ها ───
RUN apt-get update

# ─── مرحله ۲: فقط کتابخانه‌های لازم برای اکستنشن‌های ضروری ───
# intl → libicu-dev | zip → libzip-dev | mbstring (regex) → libonig-dev
RUN apt-get install -y --no-install-recommends \
    libzip-dev \
    libicu-dev 

RUN apt-get install -y --no-install-recommends \
    libonig-dev \
    git 

RUN apt-get install -y --no-install-recommends \
    curl \
    zip \
    unzip

    

# ─── مرحله ۳: اکستنشن‌های PHP بدون وابستگی اضافه ───
RUN docker-php-ext-install pdo_mysql mbstring bcmath zip intl

# ─── مرحله ۴: کتابخانه‌های لازم برای GD (تصویر) ───
RUN apt-get install -y --no-install-recommends \
    libpng-dev 

RUN apt-get install -y --no-install-recommends \
    libfreetype6-dev 

RUN apt-get install -y --no-install-recommends \
    libjpeg62-turbo-dev

# ─── مرحله ۵: اکستنشن GD ───
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# پاک‌سازی کش apt برای کم کردن حجم ایمیج
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ─── Composer ───
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ─── مرحله ۶: کپی و نصب وابستگی‌های PHP ───
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

COPY . .
RUN composer dump-autoload --optimize \
    && php -r "file_exists('.env') || copy('.env.example', '.env');" \
    && (php artisan package:discover --ansi || true) \
    && (php artisan filament:upgrade || true)

# ─── دسترسی نوشتن ───
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 8000

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
