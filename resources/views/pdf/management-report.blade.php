<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        /* ── PAGE ─────────────────────────────────────────────────────────────── */
        @page { margin: 24mm 20mm 22mm 20mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9.5pt; color: #111827; background: #fff; line-height: 1.6; }

        /* ── COVER ────────────────────────────────────────────────────────────── */
        .cov { background: #0F2255; page-break-after: always; }
        .cov-gold { background: #C9993A; height: 5mm; }
        .cov-body { padding: 28mm 28mm 24mm; text-align: center; }
        .cov-logo { width: 32mm; height: 32mm; border-radius: 50%; border: 2pt solid rgba(201,153,58,.5); display: block; margin: 0 auto 10mm; }
        .cov-org { font-size: 7pt; font-weight: bold; letter-spacing: 4px; text-transform: uppercase; color: #C9993A; margin-bottom: 8mm; }
        .cov-title { font-size: 34pt; font-weight: bold; color: #fff; line-height: 1.0; margin-bottom: 4mm; }
        .cov-sub  { font-size: 12pt; color: rgba(255,255,255,.7); margin-bottom: 3mm; }
        .cov-theme{ font-size: 8.5pt; font-style: italic; color: rgba(255,255,255,.4); margin-bottom: 12mm; }
        .cov-hr   { border: none; border-top: 0.7pt solid rgba(201,153,58,.35); margin: 0 14mm 12mm; }
        .cov-kpi  { width: 100%; border-collapse: collapse; margin-bottom: 14mm; }
        .cov-kpi td { text-align: center; padding: 4mm 5mm; border-right: 0.5pt solid rgba(255,255,255,.08); }
        .cov-kpi td:last-child { border-right: none; }
        .ck-lbl { display: block; font-size: 6pt; font-weight: bold; letter-spacing: 2.5px; text-transform: uppercase; color: rgba(255,255,255,.38); margin-bottom: 2.5mm; }
        .ck-val { display: block; font-size: 13pt; font-weight: bold; color: #fff; }
        .ck-val.g { color: #C9993A; }
        .cov-foot { font-size: 6.5pt; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.2); }

        /* ── SECTION HEADERS ─────────────────────────────────────────────────── */
        .sh { margin: 0 0 8mm; }
        .sh-num   { font-size: 7.5pt; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: #C9993A; display: block; margin-bottom: 1.5mm; }
        .sh-title { font-size: 16pt; font-weight: bold; color: #0F2255; display: block; margin-bottom: 2mm; }
        .sh-desc  { font-size: 8pt; color: #6B7280; display: block; margin-bottom: 4mm; }
        .sh-rule  { border: none; border-top: 1pt solid #E5E7EB; }

        /* Sub-section label */
        .ssub { font-size: 7pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #0F2255; border-left: 3pt solid #C9993A; padding-left: 3.5mm; margin: 8mm 0 3mm; }
        .ssub:first-child { margin-top: 0; }

        /* ── KPI CARDS ───────────────────────────────────────────────────────── */
        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 8mm; }
        .kpi-card { border: 0.5pt solid #E5E7EB; border-top: 4pt solid #CBD5E1; padding: 5mm 4mm; text-align: center; background: #fff; vertical-align: top; }
        .kpi-card.navy  { border-top-color: #0F2255; background: #0F2255; }
        .kpi-card.gold  { border-top-color: #C9993A; background: #FEFBF2; }
        .kpi-card.green { border-top-color: #059669; background: #F0FDF8; }
        .kpi-card.amber { border-top-color: #D97706; background: #FFFBEB; }
        .kpi-card.red   { border-top-color: #DC2626; background: #FEF2F2; }
        .kpi-icon { font-size: 14pt; display: block; line-height: 1; margin-bottom: 3mm; }
        .kpi-num  { font-size: 22pt; font-weight: bold; color: #0F2255; display: block; line-height: 1; margin-bottom: 2mm; }
        .kpi-lbl  { font-size: 6pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; color: #6B7280; display: block; }
        .navy .kpi-num { color: #C9993A; } .navy .kpi-lbl { color: rgba(255,255,255,.5); }
        .gold  .kpi-num { color: #92650A; } .gold  .kpi-lbl { color: #92650A; }
        .green .kpi-num { color: #059669; } .green .kpi-lbl { color: #059669; }
        .amber .kpi-num { color: #B45309; } .amber .kpi-lbl { color: #B45309; }
        .red   .kpi-num { color: #B91C1C; } .red   .kpi-lbl { color: #B91C1C; }

        /* ── REVENUE CARDS ───────────────────────────────────────────────────── */
        .rev-wrap { width: 100%; border-collapse: collapse; margin-bottom: 8mm; }
        .rev-card { border-radius: 5pt; padding: 5mm 6mm; vertical-align: top; border: 0.5pt solid #E5E7EB; }
        .rev-card.claimed { background: #F0FDF8; border-top: 4pt solid #059669; }
        .rev-card.active  { background: #EFF6FF; border-top: 4pt solid #2563EB; }
        .rev-card.total   { background: #0F2255; border-top: 4pt solid #C9993A; }
        .rev-card.pending { background: #FFFBEB; border-top: 4pt solid #D97706; }
        .rev-icon   { font-size: 12pt; display: block; margin-bottom: 2mm; }
        .rev-title  { font-size: 7pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; display: block; margin-bottom: 2mm; color: #374151; }
        .rev-amount { font-size: 16pt; font-weight: bold; display: block; line-height: 1; margin-bottom: 1.5mm; }
        .rev-note   { font-size: 7pt; color: #6B7280; display: block; }
        .claimed .rev-title { color: #065F46; } .claimed .rev-amount { color: #059669; }
        .active  .rev-title { color: #1E40AF; } .active  .rev-amount { color: #2563EB; }
        .total   .rev-title { color: #C9993A; } .total   .rev-amount { color: #fff; } .total .rev-note { color: rgba(255,255,255,.5); }
        .pending .rev-title { color: #92400E; } .pending .rev-amount { color: #D97706; }

        /* ── TABLES ───────────────────────────────────────────────────────────── */
        .tbl-outer { border: 0.5pt solid #E5E7EB; border-radius: 5pt; overflow: hidden; margin-bottom: 7mm; }
        .dt { width: 100%; border-collapse: collapse; font-size: 8.5pt; }
        .dt thead tr { background: #0F2255; }
        .dt th { color: #fff; padding: 3.5mm 4mm; text-align: left; font-size: 7.5pt; font-weight: bold; letter-spacing: 0.8px; text-transform: uppercase; }
        .dt th.r { text-align: right; }
        .dt th.c { text-align: center; }
        .dt td { padding: 3mm 4mm; border-bottom: 0.5pt solid #F3F4F6; vertical-align: middle; color: #1F2937; }
        .dt tr:last-child td { border-bottom: none; }
        .dt tr:nth-child(even) td { background: #F9FAFB; }
        .dt td.r  { text-align: right; }
        .dt td.c  { text-align: center; }
        .dt td.n  { text-align: right; font-weight: bold; color: #0F2255; }
        .dt td.mu { color: #6B7280; font-size: 8pt; }
        .dt .dr td { background: #EEF2FB !important; color: #0F2255; font-weight: bold; border-top: 0.5pt solid #C5D2EC; padding: 2.5mm 4mm; }
        .dt .dr td.n { color: #0F2255; text-align: right; }
        .dt .cat-row td { background: #F8FAFF !important; color: #374151; font-size: 8pt; padding: 2.5mm 4mm 2.5mm 9mm; }
        .dt tfoot td { background: #111827 !important; color: #fff; font-weight: bold; padding: 3.5mm 4mm; font-size: 8pt; text-transform: uppercase; letter-spacing: 0.5px; }
        .dt tfoot td.n { color: #C9993A; text-align: right; }

        /* ── BARS ─────────────────────────────────────────────────────────────── */
        .bar-track { background: #E5E7EB; border-radius: 6pt; height: 4.5mm; overflow: hidden; width: 100%; }
        .bar-fill  { height: 100%; border-radius: 6pt; }
        .bf-navy  { background: #0F2255; }
        .bf-gold  { background: #C9993A; }
        .bf-green { background: #059669; }
        .bf-amber { background: #D97706; }
        .bf-blue  { background: #2563EB; }
        .cbar-track { background: #EEF2FB; height: 4mm; border-radius: 3pt; overflow: hidden; }
        .cbar-fill  { height: 100%; border-radius: 3pt; }

        /* ── PILLS ────────────────────────────────────────────────────────────── */
        .pill { display: inline; padding: 0.6mm 3mm; border-radius: 10pt; font-size: 7pt; font-weight: bold; }
        .p-navy  { background: #0F2255; color: #fff; }
        .p-green { background: #DCFCE7; color: #14532D; }
        .p-amber { background: #FEF3C7; color: #78350F; }
        .p-red   { background: #FEE2E2; color: #7F1D1D; }
        .p-blue  { background: #DBEAFE; color: #1E3A8A; }
        .p-gray  { background: #F3F4F6; color: #374151; }

        /* ── LAYOUT ───────────────────────────────────────────────────────────── */
        .two-col { width: 100%; border-collapse: collapse; margin-bottom: 7mm; }
        .two-col td { vertical-align: top; padding: 0; }
        .col-l { padding-right: 6mm !important; }
        .page-break { page-break-before: always; }
        .note-box { font-size: 7.5pt; color: #6B7280; background: #F9FAFB; border: 0.5pt solid #E5E7EB; border-left: 3pt solid #C9993A; border-radius: 0 4pt 4pt 0; padding: 3mm 4mm; margin: 3mm 0 6mm; line-height: 1.6; }

        /* ── FOOTER ───────────────────────────────────────────────────────────── */
        .footer { border-top: 0.5pt solid #E5E7EB; padding-top: 3.5mm; margin-top: 9mm; }
        .footer table { width: 100%; border-collapse: collapse; }
        .footer td { font-size: 7pt; color: #9CA3AF; }
        .footer td.r { text-align: right; }
    </style>
</head>
<body>

@php
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

{{-- ═══ COVER ════════════════════════════════════════════ --}}
<div class="cov">
    <div class="cov-gold"></div>
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
        <div class="cov-foot">Confidential &nbsp;&#183;&nbsp; For Leadership Use Only</div>
    </div>
</div>

{{-- ═══ PAGE 2 — EXECUTIVE SUMMARY ═══════════════════════ --}}
<div class="sh">
    <span class="sh-num">01 &nbsp;/&nbsp; Overview</span>
    <span class="sh-title">Executive Summary</span>
    <span class="sh-desc">Registration performance, financial status and camp readiness &nbsp;&#8212;&nbsp; as at {{ now()->format('d M Y, H:i') }} WAT</span>
    <hr class="sh-rule"/>
</div>

<table class="kpi-grid">
    <tr>
        <td width="16.6%" style="padding:0 2.5mm 0 0"><div class="kpi-card navy"><span class="kpi-icon">&#128101;</span><span class="kpi-num">{{ number_format($s['total_campers']) }}</span><span class="kpi-lbl">Total Registered</span></div></td>
        <td width="16.6%" style="padding:0 2.5mm"><div class="kpi-card gold"><span class="kpi-icon">&#10004;</span><span class="kpi-num">{{ number_format($s['confirmed_payments']) }}</span><span class="kpi-lbl">Confirmed Payments</span></div></td>
        <td width="16.6%" style="padding:0 2.5mm"><div class="kpi-card amber"><span class="kpi-icon">&#9203;</span><span class="kpi-num">{{ number_format($s['offline_pending']) }}</span><span class="kpi-lbl">Awaiting Finance</span></div></td>
        <td width="16.6%" style="padding:0 2.5mm"><div class="kpi-card green"><span class="kpi-icon">&#8358;</span><span class="kpi-num">{{ number_format($s['total_revenue']) }}</span><span class="kpi-lbl">Total Conf. Revenue</span></div></td>
        <td width="16.6%" style="padding:0 2.5mm"><div class="kpi-card red"><span class="kpi-icon">&#128196;</span><span class="kpi-num">{{ number_format($s['consent_outstanding']) }}</span><span class="kpi-lbl">Consent Outstanding</span></div></td>
        <td width="16.6%" style="padding:0 0 0 2.5mm"><div class="kpi-card"><span class="kpi-icon">&#128247;</span><span class="kpi-num">{{ number_format($s['photos_approved']) }}</span><span class="kpi-lbl">Photos Approved</span></div></td>
    </tr>
</table>

<div class="ssub">Revenue Breakdown</div>
<table class="rev-wrap">
    <tr>
        <td width="25%" style="padding:0 3mm 0 0"><div class="rev-card claimed"><span class="rev-icon">&#10003;</span><span class="rev-title">Registered &amp; Paid</span><span class="rev-amount">&#8358;{{ number_format($s['claimed_revenue']) }}</span><span class="rev-note">Registration complete</span></div></td>
        <td width="25%" style="padding:0 3mm"><div class="rev-card active"><span class="rev-icon">&#128273;</span><span class="rev-title">Paid, Reg. Pending</span><span class="rev-amount">&#8358;{{ number_format($s['active_revenue']) }}</span><span class="rev-note">Awaiting form completion</span></div></td>
        <td width="25%" style="padding:0 3mm"><div class="rev-card total"><span class="rev-icon">&#8721;</span><span class="rev-title">Total Confirmed</span><span class="rev-amount">&#8358;{{ number_format($s['total_revenue']) }}</span><span class="rev-note">All confirmed combined</span></div></td>
        <td width="25%" style="padding:0 0 0 3mm"><div class="rev-card pending"><span class="rev-icon">&#9203;</span><span class="rev-title">Pending Approval</span><span class="rev-amount">&#8358;{{ number_format($s['pending_revenue']) }}</span><span class="rev-note">Bank transfers pending finance</span></div></td>
    </tr>
</table>

<table class="two-col">
    <tr>
        <td width="55%" class="col-l">
            <div class="ssub">Registration by Department</div>
            @php
                $depts = [
                    ['Adventurers', 'Ages 6&#8211;9',   $s['adventurers'],  'bf-navy',  '#0F2255'],
                    ['Pathfinders', 'Ages 10&#8211;15', $s['pathfinders'],  'bf-green', '#059669'],
                    ['Senior Youth','Ages 16+',         $s['senior_youth'], 'bf-gold',  '#92650A'],
                ];
            @endphp
            @foreach($depts as [$dname, $dages, $dcount, $dbar, $dcolor])
                @php $dpct = round($dcount / $deptBase * 100, 1); @endphp
                <table style="width:100%;border-collapse:collapse;margin-bottom:1.5mm">
                    <tr>
                        <td style="font-size:9pt;font-weight:bold;color:#111827">{!! $dname !!}</td>
                        <td style="font-size:8pt;color:#6B7280;text-align:center">{!! $dages !!}</td>
                        <td style="font-size:11pt;font-weight:bold;color:{{ $dcolor }};text-align:right;width:16%">{{ number_format($dcount) }}</td>
                        <td style="font-size:7.5pt;color:#6B7280;text-align:right;width:12%">{{ $dpct }}%</td>
                    </tr>
                </table>
                <div class="bar-track" style="margin-bottom:5mm"><div class="{{ $dbar }} bar-fill" style="width:{{ min($dpct,100) }}%"></div></div>
            @endforeach
            <div class="note-box">&#9733; Officials: <strong>{{ number_format($s['officials']) }}</strong> are included within their department counts above, not added separately.</div>
        </td>
        <td width="45%">
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

{{-- ═══ PAGE 3 — GENDER BY DISTRICT ════════════════════════ --}}
<div class="page-break"></div>
<div class="sh">
    <span class="sh-num">02 &nbsp;/&nbsp; Demographics</span>
    <span class="sh-title">Gender Breakdown by District &amp; Church</span>
    <span class="sh-desc">Male and female count for every district with per-church detail.</span>
    <hr class="sh-rule"/>
</div>

@if(!empty($genderByDistrict))
    <div class="tbl-outer">
        <table class="dt">
            <thead>
            <tr>
                <th width="50%">District &nbsp;/&nbsp; Church</th>
                <th class="r" width="16%">Male</th>
                <th class="r" width="16%">Female</th>
                <th class="r" width="18%">Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach($genderByDistrict as $d)
                <tr class="dr">
                    <td>{{ $d['district'] }}</td>
                    <td class="n">{{ number_format($d['male']) }}</td>
                    <td class="n">{{ number_format($d['female']) }}</td>
                    <td class="n">{{ number_format($d['total']) }}</td>
                </tr>
                @foreach($d['churches'] as $c)
                    <tr>
                        <td class="mu" style="padding-left:8mm">{{ $c['church'] }}</td>
                        <td class="n mu">{{ number_format($c['male']) }}</td>
                        <td class="n mu">{{ number_format($c['female']) }}</td>
                        <td class="n mu">{{ number_format($c['male'] + $c['female']) }}</td>
                    </tr>
                @endforeach
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td>Grand Total</td>
                <td class="n">{{ number_format(collect($genderByDistrict)->sum('male')) }}</td>
                <td class="n">{{ number_format(collect($genderByDistrict)->sum('female')) }}</td>
                <td class="n">{{ number_format(collect($genderByDistrict)->sum('total')) }}</td>
            </tr>
            </tfoot>
        </table>
    </div>
@endif

{{-- ═══ PAGE 4 — PAYMENT PIPELINE ══════════════════════════ --}}
<div class="page-break"></div>
<div class="sh">
    <span class="sh-num">03 &nbsp;/&nbsp; Operations</span>
    <span class="sh-title">Payment Status &amp; Code Pipeline</span>
    <span class="sh-desc">Registration code tracking, payment channels and outstanding approvals.</span>
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

<div class="ssub">Unclaimed Codes &#8212; By District &amp; Church</div>
@if(isset($unclaimedByChurch) && count($unclaimedByChurch) > 0)
    @php
        $unclaimedTotal = array_sum(array_column($unclaimedByChurch, 'count'));
        $unclaimedMax   = max(array_column($unclaimedByChurch, 'count') ?: [1]);
        $unclaimedBase  = max($unclaimedTotal, 1);
        $ucByDist       = [];
        foreach ($unclaimedByChurch as $row) { $ucByDist[$row['district']][] = $row; }
        ksort($ucByDist);
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead><tr><th width="22%">District</th><th width="32%">Church</th><th class="r" width="14%">Unclaimed</th><th class="r" width="12%">Share</th><th width="20%">Volume</th></tr></thead>
            <tbody>
            @foreach($ucByDist as $district => $churches)
                @php $distCount = array_sum(array_column($churches, 'count')); $isFirst = true; @endphp
                @foreach($churches as $row)
                    <tr>
                        <td class="mu">{{ $isFirst ? $district : '' }}</td>
                        <td>{{ $row['church'] }}</td>
                        <td class="n" style="color:#2563EB">{{ number_format($row['count']) }}</td>
                        <td class="n mu">{{ round($row['count']/$unclaimedBase*100,1) }}%</td>
                        <td style="padding:3mm 4mm"><div class="cbar-track"><div class="bf-blue cbar-fill" style="width:{{ $unclaimedMax>0?round($row['count']/$unclaimedMax*100):0 }}%"></div></div></td>
                    </tr>
                    @php $isFirst = false; @endphp
                @endforeach
                <tr class="dr"><td colspan="2">{{ $district }} &#8212; Subtotal</td><td class="n">{{ number_format($distCount) }}</td><td class="n">{{ round($distCount/$unclaimedBase*100,1) }}%</td><td></td></tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="2">Total Unclaimed</td><td class="n">{{ number_format($unclaimedTotal) }}</td><td class="n">100%</td><td></td></tr></tfoot>
        </table>
    </div>
@else
    <div class="note-box">&#10003; All codes have been claimed &#8212; no active unclaimed codes at this time.</div>
@endif

{{-- ═══ PAGE 5 — T-SHIRT ORDERS ═════════════════════════════ --}}
<div class="page-break"></div>
<div class="sh">
    <span class="sh-num">04 &nbsp;/&nbsp; Logistics</span>
    <span class="sh-title">T-Shirt Size Orders &#8212; General Campers</span>
    <span class="sh-desc">Overall distribution, by department, and breakdown by district &amp; church.</span>
    <hr class="sh-rule"/>
</div>

@php
    $officialCount     = $officialsTshirts['count'];
    $generalShirtTotal = array_sum(array_column($tshirtSizes, 'count'));
    $grandShirtTotal   = $generalShirtTotal + $officialCount;
@endphp

<div class="note-box">
    &#9432; <strong>Note:</strong>
    Camp officials (<strong>{{ $officialCount }}</strong> persons with an assigned role) have been extracted from
    the tables below and are shown separately in the <em>Camp Officials T-Shirt</em> section on the next page.
    <br/>
    General campers: <strong>{{ number_format($generalShirtTotal) }}</strong>
    &nbsp;+&nbsp; Camp officials: <strong>{{ $officialCount }}</strong>
    &nbsp;= Total registered: <strong>{{ number_format($grandShirtTotal) }}</strong>
</div>

<div class="ssub">Overall Distribution</div>
@php $maxSz = max(array_column($tshirtSizes, 'count') ?: [1]); $totalShirts = array_sum(array_column($tshirtSizes,'count')); @endphp
<table style="width:100%;border-collapse:collapse;margin-bottom:8mm">
    @foreach($tshirtSizes as $szRow)
        @php $szPct = $maxSz > 0 ? round($szRow['count']/$maxSz*100) : 0; @endphp
        <tr>
            <td style="width:8%;font-size:9.5pt;font-weight:bold;color:#0F2255;padding:0 4mm 3.5mm 0">{{ $szRow['size'] }}</td>
            <td style="width:10%;font-size:11pt;font-weight:bold;color:#0F2255;text-align:right;padding:0 4mm 3.5mm 0">{{ $szRow['count'] }}</td>
            <td style="padding:0 4mm 3.5mm 0"><div class="bar-track"><div class="bf-gold bar-fill" style="width:{{ $szPct }}%"></div></div></td>
            <td style="width:10%;font-size:8pt;color:#6B7280;text-align:right;padding:0 0 3.5mm 0">{{ $szPct }}%</td>
        </tr>
    @endforeach
</table>

<div class="ssub">By Department</div>
@if(isset($tshirtByDept) && count($tshirtByDept) > 0)
    @php
        $deptSizeActive = [];
        foreach($allSizes as $sz) { foreach($tshirtByDept as $cs) { if(($cs[$sz]??0)>0){$deptSizeActive[]=$sz;break;} } }
        $deptSizeActive = array_unique($deptSizeActive);
        $deptOrder = ['adventurer'=>'Adventurers','pathfinder'=>'Pathfinders','senior_youth'=>'Senior Youth'];
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead><tr><th width="22%">Department</th>@foreach($deptSizeActive as $sz)<th class="c">{{ $sz }}</th>@endforeach<th class="r" width="12%">Total</th></tr></thead>
            <tbody>
            @php $grandSzTotals = []; @endphp
            @foreach($deptOrder as $catKey => $catLabel)
                @if(!isset($tshirtByDept[$catKey])) @continue @endif
                @php $csizes=$tshirtByDept[$catKey]; $catTotal=array_sum(array_intersect_key($csizes,array_flip($deptSizeActive))); $catColor=$catKey==='adventurer'?'#1B3A8F':($catKey==='pathfinder'?'#059669':'#92650A'); @endphp
                <tr>
                    <td style="font-weight:bold;color:{{ $catColor }}">{{ $catLabel }}</td>
                    @foreach($deptSizeActive as $sz)
                        @php $cnt=$csizes[$sz]??0; $grandSzTotals[$sz]=($grandSzTotals[$sz]??0)+$cnt; @endphp
                        <td class="c" style="{{ $cnt>0?'font-weight:bold;color:#0F2255':'color:#D1D5DB' }}">{{ $cnt?:'' }}</td>
                    @endforeach
                    <td class="n" style="color:{{ $catColor }}">{{ number_format($catTotal) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot><tr><td>Grand Total</td>@foreach($deptSizeActive as $sz)<td class="c n">{{ $grandSzTotals[$sz]??0 }}</td>@endforeach<td class="n">{{ number_format($totalShirts) }}</td></tr></tfoot>
        </table>
    </div>
@endif

<div class="ssub">By District &amp; Church</div>
@if(count($tshirtByDistrictChurch) > 0)
    @php
        $activeSizes = [];
        foreach($allSizes as $sz){ foreach($tshirtByDistrictChurch as $cc){ foreach($cc as $r){ if(($r[$sz]??0)>0){$activeSizes[]=$sz;break 2;} } } }
        $activeSizes = array_unique($activeSizes);
    @endphp
    <div class="tbl-outer">
        <table class="dt">
            <thead><tr><th width="22%">District</th><th width="24%">Church</th>@foreach($activeSizes as $sz)<th class="c">{{ $sz }}</th>@endforeach<th class="r" width="10%">Total</th></tr></thead>
            <tbody>
            @foreach($tshirtByDistrictChurch as $district => $churches)
                @php $isFirst=true; @endphp
                @foreach($churches as $church => $sdata)
                    @php $rowTotal=array_sum(array_intersect_key($sdata,array_flip($activeSizes))); @endphp
                    <tr>
                        <td class="mu">{{ $isFirst?$district:'' }}</td>
                        <td>{{ $church }}</td>
                        @foreach($activeSizes as $sz)<td class="c" style="{{ ($sdata[$sz]??0)>0?'font-weight:bold;color:#0F2255':'color:#D1D5DB' }}">{{ ($sdata[$sz]??0)?:'' }}</td>@endforeach
                        <td class="n">{{ $rowTotal?:'' }}</td>
                    </tr>
                    @php $isFirst=false; @endphp
                @endforeach
                @php $dTot=[]; foreach($activeSizes as $sz){$dTot[$sz]=array_sum(array_column($churches,$sz));} $dSum=array_sum($dTot); @endphp
                <tr class="dr"><td colspan="2">{{ $district }} &#8212; Subtotal</td>@foreach($activeSizes as $sz)<td class="c n">{{ $dTot[$sz]?:'' }}</td>@endforeach<td class="n">{{ $dSum }}</td></tr>
            @endforeach
            </tbody>
            <tfoot><tr><td colspan="2">Grand Total</td>@foreach($activeSizes as $sz)@php $col=0;foreach($tshirtByDistrictChurch as $cc){foreach($cc as $r){$col+=($r[$sz]??0);}}@endphp<td class="c n">{{ $col?:'' }}</td>@endforeach<td class="n">{{ number_format($totalShirts) }}</td></tr></tfoot>
        </table>
    </div>
@endif

{{-- ═══ PAGE 6 — OFFICIALS T-SHIRT ════════════════════════ --}}
<div class="page-break"></div>
<div class="sh">
    <span class="sh-num">05b &nbsp;/&nbsp; Logistics</span>
    <span class="sh-title">Camp Officials T-Shirt Orders</span>
    <span class="sh-desc">Officials are defined as campers assigned a camp role.</span>
    <hr class="sh-rule"/>
</div>

@php $allSzOrder = ['XS','S','M','L','XL','XXL','XXXL']; @endphp

{{-- Size summary --}}
@if(!empty($officialsTshirts['summary']))
    <div class="ssub">Size Summary &#8212; Officials Only</div>
    <div class="tbl-outer" style="margin-bottom:7mm">
        <table class="dt">
            <thead>
            <tr>
                @foreach($allSzOrder as $sz)
                    @if(isset($officialsTshirts['summary'][$sz]))
                        <th class="c">{{ $sz }}</th>
                    @endif
                @endforeach
                <th class="r">Total Officials</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                @foreach($allSzOrder as $sz)
                    @if(isset($officialsTshirts['summary'][$sz]))
                        <td class="c n">{{ $officialsTshirts['summary'][$sz] }}</td>
                    @endif
                @endforeach
                <td class="n" style="color:#C9993A">{{ $officialsTshirts['count'] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
@endif

{{-- Reconciliation note --}}
<div class="note-box">
    &#10003; <strong>Reconciliation:</strong>
    General campers: <strong>{{ number_format($generalShirtTotal) }}</strong>
    &nbsp;+&nbsp; Camp officials: <strong>{{ $officialsTshirts['count'] }}</strong>
    &nbsp;= <strong>{{ number_format($grandShirtTotal) }}</strong> total registered.
</div>

{{-- Officials grouped by role --}}
@if(!empty($officialsTshirts['list']))
    @php
        // Group officials by role, sorted alphabetically
        $officialsByRole = collect($officialsTshirts['list'])
            ->groupBy('role')
            ->sortKeys();
    @endphp

    @foreach($officialsByRole as $roleName => $members)
        <div class="ssub">{{ $roleName }} <span style="font-size:6.5pt;font-weight:normal;text-transform:none;letter-spacing:0;color:#6B7280">({{ $members->count() }} {{ $members->count() === 1 ? 'member' : 'members' }})</span></div>
        <div class="tbl-outer" style="margin-bottom:6mm">
            <table class="dt">
                <thead>
                <tr>
                    <th width="40%">Name</th>
                    <th width="28%">Church</th>
                    <th width="18%">District</th>
                    <th class="c" width="14%">T-Shirt</th>
                </tr>
                </thead>
                <tbody>
                @foreach($members->sortBy('name') as $official)
                    <tr>
                        <td style="font-weight:bold">{{ $official['name'] }}</td>
                        <td class="mu">{{ $official['church'] }}</td>
                        <td class="mu">{{ $official['district'] }}</td>
                        <td class="c n">{{ $official['tshirt'] }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">{{ $roleName }} &#8212; Subtotal</td>
                    <td class="n">{{ $members->count() }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    @endforeach
@else
    <div class="note-box">No camp officials with assigned roles found.</div>
@endif

{{-- ═══ PAGE 7 — DISTRICT REGISTRATION ══════════════════════ --}}
<div class="page-break"></div>
<div class="sh">
    <span class="sh-num">06 &nbsp;/&nbsp; District Report</span>
    <span class="sh-title">Registration by District &amp; Church</span>
    <span class="sh-desc">Full breakdown per church with district subtotals. &nbsp;Adv = Adventurers &nbsp;&#183;&nbsp; PF = Pathfinders &nbsp;&#183;&nbsp; SYL = Senior Youth.</span>
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
            <th width="18%">District</th>
            <th width="28%">Church</th>
            <th class="r" width="10%">Adv</th>
            <th class="r" width="10%">PF</th>
            <th class="r" width="10%">SYL</th>
            <th class="r" width="11%">Total</th>
            <th class="r" width="8%">Share</th>
            <th width="5%"></th>
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
                <td class="mu">{{ $row['district'] }}</td>
                <td>{{ $row['church'] }}</td>
                <td class="n" style="color:#1B3A8F">{{ $row['adv'] ?: '' }}</td>
                <td class="n" style="color:#059669">{{ $row['pf']  ?: '' }}</td>
                <td class="n" style="color:#92650A">{{ $row['syl'] ?: '' }}</td>
                <td class="n">{{ number_format($row['total']) }}</td>
                <td class="n mu">{{ round($row['total']/$grandTotal*100,1) }}%</td>
                <td style="padding:3mm 3mm 3mm 0"><div class="cbar-track"><div class="bf-navy cbar-fill" style="width:{{ $maxChurch>0?round($row['total']/$maxChurch*100):0 }}%"></div></div></td>
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
    <table>
        <tr>
            <td><strong style="color:#374151">Ogun Conference Youth Congress 2026</strong> &#8212; Management Report</td>
            <td class="r">Confidential &nbsp;&#183;&nbsp; Generated {{ now()->format('d M Y, H:i') }} WAT</td>
        </tr>
    </table>
</div>

</body>
</html>
