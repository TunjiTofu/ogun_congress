<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>{{ setting('camp_name','Ogun Conference Youth Congress 2026') }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;0,900;1,500;1,700&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>

    <style>
        /* ─── Reset & Tokens ─────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; font-size: 16px; }

        :root {
            --navy:    #0B2455;
            --navy2:   #071640;
            --blue:    #1B3A8F;
            --blue2:   #2E5FAD;
            --gold:    #C9A94D;
            --gold2:   #E8C255;
            --green:   #2D6A30;
            --light:   #F4F6FB;
            --white:   #FFFFFF;
            --text:    #1C2340;
            --muted:   #64718F;
            --border:  rgba(11,36,85,0.1);
            --shadow:  0 2px 24px rgba(11,36,85,0.07);
            --shadow-md: 0 8px 40px rgba(11,36,85,0.11);
            --radius:  16px;
            --radius-sm: 10px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--text);
            background: var(--white);
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── Scroll animation ───────────────────────────────────────── */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        .reveal-delay-1 { transition-delay: 0.1s; }
        .reveal-delay-2 { transition-delay: 0.2s; }
        .reveal-delay-3 { transition-delay: 0.3s; }
        .reveal-delay-4 { transition-delay: 0.4s; }

        /* ─── Typography ─────────────────────────────────────────────── */
        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            line-height: 1.15;
            color: var(--navy);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(11,36,85,0.06);
            border: 1px solid rgba(201,169,77,0.35);
            color: var(--gold);
            font-family: 'DM Sans', sans-serif;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            padding: 0.3rem 0.85rem;
            border-radius: 100px;
            margin-bottom: 1rem;
        }
        .eyebrow-light {
            background: rgba(255,255,255,0.1);
            border-color: rgba(201,169,77,0.3);
            color: var(--gold2);
        }

        .section-heading {
            font-size: clamp(1.75rem, 3.2vw, 2.5rem);
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 0.75rem;
            line-height: 1.18;
        }
        .section-heading-light { color: var(--white); }

        .section-body {
            font-size: 1rem;
            color: var(--muted);
            line-height: 1.8;
            max-width: 560px;
        }

        /* ─── Layout containers ──────────────────────────────────────── */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .section-pad { padding: 96px 0; }
        .section-pad-sm { padding: 72px 0; }

        /* ─── NAV ────────────────────────────────────────────────────── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1000;
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 2rem; height: 68px;
            background: rgba(7,22,64,0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(201,169,77,0.12);
            transition: background 0.3s;
        }
        .nav.scrolled { background: rgba(7,22,64,0.92); }

        .nav-brand {
            display: flex; align-items: center; gap: 0.75rem; text-decoration: none; flex-shrink: 0;
        }
        .nav-logo {
            width: 42px; height: 42px; border-radius: 50%; object-fit: cover;
            border: 1.5px solid rgba(201,169,77,0.4);
            transition: border-color 0.2s;
        }
        .nav-brand:hover .nav-logo { border-color: var(--gold2); }
        .nav-name { font-family: 'Playfair Display', serif; font-size: 0.78rem; color: var(--gold2); letter-spacing: 0.05em; line-height: 1.35; }
        .nav-sub  { font-size: 0.58rem; color: rgba(255,255,255,0.4); letter-spacing: 0.04em; font-family: 'DM Sans', sans-serif; }

        .nav-links {
            display: flex; align-items: center; gap: 2rem;
        }
        .nav-links a {
            color: rgba(255,255,255,0.7); text-decoration: none;
            font-size: 0.82rem; font-weight: 500; letter-spacing: 0.01em;
            transition: color 0.2s;
        }
        .nav-links a:hover { color: var(--gold2); }

        .btn-nav {
            background: var(--gold); color: var(--navy2);
            font-family: 'DM Sans', sans-serif; font-size: 0.78rem; font-weight: 700;
            padding: 0.5rem 1.25rem; border-radius: 100px; text-decoration: none;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(201,169,77,0.3);
        }
        .btn-nav:hover { background: var(--gold2); transform: scale(1.03); box-shadow: 0 4px 20px rgba(201,169,77,0.4); }

        .nav-burger { display: none; background: none; border: none; cursor: pointer; padding: 6px; flex-direction: column; gap: 5px; }
        .nav-burger span { display: block; width: 24px; height: 2px; background: rgba(255,255,255,0.8); border-radius: 2px; transition: all 0.3s; }

        /* Mobile drawer */
        .nav-drawer {
            position: fixed; top: 68px; left: 0; right: 0; z-index: 999;
            background: rgba(7,22,64,0.97);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(201,169,77,0.15);
            max-height: 0; overflow: hidden; transition: max-height 0.35s ease;
        }
        .nav-drawer.open { max-height: 320px; }
        .nav-drawer-inner {
            display: flex; flex-direction: column; gap: 0; padding: 0.5rem 2rem 1.5rem;
        }
        .nav-drawer a {
            color: rgba(255,255,255,0.75); text-decoration: none;
            font-size: 0.9rem; font-weight: 500; padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            transition: color 0.2s;
        }
        .nav-drawer a:last-child { border: none; }
        .nav-drawer a:hover { color: var(--gold2); }

        /* ─── HERO ───────────────────────────────────────────────────── */
        .hero {
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            text-align: center; padding: 120px 2rem 80px;
            background: var(--navy2);
            position: relative; overflow: hidden;
        }

        /* Radial gradient blobs — no grid texture */
        .hero-blob {
            position: absolute; border-radius: 50%;
            filter: blur(80px); pointer-events: none;
        }
        .hero-blob-1 {
            width: 700px; height: 700px; top: -200px; left: -150px;
            background: radial-gradient(circle, rgba(27,58,143,0.45) 0%, transparent 70%);
        }
        .hero-blob-2 {
            width: 600px; height: 600px; bottom: -150px; right: -100px;
            background: radial-gradient(circle, rgba(27,58,143,0.35) 0%, transparent 70%);
        }
        .hero-blob-3 {
            width: 500px; height: 500px; top: 30%; left: 50%; transform: translateX(-50%);
            background: radial-gradient(circle, rgba(201,169,77,0.08) 0%, transparent 65%);
        }

        .hero-inner {
            position: relative; z-index: 1; max-width: 780px; margin: 0 auto;
            display: flex; flex-direction: column; align-items: center;
        }

        .hero-logo {
            width: 128px; height: 128px; border-radius: 50%; object-fit: cover;
            border: 2px solid rgba(201,169,77,0.4);
            box-shadow: 0 0 0 8px rgba(201,169,77,0.06), 0 20px 60px rgba(0,0,0,0.5);
            margin-bottom: 2rem;
            animation: heroFloat 8s ease-in-out infinite;
        }
        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50%       { transform: translateY(-12px); }
        }

        .hero-pill {
            display: inline-flex; align-items: center; gap: 0.45rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(201,169,77,0.3);
            color: var(--gold2);
            font-size: 0.68rem; font-weight: 600; letter-spacing: 0.2em; text-transform: uppercase;
            padding: 0.32rem 1rem; border-radius: 100px; margin-bottom: 1.5rem;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 900; color: var(--white);
            line-height: 1.08; letter-spacing: -0.01em;
            margin-bottom: 0.75rem;
        }
        .hero-title em { color: var(--gold2); font-style: italic; }

        .hero-verse {
            font-size: clamp(0.88rem, 1.6vw, 1.05rem);
            font-style: italic; font-weight: 300;
            color: rgba(255,255,255,0.58); line-height: 1.75;
            max-width: 560px; margin-bottom: 0.4rem;
        }
        .hero-ref {
            font-size: 0.75rem; font-weight: 700; letter-spacing: 0.18em;
            color: var(--gold); text-transform: uppercase; margin-bottom: 2.25rem;
        }

        .hero-meta {
            display: inline-flex; gap: 1.5rem; align-items: center; flex-wrap: wrap; justify-content: center;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 100px; padding: 0.6rem 1.75rem;
            font-size: 0.82rem; color: rgba(255,255,255,0.65);
            margin-bottom: 2.5rem;
        }
        .hero-meta strong { color: rgba(255,255,255,0.92); }
        .hero-dot { width: 3px; height: 3px; border-radius: 50%; background: rgba(201,169,77,0.5); flex-shrink: 0; }

        .hero-cta { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3rem; }

        .btn-primary {
            background: var(--gold); color: var(--navy2);
            font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 700;
            padding: 0.85rem 2.25rem; border-radius: 100px; text-decoration: none; border: none; cursor: pointer;
            box-shadow: 0 4px 20px rgba(201,169,77,0.4);
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s; display: inline-block;
        }
        .btn-primary:hover { background: var(--gold2); transform: scale(1.04); box-shadow: 0 8px 32px rgba(201,169,77,0.5); }

        .btn-outline {
            background: transparent; color: rgba(255,255,255,0.85);
            border: 1.5px solid rgba(255,255,255,0.22);
            font-family: 'DM Sans', sans-serif; font-size: 0.9rem; font-weight: 500;
            padding: 0.85rem 2.25rem; border-radius: 100px; text-decoration: none;
            transition: border-color 0.2s, color 0.2s, transform 0.2s; display: inline-block;
        }
        .btn-outline:hover { border-color: var(--gold); color: var(--gold2); transform: scale(1.03); }

        /* Countdown */
        .countdown { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; align-items: flex-end; }
        .cd-item { text-align: center; }
        .cd-num {
            font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700;
            color: var(--white); min-width: 64px; display: block;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(201,169,77,0.2);
            border-radius: var(--radius-sm); padding: 0.5rem 0.75rem; line-height: 1;
        }
        .cd-lbl { font-size: 0.55rem; letter-spacing: 0.14em; color: rgba(255,255,255,0.4); margin-top: 0.4rem; text-transform: uppercase; font-family: 'DM Sans', sans-serif; }
        .cd-sep { color: var(--gold); font-size: 1.5rem; line-height: 1; opacity: 0.5; padding-bottom: 0.8rem; }

        /* ─── ACCESS ─────────────────────────────────────────────────── */
        .access { background: var(--light); padding: 96px 0; }

        .access-header { text-align: center; margin-bottom: 3rem; }

        .access-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; max-width: 860px; margin: 0 auto; }

        .access-card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius); padding: 2.25rem;
            box-shadow: var(--shadow);
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .access-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }

        .access-icon {
            width: 48px; height: 48px; border-radius: 12px;
            background: rgba(11,36,85,0.06);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem; margin-bottom: 1rem;
        }
        .access-card-title { font-family: 'Playfair Display', serif; font-size: 1.15rem; color: var(--navy); font-weight: 700; margin-bottom: 0.5rem; }
        .access-card-desc  { font-size: 0.84rem; color: var(--muted); line-height: 1.7; margin-bottom: 1.5rem; }

        .code-row { display: flex; gap: 0.5rem; }
        .code-field {
            flex: 1; padding: 0.78rem 1rem;
            background: var(--light); border: 1.5px solid rgba(11,36,85,0.12);
            border-radius: 100px; color: var(--navy);
            font-family: monospace; font-size: 0.88rem; font-weight: 600;
            text-align: center; letter-spacing: 0.08em; text-transform: uppercase;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .code-field::placeholder { color: #A0AABF; letter-spacing: 0.04em; font-weight: 400; }
        .code-field:focus { border-color: var(--blue); background: var(--white); box-shadow: 0 0 0 3px rgba(27,58,143,0.08); }

        .btn-submit {
            background: var(--blue); color: var(--white);
            font-family: 'DM Sans', sans-serif; font-size: 0.82rem; font-weight: 700;
            padding: 0.78rem 1.25rem; border-radius: 100px; border: none;
            cursor: pointer; white-space: nowrap; text-decoration: none;
            display: inline-flex; align-items: center;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 12px rgba(27,58,143,0.25);
        }
        .btn-submit:hover { background: var(--navy); transform: scale(1.04); box-shadow: 0 4px 20px rgba(27,58,143,0.35); }

        .access-hint {
            display: flex; align-items: center; gap: 0.6rem;
            margin-top: 0.9rem; font-size: 0.72rem; color: #9BAAC4;
        }
        .access-hint::before, .access-hint::after { content: ''; flex: 1; height: 1px; background: var(--border); }
        .err { color: #DC2626; font-size: 0.75rem; margin-top: 0.6rem; }

        /* ─── HOW TO REGISTER ────────────────────────────────────────── */
        .how { background: var(--white); padding: 96px 0; }

        .how-header { margin-bottom: 4rem; }

        .steps { display: grid; grid-template-columns: repeat(4,1fr); gap: 1.5rem; }

        .step {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 2rem 1.5rem;
            text-align: center; position: relative;
            box-shadow: var(--shadow);
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .step:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }

        .step-badge {
            width: 38px; height: 38px; border-radius: 50%;
            background: var(--navy); color: var(--white);
            font-family: 'Playfair Display', serif; font-size: 0.9rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem; box-shadow: 0 4px 12px rgba(11,36,85,0.25);
        }
        .step-icon { font-size: 1.75rem; margin-bottom: 0.75rem; }
        .step-title { font-family: 'Playfair Display', serif; font-size: 0.92rem; color: var(--navy); font-weight: 700; margin-bottom: 0.5rem; }
        .step-desc  { font-size: 0.78rem; color: var(--muted); line-height: 1.65; }

        /* Arrow connector */
        .step:not(:last-child)::after {
            content: '→';
            position: absolute; right: -14px; top: 50%; transform: translateY(-50%);
            font-size: 1.1rem; color: var(--gold); z-index: 2;
        }

        /* Bank details */
        .bank-box {
            margin-top: 3rem;
            background: linear-gradient(135deg, rgba(11,36,85,0.04) 0%, rgba(201,169,77,0.05) 100%);
            border: 1px solid rgba(201,169,77,0.25);
            border-left: 4px solid var(--gold);
            border-radius: var(--radius); padding: 1.75rem 2.25rem;
            display: flex; gap: 3rem; align-items: center; flex-wrap: wrap;
        }
        .bank-lbl { font-size: 0.62rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--muted); margin-bottom: 0.25rem; }
        .bank-val  { font-size: 0.96rem; font-weight: 700; color: var(--navy); }
        .bank-acct { font-family: monospace; font-size: 1.6rem; font-weight: 900; color: var(--navy); letter-spacing: 0.06em; }

        /* ─── FEES ───────────────────────────────────────────────────── */
        .fees { background: var(--light); padding: 96px 0; }

        .fees-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 1.5rem; margin-top: 3rem; }

        .fee-card {
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius); overflow: hidden;
            box-shadow: var(--shadow); text-align: center;
            transition: box-shadow 0.25s, transform 0.25s;
        }
        .fee-card:hover { box-shadow: var(--shadow-md); transform: translateY(-3px); }

        .fee-bar { height: 4px; }
        .fee-bar-a { background: linear-gradient(90deg, #1E88E5, #64B5F6); }
        .fee-bar-p { background: linear-gradient(90deg, var(--green), #4CAF50); }
        .fee-bar-s { background: linear-gradient(90deg, var(--gold), var(--gold2)); }

        .fee-body { padding: 2.25rem 1.75rem 1.25rem; }
        .fee-logo { width: 84px; height: 84px; object-fit: contain; margin: 0 auto 1.25rem; display: block; }
        .fee-name  { font-family: 'Playfair Display', serif; font-size: 1.05rem; font-weight: 700; color: var(--navy); margin-bottom: 0.2rem; }
        .fee-ages  { font-size: 0.75rem; color: var(--muted); margin-bottom: 1.25rem; }
        .fee-price { font-family: 'Playfair Display', serif; font-size: 2.25rem; font-weight: 700; color: var(--navy); }

        .fee-foot {
            padding: 0.9rem 1.75rem;
            font-size: 0.73rem; color: var(--muted); line-height: 1.55;
            border-top: 1px solid var(--border);
        }

        /* ─── ABOUT ──────────────────────────────────────────────────── */
        .about { background: var(--white); padding: 96px 0; }

        .about-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 5rem; align-items: center; }

        .about-logo-wrap { text-align: center; position: relative; }
        .about-logo {
            width: 300px; height: 300px; object-fit: cover; border-radius: 50%;
            border: 4px solid rgba(201,169,77,0.2);
            box-shadow: 0 0 0 16px rgba(201,169,77,0.05), 0 20px 60px rgba(11,36,85,0.14);
            display: block; margin: 0 auto;
        }
        .about-credit { font-size: 0.68rem; color: var(--muted); margin-top: 1.25rem; font-style: italic; }

        .about-quote {
            background: var(--navy2); border-radius: var(--radius);
            padding: 1.75rem 2rem; margin-top: 2rem; position: relative; overflow: hidden;
        }
        .about-quote::before {
            content: '\201C'; position: absolute;
            top: -16px; left: 16px;
            font-size: 6rem; color: rgba(201,169,77,0.12);
            font-family: Georgia, serif; line-height: 1;
        }
        .about-quote p {
            font-style: italic; font-size: 0.92rem; color: rgba(255,255,255,0.8);
            line-height: 1.75; position: relative; z-index: 1;
        }
        .about-quote cite {
            display: block; margin-top: 0.6rem;
            font-size: 0.68rem; font-weight: 700;
            color: var(--gold); letter-spacing: 0.1em; font-style: normal;
        }

        .about-facts { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 2rem; }
        .fact {
            background: var(--light); border: 1px solid var(--border);
            border-left: 3px solid var(--gold); border-radius: var(--radius-sm);
            padding: 0.85rem 1.1rem;
        }
        .fact-lbl { font-size: 0.6rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; color: var(--muted); margin-bottom: 0.2rem; }
        .fact-val  { font-family: 'Playfair Display', serif; font-size: 0.82rem; font-weight: 600; color: var(--navy); }

        /* ─── RULES ──────────────────────────────────────────────────── */
        .rules { background: var(--light); padding: 96px 0; }

        .rules-header { margin-bottom: 3rem; }
        .rules-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 1rem; }

        .rule {
            display: flex; gap: 0.85rem; align-items: flex-start;
            background: var(--white); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 1.1rem 1.25rem;
            box-shadow: 0 1px 8px rgba(11,36,85,0.04);
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .rule:hover { box-shadow: 0 4px 20px rgba(11,36,85,0.08); transform: translateY(-2px); }
        .rule-icon { color: var(--gold); font-size: 0.85rem; margin-top: 3px; flex-shrink: 0; }
        .rule-text { font-size: 0.82rem; color: #4A5568; line-height: 1.6; }

        /* ─── CONTACT ────────────────────────────────────────────────── */
        .contact { background: var(--white); padding: 96px 0; }

        .contact-inner { display: grid; grid-template-columns: 1fr 1.45fr; gap: 4rem; }
        .contact-header { margin-bottom: 2rem; }

        .contact-channels { display: flex; flex-direction: column; gap: 1rem; }
        .contact-ch {
            display: flex; gap: 1rem; align-items: center;
            background: var(--light); border: 1px solid var(--border);
            border-radius: var(--radius-sm); padding: 1.1rem 1.4rem;
            text-decoration: none;
            transition: border-color 0.2s, box-shadow 0.2s, transform 0.2s;
        }
        .contact-ch:hover { border-color: var(--gold); box-shadow: 0 4px 20px rgba(201,169,77,0.1); transform: translateY(-2px); }
        .contact-ch-icon { font-size: 1.6rem; flex-shrink: 0; }
        .contact-ch-type { font-family: 'Playfair Display', serif; font-size: 0.86rem; font-weight: 700; color: var(--navy); }
        .contact-ch-val  { font-size: 0.74rem; color: var(--muted); margin-top: 0.1rem; }

        /* Form */
        .cform {
            background: var(--light); border: 1px solid var(--border);
            border-radius: var(--radius); padding: 2.25rem;
        }
        .cform h3 { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--navy); margin-bottom: 1.5rem; font-weight: 700; }

        .frow { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem; }
        .fgrp { display: flex; flex-direction: column; gap: 0.35rem; margin-bottom: 0.75rem; }
        .flbl { font-size: 0.62rem; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.12em; }

        .finput, .fselect, .ftextarea {
            padding: 0.75rem 1rem;
            background: var(--white); border: 1.5px solid rgba(11,36,85,0.12);
            border-radius: var(--radius-sm); color: var(--text);
            font-family: 'DM Sans', sans-serif; font-size: 0.88rem;
            outline: none; transition: border-color 0.2s, box-shadow 0.2s; width: 100%;
        }
        .finput:focus, .fselect:focus, .ftextarea:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(27,58,143,0.08);
        }
        .finput::placeholder, .ftextarea::placeholder { color: #A0AABF; }
        .fselect option { background: var(--white); color: var(--text); }
        .ftextarea { resize: vertical; min-height: 96px; }

        .flash-ok {
            background: #F0FDF4; border: 1px solid #86EFAC;
            border-radius: var(--radius-sm); padding: 0.75rem 1rem;
            color: #15803D; font-size: 0.82rem; margin-bottom: 1.25rem;
        }

        .btn-form {
            background: var(--navy); color: var(--white);
            font-family: 'DM Sans', sans-serif; font-size: 0.88rem; font-weight: 700;
            padding: 0.85rem 2rem; border-radius: 100px; border: none; cursor: pointer;
            transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 4px 16px rgba(11,36,85,0.2);
        }
        .btn-form:hover { background: var(--blue); transform: scale(1.03); box-shadow: 0 6px 24px rgba(27,58,143,0.3); }

        /* ─── FOOTER ─────────────────────────────────────────────────── */
        footer {
            background: var(--navy2); color: rgba(255,255,255,0.45);
            text-align: center; padding: 3rem 2rem; font-size: 0.78rem;
            font-family: 'DM Sans', sans-serif;
        }
        .footer-logo {
            width: 54px; height: 54px; border-radius: 50%; object-fit: cover;
            border: 1.5px solid rgba(201,169,77,0.3); opacity: 0.9;
            margin: 0 auto 0.85rem; display: block;
        }
        .footer-name {
            font-family: 'Playfair Display', serif;
            color: var(--gold); font-size: 0.85rem; letter-spacing: 0.08em; margin-bottom: 0.3rem;
        }

        /* ─── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 1024px) {
            .steps { grid-template-columns: 1fr 1fr; }
            .step:not(:last-child)::after { display: none; }
            .about-inner { gap: 3rem; }
        }
        @media (max-width: 900px) {
            .about-inner, .contact-inner { grid-template-columns: 1fr; }
            .fees-grid { grid-template-columns: 1fr 1fr; }
            .access-grid { grid-template-columns: 1fr; }
            .about-logo { width: 220px; height: 220px; }
        }
        @media (max-width: 640px) {
            .container { padding: 0 1.25rem; }
            .section-pad { padding: 64px 0; }
            .nav { padding: 0 1.25rem; }
            .nav-links { display: none; }
            .nav-burger { display: flex; }
            .steps { grid-template-columns: 1fr; }
            .fees-grid { grid-template-columns: 1fr; }
            .frow { grid-template-columns: 1fr; }
            .bank-box { flex-direction: column; gap: 1.25rem; }
            .hero-meta { padding: 0.55rem 1rem; }
            .access-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- ── NAV ──────────────────────────────────────────────────────────────── -->
<nav class="nav" id="mainNav">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="nav-logo"/>
        <div>
            <div class="nav-name">Ogun Conference</div>
            <div class="nav-sub">Youth Congress 2026 · SDA</div>
        </div>
    </a>
    <div class="nav-links">
        <a href="#register">How to Register</a>
        <a href="#fees">Departments</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
        <a href="#access" class="btn-nav">Enter Code</a>
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
        <a href="#access" onclick="toggleDrawer()" style="color:var(--gold2);font-weight:700">Enter Code →</a>
    </div>
</div>

<!-- ── HERO ──────────────────────────────────────────────────────────────── -->
<section class="hero">
    <div class="hero-blob hero-blob-1"></div>
    <div class="hero-blob hero-blob-2"></div>
    <div class="hero-blob hero-blob-3"></div>

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
            <a href="#register" class="btn-outline">How It Works</a>
        </div>

        @if(setting('camp_start_date'))
            <div class="countdown">
                <div class="cd-item"><span class="cd-num" id="cd-d">--</span><div class="cd-lbl">Days</div></div>
                <div class="cd-sep">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-h">--</span><div class="cd-lbl">Hours</div></div>
                <div class="cd-sep">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-m">--</span><div class="cd-lbl">Mins</div></div>
                <div class="cd-sep">:</div>
                <div class="cd-item"><span class="cd-num" id="cd-s">--</span><div class="cd-lbl">Secs</div></div>
            </div>
        @endif
    </div>
</section>

<!-- ── ACCESS ────────────────────────────────────────────────────────────── -->
<section class="access" id="access">
    <div class="container">
        <div class="access-header">
            <div style="display:flex;justify-content:center">
                <span class="eyebrow">Your Gateway</span>
            </div>
            <h2 class="section-heading" style="text-align:center">Access Your Registration</h2>
            <p class="section-body" style="text-align:center;margin:0 auto">
                Enter your registration code to complete your details, or log into your camper portal to download your ID card and documents.
            </p>
        </div>

        <div class="access-grid reveal">
            <div class="access-card">
                <div class="access-icon">&#128273;</div>
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
                        <button type="submit" class="btn-submit">Go &rarr;</button>
                    </div>
                </form>
                @if(session('error') && !session('portal_error'))
                    <p class="err">{{ session('error') }}</p>
                @endif
            </div>

            <div class="access-card">
                <div class="access-icon">&#127823;</div>
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
                        <button type="submit" class="btn-submit">Enter &rarr;</button>
                    </div>
                </form>
                @if(session('portal_error'))
                    <p class="err">{{ session('portal_error') }}</p>
                @endif
                <div class="access-hint">Same code used to register</div>
            </div>
        </div>
    </div>
</section>

<!-- ── HOW TO REGISTER ───────────────────────────────────────────────────── -->
<section class="how" id="register">
    <div class="container">
        <div class="how-header">
            <span class="eyebrow">Registration</span>
            <h2 class="section-heading">How to Register</h2>
            <p class="section-body">
                Registration is coordinated through your local church. Your church coordinator handles payment and code generation on behalf of your congregation.
            </p>
        </div>

        <div class="steps">
            @foreach([
                ['1','&#127978;','Contact Your Coordinator','Reach your local church coordinator to express interest and confirm your department.'],
                ['2','&#127982;','Church Makes Payment','The coordinator pays the total for all registered campers via bank transfer or Paystack.'],
                ['3','&#128273;','Receive Your Code','A unique code is sent to your phone via SMS once payment is confirmed.'],
                ['4','&#10003;','Complete Your Form','Enter your code here and fill in the registration wizard to secure your spot.'],
            ] as $i => [$n,$icon,$t,$d])
                <div class="step reveal reveal-delay-{{ $i + 1 }}">
                    <div class="step-badge">{{ $n }}</div>
                    <div class="step-icon">{!! $icon !!}</div>
                    <div class="step-title">{{ $t }}</div>
                    <div class="step-desc">{{ $d }}</div>
                </div>
            @endforeach
        </div>

        @if(setting('bank_account_number'))
            <div class="bank-box reveal">
                <div>
                    <div class="bank-lbl">Bank</div>
                    <div class="bank-val">{{ setting('bank_name') }}</div>
                </div>
                <div>
                    <div class="bank-lbl">Account Number</div>
                    <div class="bank-acct">{{ setting('bank_account_number') }}</div>
                </div>
                <div>
                    <div class="bank-lbl">Account Name</div>
                    <div class="bank-val">{{ setting('bank_account_name') }}</div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- ── DEPARTMENTS / FEES ─────────────────────────────────────────────────── -->
<section class="fees" id="fees">
    <div class="container">
        <span class="eyebrow">Departments</span>
        <h2 class="section-heading">Camp Fees {{ now()->year }}</h2>
        <p class="section-body">Fees are set per department based on age group. Contact your church coordinator to register.</p>

        <div class="fees-grid">
            <div class="fee-card reveal reveal-delay-1">
                <div class="fee-bar fee-bar-a"></div>
                <div class="fee-body">
                    <img src="{{ asset('images/adventurer_logo.png') }}" alt="Adventurer Club" class="fee-logo"/>
                    <div class="fee-name">Adventurers</div>
                    <div class="fee-ages">Ages 6 &ndash; 9</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_adventurer',5000)) }}</div>
                </div>
                <div class="fee-foot">Parent/guardian must accompany. Consent form required.</div>
            </div>
            <div class="fee-card reveal reveal-delay-2">
                <div class="fee-bar fee-bar-p"></div>
                <div class="fee-body">
                    <img src="{{ asset('images/pathfinder_logo.png') }}" alt="Pathfinder Club" class="fee-logo"/>
                    <div class="fee-name">Pathfinders</div>
                    <div class="fee-ages">Ages 10 &ndash; 15</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_pathfinder',5000)) }}</div>
                </div>
                <div class="fee-foot">Consent form required for all Pathfinders.</div>
            </div>
            <div class="fee-card reveal reveal-delay-3">
                <div class="fee-bar fee-bar-s"></div>
                <div class="fee-body">
                    <img src="{{ asset('images/senior_youth_logo.png') }}" alt="Senior Youth" class="fee-logo"/>
                    <div class="fee-name">Senior Youth (SYL)</div>
                    <div class="fee-ages">Ages 16 and above</div>
                    <div class="fee-price">&#8358;{{ number_format((int) setting('fee_senior_youth',7000)) }}</div>
                </div>
                <div class="fee-foot">Ambassador (16–21) &bull; Young Adults (22+)</div>
            </div>
        </div>
    </div>
</section>

<!-- ── ABOUT ─────────────────────────────────────────────────────────────── -->
<section class="about" id="about">
    <div class="container">
        <div class="about-inner">
            <div class="reveal">
                <span class="eyebrow">About the Congress</span>
                <h2 class="section-heading">A Visual Manifesto<br/>for Ogun's Youth</h2>
                <p class="section-body" style="max-width:100%">
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

            <div class="about-logo-wrap reveal reveal-delay-2">
                <img src="{{ asset('images/congress_logo.png') }}" alt="Congress Logo" class="about-logo"/>
                <p class="about-credit">Logo designed by Master Guide Chrisadim Emmanuel</p>
            </div>
        </div>
    </div>
</section>

<!-- ── CAMP RULES ─────────────────────────────────────────────────────────── -->
<section class="rules">
    <div class="container">
        <div class="rules-header">
            <span class="eyebrow">Guidelines</span>
            <h2 class="section-heading">Camp Rules</h2>
        </div>
        <div class="rules-grid">
            @foreach(['All campers must carry their printed ID card at all times during camp.','Campers under 18 must submit a signed parental consent form at check-in.','Participants must wear the official camp uniform during formal sessions.','Mobile phones should be kept on silent during services and meetings.','No camper may leave the camp venue without prior permission from officials.','All campers are expected to participate in the programme respectfully.'] as $rule)
                <div class="rule reveal">
                    <span class="rule-icon">&#10022;</span>
                    <span class="rule-text">{{ $rule }}</span>
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
                <div class="contact-header">
                    <span class="eyebrow">Get in Touch</span>
                    <h2 class="section-heading">Contact Us</h2>
                    <p class="section-body">For enquiries, complaints, or payment questions.</p>
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

            <div class="cform reveal reveal-delay-2">
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
                    <button type="submit" class="btn-form">Send Message &rarr;</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- ── FOOTER ─────────────────────────────────────────────────────────────── -->
<footer>
    <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="footer-logo"/>
    <div class="footer-name">Ogun Conference Youth Department</div>
    <p>Seventh-day Adventist Church &bull; {{ now()->year }}</p>
    @if(setting('secretariat_phone'))
        <p style="margin-top:0.5rem">Secretariat: <a href="tel:{{ setting('secretariat_phone') }}" style="color:var(--gold);text-decoration:none">{{ setting('secretariat_phone') }}</a></p>
    @endif
</footer>

<script>
    /* ── Drawer toggle ─────────────────────────────────────────── */
    function toggleDrawer() {
        const drawer = document.getElementById('navDrawer');
        drawer.classList.toggle('open');
    }

    /* ── Nav scroll tint ───────────────────────────────────────── */
    window.addEventListener('scroll', () => {
        document.getElementById('mainNav').classList.toggle('scrolled', window.scrollY > 40);
    }, { passive: true });

    /* ── Scroll-reveal via IntersectionObserver ─────────────────── */
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); }});
    }, { threshold: 0.12 });
    document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

    /* ── Countdown ──────────────────────────────────────────────── */
    @if(setting('camp_start_date'))
    (function(){
        const t = new Date('{{ setting('camp_start_date') }}T00:00:00');
        const p = n => String(n).padStart(2,'0');
        function tick(){
            const d = t - new Date(); if(d<=0) return;
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
