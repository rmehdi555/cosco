<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class SmsService
{
    protected $config;
    protected $provider;

    public function __construct()
    {
        $this->config = config('sms');
        $this->provider = $this->config['default'];
    }

    /**
     * Send SMS to single recipient
     *
     * @param string $to Phone number (with country code)
     * @param string $message SMS message content
     * @param string|null $from Override sender number
     * @return array Response with success status and message
     */
    public function send(string $to, string $message, string $from = null): array
    {
        return $this->sendBulk([$to], $message, $from);
    }

    /**
     * Send SMS to multiple recipients
     *
     * @param array $recipients Array of phone numbers
     * @param string $message SMS message content
     * @param string|null $from Override sender number
     * @return array Response with success status and message
     */
    public function sendBulk(array $recipients, string $message, string $from = null): array
    {
        try {
            // Validate inputs
            if (empty($recipients)) {
                throw new Exception('لیست گیرندگان خالی است');
            }

            if (empty($message)) {
                throw new Exception('متن پیام خالی است');
            }

            // Clean and format phone numbers
            $cleanRecipients = $this->formatPhoneNumbers($recipients);

            // Send based on provider
            switch ($this->provider) {
                case 'ippanel':
                    return $this->sendViaIpPanel($cleanRecipients, $message, $from);
                default:
                    throw new Exception("ارائه دهنده پیامک پشتیبانی نمی‌شود: {$this->provider}");
            }

        } catch (Exception $e) {
            $this->logError('SMS sending failed', [
                'recipients' => $recipients,
                'message' => $message,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * Send SMS via IPPanel service
     *
     * @param array $recipients
     * @param string $message
     * @param string|null $from
     * @return array
     */
    protected function sendViaIpPanel(array $recipients, string $message, string $from = null): array
    {
        $config = $this->config['ippanel'];
        $senderNumber = $from ?? $config['from'];

        // Prepare request data
        $data = [
            'uname' => $config['username'],
            'pass' => $config['password'],
            'from' => $senderNumber,
            'message' => $message,
            'to' => json_encode($recipients),
            'op' => 'send'
        ];

        // Log attempt if enabled
        if ($this->config['log_attempts']) {
            $this->logInfo('Sending SMS via IPPanel', [
                'recipients_count' => count($recipients),
                'message_length' => mb_strlen($message),
                'from' => $senderNumber
            ]);
        }

        // Send HTTP request
        $response = Http::timeout($config['timeout'])
            ->asForm()
            ->post($config['url'], $data);

        // Handle response
        if ($response->successful()) {
            $responseBody = $response->body();
            
            $this->logInfo('SMS sent successfully via IPPanel', [
                'recipients_count' => count($recipients),
                'response' => $responseBody
            ]);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => [
                    'response' => $responseBody,
                    'recipients_count' => count($recipients)
                ]
            ];
        } else {
            throw new Exception('خطا در ارسال پیامک: ' . $response->status() . ' - ' . $response->body());
        }
    }

    /**
     * Format and clean phone numbers
     *
     * @param array $phoneNumbers
     * @return array
     */
    protected function formatPhoneNumbers(array $phoneNumbers): array
    {
        $cleaned = [];
        
        foreach ($phoneNumbers as $phone) {
            // Remove spaces, dashes, and other characters
            $phone = preg_replace('/[^\d+]/', '', $phone);
            
            // Add country code if missing
            if (substr($phone, 0, 2) === '09') {
                $phone = '98' . substr($phone, 1);
            } elseif (substr($phone, 0, 1) === '9' && strlen($phone) === 10) {
                $phone = '98' . $phone;
            }
            
            // Validate format
            if (preg_match('/^98\d{10}$/', $phone)) {
                $cleaned[] = $phone;
            } else {
                throw new Exception("شماره تلفن معتبر نیست: {$phone}");
            }
        }
        
        return array_unique($cleaned);
    }

    /**
     * Send registration SMS for Refah Kala
     *
     * @param string $phoneNumber
     * @param string $name
     * @param string $code
     * @return array
     */
    public function sabtNamRefahKala(string $phoneNumber, string $name, string $code): array
    {
        $message = "سلام {$name} عزیز،\nثبت‌نام شما در سامانه رفاه کالا با موفقیت انجام شد.\nکد پیگیری: {$code}\nبا تشکر، رفاه کالا";
        
        return $this->send($phoneNumber, $message);
    }

    /**
     * Send verification code SMS
     *
     * @param string $phoneNumber
     * @param string $code
     * @return array
     */
    public function sendVerificationCode(string $phoneNumber, string $code): array
    {
        $message = "کد تایید شما: {$code}\nسامانه رفاه کالا";
        
        return $this->send($phoneNumber, $message);
    }

    /**
     * Send order status SMS
     *
     * @param string $phoneNumber
     * @param string $name
     * @param string $status
     * @param string $trackingCode
     * @return array
     */
    public function sendOrderStatus(string $phoneNumber, string $name, string $status, string $trackingCode): array
    {
        $message = "سلام {$name} عزیز،\nوضعیت سفارش شما: {$status}\nکد پیگیری: {$trackingCode}\nسامانه رفاه کالا";
        
        return $this->send($phoneNumber, $message);
    }

    /**
     * Log info message
     */
    protected function logInfo(string $message, array $context = []): void
    {
        if ($this->config['log_attempts']) {
            Log::info($message, $context);
        }
    }

    /**
     * Log error message
     */
    protected function logError(string $message, array $context = []): void
    {
        Log::error($message, $context);
    }

    /**
     * Get SMS service statistics
     *
     * @return array
     */
    public function getStats(): array
    {
        return [
            'provider' => $this->provider,
            'config' => [
                'url' => $this->config[$this->provider]['url'],
                'from' => $this->config[$this->provider]['from'],
                'timeout' => $this->config[$this->provider]['timeout'],
            ]
        ];
    }
}
