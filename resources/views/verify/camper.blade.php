<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Camper Verification &mdash; {{ $camper->camper_number }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--navy:#0B2D6B;--gold:#C9A94D;--green:#064E3B}
        body{font-family:'Lato',sans-serif;background:linear-gradient(135deg,#0B2D6B 0%,#1E5FAD 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
        .card{background:#fff;border-radius:24px;width:100%;max-width:400px;overflow:hidden;
            box-shadow:0 24px 64px rgba(0,0,0,0.35)}
        .card-header{padding:1.5rem;text-align:center;position:relative;overflow:hidden}
        .card-header::before{content:'';position:absolute;inset:0;opacity:0.12;
            background:url("{{ asset('images/congress_logo.png') }}") center/contain no-repeat}
        .badge-strip{height:6px;background:linear-gradient(90deg,var(--navy),var(--gold),var(--navy))}
        .verified-icon{width:56px;height:56px;background:#D1FAE5;border:3px solid #6EE7B7;
            border-radius:50%;display:flex;align-items:center;justify-content:center;
            margin:0 auto 0.75rem;font-size:1.6rem}
        .card-title{font-family:'Cinzel',serif;font-size:0.85rem;color:var(--navy);
            letter-spacing:0.08em;text-transform:uppercase;margin-bottom:0.2rem}
        .card-sub{font-size:0.72rem;color:#9CA3AF}
        .photo-wrap{padding:1.5rem 1.5rem 0}
        .photo{width:100%;height:200px;object-fit:cover;object-position:top;border-radius:16px;
            border:3px solid rgba(11,45,107,0.1)}
        .photo-placeholder{width:100%;height:200px;border-radius:16px;background:#F0F4FA;
            display:flex;align-items:center;justify-content:center;
            font-size:3rem;color:#CBD5E1;border:3px solid rgba(11,45,107,0.1)}
        .info{padding:1.25rem 1.5rem 1.5rem}
        .name{font-family:'Cinzel',serif;font-size:1.4rem;font-weight:700;color:var(--navy);
            text-align:center;margin-bottom:0.25rem}
        .code{font-family:monospace;font-size:0.82rem;color:#6366F1;font-weight:700;
            text-align:center;margin-bottom:1rem;letter-spacing:0.05em}
        .dept-badge{display:inline-block;font-size:0.72rem;font-weight:700;padding:0.3rem 0.9rem;
            border-radius:100px;border:1px solid var(--gold);color:var(--navy);
            background:rgba(201,169,77,0.12);margin:0 auto 1.25rem;
            display:block;text-align:center;width:fit-content;margin:0 auto 1.25rem}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1rem}
        .cell{background:#F8FAFC;border-radius:12px;padding:0.75rem 1rem;border:1px solid #E2E8F0}
        .cell-label{font-size:0.62rem;font-weight:700;color:#94A3B8;text-transform:uppercase;
            letter-spacing:0.1em;margin-bottom:0.2rem}
        .cell-value{font-size:0.82rem;font-weight:700;color:#1E293B;line-height:1.3}
        .status-verified{background:#D1FAE5;border:1px solid #6EE7B7;border-radius:12px;
            padding:0.75rem;text-align:center;font-size:0.78rem;font-weight:700;
            color:#065F46;display:flex;align-items:center;justify-content:center;gap:0.5rem}
        .footer{background:#F8FAFC;border-top:1px solid #E2E8F0;padding:1rem 1.5rem;
            text-align:center}
        .footer-logo{height:36px;display:inline-block;margin-bottom:0.4rem}
        .footer-text{font-size:0.68rem;color:#94A3B8}
    </style>
</head>
<body>
<div class="card">
    <div class="badge-strip"></div>
    <div class="card-header" style="background:linear-gradient(135deg,#0B2D6B,#1E5FAD);color:#fff;padding:1.25rem">
        <img src="{{ asset('images/congress_logo.png') }}"
             style="width:60px;height:60px;border-radius:50%;border:2px solid rgba(201,169,77,0.5);margin-bottom:0.6rem;display:block;margin-left:auto;margin-right:auto"/>
        <div style="font-family:'Cinzel',serif;font-size:0.75rem;color:rgba(201,169,77,0.9);letter-spacing:0.1em;text-transform:uppercase">
            Ogun Conference Youth Congress 2026
        </div>
        <div style="font-size:0.65rem;color:rgba(255,255,255,0.6);margin-top:0.2rem">Camper Identity Verification</div>
    </div>

    @if($camper->getFirstMediaUrl('photo','thumb'))
        <div class="photo-wrap">
            <img src="{{ $camper->getFirstMediaUrl('photo','thumb') }}" class="photo" alt="Photo"/>
        </div>
    @else
        <div class="photo-wrap">
            <div class="photo-placeholder">&#128100;</div>
        </div>
    @endif

    <div class="info">
        <div class="name">{{ $camper->full_name }}</div>
        <div class="code">{{ $camper->camper_number }}</div>
        <div class="dept-badge">{{ $camper->category->label() }}@if($camper->club_rank) &bull; {{ $camper->club_rank }}@endif</div>

        <div class="grid">
            <div class="cell">
                <div class="cell-label">Church</div>
                <div class="cell-value">{{ $camper->church?->name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">District</div>
                <div class="cell-value">{{ $camper->church?->district?->name ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Gender</div>
                <div class="cell-value" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</div>
            </div>
            <div class="cell">
                <div class="cell-label">Registered</div>
                <div class="cell-value">{{ $camper->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="status-verified">
            <span>&#10003;</span>
            Verified &mdash; {{ setting('camp_name','Ogun Youth Camp') }} {{ now()->year }}
        </div>
    </div>

    <div class="footer">
        <div class="footer-text">
            This camper has been officially registered for<br/>
            <strong style="color:#0B2D6B">{{ setting('camp_name','Ogun Youth Camp') }}</strong> &bull;
            {{ setting('camp_dates','Aug 16–22, 2026') }}
        </div>
    </div>
</div>
</body>
</html>
