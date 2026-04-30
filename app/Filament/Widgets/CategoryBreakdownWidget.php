<?php

namespace App\Filament\Widgets;

use App\Enums\CamperCategory;
use App\Models\Camper;
use Filament\Widgets\ChartWidget;

class CategoryBreakdownWidget extends ChartWidget
{
    protected static ?string $heading       = 'Campers by Category';
    protected static ?int    $sort          = 2;
    protected static ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $counts = Camper::selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        return [
            'datasets' => [
                [
                    'data'            => [
                        $counts->get(CamperCategory::ADVENTURER->value, 0),
                        $counts->get(CamperCategory::PATHFINDER->value, 0),
                        $counts->get(CamperCategory::SENIOR_YOUTH->value, 0),
                    ],
                    'backgroundColor' => ['#1B6BB5', '#1A6B3A', '#C9A94D'],
                ],
            ],
            'labels' => [
                CamperCategory::ADVENTURER->label(),
                CamperCategory::PATHFINDER->label(),
                CamperCategory::SENIOR_YOUTH->label(),
            ],
        ];
    }
}
