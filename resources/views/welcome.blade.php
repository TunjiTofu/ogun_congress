<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ setting('camp_name','Ogun Conference Youth Congress 2026') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth;font-size:16px;-webkit-text-size-adjust:100%}
        :root{
            --ink:#0A1832;--ink-2:#142547;--ink-3:#1F3360;
            --gold:#B8924A;--gold-2:#D4B26E;--gold-3:#EBD9A8;--gold-soft:rgba(184,146,74,.10);
            --cream:#F7F3EA;--cream-2:#EFE8D7;--paper:#FBF8F1;--paper-2:#FDFBF6;
            --text:#1A2238;--text-2:#4A5468;--muted:#7A8499;
            --hairline:rgba(10,24,50,.08);--hairline-2:rgba(10,24,50,.14);--rule:#E7DFC9;
            --sh-1:0 1px 2px rgba(10,24,50,.04);
            --sh-2:0 4px 16px rgba(10,24,50,.06),0 1px 2px rgba(10,24,50,.04);
            --sh-3:0 16px 48px -8px rgba(10,24,50,.12),0 4px 12px rgba(10,24,50,.06);
            --sh-gold:0 8px 28px -4px rgba(184,146,74,.45);
            --sh-ink:0 12px 40px -8px rgba(10,24,50,.4);
            --r-sm:8px;--r:14px;--r-lg:22px;--r-xl:32px;
            --font-display:'Fraunces','Playfair Display',Georgia,serif;
            --font-script:'Instrument Serif',Georgia,serif;
            --font-body:'DM Sans',-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
            --font-mono:ui-monospace,'SF Mono',Menlo,monospace;
            --adv:#1B3A8F;--pf:#2D7A3A;--syl:#C9A94D;
        }
        body{font-family:var(--font-body);color:var(--text);background:var(--paper);overflow-x:hidden;-webkit-font-smoothing:antialiased;line-height:1.55}
        img{max-width:100%;display:block}a{color:inherit}button{font:inherit;cursor:pointer}
        .container{max-width:1240px;margin:0 auto;padding:0 32px}
        @media(max-width:640px){.container{padding:0 20px}}
        .eyebrow{display:inline-flex;align-items:center;gap:8px;font-size:11px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--ink-3);padding:4px 0}
        .eyebrow-center{justify-content:center;display:flex}
        .eyebrow-dot{width:5px;height:5px;border-radius:50%;background:var(--gold);box-shadow:0 0 0 4px rgba(184,146,74,.12);display:inline-block}
        .section-head{max-width:760px;margin-bottom:56px}
        .section-head-center{text-align:center;margin:0 auto 64px}
        .section-head-center .eyebrow{margin:0 auto 18px}
        .section-head-split{display:grid;grid-template-columns:1.2fr 1fr;gap:80px;align-items:end;max-width:none;margin-bottom:64px}
        .section-title{font-family:var(--font-display);font-size:clamp(2.2rem,4.4vw,3.5rem);font-weight:500;letter-spacing:-.025em;line-height:1.04;color:var(--ink);margin:18px 0 22px;text-wrap:balance}
        .section-title em{font-style:italic;font-weight:400;color:var(--gold);font-family:var(--font-script)}
        .section-lede{font-size:1.05rem;color:var(--text-2);line-height:1.65;max-width:580px}
        .reveal{opacity:0;transform:translateY(20px);transition:opacity .6s,transform .6s}
        .reveal.visible{opacity:1;transform:translateY(0)}
        .reveal-delay-1{transition-delay:.1s}.reveal-delay-2{transition-delay:.2s}
        .reveal-delay-3{transition-delay:.3s}.reveal-delay-4{transition-delay:.4s}
        /* Topbar */
        .topbar{background:var(--ink);color:rgba(255,255,255,.85);font-size:12.5px;border-bottom:1px solid rgba(255,255,255,.06);position:relative;z-index:1001}
        .topbar-inner{max-width:1240px;margin:0 auto;padding:9px 32px;display:flex;align-items:center;justify-content:center;gap:12px;flex-wrap:wrap}
        .topbar-dot{width:6px;height:6px;border-radius:50%;flex-shrink:0;animation:pulse 2.4s infinite}
        .topbar-sep{color:rgba(255,255,255,.3)}
        .topbar a{color:var(--gold-2);text-decoration:none;font-weight:500;transition:color .2s}
        .topbar a:hover{color:var(--gold-3)}
        @keyframes pulse{0%,100%{box-shadow:0 0 0 3px rgba(94,212,138,.18)}50%{box-shadow:0 0 0 6px rgba(94,212,138,0)}}
        /* Nav */
        .nav{position:sticky;top:0;z-index:1000;display:flex;align-items:center;padding:12px 32px;background:rgba(251,248,241,.7);backdrop-filter:saturate(180%) blur(20px);border-bottom:1px solid transparent;transition:background .3s,border-color .3s,padding .3s}
        .nav.scrolled{background:rgba(251,248,241,.94);border-bottom-color:var(--hairline);padding:8px 32px}
        .nav-brand{display:flex;align-items:center;gap:12px;text-decoration:none;flex-shrink:0;margin-right:auto}
        .nav-logo{width:40px;height:40px;border-radius:50%;border:1.5px solid rgba(184,146,74,.35);transition:transform .3s,border-color .2s}
        .nav-brand:hover .nav-logo{transform:rotate(-6deg);border-color:var(--gold)}
        .nav-name{font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--ink)}
        .nav-sub{font-size:10.5px;color:var(--muted);letter-spacing:.08em;text-transform:uppercase;font-weight:500}
        .nav-links{display:flex;align-items:center;gap:28px;margin:0 32px}
        .nav-links a{color:var(--text-2);text-decoration:none;font-size:13.5px;font-weight:500;position:relative;transition:color .2s}
        .nav-links a::after{content:'';position:absolute;bottom:-6px;left:0;right:0;height:1.5px;background:var(--gold);transform:scaleX(0);transform-origin:left;transition:transform .3s}
        .nav-links a:hover{color:var(--ink)}.nav-links a:hover::after{transform:scaleX(1)}
        .nav-cta{display:flex;align-items:center;gap:10px}
        .btn-nav-ghost{font-size:13px;font-weight:500;text-decoration:none;color:var(--text-2);padding:9px 14px;border-radius:100px;transition:color .2s,background .2s}
        .btn-nav-ghost:hover{color:var(--ink);background:var(--cream-2)}
        .btn-nav{display:inline-flex;align-items:center;gap:6px;background:var(--ink);color:#fff;font-size:13px;font-weight:600;padding:9px 16px;border-radius:100px;text-decoration:none;transition:transform .25s,box-shadow .25s,background .25s}
        .btn-nav .arr{transition:transform .25s}
        .btn-nav:hover{background:var(--ink-2);box-shadow:var(--sh-ink);transform:translateY(-1px)}
        .btn-nav:hover .arr{transform:translateX(3px)}
        .nav-burger{display:none;flex-direction:column;gap:5px;background:none;border:none;padding:8px;margin-left:auto}
        .nav-burger span{width:22px;height:1.5px;background:var(--ink);border-radius:2px;transition:all .3s}
        .nav-drawer{position:fixed;top:0;left:0;right:0;z-index:999;background:rgba(251,248,241,.98);backdrop-filter:blur(20px);border-bottom:1px solid var(--hairline);max-height:0;overflow:hidden;transition:max-height .35s ease}
        .nav-drawer.open{max-height:500px}
        .nav-drawer-inner{display:flex;flex-direction:column;padding:72px 32px 24px}
        .nav-drawer a{color:var(--text);text-decoration:none;font-size:16px;font-weight:500;padding:14px 0;border-bottom:1px solid var(--hairline)}
        .nav-drawer a:last-child{border:none}.drawer-cta{color:var(--gold)!important;font-weight:700!important}
        @media(max-width:960px){.nav-links,.nav-cta{display:none}.nav-burger{display:flex}.nav{padding:12px 20px}}
        /* Hero */
        .hero{position:relative;background:var(--ink);color:#fff;padding:80px 32px 0;overflow:hidden}
        .hero-bg{position:absolute;inset:0;pointer-events:none;overflow:hidden}
        .hero-glow{position:absolute;border-radius:50%;filter:blur(100px)}
        .hero-glow-1{width:700px;height:700px;top:-150px;left:-200px;background:radial-gradient(circle,rgba(184,146,74,.25) 0%,transparent 70%)}
        .hero-glow-2{width:800px;height:800px;bottom:-300px;right:-200px;background:radial-gradient(circle,rgba(31,51,96,.7) 0%,transparent 70%)}
        .hero-grain{position:absolute;inset:0;background-image:radial-gradient(circle at 20% 30%,rgba(255,255,255,.015) 1px,transparent 1px),radial-gradient(circle at 80% 70%,rgba(255,255,255,.015) 1px,transparent 1px);background-size:40px 40px,30px 30px;opacity:.6}
        .hero-container{max-width:1240px;margin:0 auto;display:grid;grid-template-columns:1.05fr .95fr;gap:80px;align-items:center;padding:60px 0 100px;position:relative;z-index:2}
        .hero-left{max-width:600px}
        .hero-eyebrow{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(184,146,74,.25);padding:7px 16px;border-radius:100px;font-size:12px;font-weight:500;color:rgba(255,255,255,.85);letter-spacing:.04em;backdrop-filter:blur(10px);margin-bottom:32px}
        .hero-eyebrow-dot{width:6px;height:6px;border-radius:50%;background:var(--gold-2);box-shadow:0 0 12px rgba(212,178,110,.6)}
        .hero-deadline{background:rgba(184,146,74,.08); height:120px; border:1px solid rgba(184,146,74,.2);border-radius:var(--r);padding:14px 20px;margin-bottom:28px;display:flex;align-items:center;gap:16px;flex-wrap:wrap}
        .hero-deadline-pulse span{display:block;width:8px;height:8px;border-radius:50%;background:var(--gold);animation:pulse 2s infinite}
        .hero-deadline-eyebrow{font-size:10px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:2px}
        .hero-deadline-sub{font-size:12px;color:rgba(255,255,255,.6)}
        .hero-deadline-cd{display:flex;align-items:baseline;gap:4px}
        .hdcd-cell{display:flex;align-items:baseline;gap:2px}
        .hdcd-num{font-family:var(--font-display);font-size:22px;font-weight:500;color:#fff;font-variant-numeric:tabular-nums}
        .hdcd-lbl{font-size:10px;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:.06em}
        .hdcd-sep{color:rgba(184,146,74,.5);font-size:18px;margin:0 2px}
        .hero-title{font-family:var(--font-display);font-size:clamp(2.8rem,6.2vw,5.2rem);font-weight:400;line-height:.98;letter-spacing:-.035em;color:#fff;margin-bottom:28px}
        .hero-title em{font-family:var(--font-script);font-style:italic;font-weight:400;color:var(--gold-2)}
        .hero-lede{font-size:1.1rem;line-height:1.6;color:rgba(255,255,255,.7);max-width:520px;margin-bottom:36px}
        .hero-cta{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:48px}
        .btn-primary{display:inline-flex;align-items:center;gap:10px;background:var(--gold);color:var(--ink);font-weight:600;font-size:14.5px;padding:14px 24px;border-radius:100px;text-decoration:none;border:none;box-shadow:var(--sh-gold);transition:transform .25s,box-shadow .25s,background .25s}
        .btn-primary:hover{background:var(--gold-2);transform:translateY(-2px)}
        .btn-secondary{display:inline-flex;align-items:center;gap:10px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);color:#fff;font-weight:500;font-size:14.5px;padding:14px 22px;border-radius:100px;text-decoration:none;backdrop-filter:blur(10px);transition:background .25s,border-color .25s,transform .25s}
        .btn-secondary:hover{background:rgba(255,255,255,.1);border-color:rgba(184,146,74,.4);transform:translateY(-2px)}
        .hero-verse{position:relative;border-left:1.5px solid var(--gold);padding:8px 0 8px 24px;max-width:480px}
        .hero-verse-mark{position:absolute;top:-16px;left:8px;font-family:var(--font-display);font-size:64px;color:rgba(184,146,74,.22);line-height:1}
        .hero-verse p{font-family:var(--font-script);font-style:italic;font-size:1.05rem;line-height:1.6;color:rgba(255,255,255,.8);margin-bottom:10px}
        .hero-verse cite{font-family:var(--font-body);font-style:normal;font-size:11px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--gold-2)}
        .hero-right{display:flex;flex-direction:column;gap:16px}
        .hero-card{position:relative;background:linear-gradient(160deg,rgba(255,255,255,.07) 0%,rgba(255,255,255,.03) 100%);border:1px solid rgba(255,255,255,.1);border-radius:var(--r-lg);padding:32px;backdrop-filter:blur(20px);box-shadow:0 24px 60px -12px rgba(0,0,0,.45)}
        .hero-card::before{content:'';position:absolute;inset:0;border-radius:var(--r-lg);padding:1px;background:linear-gradient(160deg,rgba(184,146,74,.4),transparent 60%);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;pointer-events:none}
        .hero-card-header{display:flex;align-items:center;gap:16px;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:24px}
        .hero-card-logo{width:56px;height:56px;border-radius:50%;border:1.5px solid rgba(184,146,74,.4);animation:heroFloat 6s ease-in-out infinite}
        @keyframes heroFloat{0%,100%{transform:translateY(0)}50%{transform:translateY(-6px)}}
        .hero-card-eyebrow{font-size:10.5px;font-weight:600;letter-spacing:.18em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px}
        .hero-card-title{font-family:var(--font-display);font-size:18px;font-weight:500;color:#fff}
        .hero-card-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:rgba(255,255,255,.06);border-radius:var(--r);overflow:hidden;margin-bottom:24px}
        .hero-card-item{background:rgba(10,24,50,.5);padding:16px 18px}
        .hero-card-lbl{font-size:10px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:rgba(255,255,255,.45);margin-bottom:6px}
        .hero-card-val{font-family:var(--font-display);font-size:17px;font-weight:500;color:#fff;margin-bottom:2px}
        .hero-card-meta{font-size:11px;color:rgba(255,255,255,.5)}
        .hero-countdown{background:rgba(0,0,0,.25);border:1px solid rgba(184,146,74,.18);border-radius:var(--r);padding:16px 18px}
        .hero-countdown-lbl{font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:10px}
        .countdown{display:flex;align-items:baseline;gap:6px}
        .cd-item{display:flex;align-items:baseline;gap:4px}
        .cd-num{font-family:var(--font-display);font-size:28px;font-weight:500;color:#fff;font-variant-numeric:tabular-nums}
        .cd-lbl{font-size:10.5px;color:rgba(255,255,255,.5);text-transform:uppercase}
        .cd-sep{color:rgba(184,146,74,.4);font-size:20px;margin:0 4px}
        .hero-badge{display:flex;align-items:center;gap:14px;padding:14px 20px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:var(--r)}
        .hero-badge-star{width:32px;height:32px;border-radius:50%;background:var(--gold-soft);display:flex;align-items:center;justify-content:center;color:var(--gold-2);font-size:14px}
        .hero-badge-lbl{font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:2px}
        .hero-badge-val{font-size:13px;font-weight:500;color:#fff}
        .hero-marquee{position:relative;margin:0 -32px;padding:18px 0;border-top:1px solid rgba(255,255,255,.06);background:rgba(0,0,0,.18);overflow:hidden;mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}
        .hero-marquee-track{display:flex;gap:56px;white-space:nowrap;animation:marquee 35s linear infinite;width:max-content}
        .hero-marquee-track span{font-family:var(--font-display);font-style:italic;font-size:22px;color:#fff}
        @keyframes marquee{0%{transform:translateX(0)}100%{transform:translateX(-50%)}}
        @media(max-width:960px){.hero-container{grid-template-columns:1fr;gap:56px;padding:40px 0 72px}.hero{padding:56px 20px 0}.hero-marquee{margin:0 -20px}}
    </style>
</head>
<body>

@php
    use Illuminate\Support\Carbon;
    $campStart    = setting('camp_start_date');
    $campEnd      = setting('camp_end_date');
    $regCloses    = setting('registration_closes_at');
    $regOpen      = setting('registration_open','1') === '1'
        && (! $regCloses || now()->lt(Carbon::parse($regCloses, 'Africa/Lagos')));

    $campActive   = $campStart && $campEnd
        && now()->between(Carbon::parse($campStart), Carbon::parse($campEnd));
    $mediaEnabled = setting('media_upload_enabled','0') === '1';
    $ytChannelUrl = setting('youtube_channel_url','https://www.youtube.com/@OgunConferenceYouth');
@endphp

@include('partials.welcome-banner')

@php
    $baseUrl = rtrim(config('app.url'), '/');

    if (app()->environment('local')) {
        $port = env('APP_PORT');

        if ($port) {
            $parts = parse_url($baseUrl);

            $scheme = $parts['scheme'] ?? 'http';
            $host   = $parts['host'] ?? $baseUrl;

            $baseUrl = "{$scheme}://{$host}:{$port}";
        }
    }
@endphp

    <!-- ── TOPBAR ─────────────────────────────────────────────────────────────── -->
<div class="topbar">
    <div class="topbar-inner">
        @if($regOpen)
            <span class="topbar-dot" style="background:#5ED48A;box-shadow:0 0 0 3px rgba(94,212,138,.18)"></span>
            {{ setting('camp_name','Ogun Conference Youth Congress 2026') }}
            {{--            &mdash;--}}
            {{--            {{ setting('camp_venue','Abeokuta') }} --}}
            &middot;
            {{ setting('camp_dates','Aug 16–22, 2026') }}
            @if($regCloses)
                <span class="topbar-sep">|</span>
                Registration closes <strong>{{ Carbon::parse($regCloses)->format('d M Y') }}</strong>
                <em id="topbar-cd"></em>
            @endif
            <span class="topbar-sep">|</span>
            <a href="#access">Enter your code &rarr;</a>
        @else
            <span class="topbar-dot" style="background:#EF4444;box-shadow:0 0 0 3px rgba(239,68,68,.18)"></span>
            Registration is currently <strong>closed</strong>. Access your camper portal below.
            <span class="topbar-sep">|</span>
            <a href="#access">Camper portal &rarr;</a>
        @endif
    </div>
</div>

<!-- ── NAV ───────────────────────────────────────────────────────────────── -->
<nav class="nav" id="mainNav">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="nav-logo"/>
        <div class="nav-brand-text">
            <div class="nav-name">{{ setting('organization_name','Ogun Conference') }}</div>
            <div class="nav-sub">{{ setting('camp_name','Youth Congress · 2026') }}</div>
        </div>
    </a>
    <div class="nav-links">
        <a href="#register">How to Register</a>
        <a href="#fees">Departments</a>
        @if($campActive)<a href="#programs">Programmes</a>@endif
        <a href="#highlights">Highlights</a>
        @if($mediaEnabled)<a href="{{ url('/album') }}">Album</a>@endif
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
    </div>
    <div class="nav-cta">
        <a href="#access" class="btn-nav-ghost">Camper Portal</a>
        <a href="#access" class="btn-nav">Enter Code <span class="arr">&rarr;</span></a>
    </div>
    <button class="nav-burger" id="navBurger" onclick="toggleDrawer()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>
<div class="nav-drawer" id="navDrawer">
    <div class="nav-drawer-inner">
        <a href="#register" onclick="toggleDrawer()">How to Register</a>
        <a href="#fees" onclick="toggleDrawer()">Departments</a>
        @if($campActive)<a href="#programs" onclick="toggleDrawer()">Programmes</a>@endif
        <a href="#highlights" onclick="toggleDrawer()">Highlights</a>
        @if($mediaEnabled)<a href="{{ url('/album') }}" onclick="toggleDrawer()">Album</a>@endif
        <a href="#about" onclick="toggleDrawer()">About</a>
        <a href="#contact" onclick="toggleDrawer()">Contact</a>
        <a href="#access" onclick="toggleDrawer()" class="drawer-cta">Enter Code &rarr;</a>
    </div>
</div>

<!-- ── HERO ───────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-glow hero-glow-1"></div>
        <div class="hero-glow hero-glow-2"></div>
        <div class="hero-grain"></div>
    </div>
    <div class="hero-container">
        <div class="hero-left">
            {{--            <div class="hero-eyebrow">--}}
            {{--                <span class="hero-eyebrow-dot"></span>--}}
            {{--                {{ setting('camp_venue','Abeokuta') }} 2026 &nbsp;&bull;&nbsp; {{ setting('camp_dates','August 16–22') }}--}}
            {{--            </div>--}}

            @if($regOpen && $regCloses)
                <div class="hero-deadline">
                    <div class="hero-deadline-pulse"><span></span></div>
                    <div class="hero-deadline-lbl">
                        <span class="hero-deadline-eyebrow">Registration closes</span>
                        <span class="hero-deadline-sub">{{ Carbon::parse($regCloses)->format('d M Y · H:i') }} WAT</span>
                    </div>
                    <div class="hero-deadline-cd">
                        <div class="hdcd-cell">
                            <span class="hdcd-num" id="rd-d" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span><span class="hdcd-lbl">d</span></div>
                        <span class="hdcd-sep">:</span>
                        <div class="hdcd-cell">
                            <span class="hdcd-num" id="rd-h" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span><span class="hdcd-lbl">h</span></div>
                        <span class="hdcd-sep">:</span>
                        <div class="hdcd-cell">
                            <span class="hdcd-num" id="rd-m" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span><span class="hdcd-lbl">m</span></div>
                        <span class="hdcd-sep">:</span>
                        <div class="hdcd-cell">
                            <span class="hdcd-num" id="rd-s" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span><span class="hdcd-lbl">s</span></div>
                    </div>
                </div>

                {{--                <div class="hero-deadline">--}}
                {{--                    <div class="hero-deadline-pulse"><span></span></div>--}}
                {{--                    <div class="hero-deadline-lbl">--}}
                {{--                        <span class="hero-deadline-eyebrow">Registration closes</span>--}}
                {{--                        <span class="hero-deadline-sub">{{ Carbon::parse($regCloses)->format('d M Y · H:i') }} WAT</span>--}}
                {{--                    </div>--}}

                {{--                    <div class="hero-deadline-cd">--}}
                {{--                        <div class="hdcd-cell">--}}
                {{--                            <span class="hdcd-num" id="rd-d" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span>--}}
                {{--                            <span class="hdcd-lbl">D</span>--}}
                {{--                        </div>--}}

                {{--                        <span class="hdcd-sep">:</span>--}}

                {{--                        <div class="hdcd-cell">--}}
                {{--                            <span class="hdcd-num" id="rd-h" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span>--}}
                {{--                            <span class="hdcd-lbl">H</span>--}}
                {{--                        </div>--}}

                {{--                        <span class="hdcd-sep">:</span>--}}

                {{--                        <div class="hdcd-cell">--}}
                {{--                            <span class="hdcd-num" id="rd-m" style="font-size: 2.75rem; font-weight: 800; line-height: 1;">--</span>--}}
                {{--                            <span class="hdcd-lbl">M</span>--}}
                {{--                        </div>--}}

                {{--                        <span class="hdcd-sep">:</span>--}}

                {{--                        <div class="hdcd-cell">--}}
                {{--                            <span class="hdcd-num" id="rd-s">--</span>--}}
                {{--                            <span class="hdcd-lbl">S</span>--}}
                {{--                        </div>--}}
                {{--                    </div>--}}
                {{--                </div>--}}
            @endif

            <h1 class="hero-title">From the Word<br/><em>to the World</em></h1>
            <p class="hero-lede">The Ogun Conference Annual Youth Congress gathers Adventurers, Pathfinders, and Senior Youth for a transformative week of spiritual growth, fellowship, and missionary training.</p>
            <div class="hero-cta">
                <a href="#access" class="btn-primary">I Have a Code &rarr;</a>
                <a href="#register" class="btn-secondary">How It Works</a>
            </div>
            <div class="hero-verse">
                <div class="hero-verse-mark">&ldquo;</div>
                <p>&ldquo;Ye shall receive power &hellip; and ye shall be witnesses unto me, both in Jerusalem, and in all Judaea, and in Samaria, and unto the uttermost part of the earth.&rdquo;</p>
                <cite>Acts 1:8 &middot; Ogun Conference Youth Congress</cite>
            </div>
        </div>

        <div class="hero-right">
            <div class="hero-card">
                <div class="hero-card-header">
                    <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="hero-card-logo"/>
                    <div>
                        <div class="hero-card-eyebrow">{{ setting('organization_name','Ogun Conference SDA') }}</div>
                        <div class="hero-card-title">{{ setting('camp_name','Youth Congress 2026') }}</div>
                    </div>
                </div>
                <div class="hero-card-grid">
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Dates</div>
                        <div class="hero-card-val">{{ setting('camp_dates','Aug 16–22') }}</div>
                        <div class="hero-card-meta">7 Days</div>
                    </div>
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Venue</div>
                        <div class="hero-card-val">{{ setting('camp_venue','Abeokuta') }}</div>
                        <div class="hero-card-meta">Ogun State</div>
                    </div>
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Open to</div>
                        <div class="hero-card-val">Ages 6 +</div>
                        <div class="hero-card-meta">Adv &middot; PF &middot; SYL</div>
                    </div>
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Theme Text</div>
                        <div class="hero-card-val" style="font-size:14px">Acts 1:8</div>
                        <div class="hero-card-meta">Word &rarr; World</div>
                    </div>
                </div>
                @if(setting('camp_start_date'))
                    <div class="hero-countdown">
                        <div class="hero-countdown-lbl">Countdown to Congress</div>
                        <div class="countdown">
                            <div class="cd-item"><span class="cd-num" id="cd-d">--</span><span class="cd-lbl">d</span></div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item"><span class="cd-num" id="cd-h">--</span><span class="cd-lbl">h</span></div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item"><span class="cd-num" id="cd-m">--</span><span class="cd-lbl">m</span></div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item"><span class="cd-num" id="cd-s">--</span><span class="cd-lbl">s</span></div>
                        </div>
                    </div>
                @endif
            </div>
            <div class="hero-badge">
                <div class="hero-badge-star">&#10022;</div>
                <div>
                    <div class="hero-badge-lbl">Logo designed by</div>
                    <div class="hero-badge-val">Master Guide Chrisadim Emmanuel</div>
                </div>
            </div>
        </div>
    </div>

    <div class="hero-marquee">
        <div class="hero-marquee-track">
            @php $items=['From the Word','to the World','Acts 1:8','Abeokuta 2026','Adventurers','Pathfinders','Senior Youth','Ogun Conference','Aug 16–22, 2026','SDA Youth Congress','From the Word','to the World','Acts 1:8','Abeokuta 2026','Adventurers','Pathfinders','Senior Youth','Ogun Conference','Aug 16–22, 2026','SDA Youth Congress']; @endphp
            @foreach($items as $item)<span>{{ $item }}</span>@endforeach
        </div>
    </div>
</section>

<!-- ── ACCESS ─────────────────────────────────────────────────────────────── -->
<section class="access" id="access">
    <div class="container">
        <div class="section-head section-head-center reveal">
            <div class="eyebrow eyebrow-center"><span class="eyebrow-dot"></span>&nbsp; Your Gateway</div>
            <h2 class="section-title">Already have a code?<br/><em>Start where you are.</em></h2>
            <p class="section-lede" style="margin:0 auto">Access codes are issued by your Local Church Youth Leader upon confirmation of payment. Use the card on the left to complete your registration and the card on the right to access your camper portal.</p>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;max-width:980px;margin:0 auto">
            <div style="background:#fff;border:1px solid var(--hairline);border-radius:var(--r-lg);padding:36px;box-shadow:var(--sh-2)" class="reveal reveal-delay-1">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
                    <span style="font-family:var(--font-display);font-size:14px;font-weight:500;color:var(--gold);letter-spacing:.1em">01.</span>
                    <span style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--ink);background:var(--cream-2);padding:4px 10px;border-radius:100px">New Registration</span>
                </div>
                <h3 style="font-family:var(--font-display);font-size:24px;font-weight:500;color:var(--ink);margin-bottom:10px">Complete Your Form</h3>
                <p style="font-size:14.5px;color:var(--text-2);line-height:1.6;margin-bottom:24px">Have a code from your local church youth leader? Enter it here to fill in your personal details and secure your camp spot.</p>
                <form style="margin-bottom:16px" action="{{ route('registration.validate-code-web') }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:8px">
                        <input type="text" name="code" style="flex:1;min-width:0;padding:13px 16px;background:var(--paper);border:1px solid var(--hairline-2);border-radius:100px;color:var(--ink);font-family:var(--font-mono);font-size:13px;font-weight:600;text-align:center;letter-spacing:.06em;outline:none" placeholder="OGN-2026-XXXXXX" maxlength="15" oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')" autocomplete="off" spellcheck="false" required/>
                        <button type="submit" style="background:var(--ink);color:#fff;font-size:13.5px;font-weight:600;padding:13px 20px;border-radius:100px;border:none;white-space:nowrap;cursor:pointer">Go &rarr;</button>
                    </div>
                </form>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)">
                    <span style="width:4px;height:4px;border-radius:50%;background:var(--gold);flex-shrink:0;display:inline-block"></span>
                    Format: <code style="font-family:var(--font-mono);background:var(--cream-2);padding:2px 6px;border-radius:4px;color:var(--ink)">OGN-2026-XXXXXX</code>
                </div>
                @if(session('error') && !session('portal_error'))<p style="color:#DC2626;font-size:12.5px;margin-top:8px">{{ session('error') }}</p>@endif
            </div>

            <div style="background:#fff;border:1px solid var(--hairline);border-radius:var(--r-lg);padding:36px;box-shadow:var(--sh-2)" class="reveal reveal-delay-2">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px">
                    <span style="font-family:var(--font-display);font-size:14px;font-weight:500;color:var(--gold);letter-spacing:.1em">02.</span>
                    <span style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);background:var(--gold-soft);padding:4px 10px;border-radius:100px">Returning Camper</span>
                </div>
                <h3 style="font-family:var(--font-display);font-size:24px;font-weight:500;color:var(--ink);margin-bottom:10px">Camper Portal</h3>
                <p style="font-size:14.5px;color:var(--text-2);line-height:1.6;margin-bottom:24px">Already registered? Access your camper portal to download your ID card, consent form, and view camp announcements.</p>
                <form style="margin-bottom:16px" action="{{ route('portal.login') }}" method="POST">
                    @csrf
                    <div style="display:flex;gap:8px">
                        <input type="text" name="code" style="flex:1;min-width:0;padding:13px 16px;background:var(--paper);border:1px solid var(--hairline-2);border-radius:100px;color:var(--ink);font-family:var(--font-mono);font-size:13px;font-weight:600;text-align:center;letter-spacing:.06em;outline:none" placeholder="OGN-2026-XXXXXX" maxlength="15" oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')" autocomplete="off" spellcheck="false" required/>
                        <button type="submit" style="background:var(--gold);color:var(--ink);font-size:13.5px;font-weight:600;padding:13px 20px;border-radius:100px;border:none;white-space:nowrap;cursor:pointer">Enter &rarr;</button>
                    </div>
                </form>
                <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)">
                    <span style="width:4px;height:4px;border-radius:50%;background:var(--gold);flex-shrink:0;display:inline-block"></span>
                    Same code used to register
                </div>
                @if(session('portal_error'))<p style="color:#DC2626;font-size:12.5px;margin-top:8px">{{ session('portal_error') }}</p>@endif
            </div>
        </div>
    </div>
</section>

<!-- ── HOW TO REGISTER ────────────────────────────────────────────────────── -->
<section class="how" id="register" style="background:var(--cream);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div class="section-head-split reveal">
            <div>
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Registration</div>
                <h2 class="section-title">Four steps,<br/><em>one church-led process.</em></h2>
            </div>
            <p class="section-lede">Registration runs through your local church. Your youth leader handles payment and code generation for the whole congregation — then you finish the form with your code.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:64px">
            @foreach([
                ['01','Contact Your Youth Leader','Reach your local church youth leader to express interest and confirm your department and age group.','Adv · Pathfinders · SYL',false],
                ['02','Church Makes Payment','The youth leader pays the total for all registered campers via bank transfer.','Bank Transfer',false],
                ['03','Receive Your Code','A unique registration code appears in the youth leader\'s dashboard once payment is confirmed by the treasurer.','Delivered to Youth Leader',false],
                ['04','Complete Your Form','Enter your code on this page and fill in the registration wizard to finalize your congress spot.','You\'re in!',true],
            ] as [$no,$title,$desc,$foot,$final])
                <div style="background:{{ $final ? 'linear-gradient(180deg,var(--paper-2) 0%,rgba(184,146,74,.05) 100%)' : 'var(--paper-2)' }};border:1px solid {{ $final ? 'rgba(184,146,74,.25)' : 'var(--hairline)' }};border-radius:var(--r-lg);padding:28px 24px 24px;display:flex;flex-direction:column;min-height:240px" class="reveal reveal-delay-{{ $loop->iteration }}">
                    <div style="font-family:var(--font-display);font-size:13px;font-weight:500;color:var(--gold);letter-spacing:.12em;margin-bottom:36px;display:inline-flex;align-items:center;gap:8px">
                        {{ $no }}<span style="display:block;flex:1;height:1px;background:var(--rule);margin-left:4px"></span>
                    </div>
                    <div style="flex:1;margin-bottom:20px">
                        <div style="font-family:var(--font-display);font-size:18px;font-weight:500;color:var(--ink);line-height:1.25;margin-bottom:10px">{{ $title }}</div>
                        <div style="font-size:13.5px;color:var(--text-2);line-height:1.55">{{ $desc }}</div>
                    </div>
                    <div style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:{{ $final ? 'var(--gold)' : 'var(--muted)' }};padding-top:16px;border-top:1px solid var(--hairline)">{{ $foot }}</div>
                </div>
            @endforeach
        </div>

        @if(setting('bank_account_number'))
            <div style="background:var(--ink);color:#fff;border-radius:var(--r-lg);padding:36px 40px;position:relative;overflow:hidden" class="reveal">
                <div style="position:absolute;top:-100px;right:-100px;width:400px;height:400px;background:radial-gradient(circle,rgba(184,146,74,.18) 0%,transparent 70%);pointer-events:none"></div>
                <div style="display:flex;align-items:center;gap:16px;padding-bottom:24px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:24px;position:relative;z-index:1">
                    <div style="width:48px;height:48px;border-radius:14px;background:var(--gold-soft);border:1px solid rgba(184,146,74,.3);display:flex;align-items:center;justify-content:center;font-family:var(--font-display);font-size:22px;color:var(--gold-2);font-weight:600">&#8358;</div>
                    <div>
                        <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px">Bank Transfer Details</div>
                        <div style="font-family:var(--font-display);font-size:19px;font-weight:500;color:#fff">Pay directly into our account</div>
                    </div>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1.4fr 1.4fr auto;gap:32px;align-items:center;position:relative;z-index:1">
                    <div><div style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:6px">Bank</div><div style="font-size:15px;font-weight:500;color:#fff">{{ setting('bank_name') }}</div></div>
                    <div><div style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:6px">Account Number</div><div style="font-family:var(--font-mono);font-size:20px;font-weight:600;color:var(--gold-2);letter-spacing:.04em">{{ setting('bank_account_number') }}</div></div>
                    <div><div style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:rgba(255,255,255,.5);margin-bottom:6px">Account Name</div><div style="font-size:15px;font-weight:500;color:#fff">{{ setting('bank_account_name') }}</div></div>
                    @if(setting('treasurer_number'))
                        <div><a href="https://wa.me/{{ preg_replace('/\D/','',setting('treasurer_number')) }}" target="_blank" style="display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:var(--ink);font-size:13px;font-weight:600;padding:11px 18px;border-radius:100px;text-decoration:none">Send Teller &rarr;</a></div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ── DEPARTMENTS / FEES ─────────────────────────────────────────────────── -->
<section id="fees" style="background:var(--paper);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div class="section-head-split reveal">
            <div>
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Departments</div>
                <h2 class="section-title">Pick the camp<br/><em>that fits your age.</em></h2>
            </div>
            <p class="section-lede">Every camper belongs to one of three departments. Fees, age brackets, and uniforms differ — but the call are the same.</p>
        </div>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:32px">
            @php
                $deptCards = [
                    ['adv','01.','Ages 6–9','adventurer_logo.png','Adventurers','Ages 6 – 9 years',setting('fee_adventurer',5000),'linear-gradient(90deg,#1E88E5,#64B5F6)',
                        ['Parent/guardian must accompany campers under 6','Parental consent form required','Registration via local church','Cover letter from Church Pastor']],
                    ['pf','02.','Ages 10–15','pathfinder_logo.png','Pathfinders','Ages 10 – 15 years',setting('fee_pathfinder',5000),'linear-gradient(90deg,#2D6A30,#4CAF50)',
                        ['Registration via local church','Parental consent form required','Cover letter from Church Pastor']],
                    ['syl','03.','Ages 16+','senior_youth_logo.png','Senior Youth','Ambassador (16–21) · Young Adults (22+)',setting('fee_senior_youth',7000),'linear-gradient(90deg,#B8924A,#D4B26E)',
                        ['Registration via local church','Cover letter from Church Pastor']],
                ];
            @endphp
            @foreach($deptCards as $i => [$key,$num,$tag,$logo,$name,$ages,$fee,$accent,$items])
                <div style="position:relative;background:{{ $key==='pf' ? 'linear-gradient(180deg,#fff 0%,var(--paper-2) 100%)' : '#fff' }};border:1px solid {{ $key==='pf' ? 'rgba(45,106,48,.25)' : 'var(--hairline)' }};border-radius:var(--r-lg);padding:32px 28px 28px;display:flex;flex-direction:column;text-align:center;overflow:hidden" class="reveal reveal-delay-{{ $loop->iteration }}">
                    <div style="position:absolute;top:0;left:0;right:0;height:3px;background:{{ $accent }}"></div>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
                        <span style="font-family:var(--font-display);font-size:13px;font-weight:500;color:var(--gold);letter-spacing:.16em">{{ $num }}</span>
                        <span style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted)">{{ $tag }}</span>
                    </div>
                    <div style="width:96px;height:96px;margin:0 auto 20px;position:relative">
                        <div style="position:absolute;inset:-8px;border-radius:50%;background:var(--cream-2)"></div>
                        <img src="{{ asset('images/'.$logo) }}" alt="{{ $name }}" style="position:relative;z-index:1;width:96px;height:96px"/>
                    </div>
                    <div style="font-family:var(--font-display);font-size:22px;font-weight:500;color:var(--ink);margin-bottom:6px">{{ $name }}</div>
                    <div style="font-size:12.5px;font-weight:500;color:var(--text-2);margin-bottom:20px">{{ $ages }}</div>
                    <div style="height:1px;background:linear-gradient(90deg,transparent,var(--rule),transparent);margin:0 -28px 20px"></div>
                    <div style="font-family:var(--font-display);display:flex;align-items:flex-start;justify-content:center;gap:4px;margin-bottom:24px;line-height:1">
                        <span style="font-size:22px;font-weight:500;color:var(--gold);margin-top:8px">&#8358;</span>
                        <span style="font-size:48px;font-weight:500;color:var(--ink);letter-spacing:-.03em;font-variant-numeric:tabular-nums">{{ number_format((int)$fee) }}</span>
                    </div>
                    <ul style="list-style:none;text-align:left;border-top:1px solid var(--hairline);padding-top:18px">
                        @foreach($items as $item)
                            <li style="font-size:13px;color:var(--text-2);line-height:1.5;padding:7px 0 7px 22px;position:relative">
                                <span style="position:absolute;left:0;top:7px;color:var(--gold);font-size:12px;font-weight:600">&#10003;</span>
                                {{ $item }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
        <div style="display:flex;align-items:center;gap:10px;justify-content:center;max-width:720px;margin:32px auto 0;text-align:center;font-size:13px;color:var(--muted);padding:16px 24px;background:var(--cream-2);border-radius:var(--r)" class="reveal">
            &#128276; All fees are paid through your local church youth leader. Contact them directly to confirm your registration and arrange payment.
        </div>
    </div>
</section>

<!-- ── PROGRAMMES — only during camp ─────────────────────────────────────── -->
@if($campActive)
    @php
        $loadProg = function (string $filename): array {
            $path = storage_path('app/programmes/' . $filename);
            if (! file_exists($path)) {
                return ['days' => []];
            }
            try {
                $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
                return is_array($data) ? $data : ['days' => []];
            } catch (\Throwable $e) {
                return ['days' => []];
            }
        };

        $advProg = Cache::remember('prog_adv', 3600, fn() => $loadProg('adventurer-programme.json'));
        $pfProg  = Cache::remember('prog_pf',  3600, fn() => $loadProg('pathfinder-programme.json'));
        $sylProg = Cache::remember('prog_syl', 3600, fn() => $loadProg('senior-youth-programme.json'));
    @endphp
    <section id="programs" style="background:var(--cream);padding:120px 0;border-top:1px solid var(--rule)">
        <div class="container">
            <div class="section-head section-head-center reveal">
                <div class="eyebrow eyebrow-center"><span class="eyebrow-dot"></span>&nbsp; Programmes</div>
                <h2 class="section-title">Seven days, <em>three departments,</em><br/>one shared rhythm.</h2>
                <p class="section-lede">The full seven-day schedule for your department — sessions, classes, devotions, and outdoor activities. Updated for {{ now()->year }}.</p>
            </div>

            <!-- Day selector -->
            <div style="margin-bottom:16px">
                <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:16px">
                    <span style="font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-right:8px">{{ setting('camp_dates','Aug 16 → 22') }}</span>
                    @foreach($advProg['days'] as $di => $day)
                        <button type="button"
                                style="background:none;border:1px solid var(--hairline-2);color:var(--text-2);font-family:var(--font-body);font-size:13px;font-weight:500;padding:8px 16px;border-radius:100px;cursor:pointer;display:flex;align-items:center;gap:6px"
                                class="prog-day-btn {{ $di===0 ? 'prog-day-active' : '' }}"
                                data-day="{{ $di }}">
                            {{ substr($day['label'],0,3) }}
                            <span style="font-family:var(--font-display);font-size:15px;font-weight:500">{{ substr($day['label'],4) }}</span>
                        </button>
                    @endforeach
                </div>
                <div id="progDayDetail" style="background:#fff;border:1px solid var(--hairline);border-radius:var(--r-lg);padding:28px 32px;margin-bottom:40px;min-height:120px;box-shadow:var(--sh-1)"></div>
            </div>

            <!-- Programme cards -->
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-bottom:32px">
                @foreach([
                    ['adv','var(--adv)','adventurer_logo.png','Volume 01.','Adventurers','Ages 6–9 · Children','Devotions, nature classes, crafts, music, and special parent-child sessions in the dedicated children\'s pavilion.',['07:00|Morning Watch · sunrise devotion','10:30|Bible adventure & story hour','15:00|Nature, crafts & music classes','19:00|Evening worship with parents'],'adventurer-programme.pdf',false],
                    ['pf', 'var(--pf)', 'pathfinder_logo.png', 'Volume 02.','Pathfinders','Ages 10–15 · Juniors & Teens','Honour classes, drill, drumline, outdoor program, and advancement tracks — the spine of the Congress week.',['06:00|Reveille & flag-raising drill','09:00|Honour classes & advancement','14:00|Outdoor & drumline rehearsals','20:00|Campfire & testimony night'],'pathfinder-programme.pdf',true],
                    ['syl','var(--syl)','senior_youth_logo.png','Volume 03.','Senior Youth','Ambassadors & Young Adults','Leadership intensives, missions training, plenaries, and small-group breakouts for the next generation of leaders.',['06:30|Personal devotion & prayer walk','10:00|Leadership intensive plenary','15:30|Missions training breakouts','21:00|Late-night discipleship circles'],'senior-youth-programme.pdf',false],
                ] as $i => [$key,$color,$logo,$num,$pname,$sub,$desc,$times,$pdf,$featured])
                    <div style="background:{{ $featured ? 'linear-gradient(180deg,#fff 0%,var(--paper-2) 100%)' : '#fff' }};border:1px solid var(--hairline);border-radius:var(--r-lg);padding:28px;position:relative;overflow:hidden;display:flex;flex-direction:column" class="reveal reveal-delay-{{ $loop->iteration }}">
                        <div style="position:absolute;top:0;left:0;right:0;height:4px;background:{{ $color }}"></div>
                        @if($featured)<div style="position:absolute;top:14px;right:14px;font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--pf);background:rgba(45,122,58,.1);padding:3px 10px;border-radius:100px">Most campers</div>@endif
                        <div style="display:flex;align-items:center;gap:16px;margin-bottom:20px;margin-top:10px">
                            <img src="{{ asset('images/'.$logo) }}" alt="{{ $pname }}" style="width:52px;height:52px"/>
                            <div>
                                <div style="font-family:var(--font-display);font-size:12px;font-weight:500;color:var(--gold);letter-spacing:.14em;margin-bottom:2px">{{ $num }}</div>
                                <div style="font-family:var(--font-display);font-size:20px;font-weight:500;color:var(--ink)">{{ $pname }}</div>
                                <div style="font-size:12px;color:var(--muted)">{{ $sub }}</div>
                            </div>
                        </div>
                        <p style="font-size:13.5px;color:var(--text-2);line-height:1.55;margin-bottom:20px">{{ $desc }}</p>
                        <ul style="list-style:none;margin-bottom:24px;flex:1">
                            @foreach($times as $t)
                                @php [$time,$ev] = explode('|',$t,2); @endphp
                                <li style="font-size:13px;color:var(--text-2);padding:8px 0;border-bottom:1px solid var(--hairline);display:flex;align-items:flex-start;gap:10px">
                                    <span style="font-family:var(--font-mono);font-size:10.5px;font-weight:600;color:var(--gold);background:var(--gold-soft);padding:2px 8px;border-radius:4px;flex-shrink:0">{{ $time }}</span>
                                    {{ $ev }}
                                </li>
                            @endforeach
                        </ul>
                        <div style="margin-top:auto">
                            <a href="{{ asset('programmes/'.$pdf) }}" download style="display:inline-flex;align-items:center;gap:8px;background:var(--ink);color:#fff;font-size:12.5px;font-weight:600;padding:10px 16px;border-radius:100px;text-decoration:none;width:100%;justify-content:center">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="15" height="15"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                                Download programme <span style="font-size:10px;color:rgba(255,255,255,.55)">PDF</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--muted);padding:16px 24px;background:var(--paper-2);border-radius:var(--r);max-width:720px;margin:0 auto" class="reveal">
                <span style="width:4px;height:4px;border-radius:50%;background:var(--gold);flex-shrink:0;display:inline-block"></span>
                Programmes are subject to minor adjustments. Final printed copies will be distributed at check-in.
            </div>
        </div>
    </section>
@endif

<!-- ── HIGHLIGHTS / YOUTUBE ──────────────────────────────────────────────── -->
@php $highlights = \App\Models\YoutubeHighlight::where('is_active',true)->orderBy('sort_order')->orderByDesc('is_featured')->get(); @endphp
<section id="highlights" style="background:var(--paper);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div class="section-head-split reveal">
            <div>
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Highlights · Youth Channel</div>
                <h2 class="section-title">Witness the week,<br/><em>before · during · after.</em></h2>
            </div>
            <p class="section-lede">Tap any thumbnail to play here, or open the full channel on YouTube.</p>
        </div>

        @if($highlights->count())
            <div style="display:grid;grid-template-columns:1.6fr 1fr 1fr;grid-template-rows:auto auto;gap:16px;margin-bottom:32px">
                @foreach($highlights as $i => $yt)
                    <article style="position:relative;background:#fff;border:1px solid var(--hairline);border-radius:var(--r-lg);overflow:hidden;cursor:pointer;display:flex;flex-direction:column;transition:transform .3s,box-shadow .3s;{{ $i===0 ? 'grid-row:span 2' : '' }}" data-yt="{{ $yt->youtube_id }}" class="yt-card reveal">
                        @php $phaseColors=['before'=>'rgba(27,58,143,.85)','during'=>'rgba(184,146,74,.9)','after'=>'rgba(45,122,58,.85)',''=>'']; @endphp
                        @if($yt->phase)
                            <div style="position:absolute;top:12px;left:12px;z-index:2;font-size:9.5px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:4px 10px;border-radius:100px;background:{{ $phaseColors[$yt->phase]??'rgba(0,0,0,.7)' }};color:#fff">{{ ucfirst($yt->phase) }}</div>
                        @endif
                        <div style="position:relative;overflow:hidden;aspect-ratio:16/9;{{ $i===0 ? 'flex:1;min-height:200px;aspect-ratio:unset' : '' }}">
                            <img src="{{ $yt->thumbnail_url ?: 'https://i.ytimg.com/vi/'.$yt->youtube_id.'/'.($i===0?'maxres':'hq').'default.jpg' }}" alt="{{ $yt->title }}" loading="lazy" style="width:100%;height:100%;object-fit:cover;transition:transform .4s"/>
                            <div style="position:absolute;inset:0;background:linear-gradient(180deg,transparent 40%,rgba(10,24,50,.5) 100%)"></div>
                            <button style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:{{ $i===0?'60px':'48px' }};height:{{ $i===0?'60px':'48px' }};border-radius:50%;background:rgba(255,255,255,.92);border:none;display:flex;align-items:center;justify-content:center;color:var(--ink);cursor:pointer" aria-label="Play">
                                <svg viewBox="0 0 24 24" fill="currentColor" width="{{ $i===0?22:18 }}" height="{{ $i===0?22:18 }}"><path d="M8 5v14l11-7z"/></svg>
                            </button>
                        </div>
                        <div style="padding:18px 20px">
                            @if($yt->eyebrow)<div style="font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold);margin-bottom:6px">{{ $yt->eyebrow }}</div>@endif
                            <h3 style="font-family:var(--font-display);font-size:{{ $i===0?'20px':'15px' }};font-weight:500;color:var(--ink);line-height:1.3;margin-bottom:10px">{{ $yt->title }}</h3>
                            <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:var(--muted)">
                                @if($yt->duration_label)<span style="font-family:var(--font-mono);font-size:11px;font-weight:600;background:var(--cream-2);padding:2px 8px;border-radius:4px;color:var(--ink)">{{ $yt->duration_label }}</span>@endif
                                @if($yt->description)<span>·</span><span>{{ $yt->description }}</span>@endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:64px 0;color:var(--muted)" class="reveal">
                <p style="font-size:2rem;margin-bottom:12px">▶</p>
                <p style="font-family:var(--font-display);font-size:18px;color:var(--ink);margin-bottom:8px">Highlights coming soon</p>
                <p style="font-size:14px">Video highlights from the congress will appear here once uploaded by the admin.</p>
            </div>
        @endif

        <div style="background:var(--ink);border-radius:var(--r-lg);padding:20px 28px;display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;margin-top:24px" class="reveal">
            <div style="display:flex;align-items:center;gap:12px;color:rgba(255,255,255,.75);font-size:14px">
                <span style="color:var(--gold-2);font-size:16px">▶</span>
                <span>Full archive of trailers, recaps and testimonies — Adventurer, Pathfinder &amp; Senior Youth.</span>
            </div>
            <a href="{{ $ytChannelUrl }}" target="_blank" rel="noopener" style="display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:var(--ink);font-size:13px;font-weight:600;padding:11px 20px;border-radius:100px;text-decoration:none;white-space:nowrap">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6A3 3 0 0 0 .5 6.2C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1c1.9.6 9.4.6 9.4.6s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1c.5-1.9.5-5.8.5-5.8s0-3.9-.5-5.8zM9.6 15.6V8.4l6.3 3.6-6.3 3.6z"/></svg>
                Visit our YouTube channel ↗
            </a>
        </div>
    </div>
</section>

<!-- YouTube Lightbox -->
<div id="ytModal" role="dialog" aria-hidden="true" style="display:none;position:fixed;inset:0;z-index:2000;background:rgba(5,12,28,.94);backdrop-filter:blur(12px);align-items:center;justify-content:center;padding:60px 40px">
    <button id="ytModalClose" aria-label="Close" style="position:fixed;top:20px;right:24px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;font-size:24px;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer">×</button>
    <div style="width:100%;max-width:960px;aspect-ratio:16/9">
        <div id="ytModalPlayer" style="width:100%;height:100%"></div>
    </div>
</div>

<!-- ── ABOUT ──────────────────────────────────────────────────────────────── -->
<section id="about" style="background:var(--cream);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr .85fr;gap:80px;align-items:center">
            <div class="reveal">
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; About the Congress</div>
                <h2 class="section-title">Seven days that <em>send a generation outward.</em></h2>
                <p style="font-size:16px;line-height:1.7;color:var(--text-2);margin-bottom:18px">The <strong style="color:var(--ink);font-weight:600">Ogun Conference Annual Youth Congress</strong> gathers Adventurers, Pathfinders, and Senior Youth from churches across the conference for a transformative week of spiritual growth, fellowship, and missionary training.</p>
                <p style="font-size:16px;line-height:1.7;color:var(--text-2);margin-bottom:18px">The 2026 Congress theme &mdash; <strong style="color:var(--ink);font-weight:600">From the Word to the World</strong> &mdash; is rooted in Acts 1:8. It is a call for the youth to be witnesses, starting from their local communities and reaching outward to the world.</p>
                <div style="position:relative;background:var(--paper-2);border-left:2px solid var(--gold);border-radius:0 var(--r) var(--r) 0;padding:24px 28px;margin:32px 0">
                    <div style="position:absolute;top:-8px;left:16px;font-family:var(--font-display);font-size:64px;color:var(--gold-soft);line-height:1">&ldquo;</div>
                    <p style="font-family:var(--font-script);font-style:italic;font-size:17px;line-height:1.55;color:var(--ink);position:relative;z-index:1;margin-bottom:10px">Spiritual empowerment is not for ourselves alone, but for the world. The power received in Acts 1:8 is a mandate to move outward.</p>
                    <cite style="font-family:var(--font-body);font-style:normal;font-size:11px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold)">&mdash; 2026 Congress Visual Identity</cite>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--rule);border:1px solid var(--rule);border-radius:var(--r);overflow:hidden">
                    @foreach([['Venue',setting('camp_venue','TBA')],['Dates',setting('camp_dates','Aug 16–22, 2026')],['Theme',setting('camp_theme','From the Word to the World')],['Open To','Ages 6 and above']] as [$l,$v])
                        <div style="background:var(--paper-2);padding:16px 20px">
                            <div style="font-size:10.5px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:4px">{{ $l }}</div>
                            <div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink)">{{ $v }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div style="position:relative;display:flex;flex-direction:column;align-items:center" class="reveal reveal-delay-2">
                <div style="position:relative;width:360px;height:360px;display:flex;align-items:center;justify-content:center">
                    <div style="position:absolute;inset:0;border-radius:50%;border:1px solid rgba(184,146,74,.2);animation:ringSpin 80s linear infinite"></div>
                    <div style="position:absolute;inset:24px;border-radius:50%;border:1px dashed rgba(184,146,74,.25);animation:ringSpin 120s linear infinite reverse"></div>
                    <div style="position:absolute;inset:0;pointer-events:none">
                        @foreach(['8%;12%','88%;18%','6%;78%','90%;72%'] as $pos)
                            @php [$x,$y]=explode(';',$pos); @endphp
                            <span style="position:absolute;left:{{ $x }};top:{{ $y }};color:var(--gold);font-size:14px;opacity:.5;animation:twinkle 3s ease-in-out infinite">&#10022;</span>
                        @endforeach
                    </div>
                    <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" style="width:280px;height:280px;border-radius:50%;border:3px solid rgba(184,146,74,.25);box-shadow:0 0 0 16px rgba(184,146,74,.05),0 24px 60px -12px rgba(10,24,50,.2);position:relative;z-index:2"/>
                </div>
                <div style="display:flex;align-items:flex-start;gap:12px;margin-top:32px;font-size:12.5px;color:var(--text-2);line-height:1.55;font-style:italic;max-width:280px">
                    <span style="color:var(--gold);font-style:normal;font-size:16px;flex-shrink:0;margin-top:2px">&#10022;</span>
                    <div>Logo designed by <strong style="font-family:var(--font-display);font-style:normal;font-weight:500;color:var(--ink)">Master Guide Chrisadim Emmanuel</strong> &mdash; a visual manifesto for the Ogun Conference Youth.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CAMP RULES ─────────────────────────────────────────────────────────── -->
