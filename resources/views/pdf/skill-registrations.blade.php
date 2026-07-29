<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 20mm 16mm 18mm 16mm; size: A4; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 8.5pt; color: #1F2937; background: #fff; line-height: 1.5; }

        /* Cover strip */
        .cover-strip { background: #0B2D6B; padding: 8mm 0 7mm; text-align: center; margin-bottom: 6mm; }
        .cover-tag   { font-size: 6pt; font-weight: bold; letter-spacing: 3px; text-transform: uppercase; color: rgba(255,255,255,.45); margin-bottom: 2.5mm; }
        .cover-title { font-size: 17pt; font-weight: bold; color: #fff; margin-bottom: 1.5mm; }
        .cover-sub   { font-size: 8.5pt; color: rgba(255,255,255,.65); margin-bottom: 5mm; }
        .cover-meta  { width: 100%; border-collapse: collapse; max-width: 380px; margin: 0 auto; }
        .cover-meta td { text-align: center; padding: 2mm 4mm; border-right: 0.5pt solid rgba(255,255,255,.1); }
        .cover-meta td:last-child { border-right: none; }
        .cm-lbl { display: block; font-size: 5.5pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: rgba(255,255,255,.38); margin-bottom: 1mm; }
        .cm-val { display: block; font-size: 10pt; font-weight: bold; color: #fff; }
        .cm-val.gold { color: #D4B26E; }

        /* Section headers */
        .sh { font-size: 6.5pt; font-weight: bold; letter-spacing: 2px; text-transform: uppercase; color: #0B2D6B; border-left: 3pt solid #B8924A; padding-left: 3mm; margin: 5mm 0 2mm; }

        /* Summary table */
        .sum-wrap { border: 0.5pt solid #E5E7EB; border-radius: 5pt; overflow: hidden; margin-bottom: 6mm; }
        .dt { width: 100%; border-collapse: collapse; font-size: 7.5pt; }
        .dt thead tr { background: #0B2D6B; }
        .dt th { color: #fff; padding: 2.5mm 3mm; text-align: left; font-size: 6.5pt; font-weight: bold; letter-spacing: .5px; text-transform: uppercase; }
        .dt th.r { text-align: right; }
        .dt td { padding: 2mm 3mm; border-bottom: 0.5pt solid #F3F4F6; }
        .dt tr:nth-child(even) td { background: #FAFAFA; }
        .dt td.n { text-align: right; font-weight: bold; color: #0B2D6B; }
        .dt td.mu { color: #6B7280; font-size: 7pt; }
        .dt tfoot td { background: #111827; color: #fff; font-weight: bold; padding: 2.5mm 3mm; }
        .dt tfoot td.n { color: #D4B26E; text-align: right; }

        /* Bar */
        .bar-wrap { background: #E5E7EB; border-radius: 3pt; height: 4mm; overflow: hidden; }
        .bar-fill { height: 100%; border-radius: 3pt; }
        .bf-green { background: #059669; }
        .bf-amber { background: #D97706; }
        .bf-red   { background: #DC2626; }

        /* Skill group */
        .skill-block { margin-bottom: 6mm; page-break-inside: avoid; }
        .skill-header {
            background: #142547;
            color: #fff;
            padding: 2.5mm 4mm;
            font-size: 7.5pt;
            font-weight: bold;
        }
        .skill-header .cat {
            font-size: 6pt;
            font-weight: normal;
            color: rgba(255,255,255,.55);
            margin-left: 5mm;
        }
        .skill-header .cap {
            float: right;
            font-size: 6.5pt;
            color: #D4B26E;
        }

        /* Footer */
        .rf { border-top: 0.5pt solid #E5E7EB; padding-top: 2mm; margin-top: 5mm; }
        .rf table { width: 100%; border-collapse: collapse; }
        .rf td { font-size: 6pt; color: #9CA3AF; }
        .rf td.r { text-align: right; }

        .page-break { page-break-before: always; }
        .badge { display: inline; padding: .4mm 2mm; border-radius: 2pt; font-size: 6pt; font-weight: bold; }
        .b-green { background: #DCFCE7; color: #14532D; }
        .b-amber { background: #FEF3C7; color: #78350F; }
        .b-red   { background: #FEE2E2; color: #7F1D1D; }
        .b-navy  { background: #0B2D6B; color: #fff; }
        .b-gray  { background: #F3F4F6; color: #374151; }
    </style>
</head>
<body>

{{-- ── Cover strip ── --}}
<div class="cover-strip">
    <div class="cover-tag">Ogun Conference Youth Congress 2026 &nbsp;&#183;&nbsp; Skill Acquisition</div>
    <div class="cover-title">Skill Registration Report</div>
    <div class="cover-sub">{{ $filterLabel }}</div>
    <table class="cover-meta">
        <tr>
            <td><span class="cm-lbl">Total Registered</span><span class="cm-val gold">{{ $totalRegistered }}</span></td>
            <td><span class="cm-lbl">Skills</span><span class="cm-val">{{ $bySkill->count() }}</span></td>
            <td><span class="cm-lbl">Generated</span><span class="cm-val" style="font-size:7.5pt">{{ $generatedAt }}</span></td>
        </tr>
    </table>
</div>

{{-- ── Capacity summary ── --}}
<div class="sh">Skill Capacity Summary</div>
<div class="sum-wrap">
    <table class="dt">
        <thead>
        <tr>
            <th width="32%">Skill</th>
            <th width="14%">For</th>
            <th class="r" width="11%">Registered</th>
            <th class="r" width="11%">Capacity</th>
            <th class="r" width="11%">Remaining</th>
            <th width="21%">Fill Rate</th>
        </tr>
        </thead>
        <tbody>
        @foreach($skillSummary as $s)
            @php
                $pct     = $s['capacity'] > 0 ? round($s['registered'] / $s['capacity'] * 100) : 0;
                $barCls  = $pct >= 90 ? 'bf-red' : ($pct >= 60 ? 'bf-amber' : 'bf-green');
            @endphp
            <tr>
                <td style="font-weight:bold">{{ $s['name'] }}</td>
                <td class="mu">{{ $s['category'] }}</td>
                <td class="n">{{ $s['registered'] }}</td>
                <td class="n mu">{{ $s['capacity'] }}</td>
                <td class="n" style="color:{{ $s['remaining'] <= 5 ? '#DC2626' : ($s['remaining'] <= 15 ? '#D97706' : '#059669') }}">
                    {{ $s['remaining'] }}
                </td>
                <td style="padding:2.5mm 3mm">
                    <div class="bar-wrap"><div class="{{ $barCls }} bar-fill" style="width:{{ $pct }}%"></div></div>
                    <span style="font-size:6pt;color:#9CA3AF">{{ $pct }}%</span>
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2">Total</td>
            <td class="n">{{ $skillSummary->sum('registered') }}</td>
            <td class="n">{{ $skillSummary->sum('capacity') }}</td>
            <td class="n">{{ $skillSummary->sum('remaining') }}</td>
            <td></td>
        </tr>
        </tfoot>
    </table>
</div>

{{-- ── Registrations by skill ── --}}
<div class="page-break"></div>
<div class="sh">Registrations by Skill</div>

@foreach($bySkill as $group)
    @php
        $skill = $group['skill'];
        $rows  = $group['rows'];
        $cap   = $skill->maximum_attendees;
        $reg   = $rows->count();
    @endphp
    <div class="skill-block">
        <div class="skill-header">
            {{ $skill->name }}
            <span class="cat">{{ $skill->categoryLabel() }}{{ $skill->club_rank ? ' · ' . $skill->club_rank : '' }}</span>
            <span class="cap">{{ $reg }} / {{ $cap }} registered &nbsp;&#183;&nbsp; Facilitator: {{ $skill->facilitator ?: 'TBD' }}</span>
        </div>
        <div style="border:0.5pt solid #E5E7EB;border-top:none;overflow:hidden;border-radius:0 0 4pt 4pt">
            <table class="dt">
                <thead>
                <tr style="background:#F8F9FB">
                    <th style="color:#374151" width="5%">#</th>
                    <th style="color:#374151" width="28%">Camper Name</th>
                    <th style="color:#374151" width="16%">Reg. No</th>
                    <th style="color:#374151" width="18%">Church</th>
                    <th style="color:#374151" width="14%">District</th>
                    <th style="color:#374151" width="10%">Category</th>
                    <th style="color:#374151" width="9%">Rank</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rows as $i => $r)
                    @php
                        $catVal = $r->camper?->category?->value ?? '';
                        $catColor = match($catVal) {
                            'adventurer'   => '#1B3A8F',
                            'pathfinder'   => '#059669',
                            'senior_youth' => '#92650A',
                            default        => '#374151',
                        };
                    @endphp
                    <tr>
                        <td class="mu" style="font-size:7pt">{{ $i + 1 }}</td>
                        <td style="font-weight:bold">{{ $r->camper?->full_name ?? '—' }}</td>
                        <td style="font-family:monospace;font-size:7pt;color:#6B7280">{{ $r->camper?->camper_number ?? '—' }}</td>
                        <td class="mu">{{ $r->camper?->church?->name ?? '—' }}</td>
                        <td class="mu">{{ $r->camper?->church?->district?->name ?? '—' }}</td>
                        <td style="font-size:7pt;font-weight:bold;color:{{ $catColor }}">{{ $r->camper?->category?->label() ?? '—' }}</td>
                        <td class="mu">{{ $r->camper?->club_rank ?: '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endforeach

@if($bySkill->isEmpty())
    <div style="text-align:center;padding:12mm 0;color:#6B7280">
        <p style="font-size:9pt;font-weight:bold;color:#374151;margin-bottom:2mm">No registrations found</p>
        <p style="font-size:8pt">No campers have registered for any skill yet, or your filters returned no results.</p>
    </div>
@endif

<div class="rf">
    <table>
        <tr>
            <td><strong style="color:#374151">Ogun Conference Youth Congress 2026</strong> &#8212; Skill Registration Report</td>
            <td class="r">Generated {{ $generatedAt }} &nbsp;&#183;&nbsp; Confidential</td>
        </tr>
    </table>
</div>

</body>
</html>
