<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتیجه پرداخت</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/refah-logo.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/refah-logo.jpg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/refah-logo.jpg') }}">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'IRANSans', 'Tahoma', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 700px;
            width: 100%;
            text-align: center;
        }

        .status-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
        }

        .success {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
        }

        .error {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
        }

        .title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
        }

        .message {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .order-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: right;
        }

        .order-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: bold;
            color: #555;
        }

        .info-value {
            color: #333;
        }

        .payment-details {
            background: #e8f5e8;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: right;
        }

        .payment-details h3 {
            color: #2e7d32;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .gateway-response {
            background: #fff3cd;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: right;
        }

        .gateway-response h3 {
            color: #856404;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .callback-data {
            background: #f8d7da;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 25px;
            text-align: right;
        }

        .callback-data h3 {
            color: #721c24;
            margin-bottom: 15px;
            font-size: 18px;
        }

        .data-item {
            background: white;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 8px;
            font-family: monospace;
            font-size: 14px;
            word-break: break-all;
        }

        .buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .amount {
            font-size: 20px;
            font-weight: bold;
            color: #2e7d32;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-success {
            background: #4CAF50;
            color: white;
        }

        .status-failed {
            background: #f44336;
            color: white;
        }

        .status-pending {
            background: #ff9800;
            color: white;
        }

        .json-viewer {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            text-align: left;
            max-height: 200px;
            overflow-y: auto;
            white-space: pre-wrap;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            .buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="status-icon {{ $success ? 'success' : 'error' }}">
            @if($success)
                ✓
            @else
                ✗
            @endif
        </div>

        <h1 class="title">
            @if($success)
                پرداخت با موفقیت انجام شد
            @else
                پرداخت ناموفق بود
            @endif
        </h1>

        <p class="message">{{ $message }}</p>

        @if($order)
        <div class="order-info">
            <h3>اطلاعات سفارش</h3>
            <div class="info-row">
                <span class="info-label">شماره سفارش:</span>
                <span class="info-value">{{ $order->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">مبلغ کل:</span>
                <span class="info-value amount">{{ number_format($order->total_amount) }} تومان</span>
            </div>
            <div class="info-row">
                <span class="info-label">وضعیت سفارش:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('pending')
                                در انتظار
                                @break
                            @case('processing')
                                در حال پردازش
                                @break
                            @case('completed')
                                تکمیل شده
                                @break
                            @case('cancelled')
                                لغو شده
                                @break
                            @default
                                {{ $order->status }}
                        @endswitch
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">وضعیت پرداخت:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $order->payment_status }}">
                        @switch($order->payment_status)
                            @case('pending')
                                در انتظار
                                @break
                            @case('paid')
                                پرداخت شده
                                @break
                            @case('failed')
                                ناموفق
                                @break
                            @default
                                {{ $order->payment_status }}
                        @endswitch
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">تاریخ سفارش:</span>
                <span class="info-value">{{ $order->created_at->format('Y/m/d H:i') }}</span>
            </div>
            @if($order->user)
            <div class="info-row">
                <span class="info-label">نام مشتری:</span>
                <span class="info-value">{{ $order->user->name ?? $order->user->cell_phone }}</span>
            </div>
            @endif
        </div>
        @endif

        @if($payment)
        <div class="payment-details">
            <h3>جزئیات پرداخت</h3>
            <div class="info-row">
                <span class="info-label">شماره پرداخت:</span>
                <span class="info-value">{{ $payment->id }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">روش پرداخت:</span>
                <span class="info-value">{{ $payment->method->label() }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">وضعیت پرداخت:</span>
                <span class="info-value">
                    <span class="status-badge status-{{ $payment->status->value }}">
                        {{ $payment->status->label() }}
                    </span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">مبلغ پرداختی:</span>
                <span class="info-value amount">{{ number_format($payment->amount) }} تومان</span>
            </div>
            @if($payment->bank_transaction_id)
            <div class="info-row">
                <span class="info-label">شناسه تراکنش:</span>
                <span class="info-value">{{ $payment->bank_transaction_id }}</span>
            </div>
            @endif
            @if($payment->bank_reference_id)
            <div class="info-row">
                <span class="info-label">شماره مرجع:</span>
                <span class="info-value">{{ $payment->bank_reference_id }}</span>
            </div>
            @endif
            @if($payment->paid_at)
            <div class="info-row">
                <span class="info-label">تاریخ پرداخت:</span>
                <span class="info-value">{{ $payment->paid_at->format('Y/m/d H:i') }}</span>
            </div>
            @endif
        </div>
        @endif

        @if($gateway_response)
        <div class="gateway-response">
            <h3>پاسخ درگاه پرداخت</h3>
            <div class="json-viewer">{{ json_encode($gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</div>
        </div>
        @endif

        @if($callback_data)
        <div class="callback-data">
            <h3>اطلاعات دریافتی از درگاه</h3>
            @foreach($callback_data as $key => $value)
            <div class="data-item">
                <strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}
            </div>
            @endforeach
        </div>
        @endif

        <div class="buttons">
            @if($success && $order)
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-primary">
                    مشاهده سفارش
                </a>
            @endif
            <a href="{{ route('home') }}" class="btn btn-secondary">
                بازگشت به صفحه اصلی
            </a>
        </div>
    </div>
</body>
</html>