<section id="rules" style="background:var(--paper);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div class="section-head-split reveal">
            <div>
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Guidelines</div>
                <h2 class="section-title">Camp rules,<br/><em>kept simple.</em></h2>
            </div>
            <p class="section-lede" style="align-self:end">Commitments every camper signs onto. Coordinators will review these with each registrant before camp.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1px;background:var(--rule);border:1px solid var(--rule);border-radius:var(--r-lg);overflow:hidden">
            @foreach([['01','All campers must carry their <strong>printed ID card</strong> at all times during camp.'],['02','Campers under 18 must submit a <strong>signed parental consent form</strong> at check-in.'],['03','Participants must wear the <strong>official camp uniform/dress code</strong> during formal sessions.'],['04','Mobile phones should be kept on <strong>silent mode</strong> during services and meetings.'],['05','No camper may <strong>leave the camp venue</strong> without prior permission from officials.'],['06','All campers are expected to <strong>participate respectfully</strong> in all programme activities.']] as [$no,$text])
                <div style="background:var(--paper-2);padding:28px;display:flex;gap:18px;transition:background .25s" class="reveal">
                    <span style="font-family:var(--font-display);font-size:13px;font-weight:500;color:var(--gold);letter-spacing:.1em;flex-shrink:0;padding-top:2px">{{ $no }}</span>
                    <span style="font-size:14.5px;color:var(--text-2);line-height:1.55">{!! $text !!}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── CONTACT ────────────────────────────────────────────────────────────── -->
