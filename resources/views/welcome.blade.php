<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ setting('camp_name', 'Ogun Youth Camp') }} — SDA Ogun Conference</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;900&family=Lato:wght@300;400;700&family=Lato:ital@1&display=swap" rel="stylesheet"/>

    <style>
        :root {
            --navy:   #0B2D6B;
            --navy2:  #1B3A6B;
            --gold:   #C9A94D;
            --gold2:  #F5D060;
            --sky:    #A8C8F0;
            --white:  #FFFFFF;
            --offwhite: #F8F6F0;
            --text:   #1A1A2E;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Lato', sans-serif;
            color: var(--text);
            background: var(--offwhite);
            overflow-x: hidden;
        }

        /* ── Typography ──────────────────────────────────────────────────── */
        .font-display { font-family: 'Cinzel', serif; }

        /* ── Utilities ───────────────────────────────────────────────────── */
        .container { max-width: 1100px; margin: 0 auto; padding: 0 1.5rem; }

        /* ── Navigation ──────────────────────────────────────────────────── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(11,45,107,0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(201,169,77,0.25);
            padding: 0.75rem 1.5rem;
            display: flex; align-items: center; justify-content: space-between;
            transition: background 0.3s;
        }

        .nav-brand {
            display: flex; align-items: center; gap: 0.75rem;
            text-decoration: none;
        }
        .nav-brand img { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; }
        .nav-brand-text { line-height: 1.2; }
        .nav-brand-title {
            font-family: 'Cinzel', serif;
            font-size: 0.8rem; font-weight: 700;
            color: var(--gold2); letter-spacing: 0.08em;
        }
        .nav-brand-sub { font-size: 0.65rem; color: var(--sky); letter-spacing: 0.04em; }

        .nav-links { display: flex; align-items: center; gap: 1.75rem; }
        .nav-links a {
            color: rgba(255,255,255,0.75);
            text-decoration: none; font-size: 0.82rem;
            letter-spacing: 0.06em; transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--gold2); }

        .btn-register {
            background: var(--gold);
            color: var(--navy);
            font-family: 'Cinzel', serif;
            font-size: 0.78rem; font-weight: 700;
            letter-spacing: 0.08em;
            padding: 0.5rem 1.2rem;
            border-radius: 100px;
            text-decoration: none;
            transition: background 0.2s, transform 0.15s;
            border: none; cursor: pointer;
        }
        .btn-register:hover { background: var(--gold2); transform: translateY(-1px); }

        .nav-hamburger {
            display: none; background: none; border: none; cursor: pointer;
            flex-direction: column; gap: 5px; padding: 4px;
        }
        .nav-hamburger span {
            display: block; width: 24px; height: 2px;
            background: var(--gold2); border-radius: 2px;
            transition: 0.3s;
        }

        /* ── Hero ────────────────────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(160deg, #0B1E3D 0%, #1B3A6B 40%, #1E5FAD 70%, #0B2D6B 100%);
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            padding: 7rem 1.5rem 4rem;
            position: relative; overflow: hidden;
        }

        /* Animated globe rings in background */
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% 50%, rgba(201,169,77,0.07) 0%, transparent 70%),
                repeating-linear-gradient(0deg, transparent, transparent 48px, rgba(255,255,255,0.03) 48px, rgba(255,255,255,0.03) 49px),
                repeating-linear-gradient(90deg, transparent, transparent 48px, rgba(255,255,255,0.03) 48px, rgba(255,255,255,0.03) 49px);
            pointer-events: none;
        }

        /* Decorative large globe outline */
        .hero-globe-bg {
            position: absolute;
            width: 620px; height: 620px;
            border-radius: 50%;
            border: 1px solid rgba(201,169,77,0.12);
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            pointer-events: none;
        }
        .hero-globe-bg::before {
            content: '';
            position: absolute; inset: 40px;
            border-radius: 50%;
            border: 1px solid rgba(201,169,77,0.08);
        }
        .hero-globe-bg::after {
            content: '';
            position: absolute; inset: 90px;
            border-radius: 50%;
            border: 1px solid rgba(201,169,77,0.06);
        }

        .hero-logo {
            width: 130px; height: 130px;
            border-radius: 50%;
            border: 3px solid rgba(201,169,77,0.5);
            box-shadow: 0 0 40px rgba(201,169,77,0.25), 0 8px 32px rgba(0,0,0,0.4);
            margin-bottom: 1.5rem;
            position: relative; z-index: 1;
            animation: logoFloat 6s ease-in-out infinite;
        }
        @keyframes logoFloat {
            0%,100% { transform: translateY(0); }
            50%      { transform: translateY(-8px); }
        }

        .hero-badge {
            display: inline-block;
            background: rgba(201,169,77,0.15);
            border: 1px solid rgba(201,169,77,0.4);
            color: var(--gold2);
            font-size: 0.72rem; letter-spacing: 0.15em;
            padding: 0.35rem 1rem;
            border-radius: 100px;
            margin-bottom: 1rem;
            position: relative; z-index: 1;
        }

        .hero-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(2rem, 5vw, 3.8rem);
            font-weight: 900;
            color: var(--white);
            line-height: 1.1;
            letter-spacing: 0.02em;
            margin-bottom: 0.5rem;
            position: relative; z-index: 1;
        }

        .hero-theme {
            font-family: 'Lato', sans-serif;
            font-style: italic;
            font-size: clamp(1rem, 2.5vw, 1.4rem);
            color: var(--gold2);
            margin-bottom: 1rem;
            font-weight: 300;
            position: relative; z-index: 1;
        }

        .hero-venue {
            color: rgba(168,200,240,0.85);
            font-size: 0.9rem; letter-spacing: 0.06em;
            margin-bottom: 2.5rem;
            position: relative; z-index: 1;
        }

        .hero-cta { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; position: relative; z-index: 1; }

        .btn-primary {
            background: var(--gold);
            color: var(--navy);
            font-family: 'Cinzel', serif; font-size: 0.85rem; font-weight: 700;
            letter-spacing: 0.1em;
            padding: 0.85rem 2.2rem; border-radius: 100px;
            text-decoration: none;
            box-shadow: 0 4px 20px rgba(201,169,77,0.4);
            transition: all 0.2s;
            border: none; cursor: pointer;
        }
        .btn-primary:hover { background: var(--gold2); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(201,169,77,0.5); }

        .btn-outline {
            border: 1.5px solid rgba(255,255,255,0.35);
            color: var(--white); font-size: 0.85rem;
            padding: 0.85rem 2.2rem; border-radius: 100px;
            text-decoration: none; transition: all 0.2s;
            letter-spacing: 0.06em;
        }
        .btn-outline:hover { border-color: var(--gold2); color: var(--gold2); }

        /* Countdown */
        .countdown {
            display: flex; gap: 1.5rem; justify-content: center;
            margin-top: 3rem; position: relative; z-index: 1;
        }
        .countdown-unit { text-align: center; }
        .countdown-num {
            font-family: 'Cinzel', serif;
            font-size: 2.2rem; font-weight: 900;
            color: var(--white);
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(201,169,77,0.2);
            border-radius: 12px;
            min-width: 72px; padding: 0.5rem 0.75rem;
            display: block;
        }
        .countdown-label {
            font-size: 0.62rem; letter-spacing: 0.12em;
            color: var(--sky); margin-top: 0.4rem;
            text-transform: uppercase;
        }

        /* Scroll chevron */
        .scroll-hint {
            position: absolute; bottom: 2rem; left: 50%; transform: translateX(-50%);
            color: rgba(255,255,255,0.3); animation: bounce 2s infinite;
            font-size: 1.4rem; cursor: pointer; z-index: 1;
        }
        @keyframes bounce { 0%,100%{transform:translateX(-50%) translateY(0)} 50%{transform:translateX(-50%) translateY(6px)} }

        /* ── Section base ─────────────────────────────────────────────────── */
        section { padding: 5rem 1.5rem; }
        .section-label {
            font-size: 0.7rem; letter-spacing: 0.2em;
            color: var(--gold); font-weight: 700;
            text-transform: uppercase; margin-bottom: 0.6rem;
        }
        .section-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            font-weight: 700; color: var(--navy);
            margin-bottom: 1rem;
        }
        .section-subtitle { color: #555; font-size: 0.95rem; line-height: 1.7; max-width: 560px; }

        /* ── Steps ────────────────────────────────────────────────────────── */
        .steps-section { background: var(--white); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px,1fr));
            gap: 1.5rem; margin-top: 3rem;
        }
        .step-card {
            background: var(--offwhite);
            border: 1px solid rgba(11,45,107,0.08);
            border-radius: 20px; padding: 2rem 1.5rem;
            position: relative; transition: transform 0.2s, box-shadow 0.2s;
        }
        .step-card:hover { transform: translateY(-4px); box-shadow: 0 12px 40px rgba(11,45,107,0.1); }
        .step-num {
            font-family: 'Cinzel', serif;
            font-size: 2.8rem; font-weight: 900;
            color: rgba(11,45,107,0.08);
            position: absolute; top: 1rem; right: 1.25rem;
            line-height: 1;
        }
        .step-icon { font-size: 2rem; margin-bottom: 0.75rem; }
        .step-title { font-family: 'Cinzel', serif; font-size: 0.95rem; font-weight: 700; color: var(--navy); margin-bottom: 0.5rem; }
        .step-desc { font-size: 0.85rem; color: #666; line-height: 1.6; }

        /* Code entry box */
        .code-box {
            background: linear-gradient(135deg, var(--navy) 0%, #1E5FAD 100%);
            border-radius: 24px; padding: 2.5rem;
            text-align: center; margin-top: 3rem;
        }
        .code-box h3 { font-family: 'Cinzel', serif; color: var(--white); font-size: 1.3rem; margin-bottom: 0.5rem; }
        .code-box p  { color: rgba(255,255,255,0.65); font-size: 0.85rem; margin-bottom: 1.5rem; }
        .code-form { display: flex; gap: 0.75rem; max-width: 440px; margin: 0 auto; }
        .code-input {
            flex: 1; padding: 0.85rem 1.2rem;
            border: none; border-radius: 12px;
            font-family: monospace; font-size: 1rem;
            text-align: center; letter-spacing: 0.1em;
            text-transform: uppercase;
            outline: none;
        }
        .code-input:focus { box-shadow: 0 0 0 3px rgba(201,169,77,0.5); }

        /* Bank details */
        .bank-box {
            background: linear-gradient(135deg, #EEF3FC 0%, #F8F6F0 100%);
            border: 1px solid rgba(11,45,107,0.1);
            border-radius: 16px; padding: 1.5rem 2rem;
            margin-top: 2rem;
        }
        .bank-box h3 { font-family: 'Cinzel', serif; font-size: 0.95rem; color: var(--navy); margin-bottom: 1rem; }
        .bank-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; }
        .bank-label { font-size: 0.78rem; color: #888; }
        .bank-value { font-size: 0.88rem; font-weight: 700; color: var(--navy); }
        .bank-account { font-family: monospace; font-size: 1.4rem; font-weight: 900; color: var(--navy2); letter-spacing: 0.08em; }

        /* ── Fees ─────────────────────────────────────────────────────────── */
        .fees-section { background: linear-gradient(180deg, #F8F6F0 0%, #EEF3FC 100%); }
        .fees-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 1.25rem; margin-top: 3rem; }
        .fee-card {
            background: var(--white);
            border-radius: 20px; padding: 2rem 1.5rem;
            text-align: center;
            box-shadow: 0 4px 20px rgba(11,45,107,0.07);
            border-top: 4px solid var(--navy);
            transition: transform 0.2s;
        }
        .fee-card:hover { transform: translateY(-3px); }
        .fee-card.adventurer { border-color: #1E88E5; }
        .fee-card.pathfinder { border-color: #43A047; }
        .fee-card.senior     { border-color: var(--gold); }
        .fee-icon { font-size: 2.2rem; margin-bottom: 0.75rem; }
        .fee-cat  { font-family: 'Cinzel', serif; font-size: 1rem; font-weight: 700; color: var(--navy); }
        .fee-age  { font-size: 0.78rem; color: #888; margin: 0.2rem 0 1rem; }
        .fee-amount { font-family: 'Cinzel', serif; font-size: 2.2rem; font-weight: 900; color: var(--navy); }

        /* ── About ────────────────────────────────────────────────────────── */
        .about-section { background: var(--white); }
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center; }
        .about-logo-wrap {
            display: flex; justify-content: center;
        }
        .about-logo {
            width: 260px; height: 260px;
            border-radius: 50%;
            border: 4px solid rgba(201,169,77,0.3);
            box-shadow: 0 16px 60px rgba(11,45,107,0.18);
            object-fit: cover;
        }
        .about-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 1.5rem; }
        .about-fact {
            background: var(--offwhite);
            border-radius: 12px; padding: 0.9rem 1rem;
            border-left: 3px solid var(--gold);
        }
        .about-fact-label { font-size: 0.68rem; letter-spacing: 0.1em; color: #888; text-transform: uppercase; }
        .about-fact-value { font-family: 'Cinzel', serif; font-size: 0.88rem; font-weight: 700; color: var(--navy); margin-top: 0.2rem; }

        /* ── Rules ────────────────────────────────────────────────────────── */
        .rules-section {
            background: linear-gradient(135deg, var(--navy) 0%, #1E5FAD 100%);
            color: var(--white);
        }
        .rules-section .section-title { color: var(--white); }
        .rules-section .section-label { color: var(--gold2); }
        .rules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); gap: 1rem; margin-top: 2.5rem; }
        .rule-item {
            display: flex; gap: 0.9rem; align-items: flex-start;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px; padding: 1rem 1.2rem;
        }
        .rule-check { color: var(--gold2); font-size: 1.1rem; flex-shrink: 0; margin-top: 1px; }
        .rule-text  { font-size: 0.85rem; color: rgba(255,255,255,0.82); line-height: 1.55; }

        /* ── Contact ──────────────────────────────────────────────────────── */
        .contact-section { background: var(--offwhite); }
        .contact-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap: 1.25rem; margin-top: 2.5rem; }
        .contact-card {
            display: flex; gap: 1rem; align-items: center;
            background: var(--white); border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 2px 12px rgba(11,45,107,0.06);
            text-decoration: none; transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid rgba(11,45,107,0.07);
        }
        .contact-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(11,45,107,0.12); }
        .contact-icon { font-size: 2rem; flex-shrink: 0; }
        .contact-type  { font-family: 'Cinzel', serif; font-size: 0.85rem; font-weight: 700; color: var(--navy); }
        .contact-value { font-size: 0.8rem; color: #666; margin-top: 0.1rem; }

        /* ── Footer ───────────────────────────────────────────────────────── */
        footer {
            background: var(--navy);
            color: rgba(255,255,255,0.55);
            text-align: center; padding: 2rem 1.5rem;
            font-size: 0.8rem; letter-spacing: 0.04em;
        }
        footer .footer-logo {
            width: 52px; height: 52px; border-radius: 50%;
            margin: 0 auto 0.75rem; display: block;
            opacity: 0.85;
        }
        footer .footer-name { color: var(--gold); font-family: 'Cinzel', serif; font-size: 0.88rem; letter-spacing: 0.1em; }

        /* ── Mobile ───────────────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .nav-links { display: none; }
            #contact > .container > div { grid-template-columns: 1fr !important; }
            .nav-links.open { display: flex; flex-direction: column; position: fixed; top: 62px; left: 0; right: 0; background: rgba(11,29,61,0.98); padding: 1.5rem; gap: 1.25rem; border-bottom: 1px solid rgba(201,169,77,0.2); }
            .nav-hamburger { display: flex; }
            .about-grid { grid-template-columns: 1fr; }
            .about-logo { width: 180px; height: 180px; }
            .code-form { flex-direction: column; }
            .countdown { gap: 0.75rem; }
            .countdown-num { min-width: 56px; font-size: 1.6rem; }
        }
    </style>
</head>
<body>

<!-- ── Navigation ──────────────────────────────────────────────────────────── -->
<nav class="nav" id="navbar">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo"/>
        <div class="nav-brand-text">
            <div class="nav-brand-title">Ogun Conference</div>
            <div class="nav-brand-sub">Youth Department &bull; SDA</div>
        </div>
    </a>

    <div class="nav-links" id="navLinks">
        <a href="#about">About</a>
        <a href="#register">How to Register</a>
        <a href="#fees">Fees</a>
        <a href="#programme">Programme</a>
        <a href="#contact">Contact</a>
        <a href="{{ route('registration.index') }}" class="btn-register">Register Now</a>
    </div>

    <button class="nav-hamburger" id="hamburger" onclick="toggleMenu()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- ── Hero ────────────────────────────────────────────────────────────────── -->
<section class="hero" id="home">
    <div class="hero-globe-bg"></div>

    <img src="{{ asset('images/logo.svg') }}" alt="Ogun Youth Camp Logo" class="hero-logo"/>

    <div class="hero-badge">&#10022; {{ now()->year }} Annual Youth Congress &#10022;</div>

    <h1 class="hero-title">
        {{ setting('camp_name', 'Ogun Conference Youth Camp') }}
    </h1>

    @if(setting('camp_theme'))
        <p class="hero-theme">&ldquo;{{ setting('camp_theme') }}&rdquo;</p>
    @endif

    <p class="hero-venue">
        &#128205;&ensp;{{ setting('camp_venue', 'Venue TBA') }}
        &ensp;&bull;&ensp;
        &#128197;&ensp;{{ setting('camp_dates', 'Date TBA') }}
    </p>

    <div class="hero-cta">
        <a href="{{ route('registration.index') }}" class="btn-primary">Register Now</a>
        <a href="#about" class="btn-outline">Learn More</a>
    </div>

    @if(setting('camp_start_date'))
        <div class="countdown" id="countdown">
            <div class="countdown-unit"><span class="countdown-num" id="cd-days">--</span><div class="countdown-label">Days</div></div>
            <div class="countdown-unit"><span class="countdown-num" id="cd-hours">--</span><div class="countdown-label">Hours</div></div>
            <div class="countdown-unit"><span class="countdown-num" id="cd-mins">--</span><div class="countdown-label">Mins</div></div>
            <div class="countdown-unit"><span class="countdown-num" id="cd-secs">--</span><div class="countdown-label">Secs</div></div>
        </div>
    @endif

    <a href="#register" class="scroll-hint">&#8964;</a>
</section>

<!-- ── How to Register ─────────────────────────────────────────────────────── -->
<section class="steps-section" id="register">
    <div class="container">
        <div class="section-label">Registration</div>
        <h2 class="section-title">How to Register</h2>
        <p class="section-subtitle">Four simple steps to secure your place at this year&rsquo;s Youth Congress.</p>

        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">01</div>
                <div class="step-icon">&#127978;</div>
                <div class="step-title">Pay Registration Fee</div>
                <div class="step-desc">Transfer the fee to our bank account below, or pay instantly online via Paystack.</div>
            </div>
            <div class="step-card">
                <div class="step-num">02</div>
                <div class="step-icon">&#128242;</div>
                <div class="step-title">Send Your Receipt</div>
                <div class="step-desc">Send a photo of your payment receipt to {{ setting('whatsapp_number', 'our WhatsApp number') }}.</div>
            </div>
            <div class="step-card">
                <div class="step-num">03</div>
                <div class="step-icon">&#128273;</div>
                <div class="step-title">Receive Your Code</div>
                <div class="step-desc">A unique registration code will be sent to your phone via SMS once payment is confirmed.</div>
            </div>
            <div class="step-card">
                <div class="step-num">04</div>
                <div class="step-icon">&#9989;</div>
                <div class="step-title">Complete Registration</div>
                <div class="step-desc">Enter your code below and fill in the registration form to secure your spot.</div>
            </div>
        </div>

        @if(setting('bank_account_number'))
            <div class="bank-box">
                <h3>&#127978; Bank Transfer Details</h3>
                <div class="bank-row">
                    <span class="bank-label">Bank</span>
                    <span class="bank-value">{{ setting('bank_name') }}</span>
                </div>
                <div class="bank-row">
                    <span class="bank-label">Account Number</span>
                    <span class="bank-account">{{ setting('bank_account_number') }}</span>
                </div>
                <div class="bank-row">
                    <span class="bank-label">Account Name</span>
                    <span class="bank-value">{{ setting('bank_account_name') }}</span>
                </div>
            </div>
        @endif

        <div class="code-box">
            <h3>Already have a registration code?</h3>
            <p>Enter it below to begin filling in your registration form.</p>
            <form class="code-form" action="{{ route('registration.index') }}" method="GET">
                <input type="text" name="code" class="code-input"
                       placeholder="OGN-2026-XXXXXX" maxlength="15"
                       oninput="this.value=this.value.toUpperCase()"/>
                <button type="submit" class="btn-primary" style="white-space:nowrap">Continue &rarr;</button>
            </form>
            @if(setting('paystack_enabled','1') === '1')
                <p style="margin-top:1.2rem;font-size:0.82rem;color:rgba(255,255,255,0.5);">
                    Or &mdash;
                    <a href="{{ route('registration.pay-online') }}"
                       style="color:var(--gold2);text-decoration:none;font-weight:700;">Pay Online via Paystack &#10132;</a>
                </p>
            @endif
        </div>
    </div>
</section>

<!-- ── Fees ────────────────────────────────────────────────────────────────── -->
<section class="fees-section" id="fees">
    <div class="container">
        <div class="section-label">Registration Fees</div>
        <h2 class="section-title">Camp Fees {{ now()->year }}</h2>

        <div class="fees-grid">
            <div class="fee-card adventurer">
                <img src="{{ asset('images/adventurer_logo.png') }}" alt="Adventurer Club"
                     style="width:80px;height:80px;object-fit:contain;margin:0 auto 0.75rem;display:block;"/>
                <div class="fee-cat">Adventurers</div>
                <div class="fee-age">Ages 6 &ndash; 9</div>
                <div class="fee-amount">&#8358;{{ number_format((int) setting('fee_adventurer', 5000)) }}</div>
            </div>
            <div class="fee-card pathfinder">
                <img src="{{ asset('images/pathfinder_logo.png') }}" alt="Pathfinder Club"
                     style="width:80px;height:80px;object-fit:contain;margin:0 auto 0.75rem;display:block;"/>
                <div class="fee-cat">Pathfinders</div>
                <div class="fee-age">Ages 10 &ndash; 15</div>
                <div class="fee-amount">&#8358;{{ number_format((int) setting('fee_pathfinder', 5000)) }}</div>
            </div>
            <div class="fee-card senior">
                <img src="{{ asset('images/senior_youth_logo.png') }}" alt="Senior Youth"
                     style="width:80px;height:80px;object-fit:contain;margin:0 auto 0.75rem;display:block;"/>
                <div class="fee-cat">Senior Youth (SYL)</div>
                <div class="fee-age">Ages 16 and above</div>
                <div class="fee-amount">&#8358;{{ number_format((int) setting('fee_senior_youth', 7000)) }}</div>
            </div>
        </div>
    </div>
</section>

<!-- ── About ───────────────────────────────────────────────────────────────── -->
<section class="about-section" id="about">
    <div class="container">
        <div class="about-grid">
            <div>
                <div class="section-label">About the Camp</div>
                <h2 class="section-title">Inspiring a Generation<br/>of Faithful Youth</h2>
                <p class="section-subtitle">
                    The Ogun Conference Annual Youth Congress brings together Adventurers, Pathfinders,
                    and Senior Youth from churches across the Ogun Conference for a transformative week
                    of spiritual growth, fellowship, skills development, and ministry training.
                </p>
                <div class="about-facts">
                    @foreach([
                        ['Venue',       setting('camp_venue', 'TBA')],
                        ['Dates',       setting('camp_dates', 'TBA')],
                        ['Theme',       setting('camp_theme') ?: 'TBA'],
                        ['Open To',     'Ages 6 and above'],
                    ] as [$label, $value])
                        <div class="about-fact">
                            <div class="about-fact-label">{{ $label }}</div>
                            <div class="about-fact-value">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="about-logo-wrap">
                <img src="{{ asset('images/logo.svg') }}" alt="Camp Logo" class="about-logo"/>
            </div>
        </div>
    </div>
</section>

<!-- ── Programme ───────────────────────────────────────────────────────────── -->
<section id="programme" style="background:var(--offwhite);padding:5rem 1.5rem;">
    <div class="container" style="text-align:center;">
        <div class="section-label">Camp Programme</div>
        <h2 class="section-title" style="margin:0 auto 1rem;">{{ now()->year }} Programme Schedule</h2>
        <p style="color:#777;max-width:480px;margin:0 auto 2.5rem;font-size:0.9rem;line-height:1.7;">
            The detailed programme schedule will be published closer to the camp date.
            Follow our official channels for updates.
        </p>
        <div style="background:var(--white);border:1px solid rgba(11,45,107,0.08);border-radius:20px;padding:3rem;max-width:480px;margin:0 auto;">
            <div style="font-size:3rem;margin-bottom:1rem;">&#128197;</div>
            <p style="color:#aaa;font-style:italic;font-size:0.9rem;">Programme details coming soon.</p>
        </div>
    </div>
</section>

<!-- ── Rules ───────────────────────────────────────────────────────────────── -->
<section class="rules-section">
    <div class="container">
        <div class="section-label">Camp Guidelines</div>
        <h2 class="section-title">Camp Rules &amp; Information</h2>
        <div class="rules-grid">
            @foreach([
                'All campers must carry their printed ID card at all times during camp.',
                'Campers under 18 must submit a signed parental consent form at check-in.',
                'Participants must wear the official camp uniform during formal sessions.',
                'Mobile phones should be kept on silent during services and meetings.',
                'No camper may leave the venue without prior permission from officials.',
                'All campers are expected to participate in the programme respectfully.',
            ] as $rule)
                <div class="rule-item">
                    <span class="rule-check">&#10022;</span>
                    <span class="rule-text">{{ $rule }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── Contact ─────────────────────────────────────────────────────────────── -->
<section class="contact-section" id="contact">
    <div class="container">
        <div class="section-label">Get in Touch</div>
        <h2 class="section-title">Contact Us</h2>
        <p class="section-subtitle">Send us a message for registration help, complaints, inquiries, or payment questions.</p>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-top:2rem;">

            {{-- Contact info --}}
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @if(setting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp_number')) }}"
                       target="_blank" class="contact-card">
                        <div class="contact-icon">&#128172;</div>
                        <div>
                            <div class="contact-type">WhatsApp</div>
                            <div class="contact-value">{{ setting('whatsapp_number') }}</div>
                        </div>
                    </a>
                @endif
                @if(setting('secretariat_phone'))
                    <a href="tel:{{ setting('secretariat_phone') }}" class="contact-card">
                        <div class="contact-icon">&#128222;</div>
                        <div>
                            <div class="contact-type">Secretariat</div>
                            <div class="contact-value">{{ setting('secretariat_phone') }}</div>
                        </div>
                    </a>
                @endif
                <div class="contact-card" style="cursor:default;">
                    <div class="contact-icon">&#127760;</div>
                    <div>
                        <div class="contact-type">SDA Church</div>
                        <div class="contact-value">Ogun Conference Youth Department</div>
                    </div>
                </div>
            </div>

            {{-- Message form --}}
            <div style="background:#fff;border-radius:20px;padding:2rem;box-shadow:0 4px 20px rgba(11,45,107,0.08);border:1px solid rgba(11,45,107,0.07);">

                @if(session('contact_success'))
                    <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:12px;padding:1rem;
                        color:#065F46;font-size:0.85rem;margin-bottom:1.2rem;text-align:center;">
                        &#10003; {{ session('contact_success') }}
                    </div>
                @endif

                <h3 style="font-family:'Cinzel',serif;font-size:1rem;color:var(--navy);margin-bottom:1.2rem;">
                    Send a Message
                </h3>

                <form action="{{ route('contact.store') }}" method="POST" style="display:flex;flex-direction:column;gap:0.85rem;">
                    @csrf
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;">
                        <div>
                            <label style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;
                                      letter-spacing:0.06em;display:block;margin-bottom:0.3rem;">
                                Your Name <span style="color:#DC2626;">*</span>
                            </label>
                            <input type="text" name="sender_name" value="{{ old('sender_name') }}" required
                                   placeholder="Full name"
                                   style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid #E5E7EB;
                                      border-radius:10px;font-size:0.85rem;outline:none;
                                      box-sizing:border-box;font-family:'Lato',sans-serif;"
                                   onfocus="this.style.borderColor='#0B2D6B'"
                                   onblur="this.style.borderColor='#E5E7EB'"/>
                            @error('sender_name')<p style="color:#DC2626;font-size:0.72rem;margin-top:0.2rem;">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;
                                      letter-spacing:0.06em;display:block;margin-bottom:0.3rem;">
                                Phone <span style="color:#DC2626;">*</span>
                            </label>
                            <input type="tel" name="sender_phone" value="{{ old('sender_phone') }}" required
                                   placeholder="08012345678"
                                   style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid #E5E7EB;
                                      border-radius:10px;font-size:0.85rem;outline:none;
                                      box-sizing:border-box;font-family:'Lato',sans-serif;"
                                   onfocus="this.style.borderColor='#0B2D6B'"
                                   onblur="this.style.borderColor='#E5E7EB'"/>
                            @error('sender_phone')<p style="color:#DC2626;font-size:0.72rem;margin-top:0.2rem;">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;
                                  letter-spacing:0.06em;display:block;margin-bottom:0.3rem;">
                            Email <span style="color:#9CA3AF;font-weight:400;">(optional)</span>
                        </label>
                        <input type="email" name="sender_email" value="{{ old('sender_email') }}"
                               placeholder="your@email.com"
                               style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid #E5E7EB;
                                  border-radius:10px;font-size:0.85rem;outline:none;
                                  box-sizing:border-box;font-family:'Lato',sans-serif;"
                               onfocus="this.style.borderColor='#0B2D6B'"
                               onblur="this.style.borderColor='#E5E7EB'"/>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;
                                  letter-spacing:0.06em;display:block;margin-bottom:0.3rem;">
                            Category <span style="color:#DC2626;">*</span>
                        </label>
                        <select name="category" required
                                style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid #E5E7EB;
                                   border-radius:10px;font-size:0.85rem;outline:none;
                                   box-sizing:border-box;background:#fff;font-family:'Lato',sans-serif;"
                                onfocus="this.style.borderColor='#0B2D6B'"
                                onblur="this.style.borderColor='#E5E7EB'">
                            <option value="">— Select —</option>
                            <option value="general"   {{ old('category')==='general'   ? 'selected' : '' }}>General Enquiry</option>
                            <option value="complaint" {{ old('category')==='complaint' ? 'selected' : '' }}>Complaint</option>
                            <option value="inquiry"   {{ old('category')==='inquiry'   ? 'selected' : '' }}>Inquiry</option>
                            <option value="payment"   {{ old('category')==='payment'   ? 'selected' : '' }}>Payment Question</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.72rem;font-weight:700;color:#555;text-transform:uppercase;
                                  letter-spacing:0.06em;display:block;margin-bottom:0.3rem;">
                            Message <span style="color:#DC2626;">*</span>
                        </label>
                        <textarea name="message" required rows="4"
                                  placeholder="Write your message here..."
                                  style="width:100%;padding:0.7rem 0.9rem;border:1.5px solid #E5E7EB;
                                     border-radius:10px;font-size:0.85rem;outline:none;resize:vertical;
                                     box-sizing:border-box;font-family:'Lato',sans-serif;"
                                  onfocus="this.style.borderColor='#0B2D6B'"
                                  onblur="this.style.borderColor='#E5E7EB'">{{ old('message') }}</textarea>
                        @error('message')<p style="color:#DC2626;font-size:0.72rem;margin-top:0.2rem;">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="btn-primary" style="align-self:flex-start;border:none;cursor:pointer;">
                        Send Message &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ── Portal Access ─────────────────────────────────────────────────────────── -->
<section style="background:var(--navy);padding:4rem 1.5rem;" id="portal">
    <div class="container" style="max-width:600px;text-align:center;">
        <div style="font-size:0.7rem;letter-spacing:0.2em;color:var(--gold);font-weight:700;text-transform:uppercase;margin-bottom:0.6rem;">
            Already Registered?
        </div>
        <h2 style="font-family:'Cinzel',serif;font-size:1.8rem;font-weight:700;color:#fff;margin-bottom:0.75rem;">
            Access Your Camper Portal
        </h2>
        <p style="color:rgba(255,255,255,0.65);font-size:0.9rem;margin-bottom:2rem;line-height:1.7;">
            Enter your registration code to view and download your ID card, consent form, and camp announcements.
        </p>
        <form action="{{ route('portal.login') }}" method="POST" style="display:flex;gap:0.75rem;max-width:460px;margin:0 auto;">
            @csrf
            <input type="text" name="code" placeholder="OGN-2026-XXXXXX"
                   style="flex:1;padding:0.9rem 1.2rem;border:none;border-radius:12px;
                      font-family:monospace;font-size:1rem;text-align:center;
                      letter-spacing:0.1em;text-transform:uppercase;outline:none;
                      box-shadow:0 0 0 2px rgba(201,169,77,0.4);"
                   maxlength="15" oninput="this.value=this.value.toUpperCase()"/>
            <button type="submit"
                    style="background:var(--gold);color:var(--navy);font-family:'Cinzel',serif;
                       font-size:0.85rem;font-weight:700;padding:0.9rem 1.5rem;
                       border:none;border-radius:12px;cursor:pointer;white-space:nowrap;
                       transition:background 0.2s;">
                Enter Portal &rarr;
            </button>
        </form>
        @if(session('error'))
            <p style="color:#FCA5A5;font-size:0.82rem;margin-top:0.75rem;">{{ session('error') }}</p>
        @endif
        @if(session('success'))
            <p style="color:#6EE7B7;font-size:0.82rem;margin-top:0.75rem;">{{ session('success') }}</p>
        @endif
    </div>
</section>

<!-- ── Footer ──────────────────────────────────────────────────────────────── -->
<footer>
    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="footer-logo"/>
    <div class="footer-name">Ogun Conference Youth Department</div>
    <p style="margin-top:0.4rem;">Seventh-day Adventist Church &mdash; {{ now()->year }}</p>
    <p style="margin-top:0.25rem;font-size:0.72rem;">
        Secretariat: <a href="tel:{{ setting('secretariat_phone') }}" style="color:var(--gold);text-decoration:none;">{{ setting('secretariat_phone','TBA') }}</a>
    </p>
</footer>

<script>
    // Hamburger
    function toggleMenu() {
        document.getElementById('navLinks').classList.toggle('open');
    }

    // Close menu on link click
    document.querySelectorAll('#navLinks a').forEach(function(a) {
        a.addEventListener('click', function() {
            document.getElementById('navLinks').classList.remove('open');
        });
    });

    @if(setting('camp_start_date'))
    // Countdown
    (function() {
        const target = new Date('{{ setting('camp_start_date') }}T00:00:00');
        function pad(n) { return String(n).padStart(2,'0'); }
        function tick() {
        function tick() {
            const diff = target - new Date();
            if (diff <= 0) {
                ['cd-days','cd-hours','cd-mins','cd-secs'].forEach(function(id){ document.getElementById(id).textContent='00'; });
                return;
            }
            document.getElementById('cd-days').textContent  = pad(Math.floor(diff/86400000));
            document.getElementById('cd-hours').textContent = pad(Math.floor(diff%86400000/3600000));
            document.getElementById('cd-mins').textContent  = pad(Math.floor(diff%3600000/60000));
            document.getElementById('cd-secs').textContent  = pad(Math.floor(diff%60000/1000));
        }
        tick(); setInterval(tick, 1000);
    })();
    @endif
</script>

</body>
</html>
