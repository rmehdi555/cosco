<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه رفاه کالا</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/jpeg" href="{{ asset('images/refah-logo.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/refah-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/refah-logo.jpg') }}">
    <link rel="stylesheet" href="{{ asset('css/refah-registration.css') }}"?v={{ filemtime(public_path('css/refah-registration.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/refah-index.css') }}"?v={{ filemtime(public_path('css/refah-index.css')) }}">
</head>
<body><!-- Header -->
<header class="header">
    <div class="header-content">
        <div class="logo">
            <a href="{{ route('refah.index') }}">
                <img src="{{ asset('images/refah-logo.jpg') }}" alt="لوگو" onerror="this.style.display='none'">
            </a>
        </div>
        <div class="site-title">
            <a href="{{ route('refah.index') }}" style="color: inherit; text-decoration: none;">سامانه رفاه کالا</a>
        </div>
        <a href="tel:02126206918" class="track-order-btn">پیگیری سفارش</a>
    </div>
</header>
