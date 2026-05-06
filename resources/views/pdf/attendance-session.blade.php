<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 15mm 12mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1C2340; }

        .header { text-align: center; margin-bottom: 8mm; border-bottom: 1pt solid #0B2455; padding-bottom: 4mm; }
        .camp-name { font-size: 13pt; font-weight: bold; color: #0B2455; }
        .session-title { font-size: 11pt; font-weight: bold; color: #1B3A8F; margin: 2mm 0 1mm; }
        .meta { font-size: 8pt; color: #64718F; }

        .summary { display: table; width: 100%; margin-bottom: 6mm; }
        .summary-cell { display: table-cell; width: 33%; background: #F4F6FB; border: 0.4pt solid #D1D9F0; padding: 3mm; text-align: center; }
        .summary-lbl { font-size: 7pt; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.5pt; }
        .summary-val { font-size: 14pt; font-weight: bold; color: #0B2455; margin-top: 1mm; }

        table { width: 100%; border-collapse: collapse; }
        thead tr { background: #0B2455; color: #fff; }
        thead th { padding: 2.5mm 2mm; text-align: left; font-size: 8pt; font-weight: bold; letter-spacing: 0.3pt; }
        tbody tr:nth-child(even) { background: #F7F8FC; }
        tbody tr:nth-child(odd)  { background: #fff; }
        tbody td { padding: 2mm; font-size: 8.5pt; border-bottom: 0.3pt solid #E5E7EB; vertical-align: middle; }
        .td-num  { color: #94A3B8; width: 6mm; text-align: center; }
        .td-code { font-family: DejaVu Sans Mono; font-size: 7.5pt; color: #1B3A8F; }
        .td-name { font-weight: bold; }
        .td-cat  { }
        .td-church { font-size: 8pt; }
        .td-time { font-size: 8pt; color: #475569; }

        .footer { margin-top: 8mm; border-top: 0.4pt solid #E5E7EB; padding-top: 3mm; text-align: center; font-size: 7.5pt; color: #94A3B8; }
        .signature-row { display: table; width: 100%; margin-top: 10mm; }
        .sig-cell { display: table-cell; width: 33%; text-align: center; padding-top: 8mm; border-top: 0.4pt solid #111; }
        .sig-lbl { font-size: 7.5pt; color: #64718F; }
    </style>
</head>
<body>

<div class="header">
    <div class="camp-name">{{ setting('camp_name','Ogun Conference Youth Congress 2026') }}</div>
    <div class="session-title">{{ $session->title }}</div>
    <div class="meta">
        {{ $session->date->format('l, d F Y') }}
        &bull; {{ $session->start_time }} {{ $session->end_time ? '– ' . $session->end_time : '' }}
        &bull; {{ $session->venue ?? 'Main Hall' }}
    </div>
</div>

<div class="summary">
    <div class="summary-cell">
        <div class="summary-lbl">Total Attendees</div>
        <div class="summary-val">{{ $campers->count() }}</div>
    </div>
    <div class="summary-cell">
        <div class="summary-lbl">Session Date</div>
        <div class="summary-val" style="font-size:10pt">{{ $session->date->format('d M Y') }}</div>
    </div>
    <div class="summary-cell">
        <div class="summary-lbl">Generated At</div>
        <div class="summary-val" style="font-size:10pt">{{ now()->format('H:i, d M') }}</div>
    </div>
</div>

<table>
    <thead>
    <tr>
        <th class="td-num">#</th>
        <th style="width:22mm">Code</th>
        <th>Full Name</th>
        <th style="width:18mm">Category</th>
        <th>Church</th>
        <th>District</th>
        <th style="width:16mm">Time</th>
    </tr>
    </thead>
    <tbody>
    @foreach($campers as $i => $camper)
        <tr>
            <td class="td-num">{{ $i + 1 }}</td>
            <td class="td-code">{{ $camper->camper_number }}</td>
            <td class="td-name">{{ $camper->full_name }}</td>
            <td class="td-cat">{{ $camper->category?->label() }}</td>
            <td class="td-church">{{ $camper->church?->name ?? '—' }}</td>
            <td class="td-church">{{ $camper->church?->district?->name ?? '—' }}</td>
            <td class="td-time">
                {{ $camper->attended_at ? \Illuminate\Support\Carbon::parse($camper->attended_at)->format('H:i') : '—' }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

<div class="signature-row">
    <div class="sig-cell"><div class="sig-lbl">Secretariat Officer</div></div>
    <div class="sig-cell"><div class="sig-lbl">Camp Director</div></div>
    <div class="sig-cell"><div class="sig-lbl">Date</div></div>
</div>

<div class="footer">
    Ogun Conference Youth Congress 2026 &bull; {{ setting('camp_venue','Abeokuta, Ogun State') }}
    &bull; Printed: {{ now()->format('d M Y, H:i') }}
</div>
</body>
</html>
