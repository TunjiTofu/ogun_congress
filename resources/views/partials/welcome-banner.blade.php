@php
    $regOpen      = setting('registration_open', '1') === '1';
    $closesAt     = setting('registration_closes_at');
    $regClosed    = ! $regOpen || ($closesAt && now()->gt(\Illuminate\Support\Carbon::parse($closesAt)));
    $announcement = setting('announcement_banner');
@endphp

@if($regClosed || $announcement || session('contact_success') || $errors->any())
    <div id="siteBanner" style="position:relative;z-index:1002">

        @if($regClosed)
            <div style="background:#7F1D1D;color:#fff;padding:11px 24px;text-align:center;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:10px">
                <span style="font-size:15px">🔴</span>
                <span>Registration is currently <strong>closed</strong>. Contact your church coordinator for more information.</span>
            </div>
        @endif

        @if($announcement)
            <div style="background:#78350F;color:#fff;padding:11px 24px;text-align:center;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:10px">
                <span style="font-size:15px">📢</span>
                <span>{{ $announcement }}</span>
            </div>
        @endif

        @if(session('contact_success'))
            <div style="background:#14532D;color:#fff;padding:11px 24px;text-align:center;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:10px" id="successBanner">
                <span style="font-size:15px">✅</span>
                <span>{{ session('contact_success') }}</span>
                <button onclick="document.getElementById('successBanner').style.display='none'" style="background:none;border:none;color:rgba(255,255,255,0.7);cursor:pointer;margin-left:12px;font-size:16px">&times;</button>
            </div>
        @endif

        @if($errors->any() && ! session('portal_error'))
            <div style="background:#991B1B;color:#fff;padding:11px 24px;text-align:center;font-size:13px;font-weight:500;display:flex;align-items:center;justify-content:center;gap:10px">
                <span style="font-size:15px">⚠️</span>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

    </div>
@endif
