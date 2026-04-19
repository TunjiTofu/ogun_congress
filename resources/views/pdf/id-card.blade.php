<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 53.98mm;
        }
        html, body {
            margin: 0;
            padding: 0;
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7pt;
            color: #1A1A1A;
        }
        .card {
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            display: block;
            page-break-inside: avoid;
            page-break-after: avoid;
            position: relative;
            background: #FFFFFF;
        }

        /* ── Top band ───────────────────────────────────────────────────────── */
        .hd {
            display: block;
            width: 85.6mm;
            height: 8mm;
            background: {{ $badgeColor }};
            overflow: hidden;
        }
        .hd-inner {
            display: table;
            width: 100%;
            height: 8mm;
            padding: 0 2mm;
        }
        .hd-left  { display: table-cell; vertical-align: middle; }
        .hd-right { display: table-cell; vertical-align: middle; text-align: right; }

        .logo-img {
            width: 5.5mm;
            height: 5.5mm;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
        }
        .camp-title {
            display: inline-block;
            vertical-align: middle;
            margin-left: 1.5mm;
            color: #FFFFFF;
        }
        .camp-name { font-size: 5.5pt; font-weight: bold; letter-spacing: 0.3pt; }
        .camp-sub  { font-size: 4pt;   color: rgba(255,255,255,0.8); }
        .camp-year { font-size: 6pt;   font-weight: bold; color: #FFFFFF; }

        /* ── Body ───────────────────────────────────────────────────────────── */
        .body {
            display: table;
            width: 85.6mm;
            height: 38mm;
            padding: 1.5mm 1.5mm 0 1.5mm;
        }
        .col-photo   { display: table-cell; width: 22mm; vertical-align: top; }
        .col-info    { display: table-cell; vertical-align: top; padding-left: 1.5mm; }
        .col-qr      { display: table-cell; width: 18mm; vertical-align: top; text-align: center; }

        /* Photo */
        .photo-box {
            width: 19mm;
            height: 24mm;
            border: 0.5pt solid #CCCCCC;
            overflow: hidden;
            background: #F5F5F5;
        }
        .photo-box img {
            width: 19mm;
            height: 24mm;
            display: block;
        }
        .no-photo {
            width: 19mm;
            height: 24mm;
            font-size: 5pt;
            color: #AAAAAA;
            text-align: center;
            padding-top: 8mm;
        }

        /* Info */
        .camper-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0B2D6B;
            line-height: 1.15;
            max-width: 36mm;
            word-wrap: break-word;
        }
        .camper-code {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 5.5pt;
            color: #666666;
            letter-spacing: 0.4pt;
            margin-top: 0.5mm;
        }
        .badge {
            display: inline-block;
            background: {{ $badgeColor }};
            color: #FFFFFF;
            font-size: 5pt;
            font-weight: bold;
            padding: 0.3mm 1.5mm;
            margin-top: 1mm;
        }

        .lbl { font-size: 4pt; color: #999999; text-transform: uppercase; letter-spacing: 0.2pt; }
        .val { font-size: 5.5pt; font-weight: bold; color: #1A1A1A; line-height: 1.2; max-width: 33mm; word-wrap: break-word; }
        .row { margin-top: 1.2mm; }

        /* QR */
        .qr-img {
            width: 16mm;
            height: 16mm;
            display: block;
            margin: 0 auto;
            border: 0.3pt solid #EEEEEE;
        }
        .qr-lbl {
            font-size: 3.8pt;
            color: #AAAAAA;
            text-align: center;
            margin-top: 0.5mm;
            letter-spacing: 0.1pt;
        }

        /* ── Footer ─────────────────────────────────────────────────────────── */
        .ft {
            display: table;
            width: 85.6mm;
            height: 7.98mm;
            background: {{ $badgeColor }}18;
            border-top: 0.4pt solid {{ $badgeColor }}44;
            padding: 0 2mm;
        }
        .ft-inner { display: table-row; }
        .ft-l { display: table-cell; vertical-align: middle; font-size: 4pt; color: #777777; }
        .ft-r { display: table-cell; vertical-align: middle; text-align: right; font-size: 4pt; color: #777777; }
    </style>
</head>
<body>
<div class="card">

    {{-- Header --}}
    <div class="hd">
        <div class="hd-inner">
            <div class="hd-left">
                @php $logoPath = public_path('images/logo.svg'); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                         class="logo-img" alt="Logo"/>
                @endif
                <div class="camp-title">
                    <div class="camp-name">{{ $campName }}</div>
                    <div class="camp-sub">SDA &mdash; Ogun Conference</div>
                </div>
            </div>
            <div class="hd-right">
                <div class="camp-year">{{ $campYear }}</div>
            </div>
        </div>
    </div>

    {{-- Body --}}
    <div class="body">

        {{-- Photo column --}}
        <div class="col-photo">
            <div class="photo-box">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" alt="Photo"/>
                @else
                    <div class="no-photo">No<br/>Photo</div>
                @endif
            </div>
        </div>

        {{-- Info column --}}
        <div class="col-info">
            <div class="camper-name">{{ $camper->full_name }}</div>
            <div class="camper-code">{{ $camper->camper_number }}</div>
            <div class="badge">{{ $camper->category->label() }}</div>

            <div class="row">
                <div class="lbl">Church</div>
                <div class="val">{{ $camper->church?->name ?? '&mdash;' }}</div>
            </div>
            <div class="row">
                <div class="lbl">District</div>
                <div class="val">{{ $camper->church?->district?->name ?? '&mdash;' }}</div>
            </div>
            @if($camper->club_rank)
                <div class="row">
                    <div class="lbl">Rank</div>
                    <div class="val">{{ $camper->club_rank }}</div>
                </div>
            @endif
        </div>

        {{-- QR column --}}
        <div class="col-qr">
            {{-- $qrCode is a data:image/png;base64,... URL --}}
            <img src="{{ $qrCode }}" class="qr-img" alt="QR"/>
            <div class="qr-lbl">Scan to verify</div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="ft">
        <div class="ft-inner">
            <div class="ft-l">Seventh-day Adventist Church</div>
            <div class="ft-r">Valid {{ $campYear }}</div>
        </div>
    </div>

</div>
</body>
</html>
