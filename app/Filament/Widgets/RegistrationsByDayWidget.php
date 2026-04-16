<?php

namespace App\Filament\Widgets;

use App\Models\Camper;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RegistrationsByDayWidget extends ChartWidget
{
    protected static ?string $heading       = 'Registrations (Last 14 Days)';
    protected static ?int    $sort          = 3;
    protected static ?string $pollingInterval = '60s';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $days = collect(range(13, 0))->map(fn ($i) => now()->subDays($i)->startOfDay());

        $counts = Camper::selectRaw('DATE(created_at) as date, count(*) as total')
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label'           => 'Registrations',
                    'data'            => $days->map(fn ($d) => $counts->get($d->toDateString(), 0))->toArray(),
                    'backgroundColor' => '#1B3A6B',
                ],
            ],
            'labels' => $days->map(fn ($d) => $d->format('d M'))->toArray(),
        ];
    }
}
