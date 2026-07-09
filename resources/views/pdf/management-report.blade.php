<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>

        /* ============================================================
           PAGE & BASE
           ============================================================ */
        @page { margin: 26mm 22mm 24mm 22mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 8.5pt;
            color: #111827;
            background: #FFFFFF;
            line-height: 1.55;
        }

        /* ============================================================
           COVER
           ============================================================ */
        .cov { background: #0F2255; page-break-after: always; }
        .cov-gold-bar { background: #C9993A; height: 4mm; width: 100%; }
        .cov-body { padding: 22mm 24mm 20mm; text-align: center; }
        .cov-logo { width: 28mm; height: 28mm; border-radius: 50%; border: 2pt solid rgba(201,153,58,.55); display: block; margin: 0 auto 8mm; }
        .cov-org { font-size: 6.5pt; font-weight: bold; letter-spacing: 3.5px; text-transform: uppercase; color: #C9993A; margin-bottom: 6mm; }
        .cov-title { font-size: 30pt; font-weight: bold; color: #FFFFFF; line-height: 1.0; margin-bottom: 3mm; }
        .cov-sub { font-size: 11pt; color: rgba(255,255,255,.7); margin-bottom: 2.5mm; }
        .cov-theme { font-size: 8pt; font-style: italic; color: rgba(255,255,255,.4); margin-bottom: 10mm; }
        .cov-hr { border: none; border-top: 0.7pt solid rgba(201,153,58,.4); margin: 0 18mm 10mm; }
        .cov-kpi { width: 100%; border-collapse: collapse; margin-bottom: 12mm; }
        .cov-kpi td { text-align: center; padding: 3.5mm 4mm; border-right: 0.5pt solid rgba(255,255,255,.08); }
        .cov-kpi td:last-child { border-right: none; }
        .ck-lbl { display: block; font-size: 5.5pt; font-weight: bold; letter-spacing: 2.5px; text-transform: uppercase; color: rgba(255,255,255,.38); margin-bottom: 2mm; }
        .ck-val { display: block; font-size: 11.5pt; font-weight: bold; color: #fff; }
        .ck-val.g { color: #C9993A; }
        .cov-foot { font-size: 6pt; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.22); }

        /* ============================================================
           SECTION HEADERS (editorial style)
           ============================================================ */
        .sh-wrap { margin: 8mm 0 5mm; }
        .sh-wrap:first-child { margin-top: 0; }
        .sh-num { font-size: 7pt; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #C9993A; display: block; margin-bottom: 1mm; }
        .sh-title { font-size: 13pt; font-weight: bold; color: #0F2255; display: block; margin-bottom: 1.5mm; }
        .sh-desc { font-size: 7pt; color: #6B7280; display: block; margin-bottom: 3mm; }
        .sh-rule { width: 100%; border: none; border-top: 1pt solid #E5E7EB; margin-bottom: 5mm; }

        /* Sub-heading */
        .ssub { font-size: 6.5pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #0F2255; border-left: 2.5pt solid #C9993A; padding-left: 3mm; margin: 5mm 0 2.5mm; }
        .ssub:first-child { margin-top: 0; }

        /* ============================================================
           KPI CARDS
           ============================================================ */
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 6mm; }
        .kpi-card {
            border: 0.5pt solid #E5E7EB;
            border-top: 3pt solid #CBD5E1;
            border-radius: 4pt;
            padding: 4mm 3mm;
            text-align: center;
            background: #FFFFFF;
            vertical-align: top;
        }
        .kpi-card.c-navy  { border-top-color: #0F2255; background: #0F2255; }
        .kpi-card.c-gold  { border-top-color: #C9993A; background: #FEFBF2; }
        .kpi-card.c-green { border-top-color: #059669; background: #F0FDF8; }
        .kpi-card.c-amber { border-top-color: #D97706; background: #FFFBEB; }
        .kpi-card.c-red   { border-top-color: #DC2626; background: #FEF2F2; }
        .kpi-icon { font-size: 12pt; display: block; line-height: 1; margin-bottom: 2mm; }
        .kpi-num { font-size: 19pt; font-weight: bold; color: #0F2255; display: block; line-height: 1; margin-bottom: 1.5mm; }
        .kpi-lbl { font-size: 5.5pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #6B7280; display: block; }
        /* variants */
        .c-navy .kpi-num { color: #C9993A; }
        .c-navy .kpi-lbl { color: rgba(255,255,255,.55); }
        .c-gold  .kpi-num { color: #92650A; } .c-gold  .kpi-lbl { color: #92650A; }
        .c-green .kpi-num { color: #059669; } .c-green .kpi-lbl { color: #059669; }
        .c-amber .kpi-num { color: #B45309; } .c-amber .kpi-lbl { color: #B45309; }
        .c-red   .kpi-num { color: #B91C1C; } .c-red   .kpi-lbl { color: #B91C1C; }

        /* ============================================================
           REVENUE CARDS
           ============================================================ */
        .rev-wrap { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
        .rev-card {
            border-radius: 5pt;
            padding: 4mm 5mm;
            vertical-align: top;
            border: 0.5pt solid #E5E7EB;
        }
        .rev-card.claimed  { background: #F0FDF8; border-top: 3pt solid #059669; }
        .rev-card.active   { background: #EFF6FF; border-top: 3pt solid #2563EB; }
        .rev-card.total    { background: #0F2255; border-top: 3pt solid #C9993A; }
        .rev-card.pending  { background: #FFFBEB; border-top: 3pt solid #D97706; }
        .rev-icon   { font-size: 10pt; display: block; margin-bottom: 1.5mm; }
        .rev-title  { font-size: 6.5pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-bottom: 1.5mm; color: #374151; }
        .rev-amount { font-size: 13pt; font-weight: bold; display: block; line-height: 1; margin-bottom: 1mm; }
        .rev-note   { font-size: 6.5pt; color: #6B7280; display: block; }
        .claimed  .rev-title  { color: #065F46; }
        .claimed  .rev-amount { color: #059669; }
        .active   .rev-title  { color: #1E40AF; }
        .active   .rev-amount { color: #2563EB; }
        .total    .rev-title  { color: #C9993A; }
        .total    .rev-amount { color: #FFFFFF; }
        .total    .rev-note   { color: rgba(255,255,255,.5); }
        .pending  .rev-title  { color: #92400E; }
        .pending  .rev-amount { color: #D97706; }

        /* ============================================================
           TABLES
           ============================================================ */
        .tbl-outer { border: 0.5pt solid #E5E7EB; border-radius: 5pt; overflow: hidden; margin-bottom: 5mm; }
        .dt { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .dt thead tr { background: #0F2255; }
        .dt th {
            color: #FFFFFF;
            padding: 2.8mm 3.5mm;
            text-align: left;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.8px;
            text-transform: uppercase;
        }
        .dt th.r { text-align: right; }
        .dt th.c { text-align: center; }
        .dt td {
            padding: 2.3mm 3.5mm;
            border-bottom: 0.5pt solid #F3F4F6;
            vertical-align: middle;
            color: #1F2937;
        }
        .dt tr:last-child td { border-bottom: none; }
        .dt tr:nth-child(even) td { background: #F9FAFB; }
        .dt tr:nth-child(odd)  td { background: #FFFFFF; }
        .dt td.r  { text-align: right; }
        .dt td.c  { text-align: center; }
        .dt td.n  { text-align: right; font-weight: bold; color: #0F2255; }
        .dt td.mu { color: #6B7280; font-size: 7pt; }
        .dt td.sm { font-size: 7pt; }
        /* District row */
        .dt .dr td {
            background: #EEF2FB !important;
            color: #0F2255;
            font-weight: bold;
            font-size: 7.5pt;
            border-top: 0.5pt solid #C5D2EC;
            border-bottom: 0.5pt solid #C5D2EC;
            padding: 2mm 3.5mm;
        }
        .dt .dr td.n { color: #0F2255; text-align: right; }
        /* Category sub-row in dept tshirt table */
        .dt .cat-row td {
            background: #F8FAFF !important;
            color: #374151;
            font-size: 7pt;
            padding: 1.8mm 3.5mm 1.8mm 7mm;
            border-bottom: 0.5pt solid #F0F0F0;
        }
        .dt .cat-row td.n { color: #374151; }
        /* Grand total footer */
        .dt tfoot td {
            background: #111827 !important;
            color: #FFFFFF;
            font-weight: bold;
            padding: 2.8mm 3.5mm;
            font-size: 7pt;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: none;
        }
        .dt tfoot td.n { color: #C9993A; text-align: right; }

        /* ============================================================
           PROGRESS / BAR CHARTS
           ============================================================ */
        .bar-track { background: #E5E7EB; border-radius: 6pt; height: 4mm; overflow: hidden; width: 100%; }
        .bar-fill  { height: 100%; border-radius: 6pt; }
        .bf-navy  { background: #0F2255; }
        .bf-gold  { background: #C9993A; }
        .bf-green { background: #059669; }
        .bf-amber { background: #D97706; }
        .bf-blue  { background: #2563EB; }
        /* Inline cell bar (no radius, fits in td) */
        .cbar-track { background: #EEF2FB; height: 3.5mm; border-radius: 3pt; overflow: hidden; }
        .cbar-fill  { height: 100%; border-radius: 3pt; }

        /* ============================================================
           STATUS PILLS / BADGES
           ============================================================ */
        .pill {
            display: inline;
            padding: 0.5mm 2.5mm;
            border-radius: 10pt;
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 0.3px;
        }
        .p-navy  { background: #0F2255; color: #FFFFFF; }
        .p-green { background: #DCFCE7; color: #14532D; }
        .p-amber { background: #FEF3C7; color: #78350F; }
        .p-red   { background: #FEE2E2; color: #7F1D1D; }
        .p-blue  { background: #DBEAFE; color: #1E3A8A; }
        .p-gray  { background: #F3F4F6; color: #374151; }
        .p-gold  { background: #FEF9EC; color: #78350F; border: 0.5pt solid #C9993A; }

        /* ============================================================
           LAYOUT HELPERS
           ============================================================ */
        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 5mm; }
        .two-col td { vertical-align: top; padding: 0; }
        .col-l { padding-right: 5mm !important; }
        .page-break { page-break-before: always; }
        .note-box {
            font-size: 6.5pt;
            color: #6B7280;
            background: #F9FAFB;
            border: 0.5pt solid #E5E7EB;
            border-left: 2.5pt solid #C9993A;
            border-radius: 0 4pt 4pt 0;
            padding: 2mm 3.5mm;
            margin: 2mm 0 4mm;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .footer {
            border-top: 0.5pt solid #E5E7EB;
            padding-top: 3mm;
            margin-top: 7mm;
        }
        .footer-t { width: 100%; border-collapse: collapse; }
        .footer-t td { font-size: 6pt; color: #9CA3AF; padding: 0; vertical-align: middle; }
        .footer-t td.r { text-align: right; }
        .footer-conf {
            display: inline;
            font-size: 5.5pt;
            font-weight: bold;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #9CA3AF;
            border: 0.5pt solid #D1D5DB;
            border-radius: 2pt;
            padding: 0.3mm 1.5mm;
            margin-right: 2mm;
        }

    </style>
</head>
<body>

@php
    /* ── Normalise stats keys ─────────────────────────────────── */
    $s = [
        'total_campers'              => $stats['total_campers']              ?? 0,
        'adventurers'                => $stats['adventurers']                ?? 0,
        'pathfinders'                => $stats['pathfinders']                ?? 0,
        'senior_youth'               => $stats['senior_youth']               ?? $stats['seniorYouth'] ?? 0,
        'officials'                  => $stats['officials']                  ?? 0,
        'male'                       => $stats['male']                       ?? 0,
        'female'                     => $stats['female']                     ?? 0,
        'active_codes'               => $stats['active_codes']               ?? 0,
        'codes_pending'              => $stats['codes_pending']              ?? 0,
        'codes_claimed'              => $stats['codes_claimed']              ?? 0,
        'codes_void_expired'         => $stats['codes_void_expired']         ?? 0,
        'total_codes'                => $stats['total_codes']                ?? 0,
        'confirmed_payments'         => $stats['confirmed_payments']         ?? 0,
        'claimed_revenue'            => (int)($stats['claimed_revenue']      ?? 0),
        'active_revenue'             => (int)($stats['active_revenue']       ?? 0),
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
    ];
    $deptTotal = $s['adventurers'] + $s['pathfinders'] + $s['senior_youth'];
    $deptBase  = max($deptTotal, 1);
    $gBase     = max($s['male'] + $s['female'], 1);
    $allSizes  = ['XS','S','M','L','XL','XXL','XXXL'];
    $catLabels = ['adventurer' => 'Adventurers', 'pathfinder' => 'Pathfinders', 'senior_youth' => 'Senior Youth'];
@endphp

{{-- ═══════════════════════════════════════════
     COVER
     ═══════════════════════════════════════════ --}}
<div class="cov">
    <div class="cov-gold-bar"></div>
    <div class="cov-body">
        @if($logoBase64)
            <img src="data:image/png;base64,{{ $logoBase64 }}" class="cov-logo" alt="Logo"/>
        @endif
        <div class="cov-org">Ogun Conference &nbsp;&#183;&nbsp; Seventh-day Adventist Church</div>
        <div class="cov-title">Management<br/>Report</div>
        <div class="cov-sub">Youth Congress 2026 &#8212; {{ $campVenue }}</div>
        <div class="cov-theme">&#8220;From the Word to the World&#8221; &#8212; Acts 1:8</div>
        <hr class="cov-hr"/>
        <table class="cov-kpi">
            <tr>
                <td><span class="ck-lbl">Total Registered</span><span class="ck-val g">{{ number_format($s['total_campers']) }}</span></td>
                <td><span class="ck-lbl">Venue</span><span class="ck-val">{{ $campVenue }}</span></td>
                <td><span class="ck-lbl">Congress Dates</span><span class="ck-val">{{ $campDates }}</span></td>
                <td><span class="ck-lbl">Confirmed Revenue</span><span class="ck-val g">&#8358;{{ number_format($s['total_revenue']) }}</span></td>
                <td><span class="ck-lbl">Report Date</span><span class="ck-val">{{ now()->format('d M Y') }}</span></td>
            </tr>
        </table>
        <div class="cov-foot">Confidential &#183; For Leadership Use Only</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════
     PAGE 2 &#8212; EXECUTIVE SUMMARY
     ═══════════════════════════════════════════ --}}

<div class="sh-wrap">
    <span class="sh-num">01 / Overview</span>
    <span class="sh-title">Executive Summary</span>
    <span class="sh-desc">Registration performance, financial status and camp readiness &#8212; as at {{ now()->format('d M Y, H:i') }} WAT</span>
    <hr class="sh-rule"/>
</div>

{{-- KPI Row 1 --}}
<table class="kpi-grid">
    <tr>
        <td width="16.6%" style="padding:0 2mm 0 0">
            <div class="kpi-card c-navy">
                <span class="kpi-icon">&#128101;</span>
                <span class="kpi-num">{{ number_format($s['total_campers']) }}</span>
                <span class="kpi-lbl">Total Registered</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card c-gold">
                <span class="kpi-icon">&#10004;</span>
                <span class="kpi-num">{{ number_format($s['confirmed_payments']) }}</span>
                <span class="kpi-lbl">Confirmed Payments</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card c-amber">
                <span class="kpi-icon">&#9203;</span>
                <span class="kpi-num">{{ number_format($s['offline_pending']) }}</span>
                <span class="kpi-lbl">Awaiting Finance</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card c-green">
                <span class="kpi-icon">&#8358;</span>
                <span class="kpi-num">{{ number_format($s['total_revenue']) }}</span>
                <span class="kpi-lbl">Total Conf. Revenue</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 2mm">
            <div class="kpi-card c-red">
                <span class="kpi-icon">&#128196;</span>
                <span class="kpi-num">{{ number_format($s['consent_outstanding']) }}</span>
                <span class="kpi-lbl">Consent Outstanding</span>
            </div>
        </td>
        <td width="16.6%" style="padding:0 0 0 2mm">
            <div class="kpi-card">
                <span class="kpi-icon">&#128247;</span>
                <span class="kpi-num">{{ number_format($s['photos_approved']) }}</span>
                <span class="kpi-lbl">Photos Approved</span>
            </div>
        </td>
    </tr>
</table>

{{-- Revenue cards --}}
<div class="ssub">Revenue Breakdown</div>
<table class="rev-wrap">
    <tr>
        <td width="25%" style="padding:0 2.5mm 0 0">
            <div class="rev-card claimed">
                <span class="rev-icon">&#10003;</span>
                <span class="rev-title">Registered &amp; Paid</span>
                <span class="rev-amount">&#8358;{{ number_format($s['claimed_revenue']) }}</span>
                <span class="rev-note">Registration complete (claimed codes)</span>
            </div>
        </td>
        <td width="25%" style="padding:0 2.5mm">
            <div class="rev-card active">
                <span class="rev-icon">&#128273;</span>
                <span class="rev-title">Paid, Reg. Pending</span>
                <span class="rev-amount">&#8358;{{ number_format($s['active_revenue']) }}</span>
                <span class="rev-note">Payment confirmed, awaiting form completion</span>
            </div>
        </td>
        <td width="25%" style="padding:0 2.5mm">
            <div class="rev-card total">
                <span class="rev-icon">&#8721;</span>
                <span class="rev-title">Total Confirmed</span>
                <span class="rev-amount">&#8358;{{ number_format($s['total_revenue']) }}</span>
                <span class="rev-note">All confirmed payments combined</span>
            </div>
        </td>
        <td width="25%" style="padding:0 0 0 2.5mm">
            <div class="rev-card pending">
                <span class="rev-icon">&#9203;</span>
                <span class="rev-title">Pending Approval</span>
                <span class="rev-amount">&#8358;{{ number_format($s['pending_revenue']) }}</span>
                <span class="rev-note">Bank transfers awaiting finance team</span>
            </div>
        </td>
    </tr>
</table>

{{-- Departments + Gender --}}
<table class="two-col">
    <tr>
        <td width="58%" class="col-l">
            <div class="ssub">Registration by Department</div>
            @php
                $depts = [
                    ['Adventurers',  'Ages 6&#8211;9',   $s['adventurers'],  'bf-navy',  '#0F2255'],
                    ['Pathfinders',  'Ages 10&#8211;15', $s['pathfinders'],  'bf-green', '#059669'],
                    ['Senior Youth', 'Ages 16+',         $s['senior_youth'], 'bf-gold',  '#92650A'],
                ];
            @endphp
            @foreach($depts as [$dname, $dages, $dcount, $dbar, $dcolor])
                @php $dpct = round($dcount / $deptBase * 100, 1); @endphp
                <table style="width:100%;border-collapse:collapse;margin-bottom:1mm">
                    <tr>
                        <td style="font-size:8pt;font-weight:bold;color:#111827">{!! $dname !!}</td>
                        <td style="font-size:7pt;color:#6B7280;text-align:center">{!! $dages !!}</td>
                        <td style="font-size:9pt;font-weight:bold;color:{{ $dcolor }};text-align:right;width:16%">{{ number_format($dcount) }}</td>
                        <td style="font-size:7pt;color:#6B7280;text-align:right;width:11%">{{ $dpct }}%</td>
                    </tr>
                </table>
                <div class="bar-track" style="margin-bottom:3.5mm">
                    <div class="{{ $dbar }} bar-fill" style="width:{{ min($dpct,100) }}%"></div>
                </div>
            @endforeach
            <table style="width:100%;border-collapse:collapse">
                <tr>
                    <td style="background:#0F2255;border-radius:4pt;padding:2.5mm 4mm;font-size:7pt;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:rgba(255,255,255,.7)">Total Campers</td>
                    <td style="background:#0F2255;border-radius:4pt;padding:2.5mm 4mm;font-size:11pt;font-weight:bold;color:#C9993A;text-align:right">{{ number_format($deptTotal) }}</td>
                </tr>
            </table>
            <div class="note-box">&#9733; Officials: <strong>{{ number_format($s['officials']) }}</strong> &#8212; already counted within their respective department above and not double-counted in the total.</div>
        </td>
        <td width="42%">
            <div class="ssub">Gender Distribution</div>
            <div class="tbl-outer">
                <table class="dt">
                    <thead><tr><th>Gender</th><th class="r">Count</th><th class="r">Share</th></tr></thead>
                    <tbody>
                    <tr><td>Male</td><td class="n">{{ number_format($s['male']) }}</td><td class="n">{{ round($s['male']/$gBase*100,1) }}%</td></tr>
                    <tr><td>Female</td><td class="n">{{ number_format($s['female']) }}</td><td class="n">{{ round($s['female']/$gBase*100,1) }}%</td></tr>
                    </tbody>
                    <tfoot><tr><td>Total</td><td class="n">{{ number_format($s['male']+$s['female']) }}</td><td class="n">100%</td></tr></tfoot>
                </table>
            </div>

            <div class="ssub">Camp Readiness</div>
            <div class="tbl-outer">
                <table class="dt">
                    <thead><tr><th>Metric</th><th class="r">Status</th></tr></thead>
                    <tbody>
                    <tr><td>Consent Forms &#8212; Outstanding</td><td class="r"><span class="pill {{ $s['consent_outstanding']>0?'p-amber':'p-green' }}">{{ number_format($s['consent_outstanding']) }}</span></td></tr>
                    <tr><td>Consent Forms &#8212; Collected</td><td class="r"><span class="pill p-green">{{ number_format($s['consent_collected']) }}</span></td></tr>
                    <tr><td>Photos &#8212; Pending Approval</td><td class="r"><span class="pill {{ $s['photos_pending']>0?'p-amber':'p-green' }}">{{ number_format($s['photos_pending']) }}</span></td></tr>
                    <tr><td>Photos &#8212; Approved</td><td class="r"><span class="pill p-green">{{ number_format($s['photos_approved']) }}</span></td></tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════
     PAGE 3 &#8212; PAYMENT STATUS
     ═══════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="sh-wrap">
    <span class="sh-num">02 / Operations</span>
    <span class="sh-title">Payment Status &amp; Code Pipeline</span>
    <span class="sh-desc">Registration code tracking, payment channel breakdown, and outstanding approvals.</span>
    <hr class="sh-rule"/>
</div>

<table class="two-col">
    <tr>
        <td width="48%" class="col-l">
            <div class="ssub">Code Pipeline</div>
            <div class="tbl-outer">
                <table class="dt">
                    <thead><tr><th>Status</th><th class="r">Count</th></tr></thead>
                    <tbody>
                    <tr><td><span class="pill p-amber">Pending Payment</span></td><td class="n">{{ number_format($s['codes_pending']) }}</td></tr>
                    <tr><td><span class="pill p-green">Active &#8212; Awaiting Registration</span></td><td class="n">{{ number_format($s['active_codes']) }}</td></tr>
                    <tr><td><span class="pill p-navy">Claimed &#8212; Complete</span></td><td class="n">{{ number_format($s['codes_claimed']) }}</td></tr>
                    <tr><td><span class="pill p-red">Void / Expired</span></td><td class="n">{{ number_format($s['codes_void_expired']) }}</td></tr>
                    </tbody>
                    <tfoot><tr><td>Total Codes Issued</td><td class="n">{{ number_format($s['total_codes']) }}</td></tr></tfoot>
                </table>
            </div>
        </td>
        <td width="52%">
            <div class="ssub">Payment Channels</div>
            <div class="tbl-outer">
                <table class="dt">
                    <thead><tr><th>Channel</th><th class="r">Count</th></tr></thead>
                    <tbody>
                    <tr><td>Paystack Online <span class="pill p-green">Confirmed</span></td><td class="n">{{ number_format($s['online_payments']) }}</td></tr>
                    <tr><td>Bank Transfer <span class="pill p-green">Confirmed</span></td><td class="n">{{ number_format($s['offline_confirmed']) }}</td></tr>
                    <tr><td style="font-weight:bold;color:#B45309">Bank Transfer <span class="pill p-amber">Awaiting Finance</span></td><td class="n" style="color:#B45309">{{ number_format($s['offline_pending']) }}</td></tr>
                    <tr><td class="mu">Bank Transfer <span class="pill p-red">Rejected</span></td><td class="n mu">{{ number_format($s['offline_rejected']) }}</td></tr>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>

{{-- ═══════════════════════════════════════════
     REGISTRATION CODE SUMMARY (still page 3)
     ═══════════════════════════════════════════ --}}

<div class="ssub">Registration Code Summary</div>
<table class="two-col" style="margin-bottom:4mm">
    <tr>
        {{-- Claimed vs Unclaimed overview --}}
        <td width="38%" class="col-l">
            <div class="tbl-outer">
                <table class="dt">
                    <thead><tr><th>Code Status</th><th class="r">Count</th><th class="r">Share</th></tr></thead>
                    <tbody>
                    @php $codeBase = max($s['total_codes'], 1); @endphp
                    <tr>
                        <td><span class="pill p-navy">&#128273; Claimed</span> <span style="font-size:7pt;color:#6B7280">Registration complete</span></td>
                        <td class="n" style="color:#059669">{{ number_format($s['codes_claimed']) }}</td>
                        <td class="n">{{ round($s['codes_claimed']/$codeBase*100,1) }}%</td>
                    </tr>
                    <tr>
                        <td><span class="pill p-blue">&#9203; Unclaimed</span> <span style="font-size:7pt;color:#6B7280">Active, awaiting reg.</span></td>
                        <td class="n" style="color:#2563EB">{{ number_format($s['active_codes']) }}</td>
                        <td class="n">{{ round($s['active_codes']/$codeBase*100,1) }}%</td>
                    </tr>
                    <tr>
                        <td class="mu">Pending Payment</td>
                        <td class="n mu">{{ number_format($s['codes_pending']) }}</td>
                        <td class="n mu">{{ round($s['codes_pending']/$codeBase*100,1) }}%</td>
                    </tr>
                    <tr>
                        <td class="mu">Void / Expired</td>
                        <td class="n mu">{{ number_format($s['codes_void_expired']) }}</td>
                        <td class="n mu">{{ round($s['codes_void_expired']/$codeBase*100,1) }}%</td>
                    </tr>
                    </tbody>
                    <tfoot>
                    <tr><td>Total Codes Issued</td><td class="n">{{ number_format($s['total_codes']) }}</td><td class="n">100%</td></tr>
                    </tfoot>
                </table>
            </div>
        </td>

        {{-- Visual bar for claimed vs unclaimed --}}
        <td width="62%">
            <div style="background:#F9FAFB;border:0.5pt solid #E5E7EB;border-radius:5pt;padding:5mm">
                <table style="width:100%;border-collapse:collapse;margin-bottom:4mm">
                    <tr>
                        <td style="font-size:7.5pt;font-weight:bold;color:#059669">Claimed</td>
                        <td style="font-size:9pt;font-weight:bold;color:#059669;text-align:right">{{ number_format($s['codes_claimed']) }}</td>
                    </tr>
                </table>
                <div class="bar-track" style="margin-bottom:4mm">
                    <div class="bf-green bar-fill" style="width:{{ $s['total_codes']>0?round($s['codes_claimed']/$s['total_codes']*100):0 }}%"></div>
                </div>
                <table style="width:100%;border-collapse:collapse;margin-bottom:4mm">
                    <tr>
                        <td style="font-size:7.5pt;font-weight:bold;color:#2563EB">Unclaimed (Active)</td>
                        <td style="font-size:9pt;font-weight:bold;color:#2563EB;text-align:right">{{ number_format($s['active_codes']) }}</td>
                    </tr>
                </table>
                <div class="bar-track">
                    <div class="bf-blue bar-fill" style="width:{{ $s['total_codes']>0?round($s['active_codes']/$s['total_codes']*100):0 }}%"></div>
                </div>
                <div style="margin-top:3mm;font-size:6.5pt;color:#6B7280">
                    &#9432; Unclaimed codes represent campers whose payments are confirmed but who have not yet completed their registration form.
                </div>
            </div>
        </td>
    </tr>
</table>

{{-- Unclaimed codes by district & church --}}
<div class="ssub">Unclaimed Codes &#8212; Breakdown by District &amp; Church</div>
@if(isset($unclaimedByChurch) && count($unclaimedByChurch) > 0)
    @php
        $unclaimedTotal  = array_sum(array_column($unclaimedByChurch, 'count'));
        $unclaimedMax    = max(array_column($unclaimedByChurch, 'count') ?: [1]);
        $unclaimedBase   = max($unclaimedTotal, 1);
        $ucByDistrict    = [];
        foreach ($unclaimedByChurch as $row) {
            $ucByDistrict[$row['district']][] = $row;
        }
        ksort($ucByDistrict);
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead>
            <tr>
                <th width="22%">District</th>
                <th width="30%">Church</th>
                <th class="r" width="12%">Unclaimed</th>
                <th class="r" width="10%">% of Total</th>
                <th width="26%">Volume</th>
            </tr>
            </thead>
            <tbody>
            @foreach($ucByDistrict as $district => $churches)
                @php $distCount = array_sum(array_column($churches, 'count')); $isFirst = true; @endphp
                @foreach($churches as $row)
                    <tr>
                        <td class="mu sm">{{ $isFirst ? $district : '' }}</td>
                        <td>{{ $row['church'] }}</td>
                        <td class="n" style="color:#2563EB">{{ number_format($row['count']) }}</td>
                        <td class="n mu">{{ round($row['count']/$unclaimedBase*100,1) }}%</td>
                        <td style="padding:2.5mm 3.5mm">
                            <div class="cbar-track">
                                <div class="bf-blue cbar-fill" style="width:{{ $unclaimedMax>0?round($row['count']/$unclaimedMax*100):0 }}%"></div>
                            </div>
                        </td>
                    </tr>
                    @php $isFirst = false; @endphp
                @endforeach
                {{-- District subtotal --}}
                <tr class="dr">
                    <td colspan="2">{{ $district }} &#8212; Subtotal</td>
                    <td class="n">{{ number_format($distCount) }}</td>
                    <td class="n">{{ round($distCount/$unclaimedBase*100,1) }}%</td>
                    <td></td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Total Unclaimed Codes</td>
                <td class="n">{{ number_format($unclaimedTotal) }}</td>
                <td class="n">100%</td>
                <td></td>
            </tr>
            </tfoot>
        </table>
    </div>
@else
    <div class="note-box">&#10003; All issued codes have been claimed &#8212; no active unclaimed codes at this time.</div>
@endif

{{-- ═══════════════════════════════════════════
     PAGE 4 &#8212; T-SHIRT ORDERS
     ═══════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="sh-wrap">
    <span class="sh-num">03 / Logistics</span>
    <span class="sh-title">T-Shirt Size Orders</span>
    <span class="sh-desc">Overall size distribution, breakdown by district and church, and breakdown by department within each church.</span>
    <hr class="sh-rule"/>
</div>

{{-- Overall sizes --}}
<div class="ssub">Overall T-Shirt Distribution</div>
@php $maxSz = max(array_column($tshirtSizes, 'count') ?: [1]); @endphp
@php $totalShirts = array_sum(array_column($tshirtSizes,'count')); @endphp
<table style="width:100%;border-collapse:collapse;margin-bottom:5mm">
    @foreach($tshirtSizes as $szRow)
        @php $szPct = $maxSz > 0 ? round($szRow['count']/$maxSz*100) : 0; @endphp
        <tr>
            <td style="width:9%;font-size:8pt;font-weight:bold;color:#0F2255;padding:0 3mm 2.5mm 0">{{ $szRow['size'] }}</td>
            <td style="width:10%;font-size:9pt;font-weight:bold;color:#0F2255;text-align:right;padding:0 3mm 2.5mm 0">{{ $szRow['count'] }}</td>
            <td style="padding:0 3mm 2.5mm 0">
                <div class="bar-track"><div class="bf-gold bar-fill" style="width:{{ $szPct }}%"></div></div>
            </td>
            <td style="width:10%;font-size:7pt;color:#6B7280;text-align:right;padding:0 0 2.5mm 0">{{ $szPct }}%</td>
        </tr>
    @endforeach
</table>

{{-- T-shirt by department --}}
<div class="ssub">By Department</div>
@if(isset($tshirtByDept) && count($tshirtByDept) > 0)
    @php
        $deptSizeActive = [];
        foreach($allSizes as $sz) {
            foreach($tshirtByDept as $catSizes) {
                if(($catSizes[$sz] ?? 0) > 0) { $deptSizeActive[] = $sz; break; }
            }
        }
        $deptSizeActive = array_unique($deptSizeActive);
        $deptOrder = ['adventurer' => 'Adventurers', 'pathfinder' => 'Pathfinders', 'senior_youth' => 'Senior Youth'];
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead>
            <tr>
                <th width="20%">Department</th>
                @foreach($deptSizeActive as $sz)<th class="c" style="width:{{ floor(58/count($deptSizeActive)) }}%">{{ $sz }}</th>@endforeach
                <th class="r" width="12%">Total</th>
            </tr>
            </thead>
            <tbody>
            @php $grandSzTotals = []; @endphp
            @foreach($deptOrder as $catKey => $catLabel)
                @if(!isset($tshirtByDept[$catKey])) @continue @endif
                @php
                    $csizes   = $tshirtByDept[$catKey];
                    $catTotal = array_sum(array_intersect_key($csizes, array_flip($deptSizeActive)));
                    $catColor = $catKey==='adventurer' ? '#1B3A8F' : ($catKey==='pathfinder' ? '#059669' : '#92650A');
                @endphp
                <tr>
                    <td style="font-weight:bold;color:{{ $catColor }}">{{ $catLabel }}</td>
                    @foreach($deptSizeActive as $sz)
                        @php $cnt = $csizes[$sz] ?? 0; $grandSzTotals[$sz] = ($grandSzTotals[$sz] ?? 0) + $cnt; @endphp
                        <td class="c" style="{{ $cnt>0?'font-weight:bold;color:#0F2255':'color:#D1D5DB' }}">{{ $cnt ?: '' }}</td>
                    @endforeach
                    <td class="n" style="color:{{ $catColor }}">{{ number_format($catTotal) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Grand Total</td>
                @foreach($deptSizeActive as $sz)
                    <td class="c n">{{ $grandSzTotals[$sz] ?? 0 }}</td>
                @endforeach
                <td class="n">{{ number_format($totalShirts) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif

{{-- T-shirt by district + church cross-tab --}}
<div class="ssub">By District &amp; Church (All Departments Combined)</div>
@if(count($tshirtByDistrictChurch) > 0)
    @php
        $activeSizes = [];
        foreach($allSizes as $sz) {
            foreach($tshirtByDistrictChurch as $cc) {
                foreach($cc as $r) {
                    if(($r[$sz] ?? 0) > 0) { $activeSizes[] = $sz; break 2; }
                }
            }
        }
        $activeSizes = array_unique($activeSizes);
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead>
            <tr>
                <th width="20%">District</th>
                <th width="22%">Church</th>
                @foreach($activeSizes as $sz)<th class="c" style="width:{{ floor(46/count($activeSizes)) }}%">{{ $sz }}</th>@endforeach
                <th class="r" width="9%">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tshirtByDistrictChurch as $district => $churches)
                @php $isFirst = true; @endphp
                @foreach($churches as $church => $sdata)
                    @php $rowTotal = array_sum(array_intersect_key($sdata, array_flip($activeSizes))); @endphp
                    <tr>
                        <td class="mu sm">{{ $isFirst ? $district : '' }}</td>
                        <td>{{ $church }}</td>
                        @foreach($activeSizes as $sz)
                            <td class="c" style="font-size:7pt;{{ ($sdata[$sz]??0)>0?'font-weight:bold;color:#0F2255':'color:#D1D5DB' }}">{{ ($sdata[$sz]??0)?:'' }}</td>
                        @endforeach
                        <td class="n">{{ $rowTotal ?: '' }}</td>
                    </tr>
                    @php $isFirst = false; @endphp
                @endforeach
                @php
                    $dTot = []; foreach($activeSizes as $sz){$dTot[$sz]=array_sum(array_column($churches,$sz));} $dSum=array_sum($dTot);
                @endphp
                <tr class="dr">
                    <td colspan="2">{{ $district }} &#8212; Subtotal</td>
                    @foreach($activeSizes as $sz)<td class="c n">{{ $dTot[$sz]?:'' }}</td>@endforeach
                    <td class="n">{{ $dSum }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="2">Grand Total</td>
                @foreach($activeSizes as $sz)
                    @php $col=0; foreach($tshirtByDistrictChurch as $cc){foreach($cc as $r){$col+=($r[$sz]??0);}} @endphp
                    <td class="c n">{{ $col?:'' }}</td>
                @endforeach
                <td class="n">{{ number_format($totalShirts) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif

{{-- T-shirt by department within each district/church --}}
<div class="ssub">By Department, District &amp; Church</div>
@if(isset($tshirtByDeptDistrict) && count($tshirtByDeptDistrict) > 0)
    @php
        $deptActiveSizes = [];
        foreach($allSizes as $sz) {
            foreach($tshirtByDeptDistrict as $churches) {
                foreach($churches as $cats) {
                    foreach($cats as $catSizes) {
                        if(($catSizes[$sz] ?? 0) > 0) { $deptActiveSizes[] = $sz; break 3; }
                    }
                }
            }
        }
        $deptActiveSizes = array_unique($deptActiveSizes);
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead>
            <tr>
                <th width="18%">District</th>
                <th width="20%">Church</th>
                <th width="14%">Department</th>
                @foreach($deptActiveSizes as $sz)<th class="c" style="width:{{ floor(38/count($deptActiveSizes)) }}%">{{ $sz }}</th>@endforeach
                <th class="r" width="9%">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($tshirtByDeptDistrict as $district => $churches)
                @php $isFirstDist = true; @endphp
                @foreach($churches as $church => $cats)
                    @php $isFirstChurch = true; @endphp
                    @foreach(['adventurer','pathfinder','senior_youth'] as $catKey)
                        @if(!isset($cats[$catKey])) @continue @endif
                        @php
                            $catSizes = $cats[$catKey];
                            $catTotal = array_sum(array_intersect_key($catSizes, array_flip($deptActiveSizes)));
                        @endphp
                        <tr class="cat-row">
                            <td class="mu" style="font-size:7pt">{{ ($isFirstDist && $isFirstChurch) ? $district : '' }}</td>
                            <td style="font-size:7.5pt">{{ $isFirstChurch ? $church : '' }}</td>
                            <td style="font-size:7pt;font-weight:bold;color:{{ $catKey==='adventurer'?'#1B3A8F':($catKey==='pathfinder'?'#059669':'#92650A') }}">
                                {{ $catLabels[$catKey] ?? $catKey }}
                            </td>
                            @foreach($deptActiveSizes as $sz)
                                <td class="c" style="font-size:7pt;{{ ($catSizes[$sz]??0)>0?'font-weight:bold;color:#0F2255':'color:#D1D5DB' }}">{{ ($catSizes[$sz]??0)?:'' }}</td>
                            @endforeach
                            <td class="n" style="font-size:7.5pt">{{ $catTotal?:'' }}</td>
                        </tr>
                        @php $isFirstChurch = false; $isFirstDist = false; @endphp
                    @endforeach
                @endforeach
                {{-- District subtotal --}}
                @php
                    $deptDistTot = [];
                    foreach($deptActiveSizes as $sz) {
                        $deptDistTot[$sz] = 0;
                        foreach($churches as $cats) {
                            foreach($cats as $catSizes) {
                                $deptDistTot[$sz] += ($catSizes[$sz] ?? 0);
                            }
                        }
                    }
                    $deptDistSum = array_sum($deptDistTot);
                @endphp
                <tr class="dr">
                    <td colspan="3">{{ $district }} &#8212; Subtotal</td>
                    @foreach($deptActiveSizes as $sz)<td class="c n">{{ $deptDistTot[$sz]?:'' }}</td>@endforeach
                    <td class="n">{{ $deptDistSum }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">Grand Total</td>
                @foreach($deptActiveSizes as $sz)
                    @php
                        $gc = 0;
                        foreach($tshirtByDeptDistrict as $cc) {
                            foreach($cc as $cats) {
                                foreach($cats as $cs) { $gc += ($cs[$sz] ?? 0); }
                            }
                        }
                    @endphp
                    <td class="c n">{{ $gc?:'' }}</td>
                @endforeach
                <td class="n">{{ number_format($totalShirts) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif

{{-- ═══════════════════════════════════════════
     PAGE 5 &#8212; DISTRICT & CHURCH REGISTRATION
     ═══════════════════════════════════════════ --}}
<div class="page-break"></div>

<div class="sh-wrap">
    <span class="sh-num">04 / District Report</span>
    <span class="sh-title">Registration by District &amp; Church</span>
    <span class="sh-desc">Full breakdown per church with district subtotals. Adv = Adventurers &#183; PF = Pathfinders &#183; SYL = Senior Youth.</span>
    <hr class="sh-rule"/>
</div>

@php
    $grandTotal = max(array_sum(array_column($byChurch,'total')), 1);
    $curDist    = null;
    $dSub       = ['adv'=>0,'pf'=>0,'syl'=>0,'total'=>0];
    $maxChurch  = max(array_column($byChurch,'total') ?: [1]);
@endphp

<div class="tbl-outer">
    <table class="dt">
        <thead>
        <tr>
            <th width="19%">District</th>
            <th width="27%">Church</th>
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
                <tr class="dr">
                    <td colspan="2">{{ $curDist }} &#8212; Subtotal</td>
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
                <td class="mu sm">{{ $row['district'] }}</td>
                <td>{{ $row['church'] }}</td>
                <td class="n" style="color:#1B3A8F">{{ $row['adv'] ?: '' }}</td>
                <td class="n" style="color:#059669">{{ $row['pf']  ?: '' }}</td>
                <td class="n" style="color:#92650A">{{ $row['syl'] ?: '' }}</td>
                <td class="n">{{ number_format($row['total']) }}</td>
                <td class="n mu">{{ round($row['total']/$grandTotal*100,1) }}%</td>
                <td style="padding:2mm 3mm 2mm 0">
                    <div class="cbar-track">
                        <div class="bf-navy cbar-fill" style="width:{{ $maxChurch>0?round($row['total']/$maxChurch*100):0 }}%"></div>
                    </div>
                </td>
            </tr>
            @php $dSub['adv']+=$row['adv']; $dSub['pf']+=$row['pf']; $dSub['syl']+=$row['syl']; $dSub['total']+=$row['total']; @endphp
        @endforeach
        @if($curDist)
            <tr class="dr">
                <td colspan="2">{{ $curDist }} &#8212; Subtotal</td>
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

<div class="footer">
    <table class="footer-t">
        <tr>
            <td><strong style="color:#374151">Ogun Conference Youth Congress 2026</strong> &#8212; Management Report</td>
            <td class="r"><span class="footer-conf">Confidential</span> Generated {{ now()->format('d M Y, H:i') }} WAT</td>
        </tr>
    </table>
</div>

</body>
</html>
