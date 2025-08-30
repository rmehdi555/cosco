<?php

namespace App\Services;

use App\Models\Membership;
use App\Models\Payment;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\PaymentMembership;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OnlinePaymentMembershipService
{
    private string $defaultGateway;
    private array $gateways;

    public function __construct()
    {
        $this->defaultGateway = config('payment.default_gateway', 'melli_test');
        $this->gateways = config('payment.gateways', []);
    }

    /**
     * ارسال درخواست پرداخت به درگاه
     */
    public function sendToGateway(Membership $membership, string $gateway = null): array
    {
        try {
            $gateway = $gateway ?: $this->defaultGateway;

            if (!isset($this->gateways[$gateway])) {
                throw new \Exception("درگاه پرداخت {$gateway} یافت نشد");
            }

            $gatewayConfig = $this->gateways[$gateway];

            // بررسی فعال بودن درگاه
            if (!$gatewayConfig['enabled']) {
                throw new \Exception("درگاه پرداخت {$gateway} غیرفعال است");
            }

            // ایجاد رکورد پرداخت
            $paymentMembership = $this->createPaymentRecord($membership, $gateway);

            // ارسال به درگاه
            $result = $this->sendRequestToGateway($membership, $paymentMembership, $gatewayConfig);

            if ($result['success']) {
                // بروزرسانی رکورد پرداخت
                $paymentMembership->update([
                    'gateway_response' => json_encode($result['gateway_response']),
                    'bank_transaction_id' => $result['transaction_id'] ?? null,
                    'bank_reference_id' => $result['reference_id'] ?? null,
                ]);

                return [
                    'success' => true,
                    'payment_id' => $paymentMembership->id,
                    'gateway_url' => $result['gateway_url'],
                    'transaction_id' => $result['transaction_id'],
                    'message' => 'درخواست پرداخت با موفقیت ارسال شد'
                ];
            } else {
                // بروزرسانی وضعیت خطا
                $paymentMembership->update([
                    'status' => PaymentStatus::FAILED,
                    'gateway_response' => json_encode($result['gateway_response']),
                ]);

                return [
                    'success' => false,
                    'payment_id' => $paymentMembership->id,
                    'message' => $result['message'] ?? 'خطا در ارتباط با درگاه پرداخت'
                ];
            }

        } catch (\Exception $e) {
            Log::error('Payment Gateway Error: ' . $e->getMessage(), [
                'membership_id' => $membership->id,
                'gateway' => $gateway ?? $this->defaultGateway,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در پردازش درخواست پرداخت: ' . $e->getMessage()
            ];
        }
    }

    /**
     * تایید پرداخت از درگاه
     */
    public function verifyPayment(array $callbackData, string $gateway = null): array
    {
        try {
            $gateway = $gateway ?: $this->defaultGateway;

            if (!isset($this->gateways[$gateway])) {
                throw new \Exception("درگاه پرداخت {$gateway} یافت نشد");
            }

            $gatewayConfig = $this->gateways[$gateway];
            $payment = $this->findPaymentByCallbackData($callbackData, $gateway);

            if (!$payment) {
                throw new \Exception("پرداخت یافت نشد");
            }

            // تایید از درگاه
            $result = $this->verifyWithGateway($callbackData, $gatewayConfig, (int)$payment->amount);

            // بروزرسانی وضعیت پرداخت
            $this->updatePaymentStatus($payment, $result);

            // بروزرسانی وضعیت سفارش
            $this->updateOrderStatus($payment->membership, $result['success']);

            return [
                'success' => $result['success'],
                'payment' => $payment,
                'membership' => $payment->membership,
                'membership_id' => $payment->membership_id,
                'message' => $result['message'],
                'gateway_response' => $result['gateway_response']
            ];

        } catch (\Exception $e) {
            Log::error('Payment Verification Error: ' . $e->getMessage(), [
                'callback_data' => $callbackData,
                'gateway' => $gateway ?? $this->defaultGateway,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت: ' . $e->getMessage()
            ];
        }
    }

    /**
     * ایجاد رکورد پرداخت
     */
    private function createPaymentRecord(Membership $membership, string $gateway): PaymentMembership
    {
        return PaymentMembership::create([
            'membership_id' => $membership->id,
            'user_id' => $membership->user_id,
            'method' => PaymentMethod::ONLINE,
            'status' => PaymentStatus::PENDING,
            'amount' => $membership->membershipType->price,
            'description' => "پرداخت سفارش شماره {$membership->id}",
            'callback_url' => route('payment.callback.membership', ['gateway' => $gateway]),
            'merchant_id' => $this->gateways[$gateway]['merchant_id'] ?? null,
        ]);
    }

    /**
     * ارسال درخواست به درگاه
     */
    private function sendRequestToGateway(Membership $membership, PaymentMembership $paymentMembership, array $gatewayConfig): array
    {
        $gatewayType = $gatewayConfig['type'] ?? 'zarinpal_test';

        switch ($gatewayType) {
            case 'melli_test':
                return $this->sendToMelli($membership, $paymentMembership, $gatewayConfig);

            case 'zarinpal':
            case 'zarinpal_test':
                return $this->sendToZarinpal($membership, $paymentMembership, $gatewayConfig);

            default:
                throw new \Exception("نوع درگاه {$gatewayType} پشتیبانی نمی‌شود");
        }
    }

    /**
     * ارسال به درگاه بانک ملی
     */
    private function sendToMelli(Membership $membership, PaymentMembership $paymentMembership, array $gatewayConfig): array
    {
        $amount = $membership->membershipType->price; // تبدیل به ریال
//        $amount = $membership->total_amount * 10; // تبدیل به ریال

        $data = [
            'MerchantID' => $gatewayConfig['merchant_id'],
            'TerminalID' => $gatewayConfig['terminal_id'],
            'Amount' => $amount,
            'CallBackUrl' => $paymentMembership->callback_url,
            'membership_id' => $paymentMembership->id,
            'PurchaseTime' => date('Y/m/d H:i:s'),
            'SignData' => $this->generateMelliSignData($gatewayConfig['merchant_id'], $gatewayConfig['terminal_id'], $amount, $paymentMembership->callback_url, $gatewayConfig['key']),
        ];

        // لاگ کردن اطلاعات ارسالی
        Log::info('Melli Request Data:', [
            'membership_id' => $membership->id,
            'payment_id' => $paymentMembership->id,
            'amount' => $amount,
            'merchant_id' => $gatewayConfig['merchant_id'],
            'terminal_id' => $gatewayConfig['terminal_id'],
            'request_url' => $gatewayConfig['request_url'],
            'data' => $data
        ]);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($gatewayConfig['request_url'], $data);

        // لاگ کردن پاسخ دریافتی
        Log::info('Melli Response:', [
            'membership_id' => $membership->id,
            'payment_id' => $paymentMembership->id,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if ($response->successful()) {
            $result = $response->json();

            if (isset($result['ResCod']) && $result['ResCod'] == '0') {
                $token = $result['Token'];
                $gatewayUrl = $gatewayConfig['gateway_url'] . '?Token=' . $token;

                return [
                    'success' => true,
                    'gateway_url' => $gatewayUrl,
                    'transaction_id' => $token,
                    'reference_id' => $result['RefNum'] ?? null,
                    'gateway_response' => $result,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $this->getMelliErrorMessage($result['ResCod'] ?? 'unknown'),
                    'gateway_response' => $result,
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'خطا در ارتباط با درگاه بانک ملی',
                'gateway_response' => ['error' => 'HTTP Error', 'status' => $response->status()],
            ];
        }
    }

    /**
     * تولید SignData برای بانک ملی
     */
    private function generateMelliSignData(string $merchantId, string $terminalId, int $amount, string $callbackUrl, string $key): string
    {
        $data = $merchantId . $terminalId . $amount . $callbackUrl . $key;
        return base64_encode(sha1($data, true));
    }

    /**
     * دریافت پیام خطای بانک ملی
     */
    private function getMelliErrorMessage(string $code): string
    {
        $messages = [
            '0' => 'عملیات با موفقیت انجام شد',
            '1' => 'خطا در اطلاعات ارسالی',
            '2' => 'خطا در تایید اطلاعات',
            '3' => 'خطا در پردازش',
            '4' => 'خطا در ارتباط با بانک',
            '5' => 'خطا در تایید پرداخت',
            '6' => 'خطا در لغو پرداخت',
            '7' => 'خطا در بازگشت وجه',
            '8' => 'خطا در تایید بازگشت وجه',
            '9' => 'خطا در لغو بازگشت وجه',
            '10' => 'خطا در تایید لغو بازگشت وجه',
            '11' => 'خطا در تایید لغو پرداخت',
            '12' => 'خطا در تایید لغو بازگشت وجه',
            '13' => 'خطا در تایید لغو بازگشت وجه',
            '14' => 'خطا در تایید لغو بازگشت وجه',
            '15' => 'خطا در تایید لغو بازگشت وجه',
            '16' => 'خطا در تایید لغو بازگشت وجه',
            '17' => 'خطا در تایید لغو بازگشت وجه',
            '18' => 'خطا در تایید لغو بازگشت وجه',
            '19' => 'خطا در تایید لغو بازگشت وجه',
            '20' => 'خطا در تایید لغو بازگشت وجه',
        ];

        return $messages[$code] ?? "خطای نامشخص با کد {$code}";
    }

    /**
     * ارسال به درگاه زرین‌پال
     */
    private function sendToZarinpal(Membership $membership, PaymentMembership $paymentMembership, array $gatewayConfig): array
    {
        $data = [
            'merchant_id' => $gatewayConfig['merchant_id'],
            'amount' => (int)$paymentMembership->amount,
            'callback_url' => $paymentMembership->callback_url,
            'description' => $paymentMembership->description,
            'metadata' => [
                'mobile' => $membership->user->cell_phone ?? '',
                'email' => $membership->user->email ?? '',
                'membership_id' => (string)$membership->id,
                'payment_id' => $paymentMembership->id,
            ]
        ];

        // لاگ کردن اطلاعات ارسالی
        Log::info('Zarinpal Request Data:', [
            'membership_id' => $membership->id,
            'payment_id' => $paymentMembership->id,
            'amount' => (int)$paymentMembership->amount,
            'merchant_id' => $gatewayConfig['merchant_id'],
            'request_url' => $gatewayConfig['request_url'],
            'data' => $data
        ]);

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post($gatewayConfig['request_url'], $data);

        // لاگ کردن پاسخ دریافتی
        Log::info('Zarinpal Response:', [
            'membership_id' => $membership->id,
            'payment_id' => $paymentMembership->id,
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        if ($response->successful()) {
            $result = $response->json();

            if ($result['data']['code'] == 100) {
                $authority = $result['data']['authority'];
                $gatewayUrl = $gatewayConfig['gateway_url'] . $authority;

                return [
                    'success' => true,
                    'gateway_url' => $gatewayUrl,
                    'transaction_id' => $authority,
                    'reference_id' => $result['data']['ref_id'] ?? null,
                    'gateway_response' => $result,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $this->getZarinpalErrorMessage($result['data']['code']),
                    'gateway_response' => $result,
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'خطا در ارتباط با درگاه زرین‌پال',
                'gateway_response' => ['error' => 'HTTP Error', 'status' => $response->status()],
            ];
        }

    }

    /**
     * تایید با درگاه
     */
    private function verifyWithGateway(array $callbackData, array $gatewayConfig, int $paymentAmount): array
    {
        $gatewayType = $gatewayConfig['type'] ?? 'zarinpal_test';

        switch ($gatewayType) {
            case 'melli_test':
                return $this->verifyWithMelli($callbackData, $gatewayConfig);

            case 'zarinpal':
            case 'zarinpal_test':
                return $this->verifyWithZarinpal($callbackData, $gatewayConfig, $paymentAmount);

            default:
                throw new \Exception("نوع درگاه {$gatewayType} پشتیبانی نمی‌شود");
        }
    }

    /**
     * تایید با درگاه بانک ملی
     */
    private function verifyWithMelli(array $callbackData, array $gatewayConfig): array
    {
        $token = $callbackData['Token'] ?? null;
        $resCode = $callbackData['ResCod'] ?? null;

        if (!$token) {
            return [
                'success' => false,
                'message' => 'شناسه تراکنش یافت نشد',
                'gateway_response' => $callbackData,
            ];
        }

        // اگر ResCode = 0 نباشد، پرداخت ناموفق است
        if ($resCode !== '0') {
            return [
                'success' => false,
                'message' => $this->getMelliErrorMessage($resCode),
                'gateway_response' => $callbackData,
            ];
        }

        $data = [
            'MerchantID' => $gatewayConfig['merchant_id'],
            'TerminalID' => $gatewayConfig['terminal_id'],
            'Token' => $token,
            'SignData' => $this->generateMelliVerifySignData($gatewayConfig['merchant_id'], $gatewayConfig['terminal_id'], $token, $gatewayConfig['key']),
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($gatewayConfig['verify_url'], $data);

        if ($response->successful()) {
            $result = $response->json();

            if (isset($result['ResCod']) && $result['ResCod'] == '0') {
                return [
                    'success' => true,
                    'message' => 'پرداخت با موفقیت انجام شد',
                    'gateway_response' => $result,
                    'ref_id' => $result['RefNum'],
                    'amount' => $result['Amount'],
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $this->getMelliErrorMessage($result['ResCod'] ?? 'unknown'),
                    'gateway_response' => $result,
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت',
                'gateway_response' => ['error' => 'HTTP Error', 'status' => $response->status()],
            ];
        }
    }

    /**
     * تولید SignData برای تایید بانک ملی
     */
    private function generateMelliVerifySignData(string $merchantId, string $terminalId, string $token, string $key): string
    {
        $data = $merchantId . $terminalId . $token . $key;
        return base64_encode(sha1($data, true));
    }

    /**
     * تایید با درگاه زرین‌پال
     */
    private function verifyWithZarinpal(array $callbackData, array $gatewayConfig, int $paymentAmount): array
    {
        $authority = $callbackData['Authority'] ?? null;
        $status = $callbackData['Status'] ?? null;

        if (!$authority) {
            return [
                'success' => false,
                'message' => 'شناسه تراکنش یافت نشد',
                'gateway_response' => $callbackData,
            ];
        }

        // اگر status = OK نباشد، پرداخت ناموفق است
        if ($status !== 'OK') {
            return [
                'success' => false,
                'message' => 'پرداخت توسط کاربر لغو شد',
                'gateway_response' => $callbackData,
            ];
        }

        $data = [
            'merchant_id' => $gatewayConfig['merchant_id'],
            'authority' => $authority,
            'amount' => $paymentAmount ?? 0,
        ];

        $response = Http::withHeaders([
            'accept' => 'application/json',
            'content-type' => 'application/json',
        ])->post($gatewayConfig['verify_url'], $data);

        if ($response->successful()) {
            $result = $response->json();

            if ($result['data']['code'] == 100) {
                return [
                    'success' => true,
                    'message' => 'پرداخت با موفقیت انجام شد',
                    'gateway_response' => $result,
                    'ref_id' => $result['data']['ref_id'],
//                    'amount' => $result['data']['amount'],
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $this->getZarinpalErrorMessage($result['data']['code']),
                    'gateway_response' => $result,
                ];
            }
        } else {
            return [
                'success' => false,
                'message' => 'خطا در تایید پرداخت',
                'gateway_response' => ['error' => 'HTTP Error', 'status' => $response->status()],
            ];
        }
    }

    /**
     * یافتن پرداخت بر اساس داده‌های callback
     */
    private function findPaymentByCallbackData(array $callbackData, string $gateway): ?PaymentMembership
    {
        $gatewayConfig = $this->gateways[$gateway] ?? null;
        if (!$gatewayConfig) {
            return null;
        }

        $gatewayType = $gatewayConfig['type'] ?? 'melli_test';

        if ($gatewayType === 'melli_test') {
            $token = $callbackData['Token'] ?? null;
            if (!$token) {
                return null;
            }
            return PaymentMembership::where('bank_transaction_id', $token)
                ->where('method', PaymentMethod::ONLINE)
                ->with(['membership', 'user'])
                ->first();
        } else {
            // برای زرین‌پال
            $authority = $callbackData['Authority'] ?? null;
            if (!$authority) {
                return null;
            }
            return PaymentMembership::where('bank_transaction_id', $authority)
                ->where('method', PaymentMethod::ONLINE)
                ->with(['membership', 'user'])
                ->first();
        }
    }

    /**
     * بروزرسانی وضعیت پرداخت
     */
    private function updatePaymentStatus(PaymentMembership $payment, array $result): void
    {
        $updateData = [
            'gateway_response' => json_encode($result['gateway_response']),
        ];

        if ($result['success']) {
            $updateData['status'] = PaymentStatus::COMPLETED;
            $updateData['paid_at'] = now();
            $updateData['bank_reference_id'] = $result['ref_id'] ?? null;
        } else {
            $updateData['status'] = PaymentStatus::FAILED;
        }

        $payment->update($updateData);
    }

    /**
     * بروزرسانی وضعیت سفارش
     */
    private function updateOrderStatus(Membership $membership, bool $paymentSuccess): void
    {
        if ($paymentSuccess) {
            $membership->update([
                'payment_status' => 'paid',
                'status' => 'paid'
            ]);
        } else {
            $membership->update([
                'payment_status' => 'unpaid'
            ]);
        }
    }

    /**
     * دریافت پیام خطای زرین‌پال
     */
    private function getZarinpalErrorMessage(int $code): string
    {
        $messages = [
            -1 => 'اطلاعات ارسال شده ناقص است',
            -2 => 'IP یا مرچنت کد پذیرنده صحیح نیست',
            -3 => 'با توجه به محدودیت های شاپینگ امکان پرداخت با رقم درخواست شده میسر نمی باشد',
            -4 => 'سطح تایید پذیرنده پایین تر از سطح نقره ای است',
            -11 => 'درخواست مورد نظر یافت نشد',
            -12 => 'امکان ویرایش درخواست میسر نمی باشد',
            -21 => 'هیچ نوع عملیات مالی برای این تراکنش یافت نشد',
            -22 => 'تراکنش ناموفق می باشد',
            -33 => 'رقم تراکنش با رقم پرداخت شده مطابقت ندارد',
            -34 => 'سقف تقسیم تراکنش از لحاظ تعداد یا رقم عبور نموده است',
            -40 => 'اجازه دسترسی به متد مربوطه وجود ندارد',
            -41 => 'اطلاعات ارسال شده مربوط به AdditionalData غیرمعتبر می باشد',
            -42 => 'مدت زمان معتبر طول عمر شناسه پرداخت باید بین 30 دقیقه تا 45 روز می باشد',
            -54 => 'درخواست مورد نظر آرشیو شده است',
            100 => 'عملیات با موفقیت انجام گردیده است',
            101 => 'عملیات پرداخت قبلا با موفقیت انجام شده است',
        ];

        return $messages[$code] ?? "خطای نامشخص با کد {$code}";
    }

    /**
     * دریافت لیست درگاه‌های فعال
     */
    public function getActiveGateways(): array
    {
        $active = [];

        foreach ($this->gateways as $key => $gateway) {
            if ($gateway['enabled']) {
                $active[$key] = [
                    'name' => $gateway['name'],
                    'type' => $gateway['type'],
                    'is_default' => $key === $this->defaultGateway,
                ];
            }
        }

        return $active;
    }
}
