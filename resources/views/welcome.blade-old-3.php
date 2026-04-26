<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ setting('camp_name','Ogun Conference Youth Congress 2026') }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Open+Sans:wght@300;400;600;700&family=Lato:wght@300;400;700;900&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }

        :root {
            --navy:   #0B2455;
            --navy2:  #071640;
            --blue:   #1B3A8F;
            --blue2:  #2E5FAD;
            --gold:   #C9A94D;
            --gold2:  #E8C255;
            --green:  #2D6A30;
            --light:  #F4F6FB;
            --white:  #FFFFFF;
            --muted:  #6B7A99;
            --border: #E2E8F4;

            /* Blueprint grid color */
            --grid: rgba(27,58,143,0.06);
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: #1E2340;
            background: var(--white);
            overflow-x: hidden;
        }

        /* Blueprint texture utility */
        .blueprint-bg {
            background-color: var(--navy2);
            background-image:
                linear-gradient(rgba(201,169,77,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,169,77,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* Subtle grid on light sections */
        .grid-bg {
            background-color: var(--white);
            background-image:
                linear-gradient(var(--grid) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid) 1px, transparent 1px);
            background-size: 40px 40px;
        }

        /* ── Typography ─────────────────────────────────────────────── */
        .font-display { font-family: 'Cinzel', serif; }
        .section-eyebrow {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.2em;
            text-transform: uppercase; color: var(--gold);
            display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;
        }
        .section-eyebrow::before, .section-eyebrow::after {
            content: ''; flex: 1; max-width: 32px;
            height: 1px; background: var(--gold);
        }
        .section-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(1.5rem, 3vw, 2.1rem);
            font-weight: 700; color: var(--navy);
            line-height: 1.2; margin-bottom: 0.75rem;
        }
        .section-sub {
            font-size: 0.9rem; color: var(--muted);
            line-height: 1.75; max-width: 520px;
        }

        /* ── NAV ─────────────────────────────────────────────────────── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.7rem 2rem;
            background: rgba(7,22,64,0.97);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(201,169,77,0.15);
        }
        .nav-brand { display: flex; align-items: center; gap: 0.8rem; text-decoration: none; }
        .nav-logo   { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 1.5px solid rgba(201,169,77,0.4); }
        .nav-name   { font-family: 'Cinzel', serif; font-size: 0.72rem; color: var(--gold2); letter-spacing: 0.08em; line-height: 1.3; }
        .nav-sub    { font-size: 0.58rem; color: rgba(255,255,255,0.4); letter-spacing: 0.05em; }

        .nav-links  { display: flex; align-items: center; gap: 1.75rem; }
        .nav-links a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 0.78rem; letter-spacing: 0.04em; transition: color 0.2s; }
        .nav-links a:hover { color: var(--gold2); }

        .btn-nav {
            background: var(--gold); color: var(--navy2);
            font-family: 'Cinzel', serif; font-size: 0.7rem; font-weight: 700;
            letter-spacing: 0.1em; padding: 0.48rem 1.1rem; border-radius: 100px;
            text-decoration: none; transition: all 0.2s; border: none; cursor: pointer;
        }
        .btn-nav:hover { background: var(--gold2); transform: translateY(-1px); }

        .nav-burger { display: none; background: none; border: none; cursor: pointer; padding: 4px; }
        .nav-burger span { display: block; width: 22px; height: 2px; background: var(--gold2); border-radius: 2px; margin: 5px 0; }

        /* ── HERO ─────────────────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 8rem 1.5rem 5rem;
            position: relative; overflow: hidden;
        }

        /* Blueprint dark background */
        .hero { background-color: var(--navy2); }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(201,169,77,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,169,77,0.05) 1px, transparent 1px);
            background-size: 52px 52px;
            pointer-events: none;
        }
        /* Spotlight from above */
        .hero::after {
            content: '';
            position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
            width: 700px; height: 700px;
            background: radial-gradient(ellipse, rgba(201,169,77,0.12) 0%, transparent 65%);
            pointer-events: none;
        }

        .hero-inner { position: relative; z-index: 1; max-width: 740px; margin: 0 auto; }

        .hero-logo {
            width: 130px; height: 130px; border-radius: 50%; object-fit: cover;
            border: 3px solid rgba(201,169,77,0.45);
            box-shadow: 0 0 48px rgba(201,169,77,0.18), 0 16px 48px rgba(0,0,0,0.5);
            margin: 0 auto 1.75rem; display: block;
            animation: float 7s ease-in-out infinite;
        }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-10px)} }

        .hero-pill {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(201,169,77,0.12); border: 1px solid rgba(201,169,77,0.3);
            color: var(--gold2); font-size: 0.68rem; font-weight: 700;
            letter-spacing: 0.18em; text-transform: uppercase;
            padding: 0.3rem 0.9rem; border-radius: 100px; margin-bottom: 1.25rem;
        }

        .hero-title {
            font-family: 'Cinzel', serif;
            font-size: clamp(2rem, 5vw, 3.8rem);
            font-weight: 900; color: var(--white);
            line-height: 1.08; letter-spacing: -0.01em;
            margin-bottom: 0.6rem;
        }
        .hero-title em { color: var(--gold2); font-style: normal; }

        .hero-verse {
            font-size: clamp(0.88rem, 1.8vw, 1.05rem);
            font-style: italic; font-weight: 300;
            color: rgba(255,255,255,0.6); margin-bottom: 0.3rem;
        }
        .hero-ref {
            font-family: 'Cinzel', serif; font-size: 0.78rem; color: var(--gold);
            letter-spacing: 0.12em; font-weight: 700; margin-bottom: 2rem;
        }

        .hero-meta {
            display: inline-flex; gap: 1.5rem; align-items: center;
            background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 100px; padding: 0.55rem 1.5rem;
            font-size: 0.78rem; color: rgba(255,255,255,0.6);
            margin-bottom: 2.5rem; flex-wrap: wrap; justify-content: center;
        }
        .hero-meta strong { color: rgba(255,255,255,0.9); }
        .hero-dot { width: 3px; height: 3px; border-radius: 50%; background: rgba(201,169,77,0.5); }

        .hero-cta { display: flex; gap: 0.9rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem; }

        .btn-primary {
            background: var(--gold); color: var(--navy2);
            font-family: 'Cinzel', serif; font-size: 0.78rem; font-weight: 700;
            letter-spacing: 0.1em; padding: 0.85rem 2rem; border-radius: 8px;
            text-decoration: none; border: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(201,169,77,0.35);
            transition: all 0.2s; display: inline-block;
        }
        .btn-primary:hover { background: var(--gold2); transform: translateY(-2px); box-shadow: 0 8px 28px rgba(201,169,77,0.4); }

        .btn-outline-white {
            background: transparent; color: rgba(255,255,255,0.8);
            border: 1.5px solid rgba(255,255,255,0.2);
            font-size: 0.78rem; letter-spacing: 0.06em;
            padding: 0.85rem 2rem; border-radius: 8px;
            text-decoration: none; transition: all 0.2s; display: inline-block;
        }
        .btn-outline-white:hover { border-color: var(--gold); color: var(--gold2); }

        /* Countdown */
        .countdown { display: flex; gap: 0.8rem; justify-content: center; flex-wrap: wrap; }
        .cd-item { text-align: center; }
        .cd-num {
            font-family: 'Cinzel', serif; font-size: 1.8rem; font-weight: 700; color: var(--white);
            background: rgba(255,255,255,0.07); border: 1px solid rgba(201,169,77,0.2);
            border-radius: 8px; padding: 0.4rem 0.7rem; display: block; min-width: 58px; line-height: 1;
        }
        .cd-lbl { font-size: 0.55rem; letter-spacing: 0.14em; color: var(--muted); margin-top: 0.35rem; text-transform: uppercase; }

        /* ── ACCESS — code + portal, light background ────────────────── */
        .access { background: var(--light); padding: 5rem 1.5rem; }
        .access-inner { max-width: 880px; margin: 0 auto; }
        .access-header { text-align: center; margin-bottom: 2.5rem; }
        .access-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

        .access-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: 16px; padding: 2rem;
            box-shadow: 0 2px 16px rgba(11,36,85,0.06);
            transition: box-shadow 0.2s;
        }
        .access-card:hover { box-shadow: 0 6px 32px rgba(11,36,85,0.11); }
        .access-card-icon { font-size: 1.75rem; margin-bottom: 0.75rem; }
        .access-card-title { font-family: 'Cinzel', serif; font-size: 1rem; color: var(--navy); font-weight: 700; margin-bottom: 0.4rem; }
        .access-card-desc  { font-size: 0.8rem; color: var(--muted); line-height: 1.65; margin-bottom: 1.25rem; }

        .code-row { display: flex; gap: 0.5rem; }
        .code-field {
            flex: 1; padding: 0.75rem 1rem;
            background: var(--light); border: 1.5px solid var(--border);
            border-radius: 8px; color: var(--navy);
            font-family: monospace; font-size: 0.88rem;
            text-align: center; letter-spacing: 0.08em; text-transform: uppercase;
            outline: none; transition: border-color 0.2s;
        }
        .code-field::placeholder { color: #B0BAD4; letter-spacing: 0.04em; }
        .code-field:focus { border-color: var(--blue); background: var(--white); }

        .btn-blue {
            background: var(--blue); color: var(--white);
            font-family: 'Cinzel', serif; font-size: 0.72rem; font-weight: 700;
            letter-spacing: 0.08em; padding: 0.75rem 1.1rem;
            border-radius: 8px; border: none; cursor: pointer; white-space: nowrap;
            transition: all 0.2s; text-decoration: none; display: inline-flex; align-items: center;
        }
        .btn-blue:hover { background: var(--navy); transform: translateY(-1px); }

        .access-divider {
            text-align: center; font-size: 0.72rem; color: var(--muted);
            display: flex; align-items: center; gap: 0.75rem; margin-top: 1rem;
        }
        .access-divider::before, .access-divider::after { content:''; flex:1; height:1px; background:var(--border); }

        /* ── HOW TO REGISTER ─────────────────────────────────────────── */
        .how { background: var(--white); padding: 5rem 1.5rem; }
        .how-inner { max-width: 880px; margin: 0 auto; }
        .how-header { margin-bottom: 3rem; }
        .steps { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.25rem; }

        .step {
            padding: 1.75rem 1.25rem; text-align: center;
            background: var(--white); border: 1px solid var(--border);
            border-radius: 14px; position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .step:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(11,36,85,0.09); }
        .step-num {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--navy); color: var(--white);
            font-family: 'Cinzel', serif; font-size: 0.82rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
        }
        .step-icon { font-size: 1.6rem; margin-bottom: 0.6rem; }
        .step-title { font-family: 'Cinzel', serif; font-size: 0.82rem; color: var(--navy); font-weight: 700; margin-bottom: 0.4rem; }
        .step-desc  { font-size: 0.75rem; color: var(--muted); line-height: 1.6; }

        /* Connector arrows between steps (desktop) */
        .step:not(:last-child)::after {
            content: '→';
            position: absolute; right: -14px; top: 50%; transform: translateY(-50%);
            font-size: 1.1rem; color: var(--gold); z-index: 2;
        }

        /* Bank details box */
        .bank-box {
            background: var(--light); border: 1px solid var(--border);
            border-left: 4px solid var(--gold);
            border-radius: 12px; padding: 1.5rem 2rem;
            margin-top: 2.5rem; display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;
        }
        .bank-label { font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-bottom: 0.2rem; }
        .bank-value { font-size: 0.92rem; font-weight: 700; color: var(--navy); }
        .bank-account { font-family: monospace; font-size: 1.5rem; font-weight: 900; color: var(--navy); letter-spacing: 0.05em; }

        /* ── DEPARTMENTS / FEES ───────────────────────────────────────── */
        .fees { background: var(--light); padding: 5rem 1.5rem; }
        .fees-inner { max-width: 880px; margin: 0 auto; }
        .fees-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.25rem; margin-top: 2.5rem; }

        .fee-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 16px; overflow: hidden;
            box-shadow: 0 2px 12px rgba(11,36,85,0.05);
            text-align: center; transition: transform 0.2s, box-shadow 0.2s;
        }
        .fee-card:hover { transform: translateY(-5px); box-shadow: 0 10px 36px rgba(11,36,85,0.12); }
        .fee-top { padding: 2rem 1.5rem 1.25rem; }
        .fee-dept-img { width: 80px; height: 80px; object-fit: contain; margin: 0 auto 1rem; display: block; }
        .fee-name  { font-family: 'Cinzel', serif; font-size: 0.95rem; font-weight: 700; color: var(--navy); }
        .fee-ages  { font-size: 0.72rem; color: var(--muted); margin: 0.2rem 0 1rem; }
        .fee-price { font-family: 'Cinzel', serif; font-size: 2rem; font-weight: 900; color: var(--navy); }
        .fee-foot  { padding: 0.8rem 1.25rem; font-size: 0.72rem; color: var(--muted); border-top: 1px solid var(--border); }
        .fee-bar   { height: 4px; }
        .fee-bar-a { background: linear-gradient(90deg,#1E88E5,#64B5F6); }
        .fee-bar-p { background: linear-gradient(90deg,#2E7D32,#66BB6A); }
        .fee-bar-s { background: linear-gradient(90deg,var(--gold),var(--gold2)); }

        /* ── ABOUT / IDENTITY ─────────────────────────────────────────── */
        .about { background: var(--white); padding: 5rem 1.5rem; }
        .about-inner { max-width: 960px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 4rem; align-items: center; }
        .about-logo-wrap { text-align: center; }
        .about-logo { width: 280px; height: 280px; object-fit: cover; border-radius: 50%; border: 4px solid rgba(201,169,77,0.25); box-shadow: 0 16px 56px rgba(11,36,85,0.15); }

        .about-quote {
            background: linear-gradient(135deg, var(--navy2), var(--blue));
            border-radius: 14px; padding: 1.5rem; margin-top: 1.75rem;
            position: relative; overflow: hidden;
        }
        .about-quote::before { content: '\201C'; position: absolute; top: -10px; left: 12px; font-size: 5rem; color: rgba(201,169,77,0.15); font-family: Georgia,serif; line-height: 1; }
        .about-quote p { font-style: italic; font-size: 0.88rem; color: rgba(255,255,255,0.85); line-height: 1.7; position: relative; z-index: 1; }
        .about-quote cite { display: block; margin-top: 0.5rem; font-size: 0.7rem; font-weight: 700; color: var(--gold); letter-spacing: 0.1em; }

        .about-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem; margin-top: 1.75rem; }
        .fact { background: var(--light); border: 1px solid var(--border); border-radius: 10px; padding: 0.8rem 1rem; border-left: 3px solid var(--gold); }
        .fact-lbl { font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--muted); margin-bottom: 0.2rem; }
        .fact-val  { font-family: 'Cinzel', serif; font-size: 0.8rem; font-weight: 700; color: var(--navy); }

        /* ── RULES ───────────────────────────────────────────────────── */
        .rules { background: var(--light); padding: 5rem 1.5rem; }
        .rules-inner { max-width: 880px; margin: 0 auto; }
        .rules-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px,1fr)); gap: 0.9rem; margin-top: 2.5rem; }
        .rule { display: flex; gap: 0.8rem; align-items: flex-start; background: var(--white); border: 1px solid var(--border); border-radius: 12px; padding: 0.95rem 1.1rem; }
        .rule-icon { color: var(--gold); font-size: 0.9rem; flex-shrink: 0; margin-top: 2px; }
        .rule-text { font-size: 0.8rem; color: #4A5568; line-height: 1.55; }

        /* ── CONTACT ─────────────────────────────────────────────────── */
        .contact { background: var(--white); padding: 5rem 1.5rem; }
        .contact-inner { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.4fr; gap: 3rem; }
        .contact-header { margin-bottom: 1.75rem; }
        .contact-channels { display: flex; flex-direction: column; gap: 0.9rem; }
        .contact-ch {
            display: flex; gap: 0.9rem; align-items: center;
            background: var(--light); border: 1px solid var(--border);
            border-radius: 12px; padding: 1rem 1.2rem; text-decoration: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .contact-ch:hover { border-color: var(--gold); box-shadow: 0 4px 16px rgba(201,169,77,0.12); }
        .contact-ch-icon { font-size: 1.6rem; flex-shrink: 0; }
        .contact-ch-type { font-family: 'Cinzel', serif; font-size: 0.8rem; color: var(--navy); font-weight: 700; }
        .contact-ch-val  { font-size: 0.72rem; color: var(--muted); margin-top: 0.1rem; }

        /* Contact form */
        .cform { background: var(--light); border: 1px solid var(--border); border-radius: 16px; padding: 2rem; }
        .cform h3 { font-family: 'Cinzel', serif; font-size: 0.95rem; color: var(--navy); margin-bottom: 1.25rem; }
        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 0.7rem; margin-bottom: 0.7rem; }
        .fgrp { display: flex; flex-direction: column; gap: 0.3rem; margin-bottom: 0.7rem; }
        .flbl { font-size: 0.62rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.1em; }
        .finput, .fselect, .ftextarea {
            padding: 0.7rem 0.9rem;
            background: var(--white); border: 1.5px solid var(--border);
            border-radius: 8px; color: var(--navy);
            font-family: 'Open Sans', sans-serif; font-size: 0.84rem;
            outline: none; transition: border-color 0.2s; width: 100%;
        }
        .finput:focus, .fselect:focus, .ftextarea:focus { border-color: var(--blue); }
        .finput::placeholder, .ftextarea::placeholder { color: #B0BAD4; }
        .ftextarea { resize: vertical; min-height: 90px; }
        .flash-ok { background: #F0FDF4; border: 1px solid #6EE7B7; border-radius: 8px; padding: 0.7rem 0.9rem; color: #065F46; font-size: 0.8rem; margin-bottom: 1rem; }

        /* ── FOOTER ──────────────────────────────────────────────────── */
        footer {
            background: var(--navy2);
            background-image: linear-gradient(rgba(201,169,77,0.04) 1px, transparent 1px),
            linear-gradient(90deg, rgba(201,169,77,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            color: rgba(255,255,255,0.5);
            text-align: center; padding: 2.5rem 1.5rem; font-size: 0.75rem;
        }
        .footer-logo { height: 52px; width: 52px; border-radius: 50%; object-fit: cover; margin: 0 auto 0.75rem; display: block; border: 1.5px solid rgba(201,169,77,0.3); opacity: 0.9; }
        .footer-name { font-family: 'Cinzel', serif; color: var(--gold); font-size: 0.82rem; letter-spacing: 0.1em; margin-bottom: 0.25rem; }

        /* ── RESPONSIVE ──────────────────────────────────────────────── */
        @media (max-width: 900px) {
            .steps { grid-template-columns: 1fr 1fr; }
            .step:not(:last-child)::after { display: none; }
            .about-inner, .contact-inner { grid-template-columns: 1fr; }
            .about-logo { width: 200px; height: 200px; }
            .fees-grid { grid-template-columns: 1fr 1fr; }
            .access-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 640px) {
            .nav-links { display: none; }
            .nav-links.open { display: flex; flex-direction: column; position: fixed; top: 62px; left: 0; right: 0; background: rgba(7,22,64,0.98); padding: 1.5rem; gap: 1rem; border-bottom: 1px solid rgba(201,169,77,0.15); z-index: 999; }
            .nav-burger { display: block; }
            .steps { grid-template-columns: 1fr; }
            .fees-grid { grid-template-columns: 1fr; }
            .frow { grid-template-columns: 1fr; }
            .bank-box { flex-direction: column; gap: 1rem; }
        }
    </style>
</head>
<body>

<!-- ── NAV ─────────────────────────────────────────────────────────────── -->
<nav class="nav">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="nav-logo"/>
        <div>
            <div class="nav-name">Ogun Conference</div>
            <div class="nav-sub">Youth Congress 2026 · SDA</div>
        </div>
    </a>
    <div class="nav-links" id="navLinks">
        <a href="#register">How to Register</a>
        <a href="#fees">Departments</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="#access" class="btn-nav">Enter Code</a>
    </div>
    <button class="nav-burger" onclick="toggleNav()"><span></span><span></span><span></span></button>
</nav>

<!-- ── HERO ─────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-inner">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Congress 2026 Logo" class="hero-logo"/>

        <div class="hero-pill">✦ Abeokuta 2026 &nbsp;&bull;&nbsp; Aug 16–22 ✦</div>

        <h1 class="hero-title">
            From the Word<br/><em>to the World</em>
        </h1>

        <p class="hero-verse">&ldquo;Ye shall receive power &hellip; and ye shall be witnesses unto me, both in Jerusalem, and in all Judaea, and in Samaria, and unto the uttermost part of the earth.&rdquo;</p>
        <p class="hero-ref">Acts 1:8 &nbsp;&middot;&nbsp; Ogun Conference Youth Congress</p>

        <div class="hero-meta">
            <span>&#128197; <strong>August 16–22, 2026</strong></span>
            <span class="hero-dot"></span>
            <span>&#128205; <strong>{{ setting('camp_venue','Abeokuta, Ogun State') }}</strong></span>
        </div>

        <div class="hero-cta">
            <a href="#access" class="btn-primary">I Have a Code &rarr;</a>
            <a href="#register" class="btn-outline-white">How It Works</a>
        </div>

        @if(setting('camp_start_date'))
            <div class="countdown">
                <div class="cd-item"><span class="cd-num" id="cd-d">--</span><div class="cd-lbl">Days</div></div>
                <div class="cd-item" style="line-height:3.2rem;color:var(--gold);font-size:1.5rem;opacity:0.5">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-h">--</span><div class="cd-lbl">Hours</div></div>
                <div class="cd-item" style="line-height:3.2rem;color:var(--gold);font-size:1.5rem;opacity:0.5">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-m">--</span><div class="cd-lbl">Mins</div></div>
                <div class="cd-item" style="line-height:3.2rem;color:var(--gold);font-size:1.5rem;opacity:0.5">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-s">--</span><div class="cd-lbl">Secs</div></div>
            </div>
        @endif
    </div>
</section>

<!-- ── ACCESS — Code Entry + Camper Portal (prominent, second section) ── -->
<section class="access" id="access">
    <div class="access-inner">
        <div class="access-header">
            <div class="section-eyebrow" style="justify-content:center">Your Gateway</div>
            <h2 class="section-title" style="text-align:center">Access Your Registration</h2>
            <p class="section-sub" style="text-align:center;margin:0 auto">
                Enter your registration code to complete your details, or log into your camper portal to download your ID card and documents.
            </p>
        </div>
        <div class="access-grid">

            <div class="access-card">
                <div class="access-card-icon">&#128273;</div>
                <div class="access-card-title">Complete Registration</div>
                <p class="access-card-desc">
                    Have a code from your church coordinator? Enter it here to fill in your personal details and complete your registration.
                </p>
                <form action="{{ route('registration.validate-code-web') }}" method="POST">
                    @csrf
                    <div class="code-row">
                        <input type="text" name="code" class="code-field"
                               placeholder="OGN-2026-XXXXXX" maxlength="15"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')"
                               autocomplete="off" spellcheck="false"/>
                        <button type="submit" class="btn-blue">Go &rarr;</button>
                    </div>
                </form>
                @if(session('error') && !session('portal_error'))
                    <p style="color:#E53E3E;font-size:0.73rem;margin-top:0.6rem">{{ session('error') }}</p>
                @endif
            </div>

            <div class="access-card">
                <div class="access-card-icon">&#127823;</div>
                <div class="access-card-title">Camper Portal</div>
                <p class="access-card-desc">
                    Already registered? Access your camper portal to download your ID card, consent form, and view camp announcements.
                </p>
                <form action="{{ route('portal.login') }}" method="POST">
                    @csrf
                    <div class="code-row">
                        <input type="text" name="code" class="code-field"
                               placeholder="OGN-2026-XXXXXX" maxlength="15"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')"
                               autocomplete="off" spellcheck="false"/>
                        <button type="submit" class="btn-blue">Enter &rarr;</button>
                    </div>
                </form>
                @if(session('portal_error'))
                    <p style="color:#E53E3E;font-size:0.73rem;margin-top:0.6rem">{{ session('portal_error') }}</p>
                @endif
                <div class="access-divider" style="margin-top:1rem">Same code used to register</div>
            </div>

        </div>
    </div>
</section>

<!-- ── HOW TO REGISTER ───────────────────────────────────────────────────── -->
<section class="how" id="register">
    <div class="how-inner">
        <div class="how-header">
            <div class="section-eyebrow">Registration</div>
            <h2 class="section-title">How to Register</h2>
            <p class="section-sub">
                Registration is coordinated through your local church. Your church coordinator handles payment and code generation on behalf of your congregation.
            </p>
        </div>

        <div class="steps">
            @foreach([
                ['1','&#127978;','Contact Your Coordinator','Reach your local church coordinator to express interest and confirm your department.'],
                ['2','&#127982;','Church Makes Payment','The coordinator pays the total for all registered campers via bank transfer or Paystack.'],
                ['3','&#128273;','Receive Your Code','A unique code is sent to your phone via SMS once payment is confirmed.'],
                ['4','&#10003;','Complete Your Form','Enter your code here and fill in the registration wizard to secure your spot.'],
            ] as [$n,$icon,$t,$d])
                <div class="step">
                    <div class="step-num">{{ $n }}</div>
                    <div class="step-icon">{!! $icon !!}</div>
                    <div class="step-title">{{ $t }}</div>
                    <div class="step-desc">{{ $d }}</div>
                </div>
            @endforeach
        </div>

        @if(setting('bank_account_number'))
            <div class="bank-box">
                <div>
                    <div class="bank-label">Bank</div>
                    <div class="bank-value">{{ setting('bank_name') }}</div>
                </div>
                <div>
                    <div class="bank-label">Account Number</div>
                    <div class="bank-account">{{ setting('bank_account_number') }}</div>
                </div>
                <div>
                    <div class="bank-label">Account Name</div>
                    <div class="bank-value">{{ setting('bank_account_name') }}</div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ── DEPARTMENTS / FEES ─────────────────────────────────────────────────── -->
<section class="fees" id="fees">
    <div class="fees-inner">
        <div class="section-eyebrow">Departments</div>
        <h2 class="section-title">Camp Fees {{ now()->year }}</h2>
        <p class="section-sub">Fees are set per department based on age group. Contact your church coordinator to register.</p>

        <div class="fees-grid">
            <div class="fee-card">
                <div class="fee-bar fee-bar-a"></div>
                <div class="fee-top">
                    <img src="{{ asset('images/adventurer_logo.png') }}" alt="Adventurer Club" class="fee-dept-img"/>
                    <div class="fee-name">Adventurers</div>
                    <div class="fee-ages">Ages 6 &ndash; 9</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_adventurer',5000)) }}</div>
                </div>
                <div class="fee-foot">Parent/guardian must accompany. Consent form required.</div>
            </div>
            <div class="fee-card">
                <div class="fee-bar fee-bar-p"></div>
                <div class="fee-top">
                    <img src="{{ asset('images/pathfinder_logo.png') }}" alt="Pathfinder Club" class="fee-dept-img"/>
                    <div class="fee-name">Pathfinders</div>
                    <div class="fee-ages">Ages 10 &ndash; 15</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_pathfinder',5000)) }}</div>
                </div>
                <div class="fee-foot">Consent form required for all Pathfinders.</div>
            </div>
            <div class="fee-card">
                <div class="fee-bar fee-bar-s"></div>
                <div class="fee-top">
                    <img src="{{ asset('images/senior_youth_logo.png') }}" alt="Senior Youth" class="fee-dept-img"/>
                    <div class="fee-name">Senior Youth (SYL)</div>
                    <div class="fee-ages">Ages 16 and above</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_senior_youth',7000)) }}</div>
                </div>
                <div class="fee-foot">Ambassador (16–21) &bull; Young Adults (22+)</div>
            </div>
        </div>
    </div>
</section>

<!-- ── ABOUT / IDENTITY ────────────────────────────────────────────────────── -->
<section class="about" id="about">
    <div class="about-inner">
        <div>
            <div class="section-eyebrow">About the Congress</div>
            <h2 class="section-title">A Visual Manifesto<br/>for Ogun's Youth</h2>
            <p class="section-sub" style="max-width:100%">
                The Ogun Conference Annual Youth Congress gathers Adventurers, Pathfinders, and Senior Youth from churches across the conference for a transformative week of spiritual growth, fellowship, and missionary training.
            </p>

            <div class="about-quote">
                <p>&ldquo;Spiritual empowerment is not for ourselves alone, but for the world. The power received in Acts 1:8 is a mandate to move outward.&rdquo;</p>
                <cite>&#8212; 2026 Congress Visual Identity</cite>
            </div>

            <div class="about-facts">
                @foreach([['Venue',setting('camp_venue','TBA')],['Dates',setting('camp_dates','Aug 16–22, 2026')],['Theme',setting('camp_theme','From the Word to the World')],['Open To','Ages 6 and above']] as [$l,$v])
                    <div class="fact">
                        <div class="fact-lbl">{{ $l }}</div>
                        <div class="fact-val">{{ $v }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="about-logo-wrap">
            <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="about-logo"/>
            <p style="font-size:0.68rem;color:var(--muted);margin-top:1rem;font-style:italic">
                Logo designed by Master Guide Chrisadim Emmanuel
            </p>
        </div>
    </div>
</section>

<!-- ── CAMP RULES ─────────────────────────────────────────────────────────── -->
<section class="rules">
    <div class="rules-inner">
        <div class="section-eyebrow">Guidelines</div>
        <h2 class="section-title">Camp Rules</h2>
        <div class="rules-grid">
            @foreach(['All campers must carry their printed ID card at all times during camp.','Campers under 18 must submit a signed parental consent form at check-in.','Participants must wear the official camp uniform during formal sessions.','Mobile phones should be kept on silent during services and meetings.','No camper may leave the camp venue without prior permission from officials.','All campers are expected to participate in the programme respectfully.'] as $rule)
                <div class="rule">
                    <span class="rule-icon">&#10022;</span>
                    <span class="rule-text">{{ $rule }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── CONTACT ──────────────────────────────────────────────────────────────── -->
<section class="contact" id="contact">
    <div class="contact-inner">
        <div>
            <div class="contact-header">
                <div class="section-eyebrow">Get in Touch</div>
                <h2 class="section-title">Contact Us</h2>
                <p class="section-sub">For enquiries, complaints, or payment questions.</p>
            </div>
            <div class="contact-channels">
                @if(setting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" target="_blank" class="contact-ch">
                        <div class="contact-ch-icon">&#128172;</div>
                        <div><div class="contact-ch-type">WhatsApp</div><div class="contact-ch-val">{{ setting('whatsapp_number') }}</div></div>
                    </a>
                @endif
                @if(setting('secretariat_phone'))
                    <a href="tel:{{ setting('secretariat_phone') }}" class="contact-ch">
                        <div class="contact-ch-icon">&#128222;</div>
                        <div><div class="contact-ch-type">Secretariat</div><div class="contact-ch-val">{{ setting('secretariat_phone') }}</div></div>
                    </a>
                @endif
                <div class="contact-ch" style="cursor:default">
                    <div class="contact-ch-icon">&#127776;</div>
                    <div><div class="contact-ch-type">Seventh-day Adventist</div><div class="contact-ch-val">Ogun Conference Youth Department</div></div>
                </div>
            </div>
        </div>

        <div class="cform">
            <h3>Send a Message</h3>
            @if(session('contact_success'))
                <div class="flash-ok">&#10003; {{ session('contact_success') }}</div>
            @endif
            <form action="{{ route('contact.store') }}" method="POST">
                @csrf
                <div class="frow">
                    <div class="fgrp">
                        <label class="flbl">Your Name *</label>
                        <input type="text" name="sender_name" class="finput" placeholder="Full name" required value="{{ old('sender_name') }}"/>
                    </div>
                    <div class="fgrp">
                        <label class="flbl">Phone *</label>
                        <input type="tel" name="sender_phone" class="finput" placeholder="08012345678" required value="{{ old('sender_phone') }}"/>
                    </div>
                </div>
                <div class="fgrp">
                    <label class="flbl">Email (optional)</label>
                    <input type="email" name="sender_email" class="finput" placeholder="your@email.com" value="{{ old('sender_email') }}"/>
                </div>
                <div class="fgrp">
                    <label class="flbl">Category *</label>
                    <select name="category" class="fselect" required>
                        <option value="">— Select —</option>
                        <option value="general" {{ old('category')==='general'?'selected':'' }}>General Enquiry</option>
                        <option value="complaint" {{ old('category')==='complaint'?'selected':'' }}>Complaint</option>
                        <option value="inquiry" {{ old('category')==='inquiry'?'selected':'' }}>Inquiry</option>
                        <option value="payment" {{ old('category')==='payment'?'selected':'' }}>Payment Question</option>
                    </select>
                </div>
                <div class="fgrp">
                    <label class="flbl">Message *</label>
                    <textarea name="message" class="ftextarea" placeholder="Write your message..." required>{{ old('message') }}</textarea>
                </div>
                @if(config('services.recaptcha.site_key'))
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}" style="margin-bottom:0.75rem"></div>
                @endif
                <button type="submit" class="btn-primary" style="font-size:0.78rem;border:none;cursor:pointer">Send Message &rarr;</button>
            </form>
        </div>
    </div>
</section>

<!-- ── FOOTER ──────────────────────────────────────────────────────────────── -->
<footer>
    <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="footer-logo"/>
    <div class="footer-name">Ogun Conference Youth Department</div>
    <p>Seventh-day Adventist Church &bull; {{ now()->year }}</p>
    @if(setting('secretariat_phone'))
        <p style="margin-top:0.5rem">Secretariat: <a href="tel:{{ setting('secretariat_phone') }}" style="color:var(--gold);text-decoration:none">{{ setting('secretariat_phone') }}</a></p>
    @endif
</footer>

@if(config('services.recaptcha.site_key'))
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endif

<script>
    function toggleNav() { document.getElementById('navLinks').classList.toggle('open'); }
    document.querySelectorAll('#navLinks a').forEach(a => a.addEventListener('click', () => document.getElementById('navLinks').classList.remove('open')));

    @if(setting('camp_start_date'))
    (function(){
        const t = new Date('{{ setting('camp_start_date') }}T00:00:00');
        const p = n => String(n).padStart(2,'0');
        function tick(){
            const d = t - new Date();
            if(d<=0) return;
            document.getElementById('cd-d').textContent = p(Math.floor(d/86400000));
            document.getElementById('cd-h').textContent = p(Math.floor(d%86400000/3600000));
            document.getElementById('cd-m').textContent = p(Math.floor(d%3600000/60000));
            document.getElementById('cd-s').textContent = p(Math.floor(d%60000/1000));
        }
        tick(); setInterval(tick,1000);
    })();
    @endif
</script>
</body>
</html>
