# راهنمای راه‌اندازی پروژه با Docker و phpMyAdmin

این راهنما مرحله‌به‌مرحله اجرای پروژه Laravel با Docker و پنل phpMyAdmin را توضیح می‌دهد.

---

## پیش‌نیازها

روی سیستم خودت باید نصب باشد:

1. **Docker Desktop** (برای ویندوز/مک) یا **Docker Engine** + **Docker Compose**
   - دانلود ویندوز: https://www.docker.com/products/docker-desktop/
   - بعد از نصب، Docker را اجرا کن و مطمئن شو که در حال اجراست.

2. **Git** (در صورت نیاز برای کلون کردن پروژه)

---

## مرحله ۱: باز کردن ترمینال در پوشه پروژه

- پوشه پروژه را باز کن (مثلاً `c:\Users\ASUS\Desktop\www\cosco`).
- در همین پوشه یک ترمینال (PowerShell یا CMD) باز کن.

---

## مرحله ۲: تنظیم فایل محیط (`.env`)

برای اینکه اپ با دیتابیس MySQL داخل Docker حرف بزند، باید متغیرهای دیتابیس را درست بگذاری.

**گزینه الف – اگر الان `.env` داری:**

فایل `.env` را باز کن و این خطوط را طوری تنظیم کن که با داکر هماهنگ باشد:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=cosco
DB_USERNAME=cosco
DB_PASSWORD=secret
```

اگر خطوط `DB_*` را کامنت کرده بودی، آنها را از حالت کامنت دربیاور و مقادیر بالا را بگذار.

**گزینه ب – اگر `.env` نداری:**

از فایل نمونه مخصوص داکر کپی بگیر:

```bash
copy .env.example.docker .env
```

سپس در صورت نیاز `APP_KEY` را بعد از بالا آمدن اپ یک بار با دستور زیر تولید کن (در مرحله ۵ توضیح داده شده).

---

## مرحله ۳: ساخت و اجرای سرویس‌ها با Docker Compose

در همان ترمینال (داخل پوشه پروژه) دستور زیر را بزن:

```bash
docker-compose up -d --build
```

- `--build`: یک بار image اپ را از روی `Dockerfile` می‌سازد.
- `-d`: سرویس‌ها در پس‌زمینه (detached) اجرا می‌شوند.

اولین بار ممکن است چند دقیقه طول بکشد (دانلود imageهای MySQL و phpMyAdmin و ساخت image اپ).

---

## مرحله ۴: چک کردن اجرای سرویس‌ها

دستور:

```bash
docker-compose ps
```

باید سه سرویس را با وضعیت «Up» ببینی:

- **cosco_app** (پورت 8000) → اپلیکیشن Laravel  
- **cosco_mysql** (پورت 3306) → دیتابیس MySQL  
- **cosco_phpmyadmin** (پورت 8080) → پنل phpMyAdmin  

اگر یکی از آن‌ها Up نبود، با دستور زیر لاگ را ببین:

```bash
docker-compose logs -f app
```

با `Ctrl+C` از لاگ خارج می‌شوی.

---

## مرحله ۵: آدرس‌ها و ورود به پنل‌ها

- **اپلیکیشن Laravel:**  
  در مرورگر برو به:  
  **http://localhost:8000**

- **phpMyAdmin (پنل مدیریت دیتابیس):**  
  برو به:  
  **http://localhost:8080**

  برای ورود به phpMyAdmin از همان مقادیر `.env` استفاده کن:
  - **Username:** `cosco`  
  - **Password:** `secret`  
  (یا هر چیزی که برای `DB_USERNAME` و `DB_PASSWORD` در `.env` گذاشتی.)

  برای ورود با کاربر root دیتابیس:
  - **Username:** `root`  
  - **Password:** همان `DB_PASSWORD` (مثلاً `secret`).

---

## مرحله ۶: در صورت نیاز – تولید `APP_KEY`

اگر هنگام باز کردن اپ خطای مربوط به `APP_KEY` دیدی، یک بار این دستور را اجرا کن:

```bash
docker-compose exec app php artisan key:generate
```

بعد دوباره صفحه اپ را رفرش کن.

---

## مرحله ۷: دستورات مفید دیگر

- **دیدن لاگ اپ:**
  ```bash
  docker-compose logs -f app
  ```

- **اجرای مایگریشن دستی:**
  ```bash
  docker-compose exec app php artisan migrate
  ```

- **ورود به shell داخل کانتینر اپ:**
  ```bash
  docker-compose exec app sh
  ```

- **متوقف کردن همه سرویس‌ها:**
  ```bash
  docker-compose down
  ```

- **متوقف کردن و حذف حجم دیتابیس (پاک شدن کامل دیتا):**
  ```bash
  docker-compose down -v
  ```

---

## خلاصه آدرس‌ها و اطلاعات ورود

| سرویس      | آدرس                  | کاربر / پسورد (پیش‌فرض) |
|------------|------------------------|---------------------------|
| Laravel    | http://localhost:8000 | -                         |
| phpMyAdmin | http://localhost:8080 | cosco / secret            |
| MySQL      | localhost:3306        | cosco / secret یا root / secret |

اگر پسورد را در `.env` عوض کنی، همان را در phpMyAdmin و اپ استفاده کن.

---

با انجام این مراحل، پروژه با Docker بالا می‌آید و پنل مدیریت دیتابیس phpMyAdmin روی پورت 8080 در دسترس است.
