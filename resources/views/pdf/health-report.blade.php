<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 20mm 18mm 18mm 18mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9pt; color: #1F2937; background: #fff; line-height: 1.5; }

        /* ── Cover strip ── */
        .cover { background: #0B2D6B; padding: 8mm 0 7mm; text-align: center; margin-bottom: 5mm; }
        .cover-tag   { font-size: 6pt; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 3mm; }
        .cover-title { font-size: 18pt; font-weight: bold; color: #fff; margin-bottom: 1.5mm; }
        .cover-sub   { font-size: 8.5pt; color: rgba(255,255,255,.6); margin-bottom: 5mm; }
        .cover-meta  { width: auto; border-collapse: collapse; margin: 0 auto; }
        .cover-meta td { text-align: center; padding: 1.5mm 7mm; border-right: 0.4pt solid rgba(255,255,255,.1); }
        .cover-meta td:last-child { border-right: none; }
        .cm-lbl { display: block; font-size: 5.5pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.35); margin-bottom: 1mm; }
        .cm-val { display: block; font-size: 11pt; font-weight: bold; color: #fff; }

        /* ── Confidential banner ── */
        .conf { background: #FEF3C7; border: 0.5pt solid #D97706; border-radius: 3pt; padding: 2.5mm 4mm; margin-bottom: 6mm; font-size: 7.5pt; color: #78350F; text-align: center; font-weight: bold; }

        /* ── District heading ── */
        .dist-head { background: #1E3A5F; color: #fff; padding: 2.5mm 4mm; font-size: 8pt; font-weight: bold; letter-spacing: 0.5pt; margin-bottom: 0; margin-top: 7mm; border-radius: 3pt 3pt 0 0; }
        .dist-head-first { margin-top: 0; }

        /* ── Camper block ── */
        .cb { border: 0.5pt solid #E5E7EB; border-top: none; margin-bottom: 5mm; page-break-inside: avoid; }

        /* Camper header row */
        .cb-head { width: 100%; border-collapse: collapse; border-bottom: 0.4pt solid #E5E7EB; }
        .cb-head td { padding: 3mm 4mm; vertical-align: top; }
        .cb-name { font-size: 10pt; font-weight: bold; color: #0B2D6B; margin-bottom: 1mm; }
        .cb-meta { font-size: 7.5pt; color: #6B7280; }
        .cb-right { font-size: 7.5pt; color: #374151; text-align: right; }
        .cb-phone { font-weight: bold; color: #111827; font-size: 8pt; }
        .cb-num   { color: #6B7280; margin-top: 1mm; }

        /* Health rows */
        .ht { width: 100%; border-collapse: collapse; }
        .ht td { padding: 2mm 4mm; border-bottom: 0.3pt solid #F3F4F6; vertical-align: top; }
        .ht tr:last-child td { border-bottom: none; }
        .ht-label { width: 35mm; font-size: 7pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.3pt; color: #9CA3AF; }
        .ht-val   { font-size: 8.5pt; color: #111827; }
        .ht-none  { font-size: 8.5pt; color: #D1D5DB; font-style: italic; }

        /* Guardian row */
        .gd { background: #EEF2FF; border-top: 0.4pt solid #C5D2EC; }
        .gd td { padding: 2.5mm 4mm; font-size: 8pt; }
        .gd-lbl { font-weight: bold; color: #3730A3; }

        /* Footer */
        .rf { border-top: 0.5pt solid #E5E7EB; padding-top: 2.5mm; margin-top: 6mm; }
        .rf-table { width: 100%; border-collapse: collapse; }
        .rf-table td { font-size: 6pt; color: #9CA3AF; }
        .rf-right  { text-align: right; }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

{{-- Cover --}}
<div class="cover">
    <div class="cover-tag">Ogun Conference Youth Congress 2026 &#183; Confidential</div>
    <div class="cover-title">Health &amp; Medical Report</div>
    <div class="cover-sub">Campers with recorded medical conditions, medications, or allergies</div>
    <table class="cover-meta">
        <tr>
            <td><span class="cm-lbl">Total Campers</span><span class="cm-val">{{ $totalCount }}</span></td>
            <td><span class="cm-lbl">Districts</span><span class="cm-val">{{ $byDistrict->count() }}</span></td>
            <td><span class="cm-lbl">Generated</span><span class="cm-val" style="font-size:8pt">{{ $generatedAt }}</span></td>
        </tr>
    </table>
</div>

<div class="conf">&#128274; CONFIDENTIAL &#8212; For Health Team Use Only &#8212; Do Not Distribute</div>

@foreach($byDistrict as $districtName => $campers)
    @if(! $loop->first)<div class="page-break"></div>@endif

    <div class="dist-head {{ $loop->first ? 'dist-head-first' : '' }}">
        {{ $districtName }} &nbsp;&#183;&nbsp;
        {{ $campers->count() }} {{ $campers->count() === 1 ? 'camper' : 'campers' }} with health records
    </div>

    @foreach($campers as $camper)
        @php
            $health   = $camper->health;
            $guardian = $camper->contacts->first();
        @endphp

        <div class="cb">

            {{-- Camper header --}}
            <table class="cb-head">
                <tr>
                    <td width="70%">
                        <div class="cb-name">{{ ucwords(strtolower($camper->full_name)) }}</div>
                        <div class="cb-meta">
                            {{ $camper->category?->label() ?? '&#8212;' }}
                            @if($camper->club_rank)&nbsp;&#183;&nbsp;{{ $camper->club_rank }}@endif
                            &nbsp;&#183;&nbsp;{{ $camper->church?->name ?? '&#8212;' }}
                        </div>
                    </td>
                    <td width="30%" class="cb-right">
                        <div class="cb-phone">{{ $camper->phone ?: '&#8212;' }}</div>
                        <div class="cb-num">{{ $camper->camper_number }}</div>
                    </td>
                </tr>
            </table>

            {{-- Health details --}}
            <table class="ht">
                <tr>
                    <td class="ht-label">Medical Conditions</td>
                    <td class="{{ $health?->medical_conditions ? 'ht-val' : 'ht-none' }}">
                        {{ $health?->medical_conditions ?: 'None recorded' }}
                    </td>
                </tr>
                <tr>
                    <td class="ht-label">Medications</td>
                    <td class="{{ $health?->medications ? 'ht-val' : 'ht-none' }}">
                        {{ $health?->medications ?: 'None recorded' }}
                    </td>
                </tr>
                <tr>
                    <td class="ht-label">Allergies</td>
                    <td class="{{ $health?->allergies ? 'ht-val' : 'ht-none' }}">
                        {{ $health?->allergies ?: 'None recorded' }}
                    </td>
                </tr>
                @if($health?->doctor_name || $health?->doctor_phone)
                    <tr>
                        <td class="ht-label">Doctor</td>
                        <td class="ht-val">
                            {{ implode(' &#183; ', array_filter([$health->doctor_name, $health->doctor_phone])) }}
                        </td>
                    </tr>
                @endif
            </table>

            {{-- Guardian --}}
            @if($guardian)
                <table class="ht gd">
                    <tr>
                        <td class="ht-label" style="padding:2.5mm 4mm">
                            <span class="gd-lbl">Guardian</span>
                        </td>
                        <td style="padding:2.5mm 4mm;font-size:8pt;color:#1F2937">
                            <strong>{{ ucwords(strtolower($guardian->full_name ?? '')) }}</strong>
                            @if($guardian->relationship)({{ $guardian->relationship }})@endif
                            @if($guardian->phone)&nbsp;&#183;&nbsp; {{ $guardian->phone }}@endif
                            @if($guardian->email)&nbsp;&#183;&nbsp; {{ $guardian->email }}@endif
                        </td>
                    </tr>
                </table>
            @endif

        </div>
    @endforeach
@endforeach

@if($byDistrict->isEmpty())
    <div style="text-align:center;padding:20mm 0;color:#6B7280">
        <p style="font-size:10pt;font-weight:bold;color:#374151;margin-bottom:2mm">No health records found</p>
        <p>No campers have recorded medical conditions, medications, or allergies.</p>
    </div>
@endif

<div class="rf">
    <table class="rf-table">
        <tr>
            <td><strong style="color:#374151">Ogun Conference Youth Congress 2026</strong> &#8212; Health &amp; Medical Report</td>
            <td class="rf-right">CONFIDENTIAL &#183; {{ $generatedAt }}</td>
        </tr>
    </table>
</div>

</body>
</html>
