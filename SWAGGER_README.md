# Swagger API Documentation

## نصب و راه‌اندازی

### 1. نصب پکیج
```bash
composer require darkaonline/l5-swagger
```

### 2. انتشار فایل‌های تنظیمات
```bash
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
```

### 3. تنظیمات .env
فایل `.env` را ویرایش کرده و تنظیمات زیر را اضافه کنید:

```env
L5_SWAGGER_CONST_HOST=http://localhost:8000
L5_SWAGGER_GENERATE_ALWAYS=true
L5_SWAGGER_UI_DOC_EXPANSION=list
```

### 4. تولید مستندات
```bash
php artisan l5-swagger:generate
```

## دسترسی به مستندات

پس از راه‌اندازی، می‌توانید از طریق آدرس زیر به مستندات API دسترسی پیدا کنید:

```
http://localhost:8000/api/documentation
```

