<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8"/>
    <style>
        @page { margin: 8mm; size: A4 portrait; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, Arial, sans-serif; background: #fff; }

        /* ── Grid: 2 cards per row, 3 rows per page = 6 cards per page ── */
        .page { width: 100%; }
        .page-break { page-break-after: always; }
        .row { display: table; width: 100%; margin-bottom: 3mm; }
        .col { display: table-cell; width: 50%; padding: 0 2.5mm; vertical-align: top; }

        /* ── Card ── */
        .id-card {
            width: 55mm;
            border: 0.5mm solid #c8ccd4;
            border-radius: 3mm;
            overflow: hidden;
            background: #fff;
            page-break-inside: avoid;
            margin: 0 auto;
        }

        /* Header */
        .top-band { width: 100%; overflow: hidden; }
        .top-band table { width: 100%; border-collapse: collapse; }
        .top-band td { padding: 2mm 2.5mm; vertical-align: middle; }
        .top-band td.right { text-align: right; }
        .band-logo { width: 8mm; height: 8mm; border-radius: 50%; border: 0.4mm solid rgba(255,255,255,0.4); vertical-align: middle; }
        .band-name { color: #fff; font-size: 5pt; font-weight: bold; line-height: 1.3; display: inline-block; vertical-align: middle; padding-left: 1.5mm; }
        .band-sub { color: rgba(255,255,255,0.7); font-size: 3.5pt; }
        .band-year { color: #FFD700; font-size: 7pt; font-weight: bold; }
        .stripe { padding: 0.8mm 2.5mm; font-size: 4pt; font-style: italic; color: #fff; text-align: center; background: #C9A94D; }

        /* Body */
        .body-table { width: 49mm; margin: 2mm 3mm 0; border-collapse: collapse; }
        .photo-cell { width: 19mm; vertical-align: top; padding-right: 2mm; }
        .photo-box { width: 19mm; height: 24mm; border: 0.4pt solid #D1D5DB; overflow: hidden; background: #F9FAFB; }
        .photo-box img { width: 19mm; height: 24mm; display: block; }
        .no-photo { width: 19mm; height: 24mm; font-size: 5pt; color: #9CA3AF; text-align: center; line-height: 24mm; }
        .info-cell { vertical-align: top; }
        .camper-name { font-size: 7pt; font-weight: bold; line-height: 1.2; word-break: break-word; margin-bottom: 0.8mm; }
        .camper-code { font-family: DejaVu Sans Mono; font-size: 4.5pt; color: #6B7280; margin-bottom: 1.5mm; }
        .dept-line { font-size: 4.5pt; font-weight: bold; margin-bottom: 1mm; line-height: 1.4; }
        .dept-badge { color: #fff; padding: 0.3mm 1.2mm; border-radius: 0.5mm; display: inline; font-size: 4.5pt; }
        .rank-sep { color: #6B7280; font-weight: normal; }
        .role-line { font-weight:900; text-transform:uppercase; letter-spacing:0.8pt; margin-top:1.5mm; display:inline-block; padding:0.7mm 1.8mm; border-radius:1mm; color:#fff; font-size:5.5pt; word-break:break-word; }

        /* Details */
        .details-table { width: 49mm; margin: 1.5mm 3mm 0; border-collapse: collapse; border-top: 0.3pt solid #E5E7EB; }
        .details-table td { padding: 0.5mm 0; vertical-align: top; }
        .dlbl { width: 14mm; font-size: 3.8pt; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.2pt; }
        .dval { font-size: 5pt; font-weight: bold; color: #111827; word-break: break-word; }

        /* QR */
        .qr-table { width: 49mm; margin: 1mm 3mm 0; border-collapse: collapse; border-top: 0.3pt solid #E5E7EB; }
        .qr-table td { padding-top: 1mm; vertical-align: bottom; }
        .scan-txt { font-size: 3.8pt; color: #9CA3AF; line-height: 1.6; }
        .qr-img-cell { width: 15mm; text-align: right; }
        .qr-img-cell img { width: 14mm; height: 14mm; display: block; margin-left: auto; }

        /* Footer band on card */
        .footer-band { width: 100%; margin-top: 1mm; }
        .footer-band table { width: 100%; border-collapse: collapse; }
        .footer-band td { padding: 1mm 2.5mm; font-size: 3.8pt; color: #9CA3AF; vertical-align: middle; }
        .footer-band td.right { text-align: right; }

        /* Summary line */
        .summary { text-align: center; font-size: 7.5pt; color: #94A3B8; padding-top: 3mm; border-top: 0.3mm solid #E5E7EB; }
    </style>
</head>
<body>

@foreach($pages as $pageIndex => $chunk)
    <div class="page {{ !$loop->last ? 'page-break' : '' }}">

        {{-- 3 rows of 2 cards = 6 per page --}}
        @foreach($chunk->chunk(2) as $rowIndex => $row)
            <div class="row">
                @foreach($row as $camper)
                    <div class="col">
                        @include('pdf.partials.id-card-item', [
                            'camper'       => $camper,
                            'logoBase64'   => $logoBase64,
                            'badgeColor'   => $camper->badge_color_computed,
                            'officialRole' => $camper->official_role_label,
                            'campName'     => $campName,
                            'campYear'     => $campYear,
                            'photoBase64'  => $camper->photo_base64,
                            'qrCode'       => $camper->qr_base64,
                        ])
                    </div>
                @endforeach
                @if($row->count() < 2)<div class="col"></div>@endif
            </div>
        @endforeach

    </div>
@endforeach

<div class="summary">
    {{ $totalCards }} ID cards &bull; Ogun Conference Youth Congress {{ $campYear }} &bull; Printed: {{ now()->format('d M Y') }}
</div>

</body>
</html>
