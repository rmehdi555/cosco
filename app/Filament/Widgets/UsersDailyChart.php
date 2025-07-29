<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\User;

class UsersDailyChart extends LineChartWidget
{
    protected static ?string $heading = 'نمودار ثبت‌نام کاربران (روزانه - ۳۰ روز گذشته)';

    protected function getData(): array
    {
        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();
        $users = User::query()
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->groupBy(fn($user) => $user->created_at->format('Y-m-d'));

        $labels = [];
        $data = [];
        for ($date = $start->copy(); $date <= $end; $date->addDay()) {
            $label = $date->format('Y-m-d');
            if (function_exists('verta')) {
                $labels[] = verta($label)->format('Y/m/d');
            } else {
                $labels[] = $label;
            }
            $data[] = isset($users[$label]) ? $users[$label]->count() : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'تعداد کاربران',
                    'data' => $data,
                    'borderColor' => '#f59e42',
                    'backgroundColor' => 'rgba(245,158,66,0.1)',
                ],
            ],
            'labels' => $labels,
        ];
    }
} 