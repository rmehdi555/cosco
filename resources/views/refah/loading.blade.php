<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>در حال بروزرسانی</title>
    <style>
        @font-face {
            font-family: 'IranSans';
            src: url('{{ asset('fonts/iransanswebfanum.ttf') }}') format('truetype');
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            direction: rtl;
        }

        .container {
            text-align: center;
            color: white;
            max-width: 500px;
            padding: 40px;
        }

        .logo {
            margin-bottom: 30px;
        }

        .logo img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            animation: pulse 2s infinite;
        }

        .loading-spinner {
            margin: 30px auto;
            width: 80px;
            height: 80px;
            position: relative;
        }

        .spinner {
            width: 100%;
            height: 100%;
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .dots {
            margin-top: 20px;
        }

        .dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            background-color: white;
            border-radius: 50%;
            margin: 0 5px;
            animation: bounce 1.4s ease-in-out infinite both;
        }

        .dot:nth-child(1) { animation-delay: -0.32s; }
        .dot:nth-child(2) { animation-delay: -0.16s; }
        .dot:nth-child(3) { animation-delay: 0s; }

        .title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
            animation: fadeInUp 1s ease-out;
        }

        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 15px;
            opacity: 0.9;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .description {
            font-size: 1rem;
            opacity: 0.8;
            line-height: 1.6;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background-color: rgba(255,255,255,0.3);
            border-radius: 3px;
            margin: 30px 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
            border-radius: 3px;
            animation: progress 3s ease-in-out infinite;
        }

        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            bottom: 10%;
            right: 20%;
            animation-delay: 1s;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes bounce {
            0%, 80%, 100% {
                transform: scale(0);
            } 40% {
                transform: scale(1);
            }
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 15px 40px rgba(0,0,0,0.4);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes progress {
            0% {
                transform: translateX(-100%);
            }
            50% {
                transform: translateX(0%);
            }
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            33% {
                transform: translateY(-30px) rotate(120deg);
            }
            66% {
                transform: translateY(30px) rotate(240deg);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                max-width: 90%;
            }
            
            .title {
                font-size: 2rem;
            }
            
            .subtitle {
                font-size: 1rem;
            }
            
            .description {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="container">
        <div class="logo">
            <img src="{{ asset('images/logo.jpg') }}" alt="لوگو" onerror="this.style.display='none'">
        </div>

        <h1 class="title">در حال بروزرسانی</h1>
        
        <p class="subtitle">سایت ما در حال بهبود و ارتقاء است</p>
        
        <div class="loading-spinner">
            <div class="spinner"></div>
        </div>

        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>

        <div class="dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        
        <p class="description">
            ما به زودی با ظاهری جدید و امکانات بهتر برمی‌گردیم.<br>
            از صبر و شکیبایی شما متشکریم.
        </p>
    </div>
</body>
</html>
