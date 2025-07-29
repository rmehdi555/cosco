<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use App\Filament\Widgets\OrdersDailyChart;
use App\Filament\Widgets\OrdersMonthlyChart;
use App\Filament\Widgets\UsersMonthlyChart;
use App\Filament\Widgets\UsersDailyChart;
use App\Filament\Widgets\OrdersStatusPieChart;
use App\Filament\Widgets\PaymentsStatusPieChart;
use App\Filament\Widgets\OrdersStatusLineChart;
use App\Filament\Widgets\PaymentsStatusLineChart;
use App\Filament\Widgets\StatsOverview;

class Dashboard extends BaseDashboard
{
    public function getTitle(): string
    {
        return 'پنل مدیریت کاسکو';
    }

    public function getHeading(): string
    {
        return 'پنل مدیریت کاسکو';
    }

    public function getWidgets(): array
    {
        return [
            OrdersStatusLineChart::class,
            PaymentsStatusLineChart::class,
            OrdersStatusPieChart::class,
            PaymentsStatusPieChart::class,
            OrdersDailyChart::class,
            OrdersMonthlyChart::class,
            UsersDailyChart::class,
            UsersMonthlyChart::class,
            StatsOverview::class,
        ];
    }

} 