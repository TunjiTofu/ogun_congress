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
            font-size: 6.5pt;
            color: #111827;
        }
        .card {
            width: 54mm;
            height: 86mm;
            overflow: hidden;
            position: relative;
            background: #FFFFFF;
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        /* ── Top colour band ──────────────────── */
        .top-band {
            display: table;
            width: 54mm;
            height: 10mm;
            background: {{ $badgeColor }};
            padding: 0 2mm;
        }
        .top-band-row { display: table-row; }
        .top-band-left, .top-band-right {
            display: table-cell;
            vertical-align: middle;
        }
        .top-band-right { text-align: right; }

        .band-logo {
            width: 7mm; height: 7mm; border-radius: 50%;
            vertical-align: middle; display: inline-block;
        }
        .band-title {
            display: inline-block; vertical-align: middle;
            margin-left: 1mm; color: #fff;
        }
        .band-name { font-size: 5pt; font-weight: bold; line-height: 1.3; }
        .band-sub  { font-size: 3.8pt; color: rgba(255,255,255,0.75); }
        .band-year { font-size: 6pt; font-weight: bold; color: #fff; }

        /* ── Photo block ──────────────────────── */
        .photo-block {
            padding: 2mm 2mm 1mm;
            display: table;
            width: 100%;
        }
        .photo-col { display: table-cell; width: 18mm; vertical-align: top; }
        .info-col  { display: table-cell; vertical-align: top; padding-left: 1.5mm; }

        .photo-box {
            width: 18mm; height: 22mm;
            border: 0.4pt solid #D1D5DB;
            overflow: hidden; background: #F9FAFB;
        }
        .photo-box img { width: 18mm; height: 22mm; display: block; object-fit: cover; object-position: top; }
        .no-photo { width: 18mm; height: 22mm; display: table-cell; vertical-align: middle; text-align: center; font-size: 5pt; color: #9CA3AF; }

        /* ── Name & badge ─────────────────────── */
        .camper-name {
            font-size: 7pt; font-weight: bold; color: {{ $badgeColor }};
            line-height: 1.2; word-wrap: break-word; max-width: 30mm;
        }
        .camper-code {
            font-family: DejaVu Sans Mono, monospace;
            font-size: 4.8pt; color: #6B7280; letter-spacing: 0.3pt; margin-top: 0.5mm;
        }
        .dept-badge {
            display: inline-block;
            background: {{ $badgeColor }};
            color: #fff; font-size: 4.5pt; font-weight: bold;
            padding: 0.3mm 1.2mm; margin-top: 0.8mm;
        }

        /* ── Details table ────────────────────── */
        .details {
            width: calc(54mm - 4mm);
            margin: 0 2mm;
            border-top: 0.3pt solid #E5E7EB;
            padding-top: 1.2mm;
        }
        .detail-row { display: table; width: 100%; margin-bottom: 0.9mm; }
        .detail-lbl {
            display: table-cell; width: 14mm;
            font-size: 4.5pt; color: #9CA3AF;
            text-transform: uppercase; letter-spacing: 0.2pt;
            vertical-align: top; padding-top: 0.1mm;
        }
        .detail-val {
            display: table-cell;
            font-size: 5.8pt; font-weight: bold; color: #111827;
            line-height: 1.25; word-wrap: break-word; max-width: 32mm;
        }

        /* ── QR block ─────────────────────────── */
        .qr-block {
            display: table; width: calc(54mm - 4mm);
            margin: 1.5mm 2mm 0;
            border-top: 0.3pt solid #E5E7EB;
            padding-top: 1.5mm;
        }
        .qr-left  { display: table-cell; vertical-align: middle; }
        .qr-right { display: table-cell; width: 16mm; vertical-align: middle; text-align: right; }
        .qr-img   { width: 15mm; height: 15mm; display: block; }
        .qr-text  { font-size: 4pt; color: #9CA3AF; line-height: 1.4; }
        .qr-text strong { color: {{ $badgeColor }}; font-size: 4.5pt; }

        /* ── Footer band ──────────────────────── */
        .bottom-band {
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 6mm;
            background: {{ $badgeColor }}18;
            border-top: 0.4pt solid {{ $badgeColor }}44;
            display: table; width: 54mm; padding: 0 2mm;
        }
        .bottom-band-row { display: table-row; }
        .bottom-lft, .bottom-rgt {
            display: table-cell; vertical-align: middle;
            font-size: 3.8pt; color: #9CA3AF;
        }
        .bottom-rgt { text-align: right; }
    </style>
</head>
<body>
<div class="card">

    {{-- Top band --}}
    <div class="top-band">
        <div class="top-band-row">
            <div class="top-band-left">
                @php $logoPath = public_path('images/logo.svg'); @endphp
                @if(file_exists($logoPath))
                    <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                         class="band-logo" alt="Logo"/>
                @endif
                <div class="band-title">
                    <div class="band-name">{{ $campName }}</div>
                    <div class="band-sub">SDA · Ogun Conference</div>
                </div>
            </div>
            <div class="top-band-right">
                <div class="band-year">{{ $campYear }}</div>
            </div>
        </div>
    </div>

    {{-- Photo + Name --}}
    <div class="photo-block">
        <div class="photo-col">
            <div class="photo-box">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" alt="Photo"/>
                @else
                    <div class="no-photo">No<br/>Photo</div>
                @endif
            </div>
        </div>
        <div class="info-col">
            <div class="camper-name">{{ $camper->full_name }}</div>
            <div class="camper-code">{{ $camper->camper_number }}</div>
            <div class="dept-badge">{{ $camper->category->label() }}@if($camper->club_rank) · {{ $camper->club_rank }}@endif</div>
        </div>
    </div>

    {{-- Details --}}
    <div class="details">
        <div class="detail-row">
            <div class="detail-lbl">Church</div>
            <div class="detail-val">{{ $camper->church?->name ?? '—' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-lbl">District</div>
            <div class="detail-val">{{ $camper->church?->district?->name ?? '—' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-lbl">Gender</div>
            <div class="detail-val" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</div>
        </div>
        <div class="detail-row">
            <div class="detail-lbl">Registered</div>
            <div class="detail-val">{{ $camper->created_at->format('d M Y') }}</div>
        </div>
    </div>

    {{-- QR code --}}
    <div class="qr-block">
        <div class="qr-left">
            <div class="qr-text">
                <strong>Scan to verify</strong><br/>
                Valid for {{ $campYear }}.<br/>
                SDA Ogun Conference
            </div>
        </div>
        <div class="qr-right">
            <img src="{{ $qrCode }}" class="qr-img" alt="QR"/>
        </div>
    </div>

    {{-- Footer --}}
    <div class="bottom-band">
        <div class="bottom-band-row">
            <div class="bottom-lft">Seventh-day Adventist Church</div>
            <div class="bottom-rgt">&#10003; Official ID</div>
        </div>
    </div>

</div>
</body>
</html>
