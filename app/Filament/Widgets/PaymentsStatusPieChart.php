<?php

namespace App\Filament\Widgets;

use Filament\Widgets\PieChartWidget;
use App\Models\Payment;
use App\Enums\PaymentStatus;

class PaymentsStatusPieChart extends PieChartWidget
{
    protected static ?string $heading = 'نمودار وضعیت پرداخت‌ها';

    protected function getData(): array
    {
        $statuses = PaymentStatus::cases();
        $labels = [];
        $data = [];
        $colors = ['#3b82f6', '#10b981', '#f59e42', '#ef4444', '#a78bfa', '#fbbf24'];
        $i = 0;
        foreach ($statuses as $status) {
            $label = method_exists($status, 'getLabel') ? $status->getLabel() : $status->name;
            $labels[] = $label;
            $sum = Payment::where('status', $status->value)->sum('amount');
            $data[] = $sum;
            $i++;
        }

        return [
            'datasets' => [
                [
                    'label' => 'مبلغ پرداخت‌ها',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                ],
            ],
            'labels' => $labels,
        ];
    }

    public function getCenterLabel(): string
    {
        $total = Payment::sum('amount');
        return 'مجموع پرداخت‌ها: ' . number_format($total) . ' ریال';
    }
} 