<section id="contact" style="background:var(--cream);padding:120px 0;border-top:1px solid var(--rule)">
    <div class="container">
        <div style="display:grid;grid-template-columns:.9fr 1.1fr;gap:64px">
            <div class="reveal">
                <div class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Get in touch</div>
                <h2 class="section-title">Questions?<br/><em>We're listening.</em></h2>
                <p class="section-lede" style="max-width:100%">For enquiries, complaints, or payment questions — reach us through any of the channels below, or send a message and we'll respond within 24 hours.</p>
                <div style="display:flex;flex-direction:column;gap:10px;margin-top:36px">
                    @if(setting('treasurer_number'))
                        <a href="https://wa.me/{{ preg_replace('/\D/','',setting('treasurer_number')) }}" target="_blank" style="display:flex;align-items:center;gap:16px;background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r);padding:18px 20px;text-decoration:none;color:inherit;transition:transform .25s,border-color .25s,background .25s">
                            <div style="width:44px;height:44px;border-radius:12px;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px">&#128172;</div>
                            <div style="flex:1"><div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink);margin-bottom:2px">Treasurer</div><div style="font-size:13px;color:var(--text-2)">{{ setting('treasurer_number') }}</div></div>
                            <span style="font-size:16px;color:var(--muted)">&nearr;</span>
                        </a>
                    @endif

                    @if(setting('secretariat_phone'))
                        <a href="https://wa.me/{{ preg_replace('/\D/','',setting('secretariat_phone')) }}" target="_blank" style="display:flex;align-items:center;gap:16px;background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r);padding:18px 20px;text-decoration:none;color:inherit;transition:transform .25s,border-color .25s,background .25s">
                            <div style="width:44px;height:44px;border-radius:12px;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px">&#128172;</div>
                            <div style="flex:1"><div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink);margin-bottom:2px">Secretariat</div><div style="font-size:13px;color:var(--text-2)">{{ setting('secretariat_phone') }}</div></div>
                            <span style="font-size:16px;color:var(--muted)">&nearr;</span>
                        </a>
                    @endif

                    @if(setting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" target="_blank" style="display:flex;align-items:center;gap:16px;background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r);padding:18px 20px;text-decoration:none;color:inherit;transition:transform .25s,border-color .25s,background .25s">
                            <div style="width:44px;height:44px;border-radius:12px;background:#25D366;color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px">&#128172;</div>
                            <div style="flex:1"><div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink);margin-bottom:2px">Technical Support</div><div style="font-size:13px;color:var(--text-2)">{{ setting('whatsapp_number') }}</div></div>
                            <span style="font-size:16px;color:var(--muted)">&nearr;</span>
                        </a>
                    @endif
                    {{--                    @if(setting('secretariat_phone'))--}}
                    {{--                        <a href="tel:{{ setting('secretariat_phone') }}" style="display:flex;align-items:center;gap:16px;background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r);padding:18px 20px;text-decoration:none;color:inherit;transition:transform .25s,border-color .25s,background .25s">--}}
                    {{--                            <div style="width:44px;height:44px;border-radius:12px;background:var(--ink);color:#fff;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px">&#128222;</div>--}}
                    {{--                            <div style="flex:1"><div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink);margin-bottom:2px">Secretariat</div><div style="font-size:13px;color:var(--text-2)">{{ setting('secretariat_phone') }}</div></div>--}}
                    {{--                            <span style="font-size:16px;color:var(--muted)">&nearr;</span>--}}
                    {{--                        </a>--}}
                    {{--                    @endif--}}
                    {{--                    <div style="display:flex;align-items:center;gap:16px;background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r);padding:18px 20px">--}}
                    {{--                        <div style="width:44px;height:44px;border-radius:12px;background:var(--gold-soft);color:var(--gold);display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:20px">&#127776;</div>--}}
                    {{--                        <div><div style="font-family:var(--font-display);font-size:15px;font-weight:500;color:var(--ink);margin-bottom:2px">Seventh-day Adventist</div><div style="font-size:13px;color:var(--text-2)">Ogun Conference Youth Department</div></div>--}}
                    {{--                    </div>--}}
                </div>
            </div>

            <div style="background:var(--paper-2);border:1px solid var(--hairline);border-radius:var(--r-lg);padding:36px;box-shadow:var(--sh-2)" class="reveal reveal-delay-2">
                <div style="display:flex;align-items:baseline;justify-content:space-between;margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid var(--hairline)">
                    <h3 style="font-family:var(--font-display);font-size:22px;font-weight:500;color:var(--ink)">Send a message</h3>
                    <span style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--gold)">Replies within 24 hrs</span>
                </div>
                @if(session('contact_success'))<div style="background:#F0FDF4;border:1px solid #86EFAC;color:#15803D;border-radius:var(--r-sm);padding:12px 16px;font-size:13.5px;margin-bottom:20px">&#10003; {{ session('contact_success') }}</div>@endif
                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                        <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:18px">
                            <label style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-2)">Your Name *</label>
                            <input type="text" name="sender_name" style="padding:12px 14px;background:#fff;border:1px solid var(--hairline-2);border-radius:var(--r-sm);color:var(--text);font-family:var(--font-body);font-size:14.5px;outline:none;width:100%" placeholder="Full name" required value="{{ old('sender_name') }}"/>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:18px">
                            <label style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-2)">Phone *</label>
                            <input type="tel" name="sender_phone" style="padding:12px 14px;background:#fff;border:1px solid var(--hairline-2);border-radius:var(--r-sm);color:var(--text);font-family:var(--font-body);font-size:14.5px;outline:none;width:100%" placeholder="08012345678" required value="{{ old('sender_phone') }}"/>
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:18px">
                        <label style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-2)">Email <span style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
                        <input type="email" name="sender_email" style="padding:12px 14px;background:#fff;border:1px solid var(--hairline-2);border-radius:var(--r-sm);color:var(--text);font-family:var(--font-body);font-size:14.5px;outline:none;width:100%" placeholder="your@email.com" value="{{ old('sender_email') }}"/>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:18px">
                        <label style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-2)">Category *</label>
                        <div style="display:flex;flex-wrap:wrap;gap:8px" id="contact-cats">
                            @foreach(['general'=>'General Enquiry','complaint'=>'Complaint','inquiry'=>'Inquiry','payment'=>'Payment Question'] as $val=>$lbl)
                                <label style="cursor:pointer" onclick="selectCat(this)">
                                    <input type="radio" name="category" value="{{ $val }}" {{ old('category')===$val?'checked':'' }} required style="position:absolute;opacity:0;pointer-events:none"/>
                                    <span class="cat-pill {{ old('category')===$val ? 'cat-pill-active' : '' }}"
                                          style="display:inline-block;padding:9px 16px;border-radius:100px;font-size:13px;font-weight:500;transition:all .2s;cursor:pointer;
                                             {{ old('category')===$val
                                                ? 'background:var(--ink);color:#fff;border:1px solid var(--ink)'
                                                : 'background:#fff;color:var(--text-2);border:1px solid var(--hairline-2)' }}">
                                    {{ $lbl }}
                                </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div style="display:flex;flex-direction:column;gap:7px;margin-bottom:18px">
                        <label style="font-size:11px;font-weight:600;letter-spacing:.12em;text-transform:uppercase;color:var(--text-2)">Message *</label>
                        <textarea name="message" style="padding:12px 14px;background:#fff;border:1px solid var(--hairline-2);border-radius:var(--r-sm);color:var(--text);font-family:var(--font-body);font-size:14.5px;outline:none;width:100%;resize:vertical;min-height:100px" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" style="display:inline-flex;align-items:center;gap:10px;background:var(--ink);color:#fff;font-size:14.5px;font-weight:600;padding:13px 22px;border-radius:100px;border:none;cursor:pointer;margin-top:8px">
                        Send Message <span>&rarr;</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────────────────────── -->
