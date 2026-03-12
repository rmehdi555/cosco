<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use App\Models\Order;

class OrdersStatusPieChart extends PieChartWidget
{
    protected ?string $heading = 'نمودار وضعیت سفارش‌ها';

    protected function getData(): array
    {
        $total = Order::count();
        $statuses = Order::select('status')
            ->selectRaw('count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $labels = [];
        $data = [];
        $colors = ['#3b82f6', '#10b981', '#f59e42', '#ef4444', '#a78bfa', '#fbbf24'];
        $i = 0;
        foreach ($statuses as $status => $count) {
            $labels[] = __("وضعیت: ") . __($status);
            $data[] = $count;
            $i++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'درصد سفارش‌ها',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getCenterLabel(): string
    {
        $total = Order::count();
        return "مجموع سفارش‌ها: $total";
    }
} 