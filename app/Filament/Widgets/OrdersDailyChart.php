<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Order;
use Illuminate\Support\Carbon;

class OrdersDailyChart extends LineChartWidget
{
    protected static ?string $heading = 'نمودار سفارشات روزانه (۳۰ روز گذشته)';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $orders = Order::query()
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($order) => $order->created_at->format('Y-m-d'));

        $labels = [];
        $data = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $label = $date->format('Y-m-d');
            if (function_exists('verta')) {
                $labels[] = verta($label)->format('Y/m/d');
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
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59,130,246,0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }
} 