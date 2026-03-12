<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class PaymentsStatusLineChart extends LineChartWidget
{
    protected ?string $heading = 'نمودار روند پرداخت‌ها بر اساس وضعیت (۳۰ روز گذشته)';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $statuses = PaymentStatus::cases();
        $labels = [];
        $datasets = [];
        $colors = ['#3b82f6', '#10b981', '#f59e42', '#ef4444', '#a78bfa', '#fbbf24'];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $labels[] = function_exists('verta') ? verta($date->format('Y-m-d'))->format('Y/m/d') : $date->format('Y-m-d');
        }
        $i = 0;
        foreach ($statuses as $status) {
            $data = [];
            for ($date = $start->copy(); $date <= $end; $date->addDay()) {
                $sum = Payment::where('status', $status->value)
                    ->whereDate('created_at', $date->format('Y-m-d'))
                    ->sum('amount');
                $data[] = $sum;
            }
            $label = method_exists($status, 'getLabel') ? $status->getLabel() : $status->name;
            $datasets[] = [
                'label' => $label,
                'data' => $data,
                'borderColor' => $colors[$i % count($colors)],
                'backgroundColor' => $colors[$i % count($colors)] . '33',
            ];
            $i++;
        }
        return [
            'datasets' => $datasets,
            'labels' => $labels,
        ];
    }
} 