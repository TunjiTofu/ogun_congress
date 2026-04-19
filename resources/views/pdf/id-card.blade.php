<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <style>
        @page {
            margin: 0;
            size: 85.6mm 53.98mm;
            page-break-after: avoid;
            page-break-inside: avoid;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7pt;
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
        }
        body {
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        .card {
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
            position: relative;
            background: #FFFFFF;
        }

        /* ── Top colour band ─────────────────────────────────────────── */
        .header {
            background: {{ $badgeColor }};
            height: 8.5mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5mm;
        }
        .header-brand {
            display: flex;
            align-items: center;
            gap: 1.5mm;
        }
        .logo {
            width: 6mm;
            height: 6mm;
            border-radius: 50%;
            border: 0.4pt solid rgba(255,255,255,0.6);
            object-fit: cover;
        }
        .camp-name {
            color: #FFFFFF;
            font-size: 6pt;
            font-weight: bold;
            letter-spacing: 0.3pt;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .camp-sub {
            color: rgba(255,255,255,0.8);
            font-size: 4.5pt;
            letter-spacing: 0.2pt;
        }
        .camp-year {
            color: rgba(255,255,255,0.85);
            font-size: 6pt;
            font-weight: bold;
        }

        /* ── Body ──────────────────────────────────────────────────────── */
        .body {
            display: flex;
            height: 37mm;
            padding: 2mm 2mm 1.5mm 2mm;
            gap: 2mm;
        }

        /* Left: photo */
        .photo-col {
            width: 20mm;
            flex-shrink: 0;
        }
        .photo-box {
            width: 20mm;
            height: 24mm;
            border: 0.5pt solid #D0D0D0;
            border-radius: 1.5mm;
            overflow: hidden;
            background: #F5F5F5;
        }
        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
        }
        .no-photo {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 5pt;
            color: #AAAAAA;
            text-align: center;
        }

        /* Middle: details */
        .info-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }
        .camper-name {
            font-size: 9pt;
            font-weight: bold;
            color: #0B2D6B;
            line-height: 1.15;
            word-wrap: break-word;
            max-width: 35mm;
        }
        .code {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 5.5pt;
            color: #666666;
            letter-spacing: 0.4pt;
            margin-top: 0.8mm;
        }
        .badge {
            display: inline-block;
            background: {{ $badgeColor }};
            color: #FFFFFF;
            font-size: 5pt;
            font-weight: bold;
            padding: 0.5mm 1.8mm;
            border-radius: 2pt;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            margin-top: 1.2mm;
        }
        .detail-section { margin-top: 2mm; }
        .detail-row { margin-bottom: 1mm; }
        .detail-lbl {
            font-size: 4.5pt;
            color: #999999;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
            line-height: 1;
        }
        .detail-val {
            font-size: 6pt;
            font-weight: bold;
            color: #1A1A1A;
            line-height: 1.2;
            word-wrap: break-word;
        }

        /* Right: QR code */
        .qr-col {
            width: 17mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding-top: 1mm;
        }
        .qr-box {
            width: 17mm;
            height: 17mm;
            overflow: hidden;
        }
        .qr-box svg {
            width: 17mm !important;
            height: 17mm !important;
            display: block;
        }
        .qr-box img {
            width: 17mm;
            height: 17mm;
            display: block;
        }
        .qr-lbl {
            font-size: 4pt;
            color: #AAAAAA;
            text-align: center;
            margin-top: 0.8mm;
            letter-spacing: 0.2pt;
        }

        /* ── Footer band ─────────────────────────────────────────────── */
        .footer {
            height: 8.5mm;
            background: {{ $badgeColor }}1A;
            border-top: 0.5pt solid {{ $badgeColor }}55;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2.5mm;
        }
        .footer-txt {
            font-size: 4.5pt;
            color: #777777;
            letter-spacing: 0.2pt;
        }
    </style>
</head>
<body>
<div class="card">

    {{-- Header --}}
    <div class="header">
        <div class="header-brand">
            @php $logoPath = public_path('images/logo.svg'); @endphp
            @if(file_exists($logoPath))
                <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                     class="logo" alt="Logo"/>
            @endif
            <div>
                <div class="camp-name">{{ $campName }}</div>
                <div class="camp-sub">SDA Church &mdash; Ogun Conference</div>
            </div>
        </div>
        <div class="camp-year">{{ $campYear }}</div>
    </div>

    {{-- Body --}}
    <div class="body">

        {{-- Photo --}}
        <div class="photo-col">
            <div class="photo-box">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" alt="Photo"/>
                @else
                    <div class="no-photo">No Photo</div>
                @endif
            </div>
        </div>

        {{-- Info --}}
        <div class="info-col">
            <div>
                <div class="camper-name">{{ $camper->full_name }}</div>
                <div class="code">{{ $camper->camper_number }}</div>
                <div class="badge">{{ $camper->category->label() }}</div>
            </div>
            <div class="detail-section">
                <div class="detail-row">
                    <div class="detail-lbl">Church</div>
                    <div class="detail-val">{{ $camper->church?->name ?? '—' }}</div>
                </div>
                <div class="detail-row">
                    <div class="detail-lbl">District</div>
                    <div class="detail-val">{{ $camper->church?->district?->name ?? '—' }}</div>
                </div>
                @if($camper->club_rank)
                    <div class="detail-row">
                        <div class="detail-lbl">Rank</div>
                        <div class="detail-val">{{ $camper->club_rank }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- QR code --}}
        <div class="qr-col">
            <div class="qr-box">
                {!! $qrCode !!}
            </div>
            <div class="qr-lbl">Scan to verify</div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="footer">
        <span class="footer-txt">Seventh-day Adventist Church</span>
        <span class="footer-txt">Valid {{ $campYear }}</span>
    </div>

</div>
</body>
</html>
