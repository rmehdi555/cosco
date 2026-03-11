#!/bin/sh
set -e

# نصب وابستگی‌ها (برای وقتی که کد با volume مونت شده و vendor خالی است)
if [ ! -f /var/www/vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

# منتظر آماده شدن MySQL بمان
echo "Waiting for MySQL..."
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT') . ';dbname=' . getenv('DB_DATABASE'),
            getenv('DB_USERNAME'),
            getenv('DB_PASSWORD'),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        exit(0);
    } catch (Throwable \$e) {
        exit(1);
    }
" 2>/dev/null; do
  sleep 2
done
echo "MySQL is ready."

# تولید APP_KEY در صورت نبودن
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
  php artisan key:generate --force
fi

# اجرای مایگریشن‌ها
php artisan migrate --force --no-interaction

# اجرای سرور Laravel
exec php artisan serve --host=0.0.0.0 --port=8000
