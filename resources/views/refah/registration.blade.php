<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه معیشتی - ثبت‌نام</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @font-face {
            font-family: 'IranSans';
            src: url('/fonts/iransanswebfanum.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
            font-display: swap;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IranSans', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            direction: rtl;
            overflow-x: hidden;
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            padding: 15px 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 20px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
        }

        .site-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2d3748;
            text-align: center;
            flex: 1;
        }

        .track-order-btn {
            background: #e53e3e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .track-order-btn:hover {
            background: #c53030;
            transform: translateY(-1px);
        }

        /* Main Content */
        .main-content {
            margin-top: 100px;
            padding-bottom: 50px;
        }

        /* Static Image Styles */
        .slider-container {
            position: relative;
            width: 100%;
            height: 500px;
            margin-bottom: 50px;
            overflow: hidden;
        }

        .static-slide {
            width: 100%;
            height: 100%;
        }

        .static-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Form Container */
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 50px;
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .form-title {
            font-size: 2rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .form-subtitle {
            font-size: 1rem;
            color: #718096;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-row.single {
            grid-template-columns: 1fr;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
            font-size: 0.95rem;
        }

        input[type="text"],
        input[type="tel"],
        input[type="date"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            color: #4a5568;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
        }

        select {
            cursor: pointer;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .radio-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #3182ce;
        }

        .submit-btn {
            background: #48bb78;
            color: white;
            padding: 15px 40px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .submit-btn:hover {
            background: #38a169;
            transform: translateY(-1px);
        }

        .success-message {
            background: #48bb78;
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 600;
        }

        .error-message {
            background: #e53e3e;
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin-top: 5px;
            font-size: 0.9rem;
        }

        .success-message-inline {
            background: #48bb78;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 0.85rem;
        }

        .loading-message {
            background: #3182ce;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            margin-top: 5px;
            font-size: 0.85rem;
        }

        .input-validation {
            position: relative;
        }

        .input-validation input:valid.available {
            border-color: #48bb78;
        }

        .input-validation input:invalid,
        .input-validation input.unavailable {
            border-color: #e53e3e;
        }

        /* Footer Styles */
        .footer {
            background: #2d3748;
            color: white;
            padding: 40px 0 20px;
            margin-top: 50px;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            text-align: center;
        }

        .footer-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .footer-phone {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #e2e8f0;
        }

        .footer-address {
            font-size: 0.9rem;
            color: #a0aec0;
            line-height: 1.6;
        }

        .footer-bottom {
            border-top: 1px solid #4a5568;
            margin-top: 30px;
            padding-top: 20px;
            font-size: 0.8rem;
            color: #a0aec0;
        }

        .footer-phone a {
            color: #e2e8f0;
            text-decoration: none;
            transition: color 0.3s ease;
            cursor: pointer;
        }

        .footer-phone a:hover {
            color: #48bb78;
            text-decoration: underline;
        }

        /* Package Selection Styles */
        .package-selection {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 10px;
        }

        .package-item {
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            transition: all 0.3s ease;
            background: white;
        }

        .package-item:hover {
            border-color: #3182ce;
            box-shadow: 0 2px 8px rgba(49, 130, 206, 0.1);
        }

        .package-item input[type="radio"] {
            width: 20px;
            height: 20px;
            accent-color: #3182ce;
            cursor: pointer;
            margin: 0;
            flex-shrink: 0;
        }

        .package-item input[type="radio"]:checked + .package-label {
            border-color: #3182ce;
            background: #f7fafc;
        }

        .package-label {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 15px;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s ease;
            width: 100%;
        }

        .package-content {
            flex: 1;
        }

        .package-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 8px;
        }

        .package-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
            flex: 1;
        }

        .package-price {
            font-size: 1rem;
            font-weight: 600;
            color: #48bb78;
            margin: 0;
            white-space: nowrap;
        }

        .package-description {
            font-size: 0.9rem;
            color: #718096;
            line-height: 1.5;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
        }

        @media (max-width: 768px) {
            .header-content {
                padding: 0 10px;
            }
            
            .site-title {
                font-size: 1.2rem;
            }
            
            .track-order-btn {
                padding: 8px 15px;
                font-size: 0.8rem;
            }
            
            .form-container {
                margin: 0 10px 20px;
                padding: 20px;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .form-title {
                font-size: 1.5rem;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }

            .slider-container {
                height: 300px;
                margin-top: 20px;
            }

            .package-label {
                padding: 12px;
                gap: 12px;
            }

            .package-header {
                gap: 10px;
            }

            .package-title {
                font-size: 1rem;
            }

            .package-price {
                font-size: 0.9rem;
            }

            .package-description {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo">
                <img src="{{ asset('images/refah-logo.jpg') }}" alt="لوگو" onerror="this.style.display='none'">
            </div>
            <div class="site-title">سامانه رفاه کالا </div>
            <a href="tel:02126206918" class="track-order-btn">پیگیری سفارش</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Static Image -->
        <div class="slider-container">
            <div class="static-slide">
                <img src="{{ asset('images/slide1.jpeg') }}" alt="تصویر اصلی">
            </div>
        </div>

        <!-- Registration Form -->
        <div class="form-container">
            <div class="form-header">
                <h1 class="form-title">فرم ثبت‌نام رفاه کالا</h1>
                <p class="form-subtitle">لطفاً اطلاعات خود را با دقت وارد نمایید</p>
            </div>

            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

        <form action="{{ route('refah.registration.store') }}" method="POST" id="registrationForm">
            @csrf
            
            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="first_name">نام *</label>
                        <input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                        @error('first_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="last_name">نام خانوادگی *</label>
                        <input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                        @error('last_name')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="national_code">کد ملی *</label>
                        <input type="text" id="national_code" name="national_code" value="{{ old('national_code') }}" required>
                        @error('national_code')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-validation">
                        <label for="cell_phone">شماره موبایل *</label>
                        <input type="tel" id="cell_phone" name="cell_phone" value="{{ old('cell_phone') }}" required>
                        <div id="mobile_validation_message" style="display: none;"></div>
                        @error('cell_phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="birth_date_persian">تاریخ تولد (شمسی) *</label>
                        <input type="text" id="birth_date_persian" placeholder="1380/01/01" maxlength="10" required 
                               value="{{ old('birth_date_persian') }}">
                        <input type="hidden" id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <div id="birth_date_error" class="error-message" style="display: none;">فرمت تاریخ صحیح نیست</div>
                    </div>
                    <div>
                        <label>جنسیت *</label>
                        <div class="radio-group">
                            <div class="radio-item">
                                <input type="radio" id="male" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }} required>
                                <label for="male">مرد</label>
                            </div>
                            <div class="radio-item">
                                <input type="radio" id="female" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }} required>
                                <label for="female">زن</label>
                            </div>
                        </div>
                        @error('gender')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="number_of_family_members">تعداد اعضای خانواده *</label>
                        <input type="number" id="number_of_family_members" name="number_of_family_members" value="{{ old('number_of_family_members') }}" min="1" required>
                        @error('number_of_family_members')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="job">شغل *</label>
                        <input type="text" id="job" name="job" value="{{ old('job') }}" required>
                        @error('job')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="income">درآمد (ریال) *</label>
                        <input type="number" id="income" name="income" value="{{ old('income') }}" min="0" required>
                        @error('income')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="phone">تلفن ثابت</label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Hidden country field with default value 1 -->
            <input type="hidden" id="country_id" name="country_id" value="1">

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="province_id">استان *</label>
                        <select id="province_id" name="province_id" required>
                            <option value="">انتخاب استان</option>
                        </select>
                        @error('province_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="city_id">شهر *</label>
                        <select id="city_id" name="city_id" required>
                            <option value="">ابتدا استان را انتخاب کنید</option>
                        </select>
                        @error('city_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="postal_code">کد پستی *</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required>
                        @error('postal_code')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="how_to_receive">نحوه دریافت *</label>
                        <select id="how_to_receive" name="how_to_receive" required>
                            <option value="">انتخاب کنید</option>
                            <option value="in_person" {{ old('how_to_receive') == 'in_person' ? 'selected' : '' }}>حضوری</option>
                            <option value="mail_to_address" {{ old('how_to_receive') == 'mail_to_address' ? 'selected' : '' }}>ارسال به آدرس درج شده</option>
                        </select>
                        @error('how_to_receive')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row single">
                    <div>
                        <label for="address">آدرس کامل *</label>
                        <textarea id="address" name="address" rows="3" placeholder="آدرس کامل خود را وارد کنید" required>{{ old('address') }}</textarea>
                        @error('address')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label for="refah_organization_id">سازمان  *</label>
                        <select id="refah_organization_id" name="refah_organization_id" required>
                            <option value="">انتخاب کنید</option>
                            @foreach($refahOrganizations as $organization)
                                <option value="{{ $organization->id }}" {{ old('refah_organization_id') == $organization->id ? 'selected' : '' }}>
                                    {{ $organization->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('refah_organization_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label for="payment_method">روش پرداخت *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">انتخاب کنید</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدی</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>کارت</option>
                            <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>آنلاین</option>
                            <option value="installment" {{ old('payment_method') == 'installment' ? 'selected' : '' }}>اقساطی</option>
                        </select>
                        @error('payment_method')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="form-row single">
                    <div>
                        <label>انتخاب نوع بسته رفاهی *</label>
                        <div class="package-selection">
                            @foreach($refahCarts as $cart)
                                <div class="package-item">
                                    <label for="cart_{{ $cart->id }}" class="package-label">
                                        <input type="radio" id="cart_{{ $cart->id }}" name="refah_cart_id" value="{{ $cart->id }}" 
                                               {{ old('refah_cart_id') == $cart->id ? 'checked' : '' }} required>
                                        <div class="package-content">
                                            <div class="package-header">
                                                <div class="package-title">{{ $cart->title }}</div>
                                                <div class="package-price">{{ number_format($cart->price) }} ریال</div>
                                            </div>
                                            @if($cart->description)
                                                <div class="package-description">{{ $cart->description }}</div>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('refah_cart_id')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>





            <button type="submit" class="submit-btn">ثبت‌ نام</button>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-title">   سامانه رفاه کالا</div>
            <div class="footer-phone">تلفن: <a href="tel:02126206918" style="color: #e2e8f0; text-decoration: none;">26206918-021</a></div>
            <div class="footer-phone">تلفن: <a href="tel:02126206725" style="color: #e2e8f0; text-decoration: none;">26206725-021</a></div>
            <div class="footer-address">آدرس: تهران،الهیه خ بیدار برج جم پ۴۲</div>
            <div class="footer-bottom">
            کلیه حقوق مادی و معنوی این سایت متعلق به سامانه رفاه کالا می باشد.
            </div>
        </div>
    </footer>

    <script>
        // Persian date conversion using backend Verta library
        let dateConversionTimeout;
        function convertPersianDateOnServer(persianDate) {
            return new Promise((resolve, reject) => {
                fetch('/refah/convert-date', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        persian_date: persianDate
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        resolve(data.gregorian_date);
                    } else {
                        reject(new Error(data.message));
                    }
                })
                .catch(error => {
                    reject(error);
                });
            });
        }

        function formatPersianDate(input) {
            // Remove any non-digit characters except /
            let value = input.value.replace(/[^\d\/]/g, '');
            
            // Auto-add slashes
            if (value.length >= 5 && value.charAt(4) !== '/') {
                value = value.substring(0, 4) + '/' + value.substring(4);
            }
            if (value.length >= 8 && value.charAt(7) !== '/') {
                value = value.substring(0, 7) + '/' + value.substring(7);
            }
            
            input.value = value;
            
            // Clear previous timeout
            if (dateConversionTimeout) {
                clearTimeout(dateConversionTimeout);
            }
            
            // Hide error initially
            const errorDiv = document.getElementById('birth_date_error');
            errorDiv.style.display = 'none';
            
            // Validate and convert when complete (with debounce)
            if (value.length === 10) {
                dateConversionTimeout = setTimeout(() => {
                    convertPersianDateOnServer(value)
                        .then(gregorianDate => {
                            document.getElementById('birth_date').value = gregorianDate;
                            errorDiv.style.display = 'none';
                        })
                        .catch(error => {
                            console.error('Date conversion error:', error);
                            errorDiv.textContent = error.message || 'فرمت تاریخ صحیح نیست';
                            errorDiv.style.display = 'block';
                            document.getElementById('birth_date').value = '';
                        });
                }, 500); // 500ms delay to avoid too many requests
            } else {
                // Clear hidden field if date is incomplete
                document.getElementById('birth_date').value = '';
            }
        }

        // Add CSRF token to all AJAX requests
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Update fetch requests to include CSRF token
        const originalFetch = window.fetch;
        window.fetch = function(url, options = {}) {
            if (!options.headers) {
                options.headers = {};
            }
            options.headers['X-CSRF-TOKEN'] = token;
            return originalFetch(url, options);
        };

        // Mobile number validation
        let mobileCheckTimeout;
        function checkMobileAvailability(phoneNumber) {
            const messageDiv = document.getElementById('mobile_validation_message');
            const cellPhoneInput = document.getElementById('cell_phone');
            
            // Clear previous timeout
            if (mobileCheckTimeout) {
                clearTimeout(mobileCheckTimeout);
            }
            
            // Don't check if empty or less than 10 digits
            if (!phoneNumber || phoneNumber.length < 10) {
                messageDiv.style.display = 'none';
                cellPhoneInput.classList.remove('available', 'unavailable');
                return;
            }
            
            // Show loading message
            messageDiv.innerHTML = '<div class="loading-message">در حال بررسی...</div>';
            messageDiv.style.display = 'block';
            
            // Set timeout to avoid too many requests
            mobileCheckTimeout = setTimeout(() => {
                fetch('/refah/check-mobile', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        cell_phone: phoneNumber
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        messageDiv.innerHTML = '<div class="success-message-inline">✓ شماره موبایل در دسترس است</div>';
                        cellPhoneInput.classList.remove('unavailable');
                        cellPhoneInput.classList.add('available');
                    } else {
                        messageDiv.innerHTML = '<div class="error-message">' + data.message + '</div>';
                        cellPhoneInput.classList.remove('available');
                        cellPhoneInput.classList.add('unavailable');
                    }
                })
                .catch(error => {
                    messageDiv.innerHTML = '<div class="error-message">خطا در بررسی شماره موبایل</div>';
                    cellPhoneInput.classList.remove('available', 'unavailable');
                });
            }, 800); // 800ms delay
        }

        // Load provinces on page load (country is fixed to ID 1)
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener for Persian date input
            const persianDateInput = document.getElementById('birth_date_persian');
            persianDateInput.addEventListener('input', function() {
                formatPersianDate(this);
            });
            
            // Add event listener for mobile number validation
            const cellPhoneInput = document.getElementById('cell_phone');
            cellPhoneInput.addEventListener('input', function() {
                checkMobileAvailability(this.value.trim());
            });
            
            const provinceSelect = document.getElementById('province_id');
            const citySelect = document.getElementById('city_id');
            
            // Load provinces for country ID 1
            provinceSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
            
            fetch('/refah/provinces/1')
                .then(response => response.json())
                .then(data => {
                    provinceSelect.innerHTML = '<option value="">انتخاب استان</option>';
                    data.forEach(province => {
                        provinceSelect.innerHTML += `<option value="${province.id}">${province.title_fa}</option>`;
                    });
                });
        });

        // AJAX for cities when province changes
        document.getElementById('province_id').addEventListener('change', function() {
            const provinceId = this.value;
            const citySelect = document.getElementById('city_id');
            
            citySelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
            
            if (provinceId) {
                fetch(`/refah/cities/${provinceId}`)
                    .then(response => response.json())
                    .then(data => {
                        citySelect.innerHTML = '<option value="">انتخاب شهر</option>';
                        data.forEach(city => {
                            citySelect.innerHTML += `<option value="${city.id}">${city.title_fa}</option>`;
                        });
                    });
            } else {
                citySelect.innerHTML = '<option value="">ابتدا استان را انتخاب کنید</option>';
            }
        });
    </script>
</body>
</html>
