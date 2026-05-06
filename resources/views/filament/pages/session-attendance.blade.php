<x-filament-panels::page>
    <div style="margin-bottom:1rem">
        <div style="font-size:0.82rem;color:#94A3B8;margin-bottom:0.25rem">
            {{ $record->date->format('l, d F Y') }}
            &bull; {{ substr($record->start_time, 0, 5) }}
            @if($record->end_time) – {{ substr($record->end_time, 0, 5) }} @endif
            &bull; {{ $record->venue ?? 'Main Hall' }}
        </div>
        <h2 style="font-size:1.1rem;font-weight:800;color:#0B2455">{{ $record->title }}</h2>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
