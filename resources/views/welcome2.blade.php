<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ setting('camp_name','Ogun Conference Youth Congress 2026') }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,700;0,900;1,500;1,700&display=swap" rel="stylesheet"/>

    <style>
        /* ═══════════════════════════════════════════════════════════════
           Ogun Conference Youth Congress 2026 — Editorial Design System
           ─────────────────────────────────────────────────────────────── */

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; font-size: 16px; -webkit-text-size-adjust: 100%; }

        :root {
            --ink:        #0A1832;
            --ink-2:      #142547;
            --ink-3:      #1F3360;

            --gold:       #B8924A;
            --gold-2:     #D4B26E;
            --gold-3:     #EBD9A8;
            --gold-soft:  rgba(184,146,74,0.10);

            --cream:      #F7F3EA;
            --cream-2:    #EFE8D7;
            --paper:      #FBF8F1;
            --paper-2:    #FDFBF6;

            --text:       #1A2238;
            --text-2:     #4A5468;
            --muted:      #7A8499;
            --hairline:   rgba(10,24,50,0.08);
            --hairline-2: rgba(10,24,50,0.14);
            --rule:       #E7DFC9;

            --sh-1:       0 1px 2px rgba(10,24,50,0.04);
            --sh-2:       0 4px 16px rgba(10,24,50,0.06), 0 1px 2px rgba(10,24,50,0.04);
            --sh-3:       0 16px 48px -8px rgba(10,24,50,0.12), 0 4px 12px rgba(10,24,50,0.06);
            --sh-gold:    0 8px 28px -4px rgba(184,146,74,0.45);
            --sh-ink:     0 12px 40px -8px rgba(10,24,50,0.4);

            --r-sm: 8px;
            --r:    14px;
            --r-lg: 22px;
            --r-xl: 32px;

            --font-display: 'Fraunces', 'Playfair Display', Georgia, serif;
            --font-script:  'Instrument Serif', Georgia, serif;
            --font-body:    'DM Sans', -apple-system, BlinkMacSystemFont, system-ui, sans-serif;
            --font-mono:    ui-monospace, 'SF Mono', Menlo, monospace;
        }

        body {
            font-family: var(--font-body);
            color: var(--text);
            background: var(--paper);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
            line-height: 1.55;
        }

        img { max-width: 100%; display: block; }
        a { color: inherit; }
        button { font: inherit; cursor: pointer; }

        .container { max-width: 1240px; margin: 0 auto; padding: 0 32px; }
        @media (max-width: 640px) { .container { padding: 0 20px; } }

        /* ── Eyebrow & section heads ─────────────────────────────────── */
        .eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            font-family: var(--font-body);
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.16em; text-transform: uppercase;
            color: var(--ink-3);
            padding: 4px 0;
        }
        .eyebrow-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: var(--gold);
            box-shadow: 0 0 0 4px rgba(184,146,74,0.12);
            display: inline-block;
        }

        .section-head { max-width: 760px; margin-bottom: 56px; }
        .section-head-center { text-align: center; margin: 0 auto 64px; }
        .section-head-center .section-title,
        .section-head-center .section-lede { margin-left: auto; margin-right: auto; }
        .section-head-center .eyebrow { margin: 0 auto 18px; }

        .section-head-split {
            display: grid; grid-template-columns: 1.2fr 1fr; gap: 80px;
            align-items: end; max-width: none; margin-bottom: 64px;
        }

        .section-title {
            font-family: var(--font-display);
            font-size: clamp(2.2rem, 4.4vw, 3.5rem);
            font-weight: 500; letter-spacing: -0.025em;
            line-height: 1.04; color: var(--ink);
            margin: 18px 0 22px;
            text-wrap: balance;
        }
        .section-title em {
            font-style: italic; font-weight: 400;
            color: var(--gold);
            font-family: var(--font-script);
        }
        .section-lede {
            font-size: 1.05rem; color: var(--text-2);
            line-height: 1.65; max-width: 580px;
        }

        /* ── Reveal animation ─────────────────────────────────────────── */
        .reveal { opacity: 0; transform: translateY(20px); transition: opacity 0.6s, transform 0.6s; }
        .reveal.visible { opacity: 1; transform: translateY(0); }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ── Topbar ───────────────────────────────────────────────────── */
        .topbar {
            background: var(--ink);
            color: rgba(255,255,255,0.85);
            font-size: 12.5px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            position: relative; z-index: 1001;
        }
        .topbar-inner {
            max-width: 1240px; margin: 0 auto;
            padding: 9px 32px;
            display: flex; align-items: center; justify-content: center;
            gap: 12px; flex-wrap: wrap;
        }
        .topbar-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: #5ED48A;
            box-shadow: 0 0 0 3px rgba(94,212,138,0.18);
            animation: pulse 2.4s infinite;
            flex-shrink: 0;
        }
        @keyframes pulse {
            0%,100% { box-shadow: 0 0 0 3px rgba(94,212,138,0.18); }
            50%     { box-shadow: 0 0 0 6px rgba(94,212,138,0); }
        }
        .topbar-sep { color: rgba(255,255,255,0.3); }
        .topbar a { color: var(--gold-2); text-decoration: none; font-weight: 500; transition: color 0.2s; }
        .topbar a:hover { color: var(--gold-3); }
        @media (max-width: 640px) {
            .topbar-inner { padding: 8px 16px; font-size: 11.5px; }
            .topbar-sep { display: none; }
        }

        /* ── Nav ──────────────────────────────────────────────────────── */
        .nav {
            position: sticky; top: 0; z-index: 1000;
            display: flex; align-items: center;
            padding: 12px 32px;
            background: rgba(251,248,241,0.7);
            backdrop-filter: saturate(180%) blur(20px);
            -webkit-backdrop-filter: saturate(180%) blur(20px);
            border-bottom: 1px solid transparent;
            transition: background 0.3s, border-color 0.3s, padding 0.3s;
        }
        .nav.scrolled {
            background: rgba(251,248,241,0.94);
            border-bottom-color: var(--hairline);
            padding: 8px 32px;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 12px;
            text-decoration: none; flex-shrink: 0; margin-right: auto;
        }
        .nav-logo {
            width: 40px; height: 40px; border-radius: 50%;
            border: 1.5px solid rgba(184,146,74,0.35);
            transition: transform 0.3s, border-color 0.2s;
        }
        .nav-brand:hover .nav-logo { transform: rotate(-6deg); border-color: var(--gold); }
        .nav-brand-text { line-height: 1.2; }
        .nav-name { font-family: var(--font-display); font-size: 14px; font-weight: 600; color: var(--ink); }
        .nav-sub { font-size: 10.5px; color: var(--muted); letter-spacing: 0.08em; text-transform: uppercase; font-weight: 500; }

        .nav-links { display: flex; align-items: center; gap: 28px; margin: 0 32px; }
        .nav-links a {
            color: var(--text-2); text-decoration: none;
            font-size: 13.5px; font-weight: 500;
            position: relative; transition: color 0.2s;
        }
        .nav-links a::after {
            content: ''; position: absolute; bottom: -6px; left: 0; right: 0;
            height: 1.5px; background: var(--gold);
            transform: scaleX(0); transform-origin: left; transition: transform 0.3s;
        }
        .nav-links a:hover { color: var(--ink); }
        .nav-links a:hover::after { transform: scaleX(1); }

        .nav-cta { display: flex; align-items: center; gap: 10px; }
        .btn-nav-ghost {
            font-size: 13px; font-weight: 500; text-decoration: none;
            color: var(--text-2); padding: 9px 14px; border-radius: 100px;
            transition: color 0.2s, background 0.2s;
        }
        .btn-nav-ghost:hover { color: var(--ink); background: var(--cream-2); }
        .btn-nav {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--ink); color: #fff;
            font-size: 13px; font-weight: 600;
            padding: 9px 16px; border-radius: 100px; text-decoration: none;
            transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
        }
        .btn-nav .arr { transition: transform 0.25s; }
        .btn-nav:hover { background: var(--ink-2); box-shadow: var(--sh-ink); transform: translateY(-1px); }
        .btn-nav:hover .arr { transform: translateX(3px); }

        .nav-burger { display: none; flex-direction: column; gap: 5px; background: none; border: none; padding: 8px; margin-left: auto; }
        .nav-burger span { width: 22px; height: 1.5px; background: var(--ink); border-radius: 2px; transition: all 0.3s; }

        .nav-drawer {
            position: fixed; top: 0; left: 0; right: 0; z-index: 999;
            background: rgba(251,248,241,0.98); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--hairline);
            max-height: 0; overflow: hidden; transition: max-height 0.35s ease;
        }
        .nav-drawer.open { max-height: 460px; }
        .nav-drawer-inner { display: flex; flex-direction: column; padding: 72px 32px 24px; }
        .nav-drawer a {
            color: var(--text); text-decoration: none; font-size: 16px; font-weight: 500;
            padding: 14px 0; border-bottom: 1px solid var(--hairline);
        }
        .nav-drawer a:last-child { border: none; }
        .drawer-cta { color: var(--gold) !important; font-weight: 700 !important; }

        @media (max-width: 960px) {
            .nav-links, .nav-cta { display: none; }
            .nav-burger { display: flex; }
            .nav { padding: 12px 20px; }
        }

        /* ── Hero ─────────────────────────────────────────────────────── */
        .hero {
            position: relative; background: var(--ink); color: #fff;
            padding: 80px 32px 0; overflow: hidden;
        }
        .hero-bg { position: absolute; inset: 0; pointer-events: none; overflow: hidden; }
        .hero-glow { position: absolute; border-radius: 50%; filter: blur(100px); }
        .hero-glow-1 {
            width: 700px; height: 700px; top: -150px; left: -200px;
            background: radial-gradient(circle, rgba(184,146,74,0.25) 0%, transparent 70%);
        }
        .hero-glow-2 {
            width: 800px; height: 800px; bottom: -300px; right: -200px;
            background: radial-gradient(circle, rgba(31,51,96,0.7) 0%, transparent 70%);
        }
        .hero-grain {
            position: absolute; inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255,255,255,0.015) 1px, transparent 1px),
                radial-gradient(circle at 80% 70%, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 40px 40px, 30px 30px; opacity: 0.6;
        }

        .hero-container {
            max-width: 1240px; margin: 0 auto;
            display: grid; grid-template-columns: 1.05fr 0.95fr;
            gap: 80px; align-items: center;
            padding: 60px 0 100px; position: relative; z-index: 2;
        }

        .hero-left { max-width: 600px; }

        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(184,146,74,0.25);
            padding: 7px 16px; border-radius: 100px;
            font-size: 12px; font-weight: 500; color: rgba(255,255,255,0.85);
            letter-spacing: 0.04em; backdrop-filter: blur(10px); margin-bottom: 32px;
        }
        .hero-eyebrow-dot {
            width: 6px; height: 6px; border-radius: 50%; background: var(--gold-2);
            box-shadow: 0 0 12px rgba(212,178,110,0.6);
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: clamp(2.8rem, 6.2vw, 5.2rem);
            font-weight: 400; line-height: 0.98; letter-spacing: -0.035em;
            color: #fff; margin-bottom: 28px;
        }
        .hero-title em {
            font-family: var(--font-script); font-style: italic; font-weight: 400;
            color: var(--gold-2); letter-spacing: -0.02em;
        }

        .hero-lede {
            font-size: 1.1rem; line-height: 1.6;
            color: rgba(255,255,255,0.7); max-width: 520px; margin-bottom: 36px;
        }

        .hero-cta { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 48px; }

        .btn-primary {
            display: inline-flex; align-items: center; gap: 10px;
            background: var(--gold); color: var(--ink);
            font-weight: 600; font-size: 14.5px; padding: 14px 24px; border-radius: 100px;
            text-decoration: none; border: none; box-shadow: var(--sh-gold);
            transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
        }
        .btn-primary .btn-arrow { transition: transform 0.25s; }
        .btn-primary:hover { background: var(--gold-2); transform: translateY(-2px); box-shadow: 0 12px 36px -4px rgba(184,146,74,0.55); }
        .btn-primary:hover .btn-arrow { transform: translateX(4px); }

        .btn-secondary {
            display: inline-flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.15);
            color: #fff; font-weight: 500; font-size: 14.5px;
            padding: 14px 22px; border-radius: 100px; text-decoration: none;
            backdrop-filter: blur(10px); transition: background 0.25s, border-color 0.25s, transform 0.25s;
        }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); border-color: rgba(184,146,74,0.4); transform: translateY(-2px); }

        .hero-verse {
            position: relative; border-left: 1.5px solid var(--gold);
            padding: 8px 0 8px 24px; max-width: 480px;
        }
        .hero-verse-mark {
            position: absolute; top: -16px; left: 8px;
            font-family: var(--font-display); font-size: 64px;
            color: rgba(184,146,74,0.22); line-height: 1;
        }
        .hero-verse p {
            font-family: var(--font-script); font-style: italic;
            font-size: 1.05rem; line-height: 1.6;
            color: rgba(255,255,255,0.8); margin-bottom: 10px;
        }
        .hero-verse cite {
            font-family: var(--font-body); font-style: normal;
            font-size: 11px; font-weight: 600; letter-spacing: 0.18em;
            text-transform: uppercase; color: var(--gold-2);
        }

        /* Hero right */
        .hero-right { display: flex; flex-direction: column; gap: 16px; }

        .hero-card {
            position: relative;
            background: linear-gradient(160deg, rgba(255,255,255,0.07) 0%, rgba(255,255,255,0.03) 100%);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: var(--r-lg); padding: 32px;
            backdrop-filter: blur(20px);
            box-shadow: 0 24px 60px -12px rgba(0,0,0,0.45);
        }
        .hero-card::before {
            content: ''; position: absolute; inset: 0; border-radius: var(--r-lg); padding: 1px;
            background: linear-gradient(160deg, rgba(184,146,74,0.4), transparent 60%);
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor; mask-composite: exclude; pointer-events: none;
        }

        .hero-card-header {
            display: flex; align-items: center; gap: 16px;
            padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 24px;
        }
        .hero-card-logo {
            width: 56px; height: 56px; border-radius: 50%;
            border: 1.5px solid rgba(184,146,74,0.4);
            animation: heroFloat 6s ease-in-out infinite;
        }
        @keyframes heroFloat { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        .hero-card-eyebrow { font-size: 10.5px; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: var(--gold-2); margin-bottom: 4px; }
        .hero-card-title { font-family: var(--font-display); font-size: 18px; font-weight: 500; color: #fff; line-height: 1.2; }

        .hero-card-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 1px;
            background: rgba(255,255,255,0.06); border-radius: var(--r); overflow: hidden; margin-bottom: 24px;
        }
        .hero-card-item { background: rgba(10,24,50,0.5); padding: 16px 18px; }
        .hero-card-lbl { font-size: 10px; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: rgba(255,255,255,0.45); margin-bottom: 6px; }
        .hero-card-val { font-family: var(--font-display); font-size: 17px; font-weight: 500; color: #fff; letter-spacing: -0.01em; margin-bottom: 2px; }
        .hero-card-meta { font-size: 11px; color: rgba(255,255,255,0.5); }

        .hero-countdown {
            background: rgba(0,0,0,0.25); border: 1px solid rgba(184,146,74,0.18);
            border-radius: var(--r); padding: 16px 18px;
        }
        .hero-countdown-lbl { font-size: 10.5px; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: var(--gold-2); margin-bottom: 10px; }
        .countdown { display: flex; align-items: baseline; gap: 6px; }
        .cd-item { display: flex; align-items: baseline; gap: 4px; }
        .cd-num { font-family: var(--font-display); font-size: 28px; font-weight: 500; color: #fff; line-height: 1; letter-spacing: -0.02em; font-variant-numeric: tabular-nums; }
        .cd-lbl { font-size: 10.5px; color: rgba(255,255,255,0.5); letter-spacing: 0.08em; text-transform: uppercase; }
        .cd-sep { color: rgba(184,146,74,0.4); font-size: 20px; line-height: 1; margin: 0 4px; }

        .hero-badge {
            display: flex; align-items: center; gap: 14px; padding: 14px 20px;
            background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); border-radius: var(--r);
        }
        .hero-badge-star {
            width: 32px; height: 32px; border-radius: 50%; background: var(--gold-soft);
            display: flex; align-items: center; justify-content: center; color: var(--gold-2); font-size: 14px;
        }
        .hero-badge-lbl { font-size: 10.5px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 2px; }
        .hero-badge-val { font-size: 13px; font-weight: 500; color: #fff; }

        /* Marquee */
        .hero-marquee {
            position: relative; margin: 0 -32px; padding: 18px 0;
            border-top: 1px solid rgba(255,255,255,0.06);
            background: rgba(0,0,0,0.18); overflow: hidden;
            mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
        }
        .hero-marquee-track {
            display: flex; gap: 56px; white-space: nowrap;
            animation: marquee 35s linear infinite; width: max-content;
        }
        .hero-marquee-track span {
            font-family: var(--font-display); font-style: italic; font-weight: 400;
            font-size: 22px; color: #FFFFFF; letter-spacing: -0.01em;
        }
        @keyframes marquee { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }

        @media (max-width: 960px) {
            .hero-container { grid-template-columns: 1fr; gap: 56px; padding: 40px 0 72px; }
            .hero { padding: 56px 20px 0; }
            .hero-marquee { margin: 0 -20px; }
        }

        /* ── Access ───────────────────────────────────────────────────── */
        .access { background: var(--paper); padding: 120px 0; position: relative; }

        .access-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 24px; max-width: 980px; margin: 0 auto;
        }

        .access-card {
            position: relative; background: #fff;
            border: 1px solid var(--hairline); border-radius: var(--r-lg); padding: 36px;
            box-shadow: var(--sh-2);
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s;
        }
        .access-card:hover { transform: translateY(-4px); box-shadow: var(--sh-3); border-color: var(--hairline-2); }

        .access-card-top {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px;
        }
        .access-num { font-family: var(--font-display); font-size: 14px; font-weight: 500; color: var(--gold); letter-spacing: 0.1em; }
        .access-tag { font-size: 10.5px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--ink); background: var(--cream-2); padding: 4px 10px; border-radius: 100px; }
        .access-tag-light { background: var(--gold-soft); color: var(--gold); }

        .access-card-title { font-family: var(--font-display); font-size: 24px; font-weight: 500; color: var(--ink); letter-spacing: -0.015em; margin-bottom: 10px; line-height: 1.2; }
        .access-card-desc { font-size: 14.5px; color: var(--text-2); line-height: 1.6; margin-bottom: 24px; }

        .code-form { margin-bottom: 16px; }
        .code-row { display: flex; gap: 8px; }
        .code-field {
            flex: 1; min-width: 0; padding: 13px 16px;
            background: var(--paper); border: 1px solid var(--hairline-2); border-radius: 100px;
            color: var(--ink); font-family: var(--font-mono);
            font-size: 13px; font-weight: 600; text-align: center; letter-spacing: 0.06em;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }
        .code-field::placeholder { color: #B0B8CB; letter-spacing: 0.06em; font-weight: 400; }
        .code-field:focus { border-color: var(--ink); background: #fff; box-shadow: 0 0 0 4px rgba(10,24,50,0.06); }

        .btn-submit {
            background: var(--ink); color: #fff; font-size: 13.5px; font-weight: 600;
            padding: 13px 20px; border-radius: 100px; border: none; white-space: nowrap;
            transition: background 0.25s, transform 0.2s, box-shadow 0.25s; box-shadow: var(--sh-1);
        }
        .btn-submit:hover { background: var(--ink-2); transform: translateY(-1px); box-shadow: var(--sh-ink); }
        .btn-submit-gold { background: var(--gold); color: var(--ink); box-shadow: var(--sh-gold); }
        .btn-submit-gold:hover { background: var(--gold-2); }

        .access-meta { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--muted); }
        .access-meta code { font-family: var(--font-mono); background: var(--cream-2); padding: 2px 6px; border-radius: 4px; color: var(--ink); }
        .dot { width: 4px; height: 4px; border-radius: 50%; background: var(--gold); flex-shrink: 0; }
        .err { color: #DC2626; font-size: 12.5px; margin-top: 8px; }

        @media (max-width: 800px) { .access { padding: 80px 0; } .access-grid { grid-template-columns: 1fr; } .access-card { padding: 28px; } }

        /* ── How to Register ──────────────────────────────────────────── */
        .how { background: var(--cream); padding: 120px 0; border-top: 1px solid var(--rule); }

        .steps { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 64px; }

        .step {
            background: var(--paper-2); border: 1px solid var(--hairline);
            border-radius: var(--r-lg); padding: 28px 24px 24px;
            display: flex; flex-direction: column; position: relative;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; min-height: 240px;
        }
        .step:hover { transform: translateY(-3px); box-shadow: var(--sh-2); border-color: var(--hairline-2); }
        .step-final { background: linear-gradient(180deg, var(--paper-2) 0%, rgba(184,146,74,0.05) 100%); border-color: rgba(184,146,74,0.25); }

        .step-no {
            font-family: var(--font-display); font-size: 13px; font-weight: 500; color: var(--gold);
            letter-spacing: 0.12em; margin-bottom: 36px;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .step-no::after { content: ''; flex: 1; height: 1px; background: var(--rule); margin-left: 4px; }
        .step-body { flex: 1; margin-bottom: 20px; }
        .step-title { font-family: var(--font-display); font-size: 18px; font-weight: 500; color: var(--ink); letter-spacing: -0.015em; line-height: 1.25; margin-bottom: 10px; }
        .step-desc { font-size: 13.5px; color: var(--text-2); line-height: 1.55; }
        .step-foot { font-size: 11px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--muted); padding-top: 16px; border-top: 1px solid var(--hairline); }
        .step-final .step-foot { color: var(--gold); }

        /* Bank box */
        .bank-box {
            background: var(--ink); color: #fff; border-radius: var(--r-lg);
            padding: 36px 40px; position: relative; overflow: hidden;
        }
        .bank-box::before {
            content: ''; position: absolute; top: -100px; right: -100px;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(184,146,74,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .bank-box-head {
            display: flex; align-items: center; gap: 16px;
            padding-bottom: 24px; border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 24px; position: relative; z-index: 1;
        }
        .bank-mark {
            width: 48px; height: 48px; border-radius: 14px;
            background: var(--gold-soft); border: 1px solid rgba(184,146,74,0.3);
            display: flex; align-items: center; justify-content: center;
            font-family: var(--font-display); font-size: 22px; color: var(--gold-2); font-weight: 600;
        }
        .bank-eyebrow { font-size: 10.5px; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: var(--gold-2); margin-bottom: 4px; }
        .bank-title { font-family: var(--font-display); font-size: 19px; font-weight: 500; color: #fff; letter-spacing: -0.01em; }

        .bank-grid {
            display: grid; grid-template-columns: 1fr 1.4fr 1.4fr auto;
            gap: 32px; align-items: center; position: relative; z-index: 1;
        }
        .bank-lbl { font-size: 10.5px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: rgba(255,255,255,0.5); margin-bottom: 6px; }
        .bank-val { font-size: 15px; font-weight: 500; color: #fff; }
        .bank-acct { font-family: var(--font-mono); font-size: 20px; font-weight: 600; color: var(--gold-2); letter-spacing: 0.04em; font-variant-numeric: tabular-nums; }
        .bank-action {
            display: inline-flex; align-items: center; gap: 8px; background: var(--gold); color: var(--ink);
            font-size: 13px; font-weight: 600; padding: 11px 18px; border-radius: 100px; text-decoration: none;
            transition: background 0.2s, transform 0.2s;
        }
        .bank-action:hover { background: var(--gold-2); transform: translateY(-1px); }

        @media (max-width: 1000px) { .steps { grid-template-columns: 1fr 1fr; } }
        @media (max-width: 800px) { .how { padding: 80px 0; } .bank-grid { grid-template-columns: 1fr 1fr; gap: 20px; } .bank-cell-action { grid-column: 1 / -1; } }
        @media (max-width: 520px) { .steps { grid-template-columns: 1fr; } .bank-grid { grid-template-columns: 1fr; } .bank-box { padding: 28px; } }

        /* ── Fees ─────────────────────────────────────────────────────── */
        .fees { background: var(--paper); padding: 120px 0; border-top: 1px solid var(--rule); }
        .fees-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; margin-bottom: 32px; }

        .fee-card {
            position: relative; background: #fff; border: 1px solid var(--hairline);
            border-radius: var(--r-lg); padding: 32px 28px 28px;
            display: flex; flex-direction: column; text-align: center;
            transition: transform 0.3s, box-shadow 0.3s, border-color 0.3s; overflow: hidden;
        }
        .fee-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, var(--gold)); }
        .fee-card-adv { --accent: linear-gradient(90deg, #1E88E5, #64B5F6); }
        .fee-card-pf  { --accent: linear-gradient(90deg, #2D6A30, #4CAF50); }
        .fee-card-syl { --accent: linear-gradient(90deg, var(--gold), var(--gold-2)); }
        .fee-card:hover { transform: translateY(-4px); box-shadow: var(--sh-3); border-color: var(--hairline-2); }
        .fee-card-featured { background: linear-gradient(180deg, #fff 0%, var(--paper-2) 100%); border-color: rgba(45,106,48,0.25); }

        .fee-card-head {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px;
        }
        .fee-card-num { font-family: var(--font-display); font-size: 13px; font-weight: 500; color: var(--gold); letter-spacing: 0.16em; }
        .fee-card-tag { font-size: 10.5px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--muted); }

        .fee-card-logo-wrap { width: 96px; height: 96px; margin: 0 auto 20px; position: relative; }
        .fee-card-logo-wrap::before { content: ''; position: absolute; inset: -8px; border-radius: 50%; background: var(--cream-2); z-index: 0; }
        .fee-card-logo { position: relative; z-index: 1; width: 96px; height: 96px; }

        .fee-card-name { font-family: var(--font-display); font-size: 22px; font-weight: 500; color: var(--ink); letter-spacing: -0.015em; margin-bottom: 6px; }
        .fee-card-ages { font-size: 12.5px; font-weight: 500; letter-spacing: 0.06em; color: var(--text-2); margin-bottom: 20px; }

        .fee-card-divider { height: 1px; background: linear-gradient(90deg, transparent, var(--rule), transparent); margin: 0 -28px 20px; }

        .fee-card-price {
            font-family: var(--font-display); display: flex; align-items: flex-start;
            justify-content: center; gap: 4px; margin-bottom: 24px; line-height: 1;
        }
        .fee-card-currency { font-size: 22px; font-weight: 500; color: var(--gold); margin-top: 8px; }
        .fee-card-amount { font-size: 48px; font-weight: 500; color: var(--ink); letter-spacing: -0.03em; font-variant-numeric: tabular-nums; }

        .fee-card-list { list-style: none; text-align: left; border-top: 1px solid var(--hairline); padding-top: 18px; }
        .fee-card-list li { font-size: 13px; color: var(--text-2); line-height: 1.5; padding: 7px 0 7px 22px; position: relative; }
        .fee-card-list li::before { content: '\2713'; position: absolute; left: 0; top: 7px; color: var(--gold); font-size: 12px; font-weight: 600; }

        .fees-foot {
            display: flex; align-items: center; gap: 10px; justify-content: center;
            max-width: 720px; margin: 32px auto 0; text-align: center;
            font-size: 13px; color: var(--muted); line-height: 1.55;
            padding: 16px 24px; background: var(--cream-2); border-radius: var(--r);
        }

        @media (max-width: 960px) { .fees-grid { grid-template-columns: 1fr; max-width: 480px; margin-left: auto; margin-right: auto; } .fees { padding: 80px 0; } }

        /* ── About ────────────────────────────────────────────────────── */
        .about { background: var(--cream); padding: 120px 0; border-top: 1px solid var(--rule); }
        .about-inner { display: grid; grid-template-columns: 1fr 0.85fr; gap: 80px; align-items: center; }
        .about-text { max-width: 580px; }

        .about-para { font-size: 16px; line-height: 1.7; color: var(--text-2); margin-bottom: 18px; }
        .about-para strong { color: var(--ink); font-weight: 600; }

        .about-quote {
            position: relative; background: var(--paper-2); border-left: 2px solid var(--gold);
            border-radius: 0 var(--r) var(--r) 0; padding: 24px 28px; margin: 32px 0;
        }
        .about-quote-mark { position: absolute; top: -8px; left: 16px; font-family: var(--font-display); font-size: 64px; color: var(--gold-soft); line-height: 1; }
        .about-quote p { font-family: var(--font-script); font-style: italic; font-size: 17px; line-height: 1.55; color: var(--ink); position: relative; z-index: 1; margin-bottom: 10px; }
        .about-quote cite { font-family: var(--font-body); font-style: normal; font-size: 11px; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: var(--gold); }

        .about-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--rule); border: 1px solid var(--rule); border-radius: var(--r); overflow: hidden; }
        .fact { background: var(--paper-2); padding: 16px 20px; }
        .fact-lbl { font-size: 10.5px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--muted); margin-bottom: 4px; }
        .fact-val { font-family: var(--font-display); font-size: 15px; font-weight: 500; color: var(--ink); letter-spacing: -0.005em; }

        /* About visual */
        .about-visual { position: relative; display: flex; flex-direction: column; align-items: center; }
        .about-logo-stage { position: relative; width: 360px; height: 360px; display: flex; align-items: center; justify-content: center; }
        .about-logo-ring { position: absolute; border-radius: 50%; border: 1px solid rgba(184,146,74,0.2); }
        .about-logo-ring-1 { inset: 0; animation: ringSpin 80s linear infinite; }
        .about-logo-ring-2 { inset: 24px; border-style: dashed; border-color: rgba(184,146,74,0.25); animation: ringSpin 120s linear infinite reverse; }
        @keyframes ringSpin { to { transform: rotate(360deg); } }
        .about-logo { width: 280px; height: 280px; border-radius: 50%; border: 3px solid rgba(184,146,74,0.25); box-shadow: 0 0 0 16px rgba(184,146,74,0.05), 0 24px 60px -12px rgba(10,24,50,0.2); position: relative; z-index: 2; }

        .about-logo-stars { position: absolute; inset: 0; pointer-events: none; }
        .about-logo-stars span { position: absolute; left: var(--x); top: var(--y); color: var(--gold); font-size: 14px; opacity: 0.5; animation: twinkle 3s ease-in-out infinite; }
        .about-logo-stars span:nth-child(2) { animation-delay: 0.8s; }
        .about-logo-stars span:nth-child(3) { animation-delay: 1.6s; }
        .about-logo-stars span:nth-child(4) { animation-delay: 2.4s; }
        @keyframes twinkle { 0%,100% { opacity: 0.3; transform: scale(0.9); } 50% { opacity: 1; transform: scale(1.1); } }

        .about-credit {
            display: flex; align-items: flex-start; gap: 12px; margin-top: 32px;
            font-size: 12.5px; color: var(--text-2); line-height: 1.55;
            font-style: italic; max-width: 280px; text-align: left;
        }
        .about-credit strong { font-family: var(--font-display); font-style: normal; font-weight: 500; color: var(--ink); }
        .about-credit-mark { color: var(--gold); font-style: normal; font-size: 16px; flex-shrink: 0; margin-top: 2px; }

        @media (max-width: 960px) { .about-inner { grid-template-columns: 1fr; gap: 56px; } .about-visual { order: -1; } .about-logo-stage { width: 280px; height: 280px; } .about-logo { width: 220px; height: 220px; } .about { padding: 80px 0; } }

        /* ── Rules ────────────────────────────────────────────────────── */
        .rules { background: var(--paper); padding: 120px 0; border-top: 1px solid var(--rule); }
        .rules-grid {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px;
            background: var(--rule); border: 1px solid var(--rule); border-radius: var(--r-lg); overflow: hidden;
        }
        .rule { background: var(--paper-2); padding: 28px; display: flex; gap: 18px; transition: background 0.25s; }
        .rule:hover { background: #fff; }
        .rule-no { font-family: var(--font-display); font-size: 13px; font-weight: 500; color: var(--gold); letter-spacing: 0.1em; flex-shrink: 0; padding-top: 2px; }
        .rule-text { font-size: 14.5px; color: var(--text-2); line-height: 1.55; }
        .rule-text strong { color: var(--ink); font-weight: 600; }

        @media (max-width: 800px) { .rules-grid { grid-template-columns: 1fr 1fr; } .rules { padding: 80px 0; } }
        @media (max-width: 520px) { .rules-grid { grid-template-columns: 1fr; } }

        /* ── Contact ──────────────────────────────────────────────────── */
        .contact { background: var(--cream); padding: 120px 0; border-top: 1px solid var(--rule); }
        .contact-inner { display: grid; grid-template-columns: 0.9fr 1.1fr; gap: 64px; }

        .contact-channels { display: flex; flex-direction: column; gap: 10px; margin-top: 36px; }
        .contact-ch {
            display: flex; align-items: center; gap: 16px; background: var(--paper-2);
            border: 1px solid var(--hairline); border-radius: var(--r); padding: 18px 20px;
            text-decoration: none; color: inherit;
            transition: transform 0.25s, border-color 0.25s, background 0.25s;
        }
        .contact-ch:hover { transform: translateX(4px); border-color: var(--gold); background: #fff; }
        .contact-ch:hover .contact-ch-arr { color: var(--gold); transform: translate(2px, -2px); }

        .contact-ch-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            font-size: 20px;
        }
        .contact-ch-icon-wa { background: #25D366; color: #fff; }
        .contact-ch-icon-tel { background: var(--ink); color: #fff; }
        .contact-ch-icon-org { background: var(--gold-soft); color: var(--gold); }

        .contact-ch-text { flex: 1; }
        .contact-ch-type { font-family: var(--font-display); font-size: 15px; font-weight: 500; color: var(--ink); letter-spacing: -0.005em; margin-bottom: 2px; }
        .contact-ch-val { font-size: 13px; color: var(--text-2); }
        .contact-ch-arr { font-size: 16px; color: var(--muted); transition: color 0.25s, transform 0.25s; }
        .contact-ch-static:hover { transform: none; border-color: var(--hairline); background: var(--paper-2); }

        /* Contact form */
        .cform { background: var(--paper-2); border: 1px solid var(--hairline); border-radius: var(--r-lg); padding: 36px; box-shadow: var(--sh-2); }
        .cform-head { display: flex; align-items: baseline; justify-content: space-between; margin-bottom: 28px; padding-bottom: 20px; border-bottom: 1px solid var(--hairline); }
        .cform h3 { font-family: var(--font-display); font-size: 22px; font-weight: 500; color: var(--ink); letter-spacing: -0.015em; }
        .cform-meta { font-size: 11px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--gold); }

        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .fgrp { display: flex; flex-direction: column; gap: 7px; margin-bottom: 18px; }
        .flbl { font-size: 11px; font-weight: 600; letter-spacing: 0.12em; text-transform: uppercase; color: var(--text-2); }

        .finput, .ftextarea, .fselect {
            padding: 12px 14px; background: #fff; border: 1px solid var(--hairline-2);
            border-radius: var(--r-sm); color: var(--text); font-family: var(--font-body);
            font-size: 14.5px; outline: none; width: 100%;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .finput:focus, .ftextarea:focus, .fselect:focus { border-color: var(--ink); box-shadow: 0 0 0 3px rgba(10,24,50,0.06); }
        .finput::placeholder, .ftextarea::placeholder { color: #B0B8CB; }
        .ftextarea { resize: vertical; min-height: 100px; }

        .fcats { display: flex; flex-wrap: wrap; gap: 8px; }
        .fcat { cursor: pointer; position: relative; }
        .fcat input { position: absolute; opacity: 0; pointer-events: none; }
        .fcat span { display: inline-block; padding: 9px 16px; background: #fff; border: 1px solid var(--hairline-2); border-radius: 100px; font-size: 13px; font-weight: 500; color: var(--text-2); transition: all 0.2s; }
        .fcat:hover span { border-color: var(--ink); color: var(--ink); }
        .fcat input:checked + span { background: var(--ink); color: #fff; border-color: var(--ink); }

        .flash-ok { background: #F0FDF4; border: 1px solid #86EFAC; color: #15803D; border-radius: var(--r-sm); padding: 12px 16px; font-size: 13.5px; margin-bottom: 20px; }

        .btn-form {
            display: inline-flex; align-items: center; gap: 10px; background: var(--ink); color: #fff;
            font-size: 14.5px; font-weight: 600; padding: 13px 22px; border-radius: 100px; border: none;
            transition: background 0.25s, transform 0.2s, box-shadow 0.25s; box-shadow: var(--sh-1); margin-top: 8px;
        }
        .btn-form .btn-arrow { transition: transform 0.2s; }
        .btn-form:hover { background: var(--ink-2); transform: translateY(-1px); box-shadow: var(--sh-ink); }
        .btn-form:hover .btn-arrow { transform: translateX(3px); }

        @media (max-width: 960px) { .contact-inner { grid-template-columns: 1fr; gap: 48px; } .contact { padding: 80px 0; } }
        @media (max-width: 520px) { .frow { grid-template-columns: 1fr; } .cform { padding: 24px; } }

        /* ── Footer ───────────────────────────────────────────────────── */
        .footer {
            position: relative; background: var(--ink); color: rgba(255,255,255,0.65);
            padding: 80px 0 32px; overflow: hidden;
        }
        .footer-bg { position: absolute; inset: 0; pointer-events: none; }
        .footer-glow {
            position: absolute; top: -200px; left: 50%; transform: translateX(-50%);
            width: 800px; height: 400px;
            background: radial-gradient(ellipse, rgba(184,146,74,0.15) 0%, transparent 70%);
            filter: blur(60px);
        }
        .footer .container { position: relative; z-index: 1; }

        .footer-top {
            display: grid; grid-template-columns: 1fr 1.4fr; gap: 64px; align-items: start;
            padding-bottom: 48px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 48px;
        }
        .footer-brand { display: flex; align-items: center; gap: 16px; }
        .footer-logo { width: 64px; height: 64px; border-radius: 50%; border: 1.5px solid rgba(184,146,74,0.35); }
        .footer-brand-name { font-family: var(--font-display); font-size: 20px; font-weight: 500; color: #fff; letter-spacing: -0.01em; margin-bottom: 2px; }
        .footer-brand-sub { font-size: 11.5px; font-weight: 500; letter-spacing: 0.12em; text-transform: uppercase; color: var(--gold-2); }
        .footer-tagline { font-family: var(--font-script); font-style: italic; font-size: 19px; line-height: 1.5; color: rgba(255,255,255,0.75); max-width: 540px; }

        .footer-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 32px; margin-bottom: 56px; }
        .footer-col { display: flex; flex-direction: column; gap: 12px; }
        .footer-col-title { font-size: 10.5px; font-weight: 600; letter-spacing: 0.16em; text-transform: uppercase; color: var(--gold-2); margin-bottom: 4px; }
        .footer-col a { color: rgba(255,255,255,0.65); text-decoration: none; font-size: 13.5px; transition: color 0.2s, padding-left 0.25s; }
        .footer-col a:hover { color: #fff; padding-left: 4px; }
        .footer-verse { font-family: var(--font-script); font-style: italic; font-size: 13.5px; line-height: 1.5; color: rgba(255,255,255,0.6); }
        .footer-verse-ref { font-size: 11px; font-weight: 600; letter-spacing: 0.14em; text-transform: uppercase; color: var(--gold-2); margin-top: 4px; }

        .footer-bottom {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 28px; border-top: 1px solid rgba(255,255,255,0.06);
            font-size: 12px; color: rgba(255,255,255,0.5);
        }
        .footer-bottom-left { display: flex; align-items: center; gap: 10px; }
        .footer-star { color: var(--gold); font-size: 14px; }

        @media (max-width: 800px) { .footer-top { grid-template-columns: 1fr; gap: 24px; } .footer-grid { grid-template-columns: 1fr 1fr; gap: 28px; } .footer-bottom { flex-direction: column; gap: 12px; text-align: center; } }
        @media (max-width: 480px) { .footer-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

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

<!-- ── TOPBAR ────────────────────────────────────────────────────────────── -->
<div class="topbar">
    <div class="topbar-inner">
        <span class="topbar-dot"></span>
        Registration is now open &mdash; {{ setting('camp_name', 'Abeokuta 2026') }}  &middot; {{ setting('camp_dates','August 16 - 23') }}
        <span class="topbar-sep">|</span>
        <a href="#access">Enter your code &rarr;</a>
    </div>
    @include('partials.welcome-banner')

</div>

<!-- ── NAV ──────────────────────────────────────────────────────────────── -->
<nav class="nav" id="mainNav">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Congress 2026 Logo" class="nav-logo"/>
        <div class="nav-brand-text">
            <div class="nav-name">{{ setting('organization_name') }}</div>
            <div class="nav-sub">{{ setting('camp_name') }}</div>
        </div>
    </a>
    <div class="nav-links">
        <a href="#register">How to Register</a>
        <a href="#fees">Departments</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
    </div>
    <div class="nav-cta">
        <a href="#access" class="btn-nav-ghost">I have a code</a>
        <a href="#access" class="btn-nav">Enter Code <span class="arr">&rarr;</span></a>
    </div>
    <button class="nav-burger" id="navBurger" onclick="toggleDrawer()" aria-label="Menu">
        <span></span><span></span><span></span>
    </button>
</nav>

<!-- Mobile Drawer -->
<div class="nav-drawer" id="navDrawer">
    <div class="nav-drawer-inner">
        <a href="#register" onclick="toggleDrawer()">How to Register</a>
        <a href="#fees" onclick="toggleDrawer()">Departments</a>
        <a href="#about" onclick="toggleDrawer()">About</a>
        <a href="#contact" onclick="toggleDrawer()">Contact</a>
        <a href="#access" onclick="toggleDrawer()" class="drawer-cta">Enter Code &rarr;</a>
    </div>
</div>

<!-- ── HERO ──────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-bg">
        <div class="hero-glow hero-glow-1"></div>
        <div class="hero-glow hero-glow-2"></div>
        <div class="hero-grain"></div>
    </div>

    <div class="hero-container">
        <!-- Left -->
        <div class="hero-left">
            <div class="hero-eyebrow">
                <span class="hero-eyebrow-dot"></span>
                Abeokuta 2026 &nbsp;&bull;&nbsp; {{ setting('camp_dates','August 16 - 23') }}
            </div>

            <h1 class="hero-title">
{{--                {{ setting('camp_theme') }}--}}
                From the Word<br/><em>to the World</em>
            </h1>

            <p class="hero-lede">
                The Ogun Conference Annual Youth Congress gathers Adventurers, Pathfinders, and Senior Youth for a transformative week of spiritual growth, fellowship, and missionary training.
            </p>

            <div class="hero-cta">
                <a href="#access" class="btn-primary">
                    I Have a Code <span class="btn-arrow">&rarr;</span>
                </a>
                <a href="#register" class="btn-secondary">How It Works</a>
            </div>

            <div class="hero-verse">
                <div class="hero-verse-mark">&ldquo;</div>
                <p>&ldquo;Ye shall receive power &hellip; and ye shall be witnesses unto me, both in Jerusalem, and in all Judaea, and in Samaria, and unto the uttermost part of the earth.&rdquo;</p>
                <cite>Acts 1:8 &nbsp;&middot;&nbsp; Ogun Conference Youth Congress</cite>
            </div>
        </div>

        <!-- Right -->
        <div class="hero-right">
            <div class="hero-card">
                <div class="hero-card-header">
                    <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="hero-card-logo"/>
                    <div>
                        <div class="hero-card-eyebrow">{{ setting('organization_name') }}</div>
                        <div class="hero-card-title">{{ setting('camp_name') }}</div>
                    </div>
                </div>

                <div class="hero-card-grid">
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Date</div>
                        <div class="hero-card-val">{{ setting('camp_dates','August 16 - 23') }}</div>
                        <div class="hero-card-meta">7 Days</div>
                    </div>
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Venue</div>
                        <div class="hero-card-val">{{ setting('camp_venue','Abeokuta') }}</div>
                        <div class="hero-card-meta">Ogun State</div>
                    </div>
                    <div class="hero-card-item">
                        <div class="hero-card-lbl">Departments</div>
                        <div class="hero-card-val">3</div>
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
                            <div class="cd-item">
                                <span class="cd-num" id="cd-d">--</span>
                                <span class="cd-lbl">d</span>
                            </div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item">
                                <span class="cd-num" id="cd-h">--</span>
                                <span class="cd-lbl">h</span>
                            </div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item">
                                <span class="cd-num" id="cd-m">--</span>
                                <span class="cd-lbl">m</span>
                            </div>
                            <span class="cd-sep">:</span>
                            <div class="cd-item">
                                <span class="cd-num" id="cd-s">--</span>
                                <span class="cd-lbl">s</span>
                            </div>
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

    <!-- Marquee -->
    <div class="hero-marquee">
        <div class="hero-marquee-track">
            @php $marqueeItems = ['From the Word', 'to the World', 'Acts 1:8', 'Abeokuta 2026', 'Adventurers', 'Pathfinders', 'Senior Youth', 'Ogun Conference', 'Aug 16 - 23, 2026', 'SDA Youth Congress', 'From the Word', 'to the World', 'Acts 1:8', 'Abeokuta 2026', 'Adventurers', 'Pathfinders', 'Senior Youth', 'Ogun Conference', 'Aug 16 - 23, 2026', 'SDA Youth Congress']; @endphp
            @foreach($marqueeItems as $item)
                <span>{{ $item }}</span>
            @endforeach
        </div>
    </div>
</section>

<!-- ── ACCESS ────────────────────────────────────────────────────────────── -->
<section class="access" id="access">
    <div class="container">
        <div class="section-head section-head-center reveal">
            <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Your Gateway</span>
            <h2 class="section-title">Access Your<br/><em>Registration</em></h2>
            <p class="section-lede" style="margin: 0 auto">
                Enter your registration code to complete your details, or access your camper portal to download your ID card and documents.
            </p>
        </div>

        <div class="access-grid">
            <div class="access-card reveal reveal-delay-1">
                <div class="access-card-top">
                    <span class="access-num">01.</span>
                    <span class="access-tag">New Registration</span>
                </div>
                <h3 class="access-card-title">Complete Your Form</h3>
                <p class="access-card-desc">
                    Have a code from your local church youth leader? Enter it here to fill in your personal details and secure your camp spot.
                </p>
                <form class="code-form" action="{{ route('registration.validate-code-web') }}" method="POST">
                    @csrf
                    <div class="code-row">
                        <input type="text" name="code" class="code-field"
                               placeholder="OGN-2026-XXXXXX" maxlength="15"
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')"
                               autocomplete="off" spellcheck="false" required/>
                        <button type="submit" class="btn-submit">Go &rarr;</button>
                    </div>
                </form>
                <div class="access-meta">
                    <span class="dot"></span>
                    Format: <code>OGN-2026-XXXXXX</code>
                </div>
                @if(session('error') && !session('portal_error'))
                    <p class="err">{{ session('error') }}</p>
                @endif
            </div>

            <div class="access-card reveal reveal-delay-2">
                <div class="access-card-top">
                    <span class="access-num">02.</span>
                    <span class="access-tag access-tag-light">Returning Camper</span>
                </div>
                <h3 class="access-card-title">Camper Portal</h3>
                <p class="access-card-desc">
                    Already registered? Access your camper portal to download your ID card, consent form, and view camp announcements.
                </p>
                <form class="code-form" action="{{ route('portal.login') }}" method="POST">
                    @csrf
                    <div class="code-row">
                        <input type="text" name="code" class="code-field"
                               placeholder="OGN-2026-XXXXXX" maxlength="15" required
                               oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9\-]/g,'')"
                               autocomplete="off" spellcheck="false"/>
                        <button type="submit" class="btn-submit btn-submit-gold">Enter &rarr;</button>
                    </div>
                </form>
                <div class="access-meta">
                    <span class="dot"></span>
                    Same code used to register
                </div>
                @if(session('portal_error'))
                    <p class="err">{{ session('portal_error') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- ── HOW TO REGISTER ───────────────────────────────────────────────────── -->
<section class="how" id="register">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Registration</span>
            <h2 class="section-title">How to<br/><em>Register</em></h2>
            <p class="section-lede">
                Registration is coordinated through your local church. Your local church youth leader handles payment and code generation on behalf of your congregation.
            </p>
        </div>

        <div class="steps">
            <div class="step reveal reveal-delay-1">
                <span class="step-no">01</span>
                <div class="step-body">
                    <div class="step-title">Contact Your Youth Leader</div>
                    <div class="step-desc">Reach your local church youth leader to express interest and confirm your department and age group.</div>
                </div>
                <div class="step-foot">Adventurers &middot; Pathfinders &middot; Senior Youth</div>
            </div>
            <div class="step reveal reveal-delay-2">
                <span class="step-no">02</span>
                <div class="step-body">
                    <div class="step-title">Church Makes Payment</div>
                    <div class="step-desc">The youth leader pays the total for all registered campers via bank transfer.</div>
                </div>
                <div class="step-foot">Bank Transfer</div>
            </div>
            <div class="step reveal reveal-delay-3">
                <span class="step-no">03</span>
                <div class="step-body">
                    <div class="step-title">Receive Your Code</div>
                    <div class="step-desc">A unique registration code is sent to your youth leader's dashboard once payment is confirmed by the treasurer.</div>
                </div>
                <div class="step-foot">Delivered via Youth Leader's Dashboard</div>
            </div>
            <div class="step step-final reveal reveal-delay-4">
                <span class="step-no">04</span>
                <div class="step-body">
                    <div class="step-title">Complete Your Form</div>
                    <div class="step-desc">Enter your code on this page and fill in the registration wizard to finalize your congress spot.</div>
                </div>
                <div class="step-foot">You&rsquo;re in!</div>
            </div>
        </div>

        @if(setting('bank_account_number'))
            <div class="bank-box reveal">
                <div class="bank-box-head">
                    <div class="bank-mark">&#8358;</div>
                    <div>
                        <div class="bank-eyebrow">Bank Transfer Details</div>
                        <div class="bank-title">Pay directly into our account</div>
                    </div>
                </div>
                <div class="bank-grid">
                    <div class="bank-cell">
                        <div class="bank-lbl">Bank</div>
                        <div class="bank-val">{{ setting('bank_name') }}</div>
                    </div>
                    <div class="bank-cell">
                        <div class="bank-lbl">Account Number</div>
                        <div class="bank-acct">{{ setting('bank_account_number') }}</div>
                    </div>
                    <div class="bank-cell">
                        <div class="bank-lbl">Account Name</div>
                        <div class="bank-val">{{ setting('bank_account_name') }}</div>
                    </div>
                    <div class="bank-cell bank-cell-action">
                        @if(setting('whatsapp_number'))
                            <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" target="_blank" class="bank-action">
                                Send Teller &rarr;
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ── DEPARTMENTS / FEES ─────────────────────────────────────────────────── -->
<section class="fees" id="fees">
    <div class="container">
        <div class="section-head-split reveal">
            <div>
                <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Departments</span>
                <h2 class="section-title">Camp Fees<br/><em>{{ now()->year }}</em></h2>
            </div>
            <div>
                <p class="section-lede">
                    Fees are set per department based on age group. All registrations are coordinated through your local church youth leader.
                </p>
            </div>
        </div>

        <div class="fees-grid">
            <div class="fee-card fee-card-adv reveal reveal-delay-1">
                <div class="fee-card-head">
                    <span class="fee-card-num">01.</span>
                    <span class="fee-card-tag">Ages 6&ndash;9</span>
                </div>
                <div class="fee-card-logo-wrap">
                    <img src="{{ asset('images/adventurer_logo.png') }}" alt="Adventurer Club" class="fee-card-logo"/>
                </div>
                <div class="fee-card-name">Adventurers</div>
                <div class="fee-card-ages">Ages 6 &ndash; 9 years</div>
                <div class="fee-card-divider"></div>
                <div class="fee-card-price">
                    <span class="fee-card-currency">&#8358;</span>
                    <span class="fee-card-amount">{{ number_format((int) setting('fee_adventurer',5000)) }}</span>
                </div>
                <ul class="fee-card-list">
                    <li>Parent/guardian must accompany camper less than 6 years of age</li>
                    <li>Parental consent form required</li>
                    <li>Registration via local church</li>
                    <li>Cover letter from Church Pastor</li>
                </ul>
            </div>

            <div class="fee-card fee-card-pf fee-card-featured reveal reveal-delay-2">
                <div class="fee-card-head">
                    <span class="fee-card-num">02.</span>
                    <span class="fee-card-tag">Ages 10&ndash;15</span>
                </div>
                <div class="fee-card-logo-wrap">
                    <img src="{{ asset('images/pathfinder_logo.png') }}" alt="Pathfinder Club" class="fee-card-logo"/>
                </div>
                <div class="fee-card-name">Pathfinders</div>
                <div class="fee-card-ages">Ages 10 &ndash; 15 years</div>
                <div class="fee-card-divider"></div>
                <div class="fee-card-price">
                    <span class="fee-card-currency">&#8358;</span>
                    <span class="fee-card-amount">{{ number_format((int) setting('fee_pathfinder',5000)) }}</span>
                </div>
                <ul class="fee-card-list">
                    <li>Registration via local church</li>
                    <li>Parental consent form required</li>
                    <li>Cover letter from Church Pastor</li>
                </ul>
            </div>

            <div class="fee-card fee-card-syl reveal reveal-delay-3">
                <div class="fee-card-head">
                    <span class="fee-card-num">03.</span>
                    <span class="fee-card-tag">Ages 16+</span>
                </div>
                <div class="fee-card-logo-wrap">
                    <img src="{{ asset('images/senior_youth_logo.png') }}" alt="Senior Youth" class="fee-card-logo"/>
                </div>
                <div class="fee-card-name">Senior Youth</div>
                <div class="fee-card-ages">Ambassador (16&ndash;21) &middot; Young Adults (22+)</div>
                <div class="fee-card-divider"></div>
                <div class="fee-card-price">
                    <span class="fee-card-currency">&#8358;</span>
                    <span class="fee-card-amount">{{ number_format((int) setting('fee_senior_youth',7000)) }}</span>
                </div>
                <ul class="fee-card-list">
                    <li>Registration via local church</li>
                    <li>Cover letter from Church Pastor</li>
                </ul>
            </div>
        </div>

        <div class="fees-foot reveal">
            &#128276; All fees are paid through your local church youth leader. Contact them directly to confirm your registration and arrange payment.
        </div>
    </div>
</section>

<!-- ── ABOUT ─────────────────────────────────────────────────────────────── -->
<section class="about" id="about">
    <div class="container">
        <div class="about-inner">
            <div class="about-text reveal">
                <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; About the Congress</span>
                <h2 class="section-title">A Visual Manifesto<br/>for <em>Ogun&rsquo;s Youth</em></h2>

                <p class="about-para">
                    The <strong>Ogun Conference Annual Youth Congress</strong> gathers Adventurers, Pathfinders, and Senior Youth from churches across the conference for a transformative week of spiritual growth, fellowship, and missionary training.
                </p>
                <p class="about-para">
                    The 2026 Congress theme &mdash; <strong>From the Word to the World</strong> &mdash; is rooted in Acts 1:8. It is a call for the youth of the Ogun Conference to be witnesses, starting from their local communities and reaching outward to the world.
                </p>

                <div class="about-quote">
                    <div class="about-quote-mark">&ldquo;</div>
                    <p>Spiritual empowerment is not for ourselves alone, but for the world. The power received in Acts 1:8 is a mandate to move outward.</p>
                    <cite>&mdash; 2026 Congress Visual Identity</cite>
                </div>

                <div class="about-facts">
                    @foreach([
                        ['Venue',   setting('camp_venue','TBA')],
                        ['Dates',   setting('camp_dates','Aug 16\u201322, 2026')],
                        ['Theme',   setting('camp_theme','From the Word to the World')],
                        ['Open To', 'Ages 6 and above'],
                    ] as [$label, $value])
                        <div class="fact">
                            <div class="fact-lbl">{{ $label }}</div>
                            <div class="fact-val">{{ $value }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="about-visual reveal reveal-delay-2">
                <div class="about-logo-stage">
                    <div class="about-logo-ring about-logo-ring-1"></div>
                    <div class="about-logo-ring about-logo-ring-2"></div>
                    <div class="about-logo-stars">
                        <span style="--x:8%;--y:12%">&#10022;</span>
                        <span style="--x:88%;--y:18%">&#10022;</span>
                        <span style="--x:6%;--y:78%">&#10022;</span>
                        <span style="--x:90%;--y:72%">&#10022;</span>
                    </div>
                    <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="about-logo"/>
                </div>
                <div class="about-credit">
                    <span class="about-credit-mark">&#10022;</span>
                    <div>Logo designed by <strong>Master Guide Chrisadim Emmanuel</strong> &mdash; a visual manifesto for the Ogun Conference Youth.</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ── CAMP RULES ─────────────────────────────────────────────────────────── -->
<section class="rules">
    <div class="container">
        <div class="section-head reveal">
            <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Guidelines</span>
            <h2 class="section-title">Camp <em>Rules</em></h2>
        </div>
        <div class="rules-grid">
            @foreach([
                ['01', 'All campers must carry their <strong>printed ID card</strong> at all times during camp.'],
                ['02', 'Campers under 18 must submit a <strong>signed parental consent form</strong> at check-in.'],
                ['03', 'Participants must wear the <strong>official camp uniform/dress code</strong> during formal sessions.'],
                ['04', 'Mobile phones should be kept on <strong>silent mode</strong> during services and meetings.'],
                ['05', 'No camper may <strong>leave the camp venue</strong> without prior permission from officials.'],
                ['06', 'All campers are expected to <strong>participate respectfully</strong> in all programme activities.'],
            ] as [$no, $text])
                <div class="rule reveal">
                    <span class="rule-no">{{ $no }}</span>
                    <span class="rule-text">{!! $text !!}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- ── CONTACT ────────────────────────────────────────────────────────────── -->
<section class="contact" id="contact">
    <div class="container">
        <div class="contact-inner">
            <div class="reveal">
                <span class="eyebrow"><span class="eyebrow-dot"></span>&nbsp; Get in Touch</span>
                <h2 class="section-title">Contact<br/><em>Us</em></h2>
                <p class="section-lede" style="max-width:100%">For registration enquiries, payment questions, or general information about the congress.</p>

                <div class="contact-channels">
                    @if(setting('whatsapp_number'))
                        <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" target="_blank" class="contact-ch">
                            <div class="contact-ch-icon contact-ch-icon-wa">&#128172;</div>
                            <div class="contact-ch-text">
                                <div class="contact-ch-type">WhatsApp</div>
                                <div class="contact-ch-val">{{ setting('whatsapp_number') }}</div>
                            </div>
                            <span class="contact-ch-arr">&nearr;</span>
                        </a>
                    @endif

                    @if(setting('secretariat_phone'))
                        <a href="tel:{{ setting('secretariat_phone') }}" class="contact-ch">
                            <div class="contact-ch-icon contact-ch-icon-tel">&#128222;</div>
                            <div class="contact-ch-text">
                                <div class="contact-ch-type">Secretariat</div>
                                <div class="contact-ch-val">{{ setting('secretariat_phone') }}</div>
                            </div>
                            <span class="contact-ch-arr">&nearr;</span>
                        </a>
                    @endif

                    <div class="contact-ch contact-ch-static">
                        <div class="contact-ch-icon contact-ch-icon-org">&#127776;</div>
                        <div class="contact-ch-text">
                            <div class="contact-ch-type">Seventh-day Adventist</div>
                            <div class="contact-ch-val">Ogun Conference Youth Department</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="cform reveal reveal-delay-2">
                <div class="cform-head">
                    <h3>Send a Message</h3>
                    <span class="cform-meta">We respond within 24hours</span>
                </div>

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
                        <label class="flbl">Email <span style="font-size:10px;font-weight:400;text-transform:none;letter-spacing:0">(optional)</span></label>
                        <input type="email" name="sender_email" class="finput" placeholder="your@email.com" value="{{ old('sender_email') }}"/>
                    </div>
                    <div class="fgrp">
                        <label class="flbl">Category *</label>
                        <div class="fcats">
                            @foreach(['general' => 'General Enquiry', 'complaint' => 'Complaint', 'inquiry' => 'Inquiry', 'payment' => 'Payment Question'] as $val => $lbl)
                                <label class="fcat">
                                    <input type="radio" name="category" value="{{ $val }}" {{ old('category')===$val ? 'checked' : '' }} required/>
                                    <span>{{ $lbl }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="fgrp">
                        <label class="flbl">Message *</label>
                        <textarea name="message" class="ftextarea" placeholder="Write your message here..." required>{{ old('message') }}</textarea>
                    </div>
                    <button type="submit" class="btn-form">
                        Send Message <span class="btn-arrow">&rarr;</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────────────────────── -->
<footer class="footer">
    <div class="footer-bg"><div class="footer-glow"></div></div>
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="footer-logo"/>
                <div>
                    <div class="footer-brand-name">{{ setting('organization_name') }}</div>
                    <div class="footer-brand-sub">{{ setting('camp_name') }}</div>
                </div>
            </div>
            <div class="footer-tagline">
                &ldquo;From the Word to the World&rdquo; &mdash; a call for every young person in the Ogun Conference to carry the gospel outward.
            </div>
        </div>

        <div class="footer-grid">
            <div class="footer-col">
                <div class="footer-col-title">Navigate</div>
                <a href="{{ $baseUrl . '/admin' }}">Admin Dashboard</a>
                <a href="#access">Enter Code</a>
                <a href="#register">How to Register</a>
                <a href="#fees">Departments & Fees</a>
                <a href="#about">About the Congress</a>
                <a href="#contact">Contact Us</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Camp Info</div>
                <a href="#">Dates: Aug 16&ndash;22, 2026</a>
                <a href="#">Venue: {{ setting('camp_venue','Abeokuta, Ogun State') }}</a>
                <a href="#">Theme: Acts 1:8</a>
                <a href="#">Departments: Adv &middot; PF &middot; SYL</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Contact</div>
                @if(setting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/\D/','',setting('whatsapp_number')) }}" target="_blank">WhatsApp: {{ setting('whatsapp_number') }}</a>
                @endif
                @if(setting('secretariat_phone'))
                    <a href="tel:{{ setting('secretariat_phone') }}">Secretariat: {{ setting('secretariat_phone') }}</a>
                @endif
                <a href="#contact">Send a message</a>
            </div>
            <div class="footer-col">
                <div class="footer-col-title">Scripture</div>
                <div class="footer-verse">
                    &ldquo;But ye shall receive power, after that the Holy Ghost is come upon you: and ye shall be witnesses unto me.&rdquo;
                </div>
                <div class="footer-verse-ref">Acts 1:8</div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-left">
                <span class="footer-star">&#10022;</span>
                <span>Seventh-day Adventist Church &mdash; Ogun Conference Youth Department &middot; {{ now()->year }}</span>
            </div>
            <div>
                @if(setting('secretariat_phone'))
                    Designed & Developed By Gratus Technologies: <a href="tel:2348163513389" style="color:var(--gold-2);text-decoration:none">2348163513389</a>
                @endif
            </div>
        </div>
    </div>
</footer>

<script>
    /* ── Drawer ──────────────────────────────────────────────────── */
    function toggleDrawer() {
        document.getElementById('navDrawer').classList.toggle('open');
    }
    document.addEventListener('click', function(e) {
        const drawer = document.getElementById('navDrawer');
        const burger = document.getElementById('navBurger');
        if (drawer.classList.contains('open') && !drawer.contains(e.target) && !burger.contains(e.target)) {
            drawer.classList.remove('open');
        }
    });

    /* ── Nav scroll tint ─────────────────────────────────────────── */
    window.addEventListener('scroll', () => {
        document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });

    /* ── Scroll-reveal ───────────────────────────────────────────── */
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) { e.target.classList.add('visible'); revealObserver.unobserve(e.target); }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    /* ── Countdown ───────────────────────────────────────────────── */
    @if(setting('camp_start_date'))
    (function(){
        const target = new Date('{{ setting("camp_start_date") }}T00:00:00');
        const pad = n => String(n).padStart(2,'0');
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) return;
            document.getElementById('cd-d').textContent = pad(Math.floor(diff / 86400000));
            document.getElementById('cd-h').textContent = pad(Math.floor(diff % 86400000 / 3600000));
            document.getElementById('cd-m').textContent = pad(Math.floor(diff % 3600000 / 60000));
            document.getElementById('cd-s').textContent = pad(Math.floor(diff % 60000 / 1000));
        }
        tick(); setInterval(tick, 1000);
    })();
    @endif
</script>
</body>
</html>
