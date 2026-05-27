<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <style>
        @page {
            margin: 0;
            size: 54mm 86mm;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 54mm;
            height: 86mm;
            overflow: hidden;
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7pt;
            color: #111827;
        }

        /* ── Outer wrapper ──────────────────────────────── */
        .card {
            width: 54mm;
            height: 86mm;
            overflow: hidden;
            background: #FFFFFF;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* ── Top band ───────────────────────────────────── */
        .top-band {
            width: 54mm;
            height: 10mm;
            background: {{ $badgeColor }};
            overflow: hidden;
        }
        .top-band table {
            width: 100%;
            height: 10mm;
            border-collapse: collapse;
        }
        .top-band td {
            padding: 0 2mm;
            vertical-align: middle;
        }
        .top-band td.right {
            text-align: right;
        }
        .band-logo {
            width: 6mm;
            height: 6mm;
            border-radius: 3mm;
            vertical-align: middle;
        }
        .band-name {
            color: #FFFFFF;
            font-size: 5pt;
            font-weight: bold;
            line-height: 1.3;
            vertical-align: middle;
            padding-left: 1mm;
        }
        .band-sub {
            color: rgba(255,255,255,0.75);
            font-size: 3.5pt;
        }
        .band-year {
            color: #FFFFFF;
            font-size: 6pt;
            font-weight: bold;
        }

        /* ── Body ───────────────────────────────────────── */
        .body-table {
            width: 50mm;
            margin: 1.5mm 2mm 0;
            border-collapse: collapse;
        }
        .photo-cell {
            width: 18mm;
            vertical-align: top;
            padding-right: 1.5mm;
        }
        .photo-box {
            width: 18mm;
            height: 22mm;
            border: 0.4pt solid #D1D5DB;
            overflow: hidden;
            background: #F9FAFB;
        }
        .photo-box img {
            width: 18mm;
            height: 22mm;
            display: block;
        }
        .no-photo {
            width: 18mm;
            height: 22mm;
            font-size: 5pt;
            color: #9CA3AF;
            text-align: center;
            line-height: 22mm;
        }
        .info-cell {
            vertical-align: top;
        }
        .camper-name {
            font-size: 7pt;
            font-weight: bold;
            color: {{ $badgeColor }};
            line-height: 1.2;
            word-wrap: break-word;
        }
        .camper-code {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 4.8pt;
            color: #6B7280;
            letter-spacing: 0.3pt;
            margin-top: 0.5mm;
        }
        .dept-badge {
            background: {{ $badgeColor }};
            color: #fff;
            font-size: 4.5pt;
            font-weight: bold;
            padding: 0.3mm 1.2mm;
            margin-top: 0.8mm;
            display: inline-block;
        }

        /* ── Details ────────────────────────────────────── */
        .details-table {
            width: 50mm;
            margin: 1.5mm 2mm 0;
            border-collapse: collapse;
            border-top: 0.3pt solid #E5E7EB;
        }
        .details-table td {
            padding: 0.5mm 0;
            vertical-align: top;
        }
        .dlbl {
            width: 14mm;
            font-size: 4.2pt;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.2pt;
        }
        .dval {
            font-size: 5.5pt;
            font-weight: bold;
            color: #111827;
            line-height: 1.25;
            word-wrap: break-word;
        }

        /* ── QR + scan text ─────────────────────────────── */
        .qr-table {
            width: 50mm;
            margin: 1mm 2mm 0;
            border-collapse: collapse;
            border-top: 0.3pt solid #E5E7EB;
        }
        .qr-table td {
            padding-top: 1mm;
            vertical-align: middle;
        }
        .qr-cell {
            width: 16mm;
            text-align: right;
        }
        .qr-cell svg {
            width: 15mm !important;
            height: 15mm !important;
            display: block;
            margin-left: auto;
        }
        .scan-text {
            font-size: 4pt;
            color: #9CA3AF;
            line-height: 1.5;
        }
        .scan-text strong {
            color: {{ $badgeColor }};
            font-size: 4.5pt;
        }

        /* ── Footer ─────────────────────────────────────── */
        .footer-band {
            width: 54mm;
            height: 6mm;
            background: {{ $badgeColor }}18;
            border-top: 0.4pt solid {{ $badgeColor }}44;
            margin-top: 1mm;
            overflow: hidden;
        }
        .footer-band table {
            width: 100%;
            height: 6mm;
            border-collapse: collapse;
        }
        .footer-band td {
            padding: 0 2mm;
            font-size: 3.8pt;
            color: #9CA3AF;
            vertical-align: middle;
        }
        .footer-band td.right { text-align: right; }
    </style>
</head>
<body>
<div class="card">

    {{-- Top band --}}
    <div class="top-band">
        <table><tr>
                <td>
                    @php $logoPath = public_path('images/logo.svg'); @endphp
                    @if(file_exists($logoPath))
                        <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                             class="band-logo" alt="Logo"/>
                    @endif
                    <span class="band-name">{{ $campName }}<br/><span class="band-sub">SDA · Ogun Conference</span></span>
                </td>
                <td class="right"><span class="band-year">{{ $campYear }}</span></td>
            </tr></table>
    </div>

    {{-- Photo + Name --}}
    <table class="body-table"><tr>
            <td class="photo-cell">
                <div class="photo-box">
                    @if($photoBase64)
                        <img src="{{ $photoBase64 }}" alt="Photo"/>
                    @else
                        <div class="no-photo">No Photo</div>
                    @endif
                </div>
            </td>
            <td class="info-cell">
                <div class="camper-name">{{ $camper->full_name }}</div>
                <div class="camper-code">{{ $camper->camper_number }}</div>
                <div class="dept-badge">{{ $camper->category->label() }}@if($camper->club_rank) · {{ $camper->club_rank }}@endif</div>
            </td>
        </tr></table>

    {{-- Details rows --}}
    <table class="details-table">
        <tr>
            <td class="dlbl">Church</td>
            <td class="dval">{{ $camper->church?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="dlbl">District</td>
            <td class="dval">{{ $camper->church?->district?->name ?? '—' }}</td>
        </tr>
        <tr>
            <td class="dlbl">Gender</td>
            <td class="dval" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</td>
        </tr>
        <tr>
            <td class="dlbl">Registered</td>
            <td class="dval">{{ $camper->created_at->format('d M Y') }}</td>
        </tr>
    </table>

    {{-- QR + scan label --}}
    <table class="qr-table"><tr>
            <td>
                <div class="scan-text">
                    <strong>Scan to verify</strong><br/>
                    Valid {{ $campYear }}<br/>
                    SDA Ogun Conference
                </div>
            </td>
            <td class="qr-cell">
                @if($qrCode)
                    <img src="{{ $qrCode }}" style="width:15mm;height:15mm;display:block;margin-left:auto" alt="QR"/>
                @endif
            </td>
        </tr></table>

    {{-- Footer --}}
    <div class="footer-band">
        <table><tr>
                <td>Seventh-day Adventist Church</td>
                <td class="right">&#10003; Official ID</td>
            </tr></table>
    </div>

</div>
</body>
</html>
