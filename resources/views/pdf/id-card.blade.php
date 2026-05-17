<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 0; size: 54mm 85.6mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 54mm;
            height: 85.6mm;
            overflow: hidden;
            font-family: DejaVu Sans, Arial, sans-serif;
        }

        .card {
            width: 54mm;
            height: 85.6mm;
            overflow: hidden;
            background: #fff;
        }

        /* ── Header: 9mm ── */
        .top-band { width: 100%; overflow: hidden; }
        .top-band table { width: 100%; border-collapse: collapse; }
        .top-band td { padding: 1.5mm 2.5mm; vertical-align: middle; }
        .top-band td.right { text-align: right; }
        .band-logo { width: 7mm; height: 7mm; border-radius: 50%; border: 0.4mm solid rgba(255,255,255,0.4); vertical-align: middle; }
        .band-name { color: #fff; font-size: 5pt; font-weight: bold; line-height: 1.2; display: inline-block; vertical-align: middle; padding-left: 1.5mm; }
        .band-sub { color: rgba(255,255,255,0.7); font-size: 3pt; }
        .band-year { color: #FFD700; font-size: 7pt; font-weight: bold; }

        /* ── Stripe: 4mm ── */
        .stripe { background: #C9A94D; padding: 0.7mm 2.5mm; font-size: 4pt; font-style: italic; color: #fff; text-align: center; }

        /* ── Body: 24mm ── */
        .body-table { width: 50mm; margin: 1.5mm 2mm 0; border-collapse: collapse; }
        .photo-cell { width: 18mm; vertical-align: top; padding-right: 2mm; }
        .photo-box { width: 18mm; height: 21mm; border: 0.4pt solid #D1D5DB; overflow: hidden; background: #F9FAFB; }
        .photo-box img { width: 18mm; height: 21mm; object-fit: cover; object-position: top center; display: block; }
        .no-photo { width: 18mm; height: 21mm; font-size: 5pt; color: #9CA3AF; text-align: center; line-height: 21mm; }
        .info-cell { vertical-align: top; }
        .camper-name { font-size: 7pt; font-weight: bold; line-height: 1.2; word-break: break-word; margin-bottom: 0.6mm; }
        .camper-code { font-family: DejaVu Sans Mono; font-size: 4.5pt; color: #6B7280; letter-spacing: 0.2pt; margin-bottom: 1mm; }
        .dept-badge { color: #fff; font-size: 4.5pt; font-weight: bold; padding: 0.3mm 1.2mm; display: inline-block; border-radius: 0.5mm; }
        .rank-txt { font-size: 3.8pt; color: #6B7280; margin-top: 0.4mm; }

        /* ── Details: 13mm ── */
        .details-table { width: 50mm; margin: 1.5mm 2mm 0; border-collapse: collapse; border-top: 0.3pt solid #E5E7EB; }
        .details-table td { padding: 0.5mm 0; vertical-align: top; }
        .dlbl { width: 14mm; font-size: 3.8pt; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.2pt; }
        .dval { font-size: 5pt; font-weight: bold; color: #111827; line-height: 1.2; word-break: break-word; }

        /* ── QR: 16mm ── */
        .qr-table { width: 50mm; margin: 1mm 2mm 0; border-collapse: collapse; border-top: 0.3pt solid #E5E7EB; }
        .qr-table td { padding-top: 0.8mm; vertical-align: middle; }
        .scan-txt { font-size: 3.8pt; color: #9CA3AF; line-height: 1.5; }
        .qr-cell { width: 15mm; text-align: right; }
        .qr-cell img { width: 14mm; height: 14mm; display: block; margin-left: auto; }

        /* ── Footer: 5mm ── */
        .footer-band { width: 100%; margin-top: 1mm; }
        .footer-band table { width: 100%; border-collapse: collapse; }
        .footer-band td { padding: 0.8mm 2.5mm; font-size: 3.5pt; color: #9CA3AF; vertical-align: middle; }
        .footer-band td.right { text-align: right; }
    </style>
</head>
<body>
<div class="card">

    <div class="top-band" style="background:{{ $badgeColor }}">
        <table><tr>
                <td>
                    @if(isset($logoBase64) && $logoBase64)
                        <img src="{{ $logoBase64 }}" class="band-logo" alt="Logo"/>
                    @endif
                    <span class="band-name">{{ $campName ?? 'Ogun Youth Camp' }}<br><span class="band-sub">SDA · Ogun Conference</span></span>
                </td>
                <td class="right"><span class="band-year">{{ $campYear ?? now()->year }}</span></td>
            </tr></table>
    </div>
    <div class="stripe">From The Word To The World</div>

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
                <div class="camper-name" style="color:{{ $badgeColor }}">{{ $camper->full_name }}</div>
                <div class="camper-code">{{ $camper->camper_number }}</div>
                <div class="dept-badge" style="background:{{ $badgeColor }}">{{ $camper->category?->label() }}</div>
                @if($camper->club_rank)
                    <div class="rank-txt">{{ $camper->club_rank }}</div>
                @endif
            </td>
        </tr></table>

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

    <table class="qr-table"><tr>
            <td>
                <div class="scan-txt">
                    <strong style="color:{{ $badgeColor }}">Scan to verify</strong><br/>
                    Valid {{ $campYear ?? now()->year }}<br/>
                    SDA Ogun Conference
                </div>
            </td>
            <td class="qr-cell">
                @if($qrCode)
                    <img src="{{ $qrCode }}" alt="QR Code"/>
                @endif
            </td>
        </tr></table>

    <div class="footer-band" style="background:{{ $badgeColor }}15;border-top:0.4pt solid {{ $badgeColor }}44">
        <table><tr>
                <td>Seventh-day Adventist Church</td>
                <td class="right" style="color:{{ $badgeColor }}">&#10003; Official ID</td>
            </tr></table>
    </div>

</div>
</body>
</html>
