<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 7pt;
            color: #1A1A1A;
            width: 85.6mm;
            height: 53.98mm;
            overflow: hidden;
        }

        /* Top colour band */
        .header-band {
            background-color: {{ $badgeColor }};
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3mm;
        }

        .camp-name {
            color: #FFFFFF;
            font-size: 7pt;
            font-weight: bold;
            letter-spacing: 0.3pt;
            text-transform: uppercase;
        }

        .camp-year {
            color: rgba(255,255,255,0.85);
            font-size: 6pt;
        }

        /* Main card body */
        .card-body {
            display: flex;
            height: 40mm;
            padding: 2mm;
            gap: 2mm;
        }

        /* Left: photo */
        .photo-col {
            width: 20mm;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1mm;
        }

        .photo-wrap {
            width: 18mm;
            height: 21mm;
            border: 0.5pt solid #CCCCCC;
            border-radius: 1mm;
            overflow: hidden;
            background: #F0F0F0;
        }

        .photo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #AAAAAA;
            font-size: 6pt;
        }

        /* Right: camper details */
        .details-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .camper-name {
            font-size: 9pt;
            font-weight: bold;
            color: #1B3A6B;
            line-height: 1.2;
            word-break: break-word;
        }

        .camper-number {
            font-family: DejaVu Sans Mono, Courier New, monospace;
            font-size: 7pt;
            color: #555555;
            letter-spacing: 0.5pt;
            margin-top: 0.5mm;
        }

        .detail-row {
            display: flex;
            flex-direction: column;
            margin-top: 1mm;
        }

        .detail-label {
            font-size: 5pt;
            color: #888888;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }

        .detail-value {
            font-size: 6.5pt;
            color: #1A1A1A;
            font-weight: bold;
        }

        /* Category badge */
        .category-badge {
            display: inline-block;
            background-color: {{ $badgeColor }};
            color: #FFFFFF;
            font-size: 5.5pt;
            font-weight: bold;
            padding: 0.5mm 2mm;
            border-radius: 5pt;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
            margin-top: 1mm;
        }

        /* QR code + footer */
        .footer-band {
            background-color: #F8F8F8;
            border-top: 0.5pt solid #EEEEEE;
            height: 6mm;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2mm;
        }

        .qr-section {
            width: 12mm;
        }

        .qr-section svg {
            width: 12mm;
            height: 12mm;
        }

        .footer-text {
            font-size: 5pt;
            color: #AAAAAA;
            text-align: right;
        }

        /* Inline QR in body */
        .qr-inline {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: auto;
        }

        .qr-inline svg {
            width: 14mm;
            height: 14mm;
        }

        .qr-label {
            font-size: 4.5pt;
            color: #AAAAAA;
            margin-top: 0.5mm;
        }
    </style>
</head>
<body>

    {{-- Top band --}}
    <div class="header-band">
        <span class="camp-name">{{ $campName }}</span>
        <span class="camp-year">{{ $campYear }}</span>
    </div>

    {{-- Card body --}}
    <div class="card-body">

        {{-- Photo --}}
        <div class="photo-col">
            <div class="photo-wrap">
                @if($photoBase64)
                    <img src="{{ $photoBase64 }}" alt="Camper photo" />
                @else
                    <div class="photo-placeholder">No Photo</div>
                @endif
            </div>

            {{-- QR code beneath photo --}}
            <div class="qr-inline">
                {!! $qrCode !!}
                <span class="qr-label">Scan to verify</span>
            </div>
        </div>

        {{-- Details --}}
        <div class="details-col">
            <div>
                <div class="camper-name">{{ $camper->full_name }}</div>
                <div class="camper-number">{{ $camper->camper_number }}</div>
                <div class="category-badge">{{ $camper->category->label() }}</div>
            </div>

            <div>
                <div class="detail-row">
                    <span class="detail-label">Church</span>
                    <span class="detail-value">{{ $camper->church?->name ?? '—' }}</span>
                </div>

                <div class="detail-row">
                    <span class="detail-label">District</span>
                    <span class="detail-value">{{ $camper->church?->district?->name ?? '—' }}</span>
                </div>

                @if($camper->ministry)
                <div class="detail-row">
                    <span class="detail-label">Ministry</span>
                    <span class="detail-value">{{ $camper->ministry }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer-band">
        <span class="footer-text">SDA Church — Ogun Conference</span>
        <span class="footer-text">Valid for {{ $campYear }} only</span>
    </div>

</body>
</html>