<footer style="position:relative;background:var(--ink);color:rgba(255,255,255,.65);padding:80px 0 32px;overflow:hidden">
    <div style="position:absolute;top:-200px;left:50%;transform:translateX(-50%);width:800px;height:400px;background:radial-gradient(ellipse,rgba(184,146,74,.15) 0%,transparent 70%);filter:blur(60px);pointer-events:none"></div>
    <div class="container" style="position:relative;z-index:1">
        <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:64px;align-items:start;padding-bottom:48px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:48px">
            <div style="display:flex;align-items:center;gap:16px">
                <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" style="width:64px;height:64px;border-radius:50%;border:1.5px solid rgba(184,146,74,.35)"/>
                <div>
                    <div style="font-family:var(--font-display);font-size:20px;font-weight:500;color:#fff;margin-bottom:2px">{{ setting('organization_name','Ogun Conference') }}</div>
                    <div style="font-size:11.5px;font-weight:500;letter-spacing:.12em;text-transform:uppercase;color:var(--gold-2)">{{ setting('camp_name','Youth Congress · 2026') }}</div>
                </div>
            </div>
            <p style="font-family:var(--font-script);font-style:italic;font-size:19px;line-height:1.5;color:rgba(255,255,255,.75);max-width:540px">&ldquo;From the Word to the World&rdquo; &mdash; a seven-day gathering of Adventurers, Pathfinders, and Senior Youth in Abeokuta, August 16–22, 2026.</p>
        </div>
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:32px;margin-bottom:56px">
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px">Navigate</div>
                <a href="{{ $baseUrl . '/admin/login' }}" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;transition:color .2s,padding-left .25s" target="_blank">Admin Dashboard</a>
                <a href="#register" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px;transition:color .2s,padding-left .25s">How to Register</a>
                <a href="#fees" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Departments &amp; Fees</a>
                @if($campActive)<a href="#programs" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Programmes</a>@endif
                <a href="#about" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">About the Congress</a>
                <a href="#rules" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Camp Guidelines</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px">Watch &amp; Remember</div>
                <a href="#highlights" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Video highlights</a>
                @if($mediaEnabled)<a href="{{ url('/album') }}" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Photo album</a>@endif
                <a href="{{ $ytChannelUrl }}" target="_blank" rel="noopener" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">YouTube channel ↗</a>
                <a href="#access" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Camper Portal</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px">Reach us</div>
                @if(setting('treasurer_number'))<a href="https://wa.me/{{ preg_replace('/\D/','',setting('treasurer_number')) }}" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Treasurer</a>@endif
                @if(setting('secretariat_phone'))<a href="https://wa.me/{{ preg_replace('/\D/','',setting('secretariat_phone')) }}" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Secretariat</a>@endif
                @if(setting('whatsapp_number'))<a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Technical Support</a>@endif
                <a href="#contact" style="color:rgba(255,255,255,.65);text-decoration:none;font-size:13.5px">Send a message</a>
            </div>
            <div style="display:flex;flex-direction:column;gap:12px">
                <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold-2);margin-bottom:4px">Theme verse</div>
                <p style="font-family:var(--font-script);font-style:italic;font-size:13.5px;line-height:1.5;color:rgba(255,255,255,.6)">&ldquo;Ye shall receive power&hellip; and ye shall be witnesses unto me.&rdquo;</p>
                <p style="font-size:11px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--gold-2)">Acts 1:8</p>
            </div>
        </div>
        <div style="display:flex;justify-content:space-between;align-items:center;padding-top:28px;border-top:1px solid rgba(255,255,255,.06);font-size:12px;color:rgba(255,255,255,.5)">
            <div style="display:flex;align-items:center;gap:10px">
                <span style="color:var(--gold);font-size:14px">&#10022;</span>
                <span>Seventh-day Adventist Church &mdash; Ogun Conference Youth Department &middot; {{ now()->year }}</span>
            </div>
            <div>Designed &amp; Developed by <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" style="color:var(--gold-2);text-decoration:none">Gratus Technologies &middot; 2348163513389</a></div>
        </div>
    </div>
