<?php

namespace App\Filament\Widgets;

use Filament\Widgets\LineChartWidget;
use App\Models\User;

class UsersMonthlyChart extends LineChartWidget
{
    protected static ?string $heading = 'نمودار ثبت‌نام کاربران (ماهانه)';

    protected function getData(): array
    {
        $year = now()->year;
        $users = User::query()
            ->whereYear('created_at', $year)
            ->get()
            ->groupBy(fn($user) => $user->created_at->format('Y-m'));

        $labels = [];
        $data = [];
        for ($month = 1; $month <= 12; $month++) {
            $label = sprintf('%04d-%02d', $year, $month);
            if (function_exists('verta')) {
                $labels[] = verta($label.'-01')->format('Y/m');
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