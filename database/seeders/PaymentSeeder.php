<?php

namespace Database\Seeders;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // دریافت سفارشات موجود
        $orders = Order::with('user')->get();

        if ($orders->isEmpty()) {
            $this->command->info('هیچ سفارشی یافت نشد. ابتدا سفارشات را ایجاد کنید.');
            return;
        }

        foreach ($orders as $order) {
            // ایجاد پرداخت برای هر سفارش
            Payment::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'method' => $this->getRandomPaymentMethod(),
                'status' => $this->getRandomPaymentStatus(),
                'paid_at' => $this->getRandomPaidAt(),
                'amount' => $order->total_amount,
                'merchant_id' => $this->getRandomMerchantId(),
                'bank_transaction_id' => $this->getRandomTransactionId(),
                'bank_reference_id' => $this->getRandomReferenceId(),
                'description' => "پرداخت سفارش شماره {$order->id}",
                'callback_url' => route('payment.callback'),
                'gateway_response' => $this->getRandomGatewayResponse(),
            ]);
        }

        $this->command->info("تعداد {$orders->count()} پرداخت ایجاد شد.");
    }

    /**
     * Get random payment method
     */
    private function getRandomPaymentMethod(): PaymentMethod
    {
        $methods = PaymentMethod::cases();
        return $methods[array_rand($methods)];
    }

    /**
     * Get random payment status
     */
    private function getRandomPaymentStatus(): PaymentStatus
    {
        $statuses = PaymentStatus::cases();
        return $statuses[array_rand($statuses)];
    }

    /**
     * Get random paid at date
     */
    private function getRandomPaidAt(): ?string
    {
        // 70% احتمال پرداخت شده
        if (rand(1, 100) <= 70) {
            return now()->subDays(rand(1, 30))->format('Y-m-d H:i:s');
        }
        
        return null;
    }

    /**
     * Get random merchant ID
     */
    private function getRandomMerchantId(): ?string
    {
        $merchantIds = [
            'MERCH1234',
            'MERCH5678',
            'MERCH9012',
            'MERCH3456',
            'MERCH7890',
        ];
        
        return $merchantIds[array_rand($merchantIds)];
    }

    /**
     * Get random transaction ID
     */
    private function getRandomTransactionId(): ?string
    {
        return 'TXN' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Get random reference ID
     */
    private function getRandomReferenceId(): ?string
    {
        return 'REF' . str_pad(rand(1, 99999999), 8, '0', STR_PAD_LEFT);
    }

    /**
     * Get random gateway response
     */
    private function getRandomGatewayResponse(): ?string
    {
        $responses = [
            [
                'status' => 'success',
                'transaction_id' => $this->getRandomTransactionId(),
                'reference_id' => $this->getRandomReferenceId(),
                'amount' => rand(10000, 1000000),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'message' => 'پرداخت با موفقیت انجام شد',
            ],
            [
                'status' => 'failed',
                'error_code' => 'ERR' . rand(100, 999),
                'error_message' => 'خطا در پردازش پرداخت',
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ],
            [
                'status' => 'pending',
                'transaction_id' => $this->getRandomTransactionId(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'message' => 'پرداخت در انتظار تایید',
            ],
        ];

        return json_encode($responses[array_rand($responses)], JSON_UNESCAPED_UNICODE);
    }
} 