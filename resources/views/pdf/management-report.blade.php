<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>

        /* ================================================================
           RESET & BASE
           ================================================================ */
        @page { margin: 28mm 24mm 26mm 24mm; size: A4; }
        * { margin: 0.5mm; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8.5pt;
            color: #1F2937;
            background: #FFFFFF;
            line-height: 1.6;
        }
        img { display: block; }

        /* ================================================================
           COLOUR TOKENS
           ================================================================ */
        /* Navy #173B7A | Gold #C8A34F | Success #2E8B57 | Warning #D98E04
           Danger #D9534F | Light #F8F9FB | Border #E5E7EB
           Text #1F2937 | Muted #6B7280 | White #FFFFFF               */

        /* ================================================================
           COVER PAGE
           ================================================================ */
        .cover {
            background: #173B7A;
            padding: 0;
            page-break-after: always;
            position: relative;
        }
        .cover-inner {
            padding: 16mm 22mm 20mm;
        }
        /* Simulated gradient accent strip at top */
        .cover-accent-top {
            height: 5mm;
            background: #C8A34F;
            width: 100%;
        }
        .cover-logo-wrap {
            text-align: center;
            margin-bottom: 7mm;
        }
        .cover-logo {
            width: 28mm;
            height: 28mm;
            border-radius: 50%;
            border: 2pt solid rgba(200,163,79,.6);
            display: inline-block;
        }
        .cover-org {
            text-align: center;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 3.5px;
            text-transform: uppercase;
            color: #C8A34F;
            margin-bottom: 5mm;
        }
        .cover-title {
            text-align: center;
            font-size: 28pt;
            font-weight: bold;
            color: #FFFFFF;
            line-height: 1.05;
            margin-bottom: 2.5mm;
        }
        .cover-subtitle {
            text-align: center;
            font-size: 11pt;
            color: rgba(255,255,255,.7);
            margin-bottom: 3mm;
        }
        .cover-theme {
            text-align: center;
            font-style: italic;
            font-size: 8pt;
            color: rgba(255,255,255,.45);
            margin-bottom: 9mm;
        }
        /* Gold divider */
        .cover-divider {
            border: none;
            border-top: 1pt solid #C8A34F;
            opacity: .5;
            margin: 0 18mm 9mm;
        }
        /* Cover KPI strip */
        .cover-kpi { width: 100%; border-collapse: collapse; margin-bottom: 12mm; }
        .cover-kpi td {
            text-align: center;
            padding: 4mm 3mm;
            border-right: 0.5pt solid rgba(255,255,255,.08);
        }
        .cover-kpi td:last-child { border-right: none; }
        .ck-label {
            display: block;
            font-size: 5.5pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.4);
            margin-bottom: 2mm;
        }
        .ck-value {
            display: block;
            font-size: 11pt;
            font-weight: bold;
            color: #FFFFFF;
        }
        .ck-value.gold { color: #D4B96E; }
        .cover-footer {
            text-align: center;
            font-size: 6pt;
            letter-spacing: 2px;
            color: rgba(255,255,255,.25);
            text-transform: uppercase;
        }

        /* ================================================================
           SECTION HEADERS (premium style)
           ================================================================ */
        .section-wrap { margin-top: 7mm; margin-bottom: 6mm; }
        .section-wrap:first-child { margin-top: 0; }
        .section-num {
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 3px;
            text-transform: uppercase;
            color: #C8A34F;
            margin-bottom: 1mm;
            display: block;
        }
        .section-title {
            font-size: 13pt;
            font-weight: bold;
            color: #173B7A;
            margin-bottom: 1mm;
        }
        .section-desc {
            font-size: 7pt;
            color: #6B7280;
            margin-bottom: 3mm;
        }
        .section-rule {
            border: none;
            border-top: 1pt solid #E5E7EB;
            margin-bottom: 5mm;
        }

        /* Sub-section label */
        .ssub {
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: #173B7A;
            border-left: 3pt solid #C8A34F;
            padding-left: 3mm;
            margin: 5mm 0 2.5mm;
        }

        /* ================================================================
           KPI CARDS
           ================================================================ */
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 6mm; }
        .kpi-card {
            border: 0.5pt solid #E5E7EB;
            border-radius: 6pt;
            padding: 4mm 3.5mm;
            text-align: center;
            vertical-align: top;
            background: #F8F9FB;
        }
        .kpi-icon {
            font-size: 13pt;
            display: block;
            margin-bottom: 2mm;
            line-height: 1;
        }
        .kpi-num {
            font-size: 18pt;
            font-weight: bold;
            color: #173B7A;
            display: block;
            line-height: 1;
            margin-bottom: 1.5mm;
        }
        .kpi-label {
            font-size: 5.5pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #6B7280;
            display: block;
        }
        /* Card accent variants */
        .kpi-card.navy  { background: #173B7A; border-color: #173B7A; }
        .kpi-card.navy  .kpi-num   { color: #D4B96E; }
        .kpi-card.navy  .kpi-label { color: rgba(255,255,255,.6); }
        .kpi-card.gold  { background: #FFFBF0; border-color: #E2B95A; }
        .kpi-card.gold  .kpi-num   { color: #B8872A; }
        .kpi-card.gold  .kpi-label { color: #B8872A; }
        .kpi-card.green { background: #F0FAF4; border-color: #6EC98A; }
        .kpi-card.green .kpi-num   { color: #2E8B57; }
        .kpi-card.green .kpi-label { color: #2E8B57; }
        .kpi-card.amber { background: #FFFBEB; border-color: #F0B429; }
        .kpi-card.amber .kpi-num   { color: #92400E; }
        .kpi-card.amber .kpi-label { color: #92400E; }
        .kpi-card.red   { background: #FEF2F2; border-color: #FECACA; }
        .kpi-card.red   .kpi-num   { color: #B91C1C; }
        .kpi-card.red   .kpi-label { color: #B91C1C; }

        /* ================================================================
           REVENUE CARDS (inline side-by-side)
           ================================================================ */
        .rev-card {
            border-radius: 5pt;
            padding: 4mm;
            vertical-align: top;
        }
        .rev-card.confirmed { background: #F0FAF4; border: 0.5pt solid #6EC98A; }
        .rev-card.pending   { background: #FFFBEB; border: 0.5pt solid #F0B429; }
        .rev-amount {
            font-size: 16pt;
            font-weight: bold;
            display: block;
            line-height: 1;
            margin-bottom: 1.5mm;
        }
        .rev-card.confirmed .rev-amount { color: #2E8B57; }
        .rev-card.pending   .rev-amount { color: #D98E04; }
        .rev-title {
            font-size: 8pt;
            font-weight: bold;
            display: block;
            margin-bottom: 1mm;
            color: #1F2937;
        }
        .rev-note {
            font-size: 6.5pt;
            color: #6B7280;
            display: block;
        }
        .rev-total {
            background: #173B7A;
            border-radius: 5pt;
            padding: 4mm;
            vertical-align: middle;
            text-align: center;
        }
        .rev-total-label {
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(255,255,255,.55);
            display: block;
            margin-bottom: 2mm;
        }
        .rev-total-amount {
            font-size: 15pt;
            font-weight: bold;
            color: #D4B96E;
            display: block;
        }

        /* ================================================================
           TABLES
           ================================================================ */
        .tbl-wrap {
            border: 0.5pt solid #E5E7EB;
            border-radius: 5pt;
            overflow: hidden;
            margin-bottom: 5mm;
        }
        .dt {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }
        .dt th {
            background: #173B7A;
            color: #FFFFFF;
            padding: 2.5mm 3.5mm;
            text-align: left;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .dt th.r { text-align: right; }
        .dt td {
            padding: 2.2mm 3.5mm;
            border-bottom: 0.5pt solid #F3F4F6;
            vertical-align: middle;
            color: #1F2937;
        }
        .dt tr:last-child td { border-bottom: none; }
        .dt tr:nth-child(even) td { background: #FAFAFA; }
        .dt td.r  { text-align: right; }
        .dt td.n  { text-align: right; font-weight: bold; color: #173B7A; }
        .dt td.mu { color: #6B7280; font-size: 7pt; }
        /* District subtotal rows */
        .dt .drow td {
            background: #EEF2FB;
            color: #173B7A;
            font-weight: bold;
            font-size: 7pt;
            font-style: italic;
            border-top: 0.5pt solid #C5D0EC;
            border-bottom: 0.5pt solid #C5D0EC;
        }
        .dt .drow td.n { color: #173B7A; }
        /* Footer / grand total */
        .dt tfoot td {
            background: #173B7A;
            color: #FFFFFF;
            font-weight: bold;
            padding: 2.5mm 3.5mm;
            font-size: 7pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .dt tfoot td.n { color: #D4B96E; text-align: right; }

        /* ================================================================
           PROGRESS BARS
           ================================================================ */
        .bar-row { margin-bottom: 1mm; }
        .bar-meta {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0.5mm;
        }
        .bar-meta td { padding: 0; font-size: 7pt; }
        .bar-meta td.r { text-align: right; color: #6B7280; }
        .bar-track {
            width: 100%;
            background: #E5E7EB;
            border-radius: 3pt;
            height: 4.5mm;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            border-radius: 3pt;
        }
        .bf-navy  { background: #173B7A; }
        .bf-gold  { background: #C8A34F; }
        .bf-green { background: #2E8B57; }
        .bf-amber { background: #D98E04; }

        /* Inline bar inside table cell */
        .cell-bar-track {
            background: #EEF2FB;
            border-radius: 2pt;
            height: 3.5mm;
            overflow: hidden;
        }
        .cell-bar-fill { height: 100%; border-radius: 2pt; }

        /* ================================================================
           BADGES / STATUS PILLS
           ================================================================ */
        .badge {
            display: inline;
            padding: 0.6mm 2.5mm;
            border-radius: 10pt;
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .bg-navy  { background: #173B7A; color: #FFFFFF; }
        .bg-gold  { background: #FFF8E8; color: #92650A; border: 0.5pt solid #E2B95A; }
        .bg-green { background: #F0FAF4; color: #1E6E3C; border: 0.5pt solid #6EC98A; }
        .bg-amber { background: #FFFBEB; color: #92400E; border: 0.5pt solid #F0B429; }
        .bg-red   { background: #FEF2F2; color: #991B1B; border: 0.5pt solid #FECACA; }

        /* ================================================================
           LAYOUT UTILITIES
           ================================================================ */
        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
        .two-col td { vertical-align: top; padding: 0; }
        .col-l { padding-right: 5mm !important; }

        .page-break { page-break-before: always; }

        /* Note / footnote */
        .note {
            font-size: 6.5pt;
            color: #9CA3AF;
            margin-top: 1.5mm;
            margin-bottom: 3mm;
            padding: 2mm 3mm;
            background: #F8F9FB;
            border-left: 2pt solid #E5E7EB;
            border-radius: 0 3pt 3pt 0;
        }

        /* ================================================================
           FOOTER
           ================================================================ */
        .rf {
            border-top: 0.5pt solid #E5E7EB;
            padding-top: 2.5mm;
            margin-top: 6mm;
        }
        .rf table { width: 100%; border-collapse: collapse; }
        .rf td { font-size: 6pt; color: #9CA3AF; padding: 0; vertical-align: middle; }
        .rf td.r { text-align: right; }
        .rf-brand { font-weight: bold; color: #6B7280; }

    </style>
</head>
<body>

@php
    $s = array_merge($stats, [
        'total_campers'              => $stats['total_campers']              ?? 0,
        'adventurers'                => $stats['adventurers']                ?? 0,
        'pathfinders'                => $stats['pathfinders']                ?? 0,
        'senior_youth'               => $stats['senior_youth']               ?? $stats['seniorYouth']  ?? 0,
        'officials'                  => $stats['officials']                  ?? 0,
        'male'                       => $stats['male']                       ?? 0,
        'female'                     => $stats['female']                     ?? 0,
        'active_codes'               => $stats['active_codes']               ?? $stats['codesActive']  ?? 0,
        'codes_pending'              => $stats['codes_pending']              ?? 0,
        'codes_claimed'              => $stats['codes_claimed']              ?? 0,
        'codes_void_expired'         => $stats['codes_void_expired']         ?? 0,
        'total_codes'                => $stats['total_codes']                ?? 0,
        'confirmed_payments'         => $stats['confirmed_payments']         ?? 0,
        'total_revenue'              => (int)($stats['total_revenue']        ?? 0),
        'pending_revenue'            => (int)($stats['pending_revenue']      ?? 0),
        'online_payments'            => $stats['online_payments']            ?? 0,
        'offline_confirmed'          => $stats['offline_payments_confirmed'] ?? $stats['offline_confirmed'] ?? 0,
        'offline_pending'            => $stats['offline_payments_pending']   ?? $stats['offline_pending']   ?? 0,
        'offline_rejected'           => $stats['offline_payments_rejected']  ?? $stats['offline_rejected']  ?? 0,
        'consent_outstanding'        => $stats['consent_outstanding']        ?? 0,
        'consent_collected'          => $stats['consent_collected']          ?? 0,
        'photos_pending'             => $stats['photos_pending']             ?? 0,
        'photos_approved'            => $stats['photos_approved']            ?? 0,
    ]);
    $deptTotal = $s['adventurers'] + $s['pathfinders'] + $s['senior_youth'];
    $deptBase  = max($deptTotal, 1);
    $gBase     = max($s['male'] + $s['female'], 1);
@endphp

{{-- ══════════════════════════════════════════════════════════
     COVER PAGE
     ══════════════════════════════════════════════════════════ --}}
<div class="cover">
    <div class="cover-accent-top"></div>
    <div class="cover-inner">

        <div class="cover-logo-wrap">
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" class="cover-logo" alt="Logo"/>
            @endif
        </div>

        <div class="cover-org">Ogun Conference &nbsp;&middot;&nbsp; Seventh-day Adventist Church</div>
        <div class="cover-title">Management Report</div>
        <div class="cover-subtitle">Youth Congress 2026 &mdash; {{ $campVenue }}</div>
        <div class="cover-theme">&ldquo;From the Word to the World&rdquo; &mdash; Acts 1:8</div>

        <hr class="cover-divider"/>

        <table class="cover-kpi">
            <tr>
                <td>
                    <span class="ck-label">Total Registered</span>
                    <span class="ck-value gold">{{ number_format($s['total_campers']) }}</span>
                </td>
                <td>
                    <span class="ck-label">Venue</span>
                    <span class="ck-value">{{ $campVenue }}</span>
                </td>
                <td>
                    <span class="ck-label">Congress Dates</span>
                    <span class="ck-value">{{ $campDates }}</span>
                </td>
                <td>
                    <span class="ck-label">Report Generated</span>
                    <span class="ck-value">{{ now()->format('d M Y, H:i') }}</span>
                </td>
            </tr>
        </table>

        <div class="cover-footer">Confidential &nbsp;&middot;&nbsp; For Leadership Use Only</div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     PAGE 2 — EXECUTIVE SUMMARY
     ══════════════════════════════════════════════════════════ --}}

<div class="section-wrap">
    <span class="section-num">01 &nbsp;/&nbsp; Overview</span>
    <div class="section-title">Executive Summary</div>
    <div class="section-desc">Registration overview, financial performance and camp readiness at a glance.</div>
    <hr class="section-rule"/>
</div>

{{-- KPI Cards --}}
<table class="kpi-grid">
    <tr>
        <td width="16.6%" style="padding:0 2mm 0 0">
            <div class="kpi-card navy">
                <span class="kpi-icon">&#128101;</span>
                <span class="kpi-num">{{ number_format($s['total_campers']) }}</span>
                <span class="kpi-label">Total Registered</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card gold">
                <span class="kpi-icon">&#x2714;</span>
                <span class="kpi-num">{{ number_format($s['confirmed_payments']) }}</span>
                <span class="kpi-label">Confirmed Payments</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card amber">
                <span class="kpi-icon">&#9203;</span>
                <span class="kpi-num">{{ number_format($s['offline_pending']) }}</span>
                <span class="kpi-label">Awaiting Finance</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card green">
                <span class="kpi-icon">&#8358;</span>
                <span class="kpi-num">{{ number_format($s['total_revenue']) }}</span>
                <span class="kpi-label">Confirmed Revenue</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card red">
                <span class="kpi-icon">&#128196;</span>
                <span class="kpi-num">{{ number_format($s['consent_outstanding']) }}</span>
                <span class="kpi-label">Consent Outstanding</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 0 0 2mm">
            <div class="kpi-card">
                <span class="kpi-icon">&#128247;</span>
                <span class="kpi-num">{{ number_format($s['photos_approved']) }}</span>
                <span class="kpi-label">Photos Approved</span>
            </div>
        </td>
    </tr>
</table>

{{-- Revenue Cards --}}
<div class="ssub">Revenue Breakdown</div>
<table style="width:100%;border-collapse:collapse;margin-bottom:5mm">
    <tr>
        <td width="38%" style="padding:0 3mm 0 0">
            <div class="rev-card confirmed">
                <span class="rev-title">&#9989; Confirmed Revenue</span>
                <span class="rev-amount">&#8358;{{ number_format($s['total_revenue']) }}</span>
                <span class="rev-note">Payments confirmed by the finance team</span>
            </div>
        </td>
        <td width="38%" style="padding:0 3mm">
            <div class="rev-card pending">
                <span class="rev-title">&#9203; Pending Revenue</span>
                <span class="rev-amount">&#8358;{{ number_format($s['pending_revenue']) }}</span>
                <span class="rev-note">Bank transfers awaiting finance confirmation</span>
            </div>
        </td>
        <td width="24%" style="padding:0 0 0 0">
            <div class="rev-total">
                <span class="rev-total-label">Total Expected</span>
                <span class="rev-total-amount">&#8358;{{ number_format($s['total_revenue'] + $s['pending_revenue']) }}</span>
            </div>
        </td>
    </tr>
</table>

{{-- Departments + Gender side-by-side --}}
<table class="two-col">
    <tr>
        <td width="57%" class="col-l">
            <div class="ssub">Registration by Department</div>
            @php
                $depts = [
                    ['Adventurers',  'Ages 6–9',   $s['adventurers'],  'bf-navy',  '#1B3A8F'],
                    ['Pathfinders',  'Ages 10–15', $s['pathfinders'],  'bf-green', '#2E8B57'],
                    ['Senior Youth', 'Ages 16+',   $s['senior_youth'], 'bf-gold',  '#B8872A'],
                ];
            @endphp
            @foreach($depts as [$name, $ages, $count, $bar, $color])
                @php $pct = round($count / $deptBase * 100, 1); @endphp
                <table style="width:100%;border-collapse:collapse;margin-bottom:3mm">
                    <tr>
                        <td style="font-size:7.5pt;font-weight:bold;color:#1F2937">{{ $name }}</td>
                        <td style="font-size:7pt;color:#6B7280;text-align:right">{{ $ages }}</td>
                        <td style="font-size:8pt;font-weight:bold;color:{{ $color }};text-align:right;width:18%">{{ number_format($count) }}</td>
                        <td style="font-size:7pt;color:#6B7280;text-align:right;width:12%">{{ $pct }}%</td>
                    </tr>
                </table>
                <div class="bar-track" style="margin-bottom:4mm">
                    <div class="{{ $bar }} bar-fill" style="width:{{ min($pct,100) }}%"></div>
                </div>
            @endforeach
            <table style="width:100%;border-collapse:collapse;background:#F8F9FB;border:0.5pt solid #E5E7EB;border-radius:4pt">
                <tr>
                    <td style="padding:2mm 3mm;font-size:7pt;font-weight:bold;color:#173B7A">Total Campers</td>
                    <td style="padding:2mm 3mm;font-size:10pt;font-weight:bold;color:#173B7A;text-align:right">{{ number_format($deptTotal) }}</td>
                </tr>
            </table>
            <p class="note">&#9733; Officials: <strong>{{ number_format($s['officials']) }}</strong> &mdash; already counted within their respective department above.</p>
        </td>
        <td width="43%">
            <div class="ssub">Gender Distribution</div>
            <div class="tbl-wrap">
                <table class="dt">
                    <thead><tr><th>Gender</th><th class="r">Count</th><th class="r">Share</th></tr></thead>
                    <tbody>
                    <tr>
                        <td>&#9794; Male</td>
                        <td class="n">{{ number_format($s['male']) }}</td>
                        <td class="n">{{ round($s['male']/$gBase*100,1) }}%</td>
                    </tr>
                    <tr>
                        <td>&#9792; Female</td>
                        <td class="n">{{ number_format($s['female']) }}</td>
                        <td class="n">{{ round($s['female']/$gBase*100,1) }}%</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr><td>Total</td><td class="n">{{ number_format($s['male']+$s['female']) }}</td><td class="n">100%</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="ssub">Camp Readiness</div>
            <div class="tbl-wrap">
                <table class="dt">
                    <thead><tr><th>Metric</th><th class="r">Status</th></tr></thead>
                    <tbody>
                    <tr>
                        <td>Consent Forms — Outstanding</td>
                        <td class="r">
                            <span class="badge {{ $s['consent_outstanding']>0?'bg-amber':'bg-green' }}">{{ number_format($s['consent_outstanding']) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Consent Forms — Collected</td>
                        <td class="r"><span class="badge bg-green">{{ number_format($s['consent_collected']) }}</span></td>
                    </tr>
                    <tr>
                        <td>Photos — Pending Approval</td>
                        <td class="r">
                            <span class="badge {{ $s['photos_pending']>0?'bg-amber':'bg-green' }}">{{ number_format($s['photos_pending']) }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td>Photos — Approved</td>
                        <td class="r"><span class="badge bg-green">{{ number_format($s['photos_approved']) }}</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- ══════════════════════════════════════════════════════════
     PAGE 3 — PAYMENT STATUS & T-SHIRT ORDERS
     ══════════════════════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="section-wrap">
    <span class="section-num">02 &nbsp;/&nbsp; Operations</span>
    <div class="section-title">Payment Status &amp; T-Shirt Orders</div>
    <div class="section-desc">Code pipeline, payment channel breakdown, and uniform size distribution.</div>
    <hr class="section-rule"/>
</div>

<table class="two-col">
    <tr>
        <td width="45%" class="col-l">
            <div class="ssub">Code Pipeline</div>
            <div class="tbl-wrap">
                <table class="dt">
                    <thead><tr><th>Status</th><th class="r">Count</th></tr></thead>
                    <tbody>
                    <tr>
                        <td><span class="badge bg-amber">Pending Payment</span></td>
                        <td class="n">{{ number_format($s['codes_pending']) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-green">Active — Awaiting Registration</span></td>
                        <td class="n">{{ number_format($s['active_codes']) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-navy">Claimed — Registration Complete</span></td>
                        <td class="n">{{ number_format($s['codes_claimed']) }}</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-red">Void / Expired</span></td>
                        <td class="n">{{ number_format($s['codes_void_expired']) }}</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr><td>Total Codes Issued</td><td class="n">{{ number_format($s['total_codes']) }}</td></tr>
                    </tfoot>
                </table>
            </div>

            <div class="ssub">Payment Channels</div>
            <div class="tbl-wrap">
                <table class="dt">
                    <thead><tr><th>Channel</th><th class="r">Count</th></tr></thead>
                    <tbody>
                    <tr>
                        <td>&#128179; Online &mdash; Paystack (Confirmed)</td>
                        <td class="n">{{ number_format($s['online_payments']) }}</td>
                    </tr>
                    <tr>
                        <td>&#127974; Bank Transfer (Confirmed)</td>
                        <td class="n">{{ number_format($s['offline_confirmed']) }}</td>
                    </tr>
                    <tr>
                        <td style="color:#D98E04;font-weight:bold">&#9203; Bank Transfer — Awaiting Finance</td>
                        <td class="n" style="color:#D98E04">{{ number_format($s['offline_pending']) }}</td>
                    </tr>
                    <tr>
                        <td class="mu">&#10006; Bank Transfer — Rejected</td>
                        <td class="n mu">{{ number_format($s['offline_rejected']) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </td>
        <td width="55%">
            <div class="ssub">T-Shirt Size Orders &mdash; Overall</div>
            @php $maxSz = max(array_column($tshirtSizes, 'count') ?: [1]); @endphp
            @php $totalShirts = array_sum(array_column($tshirtSizes,'count')); @endphp
            @foreach($tshirtSizes as $row)
                @php $szPct = $maxSz > 0 ? round($row['count']/$maxSz*100) : 0; @endphp
                <table style="width:100%;border-collapse:collapse;margin-bottom:1mm">
                    <tr>
                        <td style="font-size:7.5pt;font-weight:bold;color:#1F2937;width:12%">{{ $row['size'] }}</td>
                        <td style="font-size:8pt;font-weight:bold;color:#173B7A;width:12%;text-align:right">{{ $row['count'] }}</td>
                        <td style="padding:0 3mm;width:64%">
                            <div class="bar-track">
                                <div class="bf-gold bar-fill" style="width:{{ $szPct }}%"></div>
                            </div>
                        </td>
                        <td style="font-size:7pt;color:#6B7280;text-align:right;width:12%">{{ $szPct }}%</td>
                    </tr>
                </table>
            @endforeach
            <table style="width:100%;border-collapse:collapse;background:#173B7A;border-radius:4pt;margin-top:2mm">
                <tr>
                    <td style="padding:2mm 3mm;font-size:7pt;font-weight:bold;color:rgba(255,255,255,.7);text-transform:uppercase;letter-spacing:1px">Total Shirts</td>
                    <td style="padding:2mm 3mm;font-size:10pt;font-weight:bold;color:#D4B96E;text-align:right">{{ number_format($totalShirts) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- T-Shirt by District & Church --}}
<div class="ssub">T-Shirt Size Orders &mdash; By District &amp; Church</div>

@if(count($tshirtByDistrictChurch) > 0)
    @php
        $sizes = ['XS','S','M','L','XL','XXL','XXXL'];
        $activeSizes = [];
        foreach($sizes as $sz) {
            foreach($tshirtByDistrictChurch as $churches) {
                foreach($churches as $row) {
                    if(($row[$sz] ?? 0) > 0) { $activeSizes[] = $sz; break 2; }
                }
            }
        }
        $activeSizes = array_unique($activeSizes);
    @endphp
    <div class="tbl-wrap">
        <table class="dt">
            <thead>
            <tr>
                <th width="18%">District</th>
                <th width="22%">Church</th>
                @foreach($activeSizes as $sz)<th class="r" style="width:{{ floor(44/count($activeSizes)) }}%">{{ $sz }}</th>@endforeach
                <th class="r" width="10%">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tshirtByDistrictChurch as $district => $churches)
                @php $isFirst = true; @endphp
                @foreach($churches as $church => $sizes_data)
                    @php
                        $rowTotal = array_sum(array_intersect_key($sizes_data, array_flip($activeSizes)));
                    @endphp
                    <tr>
                        <td class="mu" style="font-size:7pt">{{ $isFirst ? $district : '' }}</td>
                        <td>{{ $church }}</td>
                        @foreach($activeSizes as $sz)
                            <td class="n" style="font-size:7pt;color:{{ ($sizes_data[$sz] ?? 0) > 0 ? '#173B7A' : '#D1D5DB' }}">
                                {{ ($sizes_data[$sz] ?? 0) ?: '&mdash;' }}
                            </td>
                        @endforeach
                        <td class="n">{{ $rowTotal ?: '&mdash;' }}</td>
                    </tr>
                    @php $isFirst = false; @endphp
                @endforeach
                @php
                    $distTotals = [];
                    foreach($activeSizes as $sz) {
                        $distTotals[$sz] = array_sum(array_column($churches, $sz));
                    }
                    $distGrandTotal = array_sum($distTotals);
                @endphp
                <tr class="drow">
                    <td colspan="2">{{ $district }} &mdash; Subtotal</td>
                    @foreach($activeSizes as $sz)
                        <td class="n">{{ $distTotals[$sz] ?: '&mdash;' }}</td>
                    @endforeach
                    <td class="n">{{ $distGrandTotal }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Grand Total</td>
                @foreach($activeSizes as $sz)
                    @php $col = 0; foreach($tshirtByDistrictChurch as $churches) { foreach($churches as $r) { $col += ($r[$sz] ?? 0); } } @endphp
                    <td class="n">{{ $col ?: '&mdash;' }}</td>
                @endforeach
                <td class="n">{{ number_format($totalShirts) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif

{{-- ══════════════════════════════════════════════════════════
     PAGE 4 — REGISTRATION BY DISTRICT & CHURCH
     ══════════════════════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="section-wrap">
    <span class="section-num">03 &nbsp;/&nbsp; District Report</span>
    <div class="section-title">Registration by District &amp; Church</div>
    <div class="section-desc">Full breakdown of registrations per church, grouped by district with subtotals.</div>
    <hr class="section-rule"/>
</div>

@php
    $grandTotal = max(array_sum(array_column($byChurch,'total')), 1);
    $curDist    = null;
    $dSub       = ['adv'=>0,'pf'=>0,'syl'=>0,'total'=>0];
    $maxChurch  = max(array_column($byChurch,'total') ?: [1]);
@endphp

<div class="tbl-wrap">
    <table class="dt">
        <thead>
        <tr>
            <th width="20%">District</th>
            <th width="26%">Church</th>
            <th class="r" width="9%">Adv</th>
            <th class="r" width="9%">PF</th>
            <th class="r" width="9%">SYL</th>
            <th class="r" width="10%">Total</th>
            <th class="r" width="8%">Share</th>
            <th width="9%"></th>
        </tr>
        </thead>
        <tbody>
        @foreach($byChurch as $row)
            @if($curDist !== null && $curDist !== $row['district'])
                <tr class="drow">
                    <td colspan="2">{{ $curDist }} &mdash; Subtotal</td>
                    <td class="n">{{ number_format($dSub['adv']) }}</td>
                    <td class="n">{{ number_format($dSub['pf']) }}</td>
                    <td class="n">{{ number_format($dSub['syl']) }}</td>
                    <td class="n">{{ number_format($dSub['total']) }}</td>
                    <td class="n">{{ round($dSub['total']/$grandTotal*100,1) }}%</td>
                    <td></td>
                </tr>
                @php $dSub = ['adv'=>0,'pf'=>0,'syl'=>0,'total'=>0]; @endphp
            @endif
            @php $curDist = $row['district']; @endphp
            <tr>
                <td class="mu" style="font-size:7pt">{{ $row['district'] }}</td>
                <td style="font-weight:500">{{ $row['church'] }}</td>
                <td class="n" style="color:#1B3A8F">{{ $row['adv'] ?: '&mdash;' }}</td>
                <td class="n" style="color:#2E8B57">{{ $row['pf']  ?: '&mdash;' }}</td>
                <td class="n" style="color:#B8872A">{{ $row['syl'] ?: '&mdash;' }}</td>
                <td class="n">{{ number_format($row['total']) }}</td>
                <td class="n mu">{{ round($row['total']/$grandTotal*100,1) }}%</td>
                <td style="padding:2mm 3mm 2mm 0">
                    <div class="cell-bar-track">
                        <div class="bf-navy cell-bar-fill" style="width:{{ $maxChurch>0?round($row['total']/$maxChurch*100):0 }}%"></div>
                    </div>
                </td>
            </tr>
            @php $dSub['adv']+=$row['adv']; $dSub['pf']+=$row['pf']; $dSub['syl']+=$row['syl']; $dSub['total']+=$row['total']; @endphp
        @endforeach
        @if($curDist)
            <tr class="drow">
                <td colspan="2">{{ $curDist }} &mdash; Subtotal</td>
                <td class="n">{{ number_format($dSub['adv']) }}</td>
                <td class="n">{{ number_format($dSub['pf']) }}</td>
                <td class="n">{{ number_format($dSub['syl']) }}</td>
                <td class="n">{{ number_format($dSub['total']) }}</td>
                <td class="n">{{ round($dSub['total']/$grandTotal*100,1) }}%</td>
                <td></td>
            </tr>
        @endif
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">Grand Total</td>
            <td class="n">{{ number_format($s['adventurers']) }}</td>
            <td class="n">{{ number_format($s['pathfinders']) }}</td>
            <td class="n">{{ number_format($s['senior_youth']) }}</td>
            <td class="n">{{ number_format($deptTotal) }}</td>
            <td class="n">100%</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>

<div class="rf">
    <table>
        <tr>
            <td class="rf-brand">Ogun Conference Youth Congress 2026</td>
            <td class="r">Management Report &nbsp;&middot;&nbsp; Generated {{ now()->format('d M Y, H:i') }} WAT &nbsp;&middot;&nbsp; Confidential</td>
        </tr>
    </table>
</div>

</body>
</html>
