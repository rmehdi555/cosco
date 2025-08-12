<?php

namespace App\Filament\ExcelExport;

use App\Models\Order;
use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderExport extends BaseExport
{
    /**
     * Export all orders to CSV format
     */
    public static function exportAll(): StreamedResponse
    {
        $orders = Order::with(['user', 'items.product'])->get();
        
        $filename = self::getFilename('orders');
        
        return self::generateCsvResponse($orders, $filename);
    }

    /**
     * Export selected orders to CSV format
     */
    public static function exportSelected(Collection $selectedRecords): StreamedResponse
    {
        $orders = $selectedRecords->load(['user', 'items.product']);
        
        $filename = self::getFilename('orders', true);
        
        return self::generateCsvResponse($orders, $filename);
    }

    /**
     * Get CSV column headers in Persian
     */
    protected static function getCsvHeaders(): array
    {
        return [
            'شماره سفارش',
            'نام مشتری',
            'وضعیت سفارش',
            'مبلغ کل',
            'تاریخ سفارش',
            'آدرس تحویل',
        ];
    }

    /**
     * Format order data for CSV export
     */
    protected static function formatRecordData($order): array
    {
        return [
            $order->id,
            $order->user?->first_name . ' ' . $order->user?->last_name,
            self::formatOrderStatus($order->status),
            $order->total_amount ? number_format($order->total_amount) . ' ریال' : '-',
            $order->created_at ? Verta::instance($order->created_at)->format('Y/n/j H:i') : '-',
            $order->shipping_address ?? '-',
        ];
    }

    /**
     * Format order status for display
     */
    private static function formatOrderStatus(?string $status): string
    {
        return match($status) {
            'pending' => 'در انتظار',
            'processing' => 'در حال پردازش',
            'shipped' => 'ارسال شده',
            'delivered' => 'تحویل داده شده',
            'cancelled' => 'لغو شده',
            default => $status ?? '-'
        };
    }
}