</footer>

<!-- ── CSS Animations ─────────────────────────────────────────────────────── -->
<style>
    @keyframes ringSpin{to{transform:rotate(360deg)}}
    @keyframes twinkle{0%,100%{opacity:.3;transform:scale(.9)}50%{opacity:1;transform:scale(1.1)}}
    .prog-day-btn.prog-day-active{background:var(--ink)!important;color:#fff!important;border-color:var(--ink)!important}
    .yt-card:hover{transform:translateY(-4px);box-shadow:var(--sh-3)}
    .yt-card:hover img{transform:scale(1.04)}
    @media(max-width:960px){
        .section-head-split{grid-template-columns:1fr!important}
        [style*="grid-template-columns:1fr .85fr"]{display:block!important}
        [style*="grid-template-columns:.9fr 1.1fr"]{display:block!important}
        [style*="grid-template-columns:1fr 1.4fr"]{display:block!important}
        [style*="grid-template-columns:repeat(3,1fr)"]{grid-template-columns:1fr!important}
        [style*="grid-template-columns:repeat(4,1fr)"]{grid-template-columns:1fr 1fr!important}
        [style*="grid-template-columns:1fr 1fr"]{grid-template-columns:1fr!important}
    }
</style>

<script>
    /* ── Base utils ── */
    function toggleDrawer(){ document.getElementById('navDrawer').classList.toggle('open'); }
    window.addEventListener('scroll',()=>{ document.getElementById('mainNav').classList.toggle('scrolled',window.scrollY>20); },{passive:true});
    const revealObs=new IntersectionObserver(e=>{e.forEach(x=>{if(x.isIntersecting){x.target.classList.add('visible');revealObs.unobserve(x.target)}});},{threshold:0.08});
    document.querySelectorAll('.reveal').forEach(el=>revealObs.observe(el));
    document.addEventListener('click',e=>{
        const d=document.getElementById('navDrawer'),b=document.getElementById('navBurger');
        if(d&&d.classList.contains('open')&&!d.contains(e.target)&&!b.contains(e.target)) d.classList.remove('open');
    });

    /* ── Countdowns ── */
    (function(){
        const campDate='{{ setting("camp_start_date") }}';
        const regDate='{{ $regCloses ?? "" }}';
        const p=n=>String(Math.max(0,n)).padStart(2,'0');
        function $id(id){return document.getElementById(id);}
        function setCd(pre,target){
            if(!target)return null;
            const diff=new Date(target)-Date.now();
            const d=Math.floor(diff/86400000),h=Math.floor(diff%86400000/3600000),
                m=Math.floor(diff%3600000/60000),s=Math.floor(diff%60000/1000);
            const v=d2=>diff>0?p(d2):'00';
            if($id(pre+'-d'))$id(pre+'-d').textContent=v(d);
            if($id(pre+'-h'))$id(pre+'-h').textContent=v(h);
            if($id(pre+'-m'))$id(pre+'-m').textContent=v(m);
            if($id(pre+'-s'))$id(pre+'-s').textContent=v(s);
            return{d,h,m};
        }
        function tick(){
            if(campDate)setCd('cd',campDate+'T00:00:00');
            if(regDate){
                const r=setCd('rd',regDate);
                const tb=$id('topbar-cd');
                if(tb&&r)tb.textContent=` · ${r.d}d ${p(r.h)}h ${p(r.m)}m`;
            }
        }
        tick();setInterval(tick,1000);
    })();

    /* ── YouTube lightbox ── */
    (function(){
        const modal=document.getElementById('ytModal'),player=document.getElementById('ytModalPlayer'),close=document.getElementById('ytModalClose');
        if(!modal)return;
        function open(id){
            player.innerHTML=`<iframe src="https://www.youtube.com/embed/${id}?autoplay=1&rel=0" title="Highlight" frameborder="0" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen style="width:100%;height:100%;border-radius:14px;border:none"></iframe>`;
            modal.style.display='flex';modal.setAttribute('aria-hidden','false');document.body.style.overflow='hidden';
        }
        function shut(){player.innerHTML='';modal.style.display='none';modal.setAttribute('aria-hidden','true');document.body.style.overflow='';}
        document.querySelectorAll('.yt-card').forEach(c=>c.addEventListener('click',()=>{ if(c.dataset.yt)open(c.dataset.yt); }));
        close&&close.addEventListener('click',shut);
        modal.addEventListener('click',e=>{if(e.target===modal)shut();});
        document.addEventListener('keydown',e=>{if(e.key==='Escape')shut();});
    })();

    /* ── Radio button active styles ── */
    document.querySelectorAll('.fcat input[type=radio]').forEach(r=>{
        r.addEventListener('change',()=>{
            document.querySelectorAll('.fcat span').forEach(s=>s.style.cssText='display:inline-block;padding:9px 16px;background:#fff;border:1px solid var(--hairline-2);border-radius:100px;font-size:13px;font-weight:500;color:var(--text-2)');
            if(r.checked) r.nextElementSibling.style.cssText='display:inline-block;padding:9px 16px;background:var(--ink);color:#fff;border:1px solid var(--ink);border-radius:100px;font-size:13px;font-weight:500';
        });
    });

    @if($campActive)
    /* ── Programme day-selector ── */
    (function(){
        const advDays=@json($advProg['days']);
        const pfDays=@json($pfProg['days']);
        const sylDays=@json($sylProg['days']);
        const panel=document.getElementById('progDayDetail');
        const pills=document.querySelectorAll('.prog-day-btn');
        if(!panel||!pills.length)return;
        const depStyle={
            'Adventurers':'font-family:var(--font-mono);font-size:10.5px;font-weight:600;color:#1B3A8F;background:rgba(27,58,143,.1);padding:2px 10px;border-radius:100px;white-space:nowrap',
            'Pathfinders':'font-family:var(--font-mono);font-size:10.5px;font-weight:600;color:#2D7A3A;background:rgba(45,122,58,.1);padding:2px 10px;border-radius:100px;white-space:nowrap',
            'Senior Youth':'font-family:var(--font-mono);font-size:10.5px;font-weight:600;color:#8B6914;background:rgba(201,169,77,.15);padding:2px 10px;border-radius:100px;white-space:nowrap',
        };
        function paint(i){
            const a=advDays[i]||{},pf=pfDays[i]||{},syl=sylDays[i]||{};
            const rows=[
                {dep:'Adventurers',  ev:(a.events||[])[0]||({}),  theme:a.theme||''},
                {dep:'Pathfinders',  ev:(pf.events||[])[0]||({}), theme:pf.theme||''},
                {dep:'Senior Youth', ev:(syl.events||[])[0]||{},  theme:syl.theme||''},
            ].filter(r=>r.ev&&r.ev.title);
            panel.innerHTML=`
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:24px;margin-bottom:20px;flex-wrap:wrap">
                <div>
                    <div style="font-size:10.5px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);margin-bottom:4px">${a.label||''}</div>
                    <div style="font-family:var(--font-display);font-size:22px;font-weight:500;color:var(--ink);margin-bottom:4px">${a.day_title||''}</div>
                </div>
                <div style="text-align:right">
                    <div style="font-size:10px;font-weight:600;letter-spacing:.14em;text-transform:uppercase;color:var(--muted);margin-bottom:4px">Theme</div>
                    <div style="font-family:var(--font-display);font-size:16px;font-weight:500;color:var(--ink)">${a.theme||''}</div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:1px;background:var(--rule);border:1px solid var(--rule);border-radius:var(--r);overflow:hidden">
                ${rows.map(r=>`
                    <div style="background:#fff;display:grid;grid-template-columns:140px 1fr 120px;gap:12px;padding:12px 16px;align-items:center">
                        <div><span style="${depStyle[r.dep]}">${r.dep}</span></div>
                        <div style="font-size:13.5px;color:var(--text)">${r.ev.title||''}${r.ev.venue?' <span style="color:var(--muted);font-size:12px">· '+r.ev.venue+'</span>':''}</div>
                        <div style="font-family:var(--font-mono);font-size:12px;color:var(--muted);text-align:right">${r.ev.time||''}</div>
                    </div>
                `).join('')}
            </div>`;
        }
        pills.forEach(btn=>{
            btn.addEventListener('click',()=>{
                pills.forEach(b=>{b.classList.remove('prog-day-active');b.style.background='';b.style.color='';b.style.borderColor='';});
                btn.classList.add('prog-day-active');
                btn.style.background='var(--ink)';btn.style.color='#fff';btn.style.borderColor='var(--ink)';
                paint(+btn.dataset.day);
            });
        });
        paint(0);
    })();
    @endif

    /* ── Contact category pill selection ── */
    function selectCat(label) {
        document.querySelectorAll('#contact-cats .cat-pill').forEach(function(pill) {
            pill.style.background  = '#fff';
            pill.style.color       = 'var(--text-2)';
            pill.style.border      = '1px solid var(--hairline-2)';
        });
        var pill = label.querySelector('.cat-pill');
        if (pill) {
            pill.style.background = 'var(--ink)';
            pill.style.color      = '#fff';
            pill.style.border     = '1px solid var(--ink)';
        }
        var radio = label.querySelector('input[type=radio]');
        if (radio) radio.checked = true;
    }
    // Restore selected state on page load (e.g. after validation failure)
    document.addEventListener('DOMContentLoaded', function() {
        var checked = document.querySelector('#contact-cats input[type=radio]:checked');
        if (checked) selectCat(checked.closest('label'));
    });
</script>
</body>
</html>
