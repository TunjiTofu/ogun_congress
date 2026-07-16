<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 22mm 18mm 20mm 18mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8.5pt; color: #1F2937; background: #fff; line-height: 1.5; }

        /* Cover strip */
        .header { background: #7F1D1D; padding: 10mm 0 8mm; text-align: center; margin-bottom: 7mm; }
        .header-tag { font-size: 6pt; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,.5); margin-bottom: 3mm; }
        .header-title { font-size: 18pt; font-weight: bold; color: #fff; margin-bottom: 1.5mm; }
        .header-sub { font-size: 9pt; color: rgba(255,255,255,.7); margin-bottom: 5mm; }
        .header-meta { width: 100%; border-collapse: collapse; max-width: 400px; margin: 0 auto; }
        .header-meta td { text-align: center; padding: 2mm 5mm; border-right: 0.5pt solid rgba(255,255,255,.15); }
        .header-meta td:last-child { border-right: none; }
        .hm-label { display: block; font-size: 5.5pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.4); margin-bottom: 1.5mm; }
        .hm-value { display: block; font-size: 12pt; font-weight: bold; color: #FCA5A5; }

        /* Stat cards */
        .stats { width: 100%; border-collapse: collapse; margin-bottom: 6mm; }
        .stat-card { border: 0.5pt solid #E5E7EB; border-radius: 5pt; padding: 3.5mm 4mm; text-align: center; vertical-align: top; }
        .stat-card.red    { background: #FEF2F2; border-top: 3pt solid #DC2626; }
        .stat-card.orange { background: #FFF7ED; border-top: 3pt solid #F97316; }
        .stat-card.blue   { background: #EFF6FF; border-top: 3pt solid #3B82F6; }
        .stat-num   { font-size: 18pt; font-weight: bold; display: block; line-height: 1; margin-bottom: 1mm; }
        .red    .stat-num { color: #DC2626; }
        .orange .stat-num { color: #F97316; }
        .blue   .stat-num { color: #3B82F6; }
        .stat-lbl { font-size: 6pt; font-weight: bold; letter-spacing: 1.5px; text-transform: uppercase; display: block; }
        .red    .stat-lbl { color: #991B1B; }
        .orange .stat-lbl { color: #92400E; }
        .blue   .stat-lbl { color: #1E40AF; }

        /* District block */
        .district-block { margin-bottom: 6mm; page-break-inside: avoid; }
        .district-header {
            background: #1E3A5F;
            color: #fff;
            padding: 2.5mm 4mm;
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 0;
        }
        .district-header span { color: rgba(255,255,255,.55); font-weight: normal; font-size: 7pt; }

        /* Church block */
        .church-block { margin-bottom: 0; border-left: 3pt solid #93C5FD; margin-left: 3mm; }
        .church-header {
            background: #EFF6FF;
            border-bottom: 0.5pt solid #BFDBFE;
            padding: 2mm 4mm;
            font-size: 7.5pt;
            font-weight: bold;
            color: #1E40AF;
        }
        .church-header span { color: #6B7280; font-weight: normal; font-size: 7pt; margin-left: 3mm; }

        /* Camper table */
        .ct { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .ct th {
            background: #F3F4F6;
            color: #374151;
            padding: 2mm 3.5mm;
            text-align: left;
            font-size: 6.5pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 0.5pt solid #E5E7EB;
        }
        .ct th.r { text-align: right; }
        .ct td { padding: 2mm 3.5mm; border-bottom: 0.5pt solid #F9FAFB; color: #1F2937; vertical-align: middle; }
        .ct tr:last-child td { border-bottom: none; }
        .ct tr:nth-child(even) td { background: #FAFAFA; }
        .ct td.cat { text-align: left; }
        .ct td.reason { color: #DC2626; font-size: 7pt; }
        .ct td.date { color: #6B7280; font-size: 7pt; text-align: right; }
        .ct .no-num { color: #D1D5DB; }

        /* Footer */
        .rf { border-top: 0.5pt solid #E5E7EB; padding-top: 2mm; margin-top: 6mm; }
        .rf table { width: 100%; border-collapse: collapse; }
        .rf td { font-size: 6pt; color: #9CA3AF; padding: 0; vertical-align: middle; }
        .rf td.r { text-align: right; }

        /* Call to action notice */
        .notice {
            background: #FFFBEB;
            border: 0.5pt solid #FDE68A;
            border-left: 3pt solid #F59E0B;
            border-radius: 0 4pt 4pt 0;
            padding: 3mm 4mm;
            font-size: 7.5pt;
            color: #78350F;
            margin-bottom: 6mm;
        }
        .notice strong { color: #92400E; }
    </style>
</head>
<body>

{{-- ── Header ── --}}
<div class="header">
    <div class="header-tag">Ogun Conference Youth Congress 2026 &nbsp;&#183;&nbsp; Photo Review</div>
    <div class="header-title">Rejected Photos Report</div>
    <div class="header-sub">Campers awaiting coordinator re-upload &nbsp;&#183;&nbsp; Action required</div>
    <table class="header-meta">
        <tr>
            <td><span class="hm-label">Pending Re-upload</span><span class="hm-value">{{ $totalCount }}</span></td>
            <td><span class="hm-label">Churches</span><span class="hm-value">{{ $churchCount }}</span></td>
            <td><span class="hm-label">Districts</span><span class="hm-value">{{ $districtCount }}</span></td>
            <td><span class="hm-label">Generated</span><span class="hm-value" style="font-size:8pt">{{ $generatedAt }}</span></td>
        </tr>
    </table>
</div>

{{-- ── Stat Cards ── --}}
<table class="stats">
    <tr>
        <td width="33%" style="padding:0 2mm 0 0">
            <div class="stat-card red">
                <span class="stat-num">{{ $totalCount }}</span>
                <span class="stat-lbl">Campers Awaiting Re-upload</span>
            </div>
        </td>
        <td width="33%" style="padding:0 2mm">
            <div class="stat-card orange">
                <span class="stat-num">{{ $churchCount }}</span>
                <span class="stat-lbl">Churches to Follow Up</span>
            </div>
        </td>
        <td width="33%" style="padding:0 0 0 2mm">
            <div class="stat-card blue">
                <span class="stat-num">{{ $districtCount }}</span>
                <span class="stat-lbl">Districts Affected</span>
            </div>
        </td>
    </tr>
</table>

{{-- ── Notice ── --}}
<div class="notice">
    <strong>&#9888; Action Required:</strong>
    The campers listed below had their photos rejected. Their church coordinators have not yet re-uploaded a replacement.
    Please contact the relevant coordinators to prompt re-submission before camp begins.
</div>

{{-- ── Grouped table ── --}}
@php $rowNum = 0; @endphp
@foreach($grouped as $district => $churches)
    <div class="district-block">

        <div class="district-header">
            {{ $district }}
            <span>&nbsp;&#8212;&nbsp;{{ array_sum(array_map('count', $churches)) }} camper(s) across {{ count($churches) }} church(es)</span>
        </div>

        @foreach($churches as $church => $campers)
            <div class="church-block">

                <div class="church-header">
                    {{ $church }}
                    <span>{{ count($campers) }} camper(s) pending re-upload</span>
                </div>

                <table class="ct">
                    <thead>
                    <tr>
                        <th width="6%">#</th>
                        <th width="32%">Camper Name</th>
                        <th width="14%" class="cat">Dept</th>
                        <th width="30%">Rejection Reason</th>
                        <th width="18%" class="r">Rejected At</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($campers as $camper)
                        @php $rowNum++; @endphp
                        <tr>
                            <td class="no-num" style="font-size:7pt;color:#9CA3AF">{{ $rowNum }}</td>
                            <td style="font-weight:bold">{{ $camper->full_name }}</td>
                            <td class="cat">
                                @php
                                    $catVal = $camper->category instanceof \App\Enums\CamperCategory
                                        ? $camper->category->value : (string)$camper->category;
                                    $catColor = match($catVal) {
                                        'adventurer'  => '#1B3A8F',
                                        'pathfinder'  => '#2E8B57',
                                        'senior_youth'=> '#92650A',
                                        default       => '#374151',
                                    };
                                    $catLabel = $camper->category?->label() ?? $catVal;
                                @endphp
                                <span style="font-size:6.5pt;font-weight:bold;color:{{ $catColor }}">{{ $catLabel }}</span>
                            </td>
                            <td class="reason">{{ $camper->photo_rejection_reason ?: '—' }}</td>
                            <td class="date">{{ $camper->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        @endforeach

    </div>
@endforeach

@if($totalCount === 0)
    <div style="text-align:center;padding:16mm 0;color:#6B7280">
        <div style="font-size:18pt;margin-bottom:3mm">&#10003;</div>
        <div style="font-size:10pt;font-weight:bold;color:#059669;margin-bottom:1.5mm">All Clear</div>
        <div style="font-size:8pt">No campers currently have rejected photos awaiting re-upload.</div>
    </div>
@endif

<div class="rf">
    <table>
        <tr>
            <td><strong style="color:#374151">Ogun Conference Youth Congress 2026</strong> &#8212; Rejected Photos Report</td>
            <td class="r">Generated {{ $generatedAt }} &nbsp;&#183;&nbsp; Confidential</td>
        </tr>
    </table>
</div>

</body>
</html>
