<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Order;

class OrdersMonthlyChart extends LineChartWidget
{
    protected static ?string $heading = 'نمودار سفارشات ماهانه (سال جاری)';

    protected function getData(): array
    {
        $year = now()->year;
        $orders = Order::query()
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($order) => $order->created_at->format('Y-m'));

        $labels = [];
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $label = sprintf('%04d-%02d', $year, $month);
            if (function_exists('verta')) {
                $labels[] = verta($label.'-01')->format('Y/m');
            } else {
                $labels[] = $label;
            }
            $data[] = isset($orders[$label]) ? $orders[$label]->count() : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'تعداد سفارشات',
                    'data' => $data,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16,185,129,0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }
} 