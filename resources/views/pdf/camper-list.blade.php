<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 15mm 12mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1C2340; }

        .header { text-align: center; margin-bottom: 6mm; border-bottom: 1pt solid #0B2455; padding-bottom: 3mm; }
        .camp-name { font-size: 13pt; font-weight: bold; color: #0B2455; }
        .report-title { font-size: 10pt; color: #1B3A8F; margin: 1mm 0; }
        .meta { font-size: 8pt; color: #64718F; }

        .summary-bar { display: table; width: 100%; margin-bottom: 6mm; }
        .sum-cell { display: table-cell; width: 25%; background: #F4F6FB; border: 0.3pt solid #D1D9F0; padding: 2.5mm; text-align: center; }
        .sum-lbl { font-size: 6.5pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5pt; }
        .sum-val { font-size: 13pt; font-weight: bold; color: #0B2455; margin-top: 1mm; }

        .church-section { margin-bottom: 6mm; }
        .church-header { background: #EEF2FF; border-left: 3pt solid #0B2455; padding: 2mm 3mm; font-size: 8.5pt; font-weight: bold; color: #0B2455; margin-bottom: 2mm; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0B2455; color: #fff; }
        thead th { padding: 2mm; text-align: left; font-size: 7.5pt; }
        tbody tr:nth-child(even) { background: #F7F8FC; }
        tbody td { padding: 1.8mm 2mm; font-size: 8pt; border-bottom: 0.3pt solid #E5E7EB; }
        .td-num { color: #94A3B8; width: 5mm; text-align: center; }
        .td-code { font-family: DejaVu Sans Mono; font-size: 7pt; color: #1B3A8F; }
        .td-name { font-weight: bold; }

        .church-total { font-size: 7.5pt; color: #64748B; text-align: right; padding: 1.5mm 2mm; font-style: italic; }

        .footer { margin-top: 6mm; border-top: 0.4pt solid #E5E7EB; padding-top: 2mm; text-align: center; font-size: 7.5pt; color: #94A3B8; }
    </style>
</head>
<body>

<div class="header">
    <div class="camp-name">Ogun Conference Youth Congress 2026</div>
    <div class="report-title">Camper Registration Report
        @if($filters['church']) — {{ $filters['church']->name }}
        @elseif($filters['district']) — {{ $filters['district']->name }} District
        @endif
    </div>
    <div class="meta">Generated: {{ now()->format('l, d F Y g:i A') }}</div>
</div>

<div class="summary-bar">
    <div class="sum-cell">
        <div class="sum-lbl">Total Campers</div>
        <div class="sum-val">{{ $campers->count() }}</div>
    </div>
    <div class="sum-cell">
        <div class="sum-lbl">Adventurers</div>
        <div class="sum-val">{{ $campers->where('category->value', 'adventurer')->count() ?: $campers->filter(fn($c) => ($c->category?->value ?? $c->category) === 'adventurer')->count() }}</div>
    </div>
    <div class="sum-cell">
        <div class="sum-lbl">Pathfinders</div>
        <div class="sum-val">{{ $campers->filter(fn($c) => ($c->category?->value ?? $c->category) === 'pathfinder')->count() }}</div>
    </div>
    <div class="sum-cell">
        <div class="sum-lbl">Senior Youth</div>
        <div class="sum-val">{{ $campers->filter(fn($c) => ($c->category?->value ?? $c->category) === 'senior_youth')->count() }}</div>
    </div>
</div>

@foreach($byChurch as $churchId => $churchCampers)
    @php $church = $churchCampers->first()->church; @endphp
    <div class="church-section">
        <div class="church-header">{{ $church?->name ?? 'Unknown Church' }} — {{ $church?->district?->name }}</div>
        <table>
            <thead>
            <tr>
                <th class="td-num">#</th>
                <th style="width:24mm">Code</th>
                <th>Full Name</th>
                <th style="width:18mm">Category</th>
                <th style="width:18mm">Rank</th>
                <th style="width:14mm">Gender</th>
                <th style="width:12mm">Consent</th>
            </tr>
            </thead>
            <tbody>
            @foreach($churchCampers->values() as $i => $camper)
                <tr>
                    <td class="td-num">{{ $i + 1 }}</td>
                    <td class="td-code">{{ $camper->camper_number }}</td>
                    <td class="td-name">{{ $camper->full_name }}</td>
                    <td>{{ $camper->category?->label() }}</td>
                    <td>{{ $camper->club_rank ?? '—' }}</td>
                    <td>{{ ucfirst($camper->gender?->value ?? '—') }}</td>
                    <td>{{ $camper->consent_collected ? '✓' : '—' }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div class="church-total">{{ $churchCampers->count() }} camper(s) from {{ $church?->name }}</div>
    </div>
@endforeach

<div class="footer">
    Ogun Conference Youth Congress 2026 &bull; Abeokuta, Ogun State &bull; Printed: {{ now()->format('d M Y, g:i A') }}
</div>
</body>
</html>
