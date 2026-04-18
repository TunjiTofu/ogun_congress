<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Portal &mdash; {{ $camper->full_name }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--navy:#0B2D6B;--gold:#C9A94D;--gold2:#F5D060}
        body{font-family:'Lato',sans-serif;background:#F0F4FA;min-height:100vh}
        nav{background:var(--navy);padding:0.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:0.75rem}
        .nav-brand img{width:38px;height:38px;border-radius:50%;border:1px solid rgba(201,169,77,0.4)}
        .nav-title{font-family:'Cinzel',serif;font-size:0.78rem;color:var(--gold2)}
        .nav-sub{font-size:0.65rem;color:rgba(168,200,240,0.8)}
        .nav-right{display:flex;align-items:center;gap:1rem}
        .nav-camper{font-size:0.8rem;color:rgba(255,255,255,0.7)}
        .btn-logout{background:transparent;border:1px solid rgba(255,255,255,0.25);color:rgba(255,255,255,0.7);font-size:0.75rem;padding:0.35rem 0.9rem;border-radius:100px;cursor:pointer;transition:0.2s;text-decoration:none}
        .btn-logout:hover{border-color:var(--gold);color:var(--gold)}
        .container{max-width:900px;margin:0 auto;padding:2rem 1.5rem}
        /* Welcome banner */
        .welcome{background:linear-gradient(135deg,var(--navy) 0%,#1E5FAD 100%);border-radius:20px;padding:2rem;color:#fff;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.5rem}
        .welcome-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid rgba(201,169,77,0.5);flex-shrink:0;background:#1B3A6B}
        .welcome-name{font-family:'Cinzel',serif;font-size:1.4rem;font-weight:700;margin-bottom:0.25rem}
        .welcome-detail{font-size:0.82rem;color:rgba(255,255,255,0.7)}
        .badge{display:inline-block;background:rgba(201,169,77,0.2);border:1px solid rgba(201,169,77,0.4);color:var(--gold2);font-size:0.7rem;font-weight:700;padding:0.2rem 0.6rem;border-radius:100px;letter-spacing:0.08em}
        /* Section */
        h2{font-family:'Cinzel',serif;font-size:1rem;color:var(--navy);margin-bottom:1rem;letter-spacing:0.04em}
        .grid2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem}
        /* Doc card */
        .doc-card{background:#fff;border-radius:16px;padding:1.5rem;box-shadow:0 2px 12px rgba(11,45,107,0.07);border:1px solid rgba(11,45,107,0.07)}
        .doc-icon{font-size:2.2rem;margin-bottom:0.75rem}
        .doc-title{font-family:'Cinzel',serif;font-size:0.9rem;color:var(--navy);margin-bottom:0.25rem}
        .doc-desc{font-size:0.78rem;color:#888;margin-bottom:1rem;line-height:1.5}
        .btn-download{display:inline-block;background:var(--navy);color:#fff;font-size:0.8rem;font-weight:700;padding:0.6rem 1.2rem;border-radius:10px;text-decoration:none;transition:background 0.2s}
        .btn-download:hover{background:#1E5FAD}
        .btn-pending{display:inline-block;background:#F3F4F6;color:#9CA3AF;font-size:0.8rem;padding:0.6rem 1.2rem;border-radius:10px;cursor:default}
        /* Info table */
        .info-card{background:#fff;border-radius:16px;padding:1.5rem;box-shadow:0 2px 12px rgba(11,45,107,0.07);border:1px solid rgba(11,45,107,0.07);margin-bottom:1.5rem}
        .info-row{display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid #F3F4F6;font-size:0.85rem}
        .info-row:last-child{border:none}
        .info-label{color:#888}
        .info-value{font-weight:700;color:var(--navy)}
        .info-mono{font-family:monospace;font-size:0.95rem;letter-spacing:0.05em}
        /* Announcements */
        .announcement{background:#fff;border-radius:14px;padding:1.2rem 1.5rem;margin-bottom:0.75rem;border-left:4px solid var(--gold);box-shadow:0 2px 8px rgba(11,45,107,0.05)}
        .ann-title{font-weight:700;font-size:0.88rem;color:var(--navy);margin-bottom:0.3rem}
        .ann-body{font-size:0.82rem;color:#555;line-height:1.55}
        .ann-date{font-size:0.7rem;color:#aaa;margin-top:0.4rem}
        .empty{text-align:center;color:#aaa;font-style:italic;padding:2rem;font-size:0.85rem}
        @media(max-width:600px){.grid2{grid-template-columns:1fr}.welcome{flex-direction:column;text-align:center}}
    </style>
</head>
<body>

<nav>
    <div class="nav-brand">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo"/>
        <div>
            <div class="nav-title">Camper Portal</div>
            <div class="nav-sub">{{ setting('camp_name', 'Ogun Youth Camp') }}</div>
        </div>
    </div>
    <div class="nav-right">
        <span class="nav-camper">{{ $camper->camper_number }}</span>
        <form method="POST" action="{{ route('portal.logout') }}" style="display:inline">
            @csrf
            <button type="submit" class="btn-logout">Logout</button>
        </form>
    </div>
</nav>

<div class="container">

    {{-- Welcome banner --}}
    <div class="welcome">
        @if($camper->getFirstMediaUrl('photo', 'thumb'))
            <img src="{{ $camper->getFirstMediaUrl('photo', 'thumb') }}" alt="Photo" class="welcome-avatar"/>
        @else
            <div class="welcome-avatar" style="display:flex;align-items:center;justify-content:center;font-size:2rem;">&#128100;</div>
        @endif
        <div>
            <div class="welcome-name">{{ $camper->full_name }}</div>
            <div class="welcome-detail" style="margin-bottom:0.4rem">
                {{ $camper->church?->name }} &bull; {{ $camper->church?->district?->name }}
            </div>
            <span class="badge">{{ $camper->category->label() }}</span>
            @if($camper->club_rank)
                <span class="badge" style="margin-left:0.4rem">{{ $camper->club_rank }}</span>
            @endif
        </div>
    </div>

    {{-- Registration info --}}
    <h2>Registration Details</h2>
    <div class="info-card">
        <div class="info-row">
            <span class="info-label">Registration Code</span>
            <span class="info-value info-mono">{{ $camper->camper_number }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Payment Method</span>
            <span class="info-value">{{ $registrationCode->payment_type->label() }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Amount Paid</span>
            <span class="info-value">&#8358;{{ number_format($registrationCode->amount_paid ?? 0) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Camp Dates</span>
            <span class="info-value">{{ setting('camp_dates', 'TBA') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Venue</span>
            <span class="info-value">{{ setting('camp_venue', 'TBA') }}</span>
        </div>
    </div>

    {{-- Documents --}}
    <h2>Your Documents</h2>
    <div class="grid2">
        <div class="doc-card">
            <div class="doc-icon">&#128208;</div>
            <div class="doc-title">Camper ID Card</div>
            <div class="doc-desc">Print and laminate. Present at camp entry and all sessions.</div>
            @if($idCardUrl)
                <a href="{{ $idCardUrl }}" target="_blank" class="btn-download">Download PDF</a>
            @else
                <span class="btn-pending">&#8987; Generating&hellip;</span>
            @endif
        </div>

        @if($camper->requiresConsentForm())
            <div class="doc-card">
                <div class="doc-icon">&#128203;</div>
                <div class="doc-title">Parental Consent Form</div>
                <div class="doc-desc">Print, sign, and submit at check-in. Required for under-18.</div>
                @if($consentFormUrl)
                    <a href="{{ $consentFormUrl }}" target="_blank" class="btn-download">Download PDF</a>
                @else
                    <span class="btn-pending">&#8987; Generating&hellip;</span>
                @endif
            </div>
        @endif
    </div>

    @if(! $idCardUrl)
        <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:12px;padding:1rem 1.25rem;font-size:0.82rem;color:#92400E;margin-bottom:1.5rem">
            &#9888; Your documents are still being generated. Refresh this page in a moment.
            <a href="{{ route('portal.dashboard') }}" style="color:#92400E;font-weight:700;margin-left:0.5rem">Refresh &rarr;</a>
        </div>
    @endif

    {{-- Announcements --}}
    <h2>Camp Announcements</h2>
    @forelse($announcements as $ann)
        <div class="announcement">
            <div class="ann-title">{{ $ann->label }}</div>
            <div class="ann-body">{{ $ann->value }}</div>
            <div class="ann-date">{{ $ann->updated_at->format('d M Y') }}</div>
        </div>
    @empty
        <div class="empty">No announcements yet. Check back closer to camp.</div>
    @endforelse

</div>
</body>
</html>
