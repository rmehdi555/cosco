<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Article;

class StatsOverview extends Widget
{
    protected string $view = 'filament.widgets.stats-overview';

    protected function getViewData(): array
    {
        return [
            'usersCount' => User::count(),
            'ordersCount' => Order::count(),
            'productsCount' => Product::count(),
            'articlesCount' => Article::count(),
        ];
    }
} 