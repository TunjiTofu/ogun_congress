<div class="id-card">

    <div class="top-band" style="background:{{ $badgeColor }}">
        <table><tr>
                <td>
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" class="band-logo" alt="Logo"/>
                    @endif
                    <span class="band-name">{{ $campName ?? 'Ogun Conference Youth Congress.' }}<br><span class="band-sub">SDA · Ogun Conference</span></span>
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
        <tr><td class="dlbl">Church</td><td class="dval">{{ $camper->church?->name ?? '—' }}</td></tr>
        <tr><td class="dlbl">District</td><td class="dval">{{ $camper->church?->district?->name ?? '—' }}</td></tr>
        <tr><td class="dlbl">Gender</td><td class="dval" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</td></tr>
        <tr><td class="dlbl">Registered</td><td class="dval">{{ $camper->created_at->format('d M Y') }}</td></tr>
    </table>

    <table class="qr-table"><tr>
            <td class="scan-col">
                <div class="scan-txt">
                    <strong style="color:{{ $badgeColor }}">Scan to verify</strong><br/>
                    Valid {{ $campYear ?? now()->year }}<br/>
                    SDA Ogun Conference
                </div>
            </td>
            <td class="qr-img-cell">
                @if($qrCode)
                    <img src="{{ $qrCode }}" alt="QR"/>
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
