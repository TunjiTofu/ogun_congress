<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>My Camp Portal — {{ $camper->full_name }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #0B2455; --navy2: #071640; --blue: #1B3A8F; --blue2: #2E5FAD;
            --gold: #C9A94D; --gold2: #E8C255; --light: #F4F6FB; --white: #fff;
            --muted: #64718F; --border: rgba(11,36,85,0.09);
            --shadow: 0 2px 20px rgba(11,36,85,0.07);
            --shadow-md: 0 8px 36px rgba(11,36,85,0.12);
        }
        body { font-family: 'DM Sans', sans-serif; background: var(--light); color: #1C2340; min-height: 100vh; }

        /* ── NAV ──────────────────────────────── */
        .nav {
            background: var(--navy2); padding: 0 1.5rem; height: 60px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid rgba(201,169,77,0.12);
        }
        .nav-brand { display: flex; align-items: center; gap: 0.7rem; text-decoration: none; }
        .nav-logo   { width: 36px; height: 36px; border-radius: 50%; border: 1.5px solid rgba(201,169,77,0.4); object-fit: cover; }
        .nav-name   { font-family: 'Playfair Display', serif; font-size: 0.75rem; color: var(--gold2); line-height: 1.3; }
        .nav-sub    { font-size: 0.58rem; color: rgba(255,255,255,0.4); }
        .nav-right  { display: flex; align-items: center; gap: 0.75rem; }
        .nav-code   { font-family: monospace; font-size: 0.75rem; color: rgba(255,255,255,0.55); }
        .btn-logout {
            font-size: 0.75rem; font-weight: 600; padding: 0.35rem 0.85rem;
            border-radius: 100px; border: 1px solid rgba(255,255,255,0.2);
            background: transparent; color: rgba(255,255,255,0.7); cursor: pointer; transition: 0.2s;
        }
        .btn-logout:hover { border-color: var(--gold); color: var(--gold2); }

        /* ── LAYOUT ────────────────────────────── */
        .container { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }

        /* ── HERO CARD ─────────────────────────── */
        .hero-card {
            background: linear-gradient(135deg, var(--navy2) 0%, var(--blue) 100%);
            border-radius: 22px; padding: 2rem;
            display: flex; align-items: center; gap: 1.5rem;
            margin-bottom: 1.5rem; position: relative; overflow: hidden;
        }
        .hero-card::after {
            content: ''; position: absolute; top: -30px; right: -30px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,0.04); pointer-events: none;
        }
        .hero-photo {
            width: 90px; height: 90px; border-radius: 50%; object-fit: cover;
            border: 3px solid rgba(201,169,77,0.45); flex-shrink: 0;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .hero-photo-ph {
            width: 90px; height: 90px; border-radius: 50%; flex-shrink: 0;
            background: rgba(255,255,255,0.08); display: flex; align-items: center;
            justify-content: center; font-size: 2.5rem;
            border: 3px solid rgba(201,169,77,0.3);
        }
        .hero-text { flex: 1; min-width: 0; }
        .hero-greeting { font-size: 0.68rem; font-weight: 700; letter-spacing: 0.18em; color: rgba(232,194,85,0.8); text-transform: uppercase; margin-bottom: 0.3rem; }
        .hero-name { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: #fff; line-height: 1.2; margin-bottom: 0.4rem; }
        .hero-dept {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(201,169,77,0.15); border: 1px solid rgba(201,169,77,0.3);
            color: var(--gold2); font-size: 0.72rem; font-weight: 700;
            padding: 0.25rem 0.75rem; border-radius: 100px; margin-bottom: 0.6rem;
        }
        .hero-code {
            font-family: monospace; font-size: 0.82rem; color: rgba(255,255,255,0.6); font-weight: 700;
            letter-spacing: 0.06em;
        }

        /* ── SECTION TITLE ─────────────────────── */
        .section-head { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; }
        .section-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 1rem; color: var(--navy); font-weight: 700; }

        /* ── PANEL ─────────────────────────────── */
        .panel {
            background: var(--white); border: 1px solid var(--border);
            border-radius: 18px; overflow: hidden;
            box-shadow: var(--shadow); margin-bottom: 1.5rem;
        }
        .panel-head {
            padding: 0.9rem 1.4rem; border-bottom: 1px solid #F1F5F9;
            background: #FAFBFF; display: flex; align-items: center; justify-content: space-between;
        }
        .panel-head-title { font-size: 0.85rem; font-weight: 700; color: var(--navy); display: flex; align-items: center; gap: 0.5rem; }
        .panel-body { padding: 1.25rem 1.4rem; }

        /* ── DOC CARDS ─────────────────────────── */
        .doc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
        .doc-card {
            border: 1px solid var(--border); border-radius: 14px; padding: 1.4rem;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .doc-card:hover { box-shadow: var(--shadow-md); transform: translateY(-2px); }
        .doc-icon { font-size: 2rem; margin-bottom: 0.75rem; line-height: 1; }
        .doc-title { font-family: 'Playfair Display', serif; font-size: 0.92rem; font-weight: 700; color: var(--navy); margin-bottom: 0.3rem; }
        .doc-desc  { font-size: 0.76rem; color: var(--muted); line-height: 1.55; margin-bottom: 1rem; }
        .btn-dl {
            display: inline-block; font-size: 0.78rem; font-weight: 700;
            padding: 0.55rem 1.1rem; border-radius: 100px; text-decoration: none; transition: 0.2s;
        }
        .btn-dl-navy { background: var(--navy); color: #fff; }
        .btn-dl-navy:hover { background: var(--blue); }
        .btn-dl-muted { background: #F1F5F9; color: #94A3B8; cursor: not-allowed; }

        /* ── DETAILS GRID ──────────────────────── */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
        .detail-cell { background: var(--light); border: 1px solid var(--border); border-radius: 10px; padding: 0.7rem 0.9rem; }
        .detail-cell.full { grid-column: 1 / -1; }
        .dlbl { font-size: 0.58rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 0.2rem; }
        .dval { font-size: 0.84rem; font-weight: 600; color: var(--navy); line-height: 1.3; }

        /* ── HEALTH ────────────────────────────── */
        .health-row { display: flex; gap: 0.6rem; align-items: flex-start; padding: 0.6rem 0; border-bottom: 1px solid #F8FAFF; }
        .health-row:last-child { border: none; }
        .health-icon { font-size: 1rem; flex-shrink: 0; }
        .health-text { font-size: 0.82rem; color: #374151; line-height: 1.5; }
        .health-lbl  { font-weight: 700; color: var(--navy); font-size: 0.75rem; display: block; margin-bottom: 0.1rem; }
        .no-issues   { display: flex; align-items: center; gap: 0.6rem; font-size: 0.82rem; color: #10B981; font-weight: 600; padding: 0.5rem 0; }

        /* ── CONTACTS ──────────────────────────── */
        .contact-row { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.75rem 0; border-bottom: 1px solid #F8FAFF; }
        .contact-row:last-child { border: none; }
        .contact-icon { font-size: 1.4rem; flex-shrink: 0; margin-top: 2px; }
        .contact-type  { font-size: 0.6rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.15rem; }
        .contact-name  { font-size: 0.84rem; font-weight: 700; color: var(--navy); }
        .contact-meta  { font-size: 0.73rem; color: var(--muted); margin-top: 0.1rem; }

        /* ── CONSENT STATUS ────────────────────── */
        .consent-banner { border-radius: 12px; padding: 1rem 1.2rem; display: flex; align-items: flex-start; gap: 0.75rem; margin-bottom: 0; }
        .consent-icon  { font-size: 1.4rem; flex-shrink: 0; }
        .consent-title { font-size: 0.82rem; font-weight: 700; margin-bottom: 0.2rem; }
        .consent-body  { font-size: 0.74rem; line-height: 1.55; }

        /* ── CHECKIN STATUS ────────────────────── */
        .checkin-badge {
            display: inline-flex; align-items: center; gap: 0.5rem;
            font-size: 0.78rem; font-weight: 700;
            padding: 0.5rem 1rem; border-radius: 100px;
        }

        @media (max-width: 600px) {
            .hero-card { flex-direction: column; text-align: center; }
            .doc-grid  { grid-template-columns: 1fr; }
            .detail-grid { grid-template-columns: 1fr; }
            .detail-cell.full { grid-column: 1; }
        }
    </style>
</head>
<body>

<nav class="nav">
    <a href="{{ route('home') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" class="nav-logo" alt="Logo"/>
        <div>
            <div class="nav-name">Camper Portal</div>
            <div class="nav-sub">{{ setting('camp_name','Ogun Youth Camp') }}</div>
        </div>
    </a>
    <div class="nav-right">
        <span class="nav-code">{{ $camper->camper_number }}</span>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit" class="btn-logout">Sign Out</button>
        </form>
    </div>
</nav>

<div class="container">

    {{-- ── Hero card ──────────────────────────────── --}}
    <div class="hero-card">
        @if($camper->getFirstMedia('photo'))
            <img src="{{ route('camper.photo', $camper->id) }}" class="hero-photo" alt="Photo"
                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
            <div class="hero-photo-ph" style="display:none">👤</div>
        @else
            <div class="hero-photo-ph">👤</div>
        @endif

        <div class="hero-text">
            <div class="hero-greeting">🎉 Welcome to Congress 2026</div>
            <div class="hero-name">{{ $camper->full_name }}</div>
            <div class="hero-dept">
                🏕️ {{ $camper->category->label() }}
                @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
            </div>
            <div class="hero-code">{{ $camper->camper_number }}</div>
        </div>
    </div>

    {{-- ── Documents ────────────────────────────────── --}}
    <div class="section-head">
        <span class="section-dot" style="background:#6366F1"></span>
        <span class="section-title">My Documents</span>
    </div>
    <div class="doc-grid" style="margin-bottom:1.5rem">
        <div class="doc-card">
            <div class="doc-icon">🪪</div>
            <div class="doc-title">ID Card</div>
            <div class="doc-desc">Your official camp ID card with QR code. Print and carry at all times during the congress.</div>
            @if($idCardUrl)
                <a href="{{ $idCardUrl }}" target="_blank" class="btn-dl btn-dl-navy">⬇ Download ID Card</a>
            @else
                <span class="btn-dl btn-dl-muted">⏳ Generating...</span>
            @endif
        </div>

        @if($camper->requiresConsentForm())
            <div class="doc-card">
                <div class="doc-icon">📋</div>
                <div class="doc-title">Consent Form</div>
                <div class="doc-desc">
                    Parental consent form for camp activities. Download, print, sign, and submit at check-in.
                </div>
                @if($consentFormUrl)
                    <a href="{{ $consentFormUrl }}" target="_blank" class="btn-dl btn-dl-navy">⬇ Download Consent Form</a>
                @else
                    <span class="btn-dl btn-dl-muted">⏳ Generating...</span>
                @endif
            </div>
        @endif
    </div>

    {{-- ── Check-in status ──────────────────────────── --}}
    @php
        $lastCheckin = $camper->checkinEvents()->latest()->first();
        $isCheckedIn = $lastCheckin && $lastCheckin->event_type?->value === 'check_in';
    @endphp
    <div class="section-head">
        <span class="section-dot" style="background:#10B981"></span>
        <span class="section-title">Camp Status</span>
    </div>
    <div class="panel" style="margin-bottom:1.5rem">
        <div class="panel-body" style="display:flex;align-items:center;gap:1.25rem;flex-wrap:wrap">
            <span class="checkin-badge" style="{{ $isCheckedIn
                ? 'background:#D1FAE5;color:#065F46;border:1px solid #6EE7B7'
                : 'background:#F1F5F9;color:#64718F;border:1px solid #E2E8F0' }}">
                {{ $isCheckedIn ? '✅ Checked In' : '⏳ Not Yet Checked In' }}
            </span>
            @if($lastCheckin)
                <span style="font-size:0.75rem;color:var(--muted)">
                Last scan: {{ $lastCheckin->created_at->format('d M Y, H:i') }}
            </span>
            @else
                <span style="font-size:0.75rem;color:var(--muted)">Check-in opens at camp registration on arrival day.</span>
            @endif

            @if($camper->requiresConsentForm())
                <span class="checkin-badge" style="{{ $camper->consent_collected
                ? 'background:#D1FAE5;color:#065F46;border:1px solid #6EE7B7'
                : 'background:#FEF3C7;color:#92400E;border:1px solid #FCD34D' }}">
                {{ $camper->consent_collected ? '📄 Consent Collected' : '⚠️ Consent Pending' }}
            </span>
            @endif
        </div>
    </div>

    {{-- ── Registration details ─────────────────────── --}}
    <div class="section-head">
        <span class="section-dot" style="background:var(--gold)"></span>
        <span class="section-title">My Registration Details</span>
    </div>
    <div class="panel">
        <div class="panel-head">
            <div class="panel-head-title">🏛️ Church & Ministry</div>
        </div>
        <div class="panel-body">
            <div class="detail-grid">
                <div class="detail-cell">
                    <div class="dlbl">Church</div>
                    <div class="dval">{{ $camper->church?->name ?? '—' }}</div>
                </div>
                <div class="detail-cell">
                    <div class="dlbl">District</div>
                    <div class="dval">{{ $camper->church?->district?->name ?? '—' }}</div>
                </div>
                <div class="detail-cell">
                    <div class="dlbl">Department</div>
                    <div class="dval">{{ $camper->category->label() }}</div>
                </div>
                @if($camper->club_rank)
                    <div class="detail-cell">
                        <div class="dlbl">Rank / Group</div>
                        <div class="dval">{{ $camper->club_rank }}</div>
                    </div>
                @endif
                <div class="detail-cell">
                    <div class="dlbl">Gender</div>
                    <div class="dval" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</div>
                </div>
                <div class="detail-cell">
                    <div class="dlbl">Registered On</div>
                    <div class="dval">{{ $camper->created_at->format('d M Y') }}</div>
                </div>
                @if($camper->home_address)
                    <div class="detail-cell full">
                        <div class="dlbl">Home Address</div>
                        <div class="dval">{{ $camper->home_address }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Health --}}
        @php $health = $camper->health; @endphp
        @if($health)
            <div class="panel-head" style="border-top:1px solid #F1F5F9">
                <div class="panel-head-title">🏥 Health Information</div>
            </div>
            <div class="panel-body">
                @if(!$health->medical_conditions && !$health->medications && !$health->allergies)
                    <div class="no-issues">✅ No known medical conditions, medications, or allergies on file.</div>
                @else
                    @if($health->medical_conditions)
                        <div class="health-row">
                            <span class="health-icon">🩺</span>
                            <div class="health-text">
                                <span class="health-lbl">Medical Conditions</span>
                                {{ $health->medical_conditions }}
                            </div>
                        </div>
                    @endif
                    @if($health->medications)
                        <div class="health-row">
                            <span class="health-icon">💊</span>
                            <div class="health-text">
                                <span class="health-lbl">Medications</span>
                                {{ $health->medications }}
                            </div>
                        </div>
                    @endif
                    @if($health->allergies)
                        <div class="health-row">
                            <span class="health-icon">⚠️</span>
                            <div class="health-text">
                                <span class="health-lbl">Allergies</span>
                                {{ $health->allergies }}
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Contacts --}}
        @php $contacts = $camper->contacts()->get(); @endphp
        @if($contacts->count())
            <div class="panel-head" style="border-top:1px solid #F1F5F9">
                <div class="panel-head-title">📞 Emergency Contacts</div>
            </div>
            <div class="panel-body">
                @foreach($contacts as $contact)
                    <div class="contact-row">
                        <span class="contact-icon">👤</span>
                        <div>
                            <div class="contact-type">{{ ucwords(str_replace('_',' ',$contact->type?->value ?? '')) }}</div>
                            <div class="contact-name">{{ $contact->full_name }}</div>
                            <div class="contact-meta">
                                @if($contact->relationship){{ $contact->relationship }} &bull; @endif
                                {{ $contact->phone ?? '' }}
                                @if($contact->email) &bull; {{ $contact->email }}@endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Consent status --}}
        @if($camper->requiresConsentForm())
            <div class="panel-head" style="border-top:1px solid #F1F5F9">
                <div class="panel-head-title">📋 Consent Form Status</div>
            </div>
            <div class="panel-body">
                <div class="consent-banner" style="{{ $camper->consent_collected
                ? 'background:#D1FAE5;border:1px solid #6EE7B7'
                : ($camper->consent_form_path
                    ? 'background:#DBEAFE;border:1px solid #93C5FD'
                    : 'background:#FEF3C7;border:1px solid #FCD34D') }}">
                <span class="consent-icon">
                    @if($camper->consent_collected) ✅
                    @elseif($camper->consent_form_path) 📋
                    @else ⚠️
                    @endif
                </span>
                    <div>
                        <div class="consent-title" style="{{ $camper->consent_collected
                        ? 'color:#065F46' : ($camper->consent_form_path ? 'color:#1E40AF' : 'color:#92400E') }}">
                            @if($camper->consent_collected)
                                Physical form received by secretariat on —
                            @elseif($camper->consent_form_path)
                                Form generated — physical copy not yet submitted
                            @else
                                Consent form not yet generated
                            @endif
                        </div>
                        <div class="consent-body" style="{{ $camper->consent_collected
                        ? 'color:#047857' : ($camper->consent_form_path ? 'color:#1D4ED8' : 'color:#B45309') }}">
                            @if($camper->consent_collected)
                                You are fully cleared. No further action needed.
                            @elseif($camper->consent_form_path)
                                Please download the form above, print it, get it signed by a parent or guardian, and bring it to check-in.
                            @else
                                This will be generated shortly. Check back soon.
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- ── Camp info ────────────────────────────────── --}}
    <div class="panel">
        <div class="panel-head">
            <div class="panel-head-title">📌 Camp Information</div>
        </div>
        <div class="panel-body">
            <div class="detail-grid">
                @foreach([
                    ['📅','Dates',setting('camp_dates','Aug 16–22, 2026')],
                    ['📍','Venue',setting('camp_venue','Abeokuta, Ogun State')],
                    ['🎯','Theme',setting('camp_theme','From the Word to the World')],
                    ['📞','Secretariat',setting('secretariat_phone','—')],
                ] as [$emoji,$lbl,$val])
                    <div class="detail-cell">
                        <div class="dlbl">{{ $emoji }} {{ $lbl }}</div>
                        <div class="dval">{{ $val }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

</div>
</body>
</html>
