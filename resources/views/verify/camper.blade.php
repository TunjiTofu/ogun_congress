<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ $camper->full_name }} — Verified</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: #0B2455;
            min-height: 100vh;
            padding: 2rem 1rem;
            display: flex;
            align-items: flex-start;
            justify-content: center;
        }

        .wrapper { width: 100%; max-width: 420px; }

        /* ── Header ─────────────────────────────── */
        .header {
            display: flex; align-items: center; gap: 0.75rem;
            margin-bottom: 1.25rem;
        }
        .header img { width: 40px; height: 40px; border-radius: 50%; border: 1.5px solid rgba(201,169,77,0.5); }
        .header-text { color: rgba(255,255,255,0.65); font-size: 0.72rem; line-height: 1.4; }
        .header-text strong { color: #E8C255; font-size: 0.8rem; display: block; }

        /* ── Card ───────────────────────────────── */
        .card { background: #fff; border-radius: 24px; overflow: hidden; box-shadow: 0 24px 60px rgba(0,0,0,0.4); }

        /* ── Photo + identity hero ──────────────── */
        .hero { position: relative; }
        .photo-frame {
            width: 100%; height: 450px; overflow: hidden;
            background: linear-gradient(135deg, #c8d6e5, #dfe6ee);
        }
        .photo-frame img {
            width: 100%; height: 100%;
            object-fit: cover; object-position: center top;
            display: block;
        }
        .photo-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            font-size: 5rem; color: #A5B4FC;
        }
        /* Dark overlay gradient at bottom */
        .photo-overlay {
            position: absolute; bottom: 0; left: 0; right: 0; height: 160px;
            background: linear-gradient(to top, rgba(11,36,85,0.95) 0%, rgba(11,36,85,0.4) 60%, transparent 100%);
        }
        /* Identity text on top of photo */
        .photo-identity {
            position: absolute; bottom: 0; left: 0; right: 0;
            padding: 1.5rem 1.5rem 1.25rem;
            z-index: 2;
        }
        .camper-name {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem; font-weight: 700;
            color: #fff; line-height: 1.1;
            margin-bottom: 0.25rem;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }
        .camper-code {
            font-family: monospace; font-size: 0.75rem;
            color: rgba(255,255,255,0.65); letter-spacing: 0.12em;
            font-weight: 600; margin-bottom: 0.6rem;
        }
        .dept-pill {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: rgba(201,169,77,0.2);
            border: 1px solid rgba(201,169,77,0.5);
            color: #E8C255; font-size: 0.7rem; font-weight: 700;
            padding: 0.25rem 0.75rem; border-radius: 100px;
        }
        .verified-badge {
            position: absolute; top: 12px; right: 12px;
            background: #065F46; color: #fff;
            font-size: 0.6rem; font-weight: 700;
            padding: 0.3rem 0.75rem; border-radius: 100px;
            letter-spacing: 0.06em; border: 1px solid rgba(255,255,255,0.2);
        }

        /* ── Body ───────────────────────────────── */
        .body { padding: 1.5rem; }

        /* ── Info rows ──────────────────────────── */
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1.25rem; }
        .info-item { }
        .info-item.full { grid-column: 1 / -1; }
        .info-lbl {
            font-size: 0.6rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.12em; color: #94A3B8; margin-bottom: 0.2rem;
        }
        .info-val { font-size: 0.88rem; font-weight: 600; color: #0F1C3F; line-height: 1.3; }

        .divider { height: 1px; background: #F1F5F9; margin: 1rem 0; }

        /* ── Section heading ────────────────────── */
        .section-h {
            font-size: 0.6rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.15em; color: #94A3B8;
            margin-bottom: 0.75rem;
        }

        /* ── Health ─────────────────────────────── */
        .health-ok { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 600; color: #059669; margin-bottom: 1rem; }
        .health-row { display: flex; gap: 0.5rem; margin-bottom: 0.4rem; font-size: 0.8rem; color: #374151; line-height: 1.5; }
        .health-row strong { color: #0F1C3F; min-width: 80px; font-size: 0.75rem; }

        /* ── Contact ────────────────────────────── */
        .contact-block {
            background: #F7F8FC; border-radius: 12px;
            padding: 0.9rem 1rem; margin-bottom: 0.5rem;
        }
        .contact-type { font-size: 0.6rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.2rem; }
        .contact-name { font-size: 0.9rem; font-weight: 700; color: #0F1C3F; }
        .contact-meta { font-size: 0.72rem; color: #64748B; margin-top: 0.15rem; line-height: 1.5; }

        /* ── Consent ────────────────────────────── */
        .consent-block {
            border-radius: 12px; padding: 0.9rem 1rem;
            display: flex; gap: 0.75rem; align-items: flex-start;
            margin-bottom: 0.75rem;
        }
        .consent-icon { font-size: 1.25rem; flex-shrink: 0; line-height: 1.4; }
        .consent-title { font-size: 0.8rem; font-weight: 700; margin-bottom: 0.15rem; }
        .consent-body  { font-size: 0.72rem; line-height: 1.5; }

        /* ── Verified footer ────────────────────── */
        .verified-footer {
            background: linear-gradient(135deg, #0B2455, #1B3A8F);
            border-radius: 14px; padding: 1rem 1.2rem;
            display: flex; align-items: center; gap: 0.75rem;
            margin-top: 1rem;
        }
        .verified-footer-check {
            width: 32px; height: 32px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; color: #E8C255; flex-shrink: 0;
        }
        .verified-footer-title { font-size: 0.78rem; font-weight: 700; color: #fff; }
        .verified-footer-sub { font-size: 0.67rem; color: rgba(255,255,255,0.55); margin-top: 0.1rem; }

        /* ── Footer ─────────────────────────────── */
        .page-footer {
            text-align: center; font-size: 0.65rem;
            color: rgba(255,255,255,0.35); margin-top: 1.25rem; line-height: 1.7;
        }
    </style>
</head>
<body>
<div class="wrapper">

    <div class="header">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Logo"/>
        <div class="header-text">
            <strong>Ogun Conference Youth Congress 2026</strong>
            Official Camper Verification Record
        </div>
    </div>

    <div class="card">

        {{-- Photo hero --}}
        <div class="hero">
            <div class="photo-frame">
                @if($camper->getFirstMedia('photo'))
                    <img src="{{ route('camper.photo', $camper->id) }}" alt="{{ $camper->full_name }}"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                    <div class="photo-placeholder" style="display:none">&#128100;</div>
                @else
                    <div class="photo-placeholder">&#128100;</div>
                @endif
            </div>
            <div class="photo-overlay"></div>
            <div class="photo-identity">
                <div class="camper-name">{{ $camper->full_name }}</div>
                <div class="camper-code">{{ $camper->camper_number }}</div>
                <span class="dept-pill">
                    &#127775; {{ $camper->category->label() }}
                    @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
                </span>
            </div>
            <div class="verified-badge">&#10003; VERIFIED</div>
        </div>

        {{-- Body --}}
        <div class="body">

            {{-- Personal info --}}
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-lbl">Church</div>
                    <div class="info-val">{{ $camper->church?->name ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-lbl">District</div>
                    <div class="info-val">{{ $camper->church?->district?->name ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-lbl">Gender</div>
                    <div class="info-val" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-lbl">Registered</div>
                    <div class="info-val">{{ $camper->created_at->format('d M Y') }}</div>
                </div>
                @if($camper->home_address)
                    <div class="info-item full">
                        <div class="info-lbl">Home Address</div>
                        <div class="info-val">{{ $camper->home_address }}</div>
                    </div>
                @endif
            </div>

            {{-- Health --}}
            @php $health = $camper->health; @endphp
            @if($health)
                <div class="divider"></div>
                <div class="section-h">Health &amp; Medical</div>
                @if(!$health->medical_conditions && !$health->medications && !$health->allergies)
                    <div class="health-ok">&#10003; No known medical conditions, medications, or allergies.</div>
                @else
                    @if($health->medical_conditions)
                        <div class="health-row"><strong>Conditions</strong> {{ $health->medical_conditions }}</div>
                    @endif
                    @if($health->medications)
                        <div class="health-row"><strong>Medications</strong> {{ $health->medications }}</div>
                    @endif
                    @if($health->allergies)
                        <div class="health-row"><strong>Allergies</strong> {{ $health->allergies }}</div>
                    @endif
                @endif
            @endif

            {{-- Contacts --}}
            @php $contacts = $camper->contacts()->get(); @endphp
            @if($contacts->count())
                <div class="divider"></div>
                <div class="section-h">Emergency &amp; Guardian Contacts</div>
                @foreach($contacts as $contact)
                    <div class="contact-block">
                        <div class="contact-type">{{ ucwords(str_replace('_',' ',$contact->type?->value ?? '')) }}</div>
                        <div class="contact-name">{{ $contact->full_name }}</div>
                        <div class="contact-meta">
                            @if($contact->relationship){{ $contact->relationship }} &bull; @endif
                            {{ $contact->phone ?? '' }}
                            @if($contact->email) &bull; {{ $contact->email }} @endif
                        </div>
                    </div>
                @endforeach
            @endif

            {{-- Consent --}}
            @if($camper->requiresConsentForm())
                <div class="divider"></div>
                <div class="section-h">Consent Form</div>
                @if($camper->consent_collected)
                    <div class="consent-block" style="background:#D1FAE5;border:1px solid #6EE7B7">
                        <div class="consent-icon">✅</div>
                        <div>
                            <div class="consent-title" style="color:#065F46">Physically collected by secretariat</div>
                            <div class="consent-body" style="color:#047857">This camper is fully cleared for all camp activities.</div>
                        </div>
                    </div>
                @elseif($camper->consent_form_path)
                    <div class="consent-block" style="background:#DBEAFE;border:1px solid #93C5FD">
                        <div class="consent-icon">📋</div>
                        <div>
                            <div class="consent-title" style="color:#1E40AF">Generated — not yet submitted</div>
                            <div class="consent-body" style="color:#1D4ED8">The signed physical copy must be submitted at check-in.</div>
                        </div>
                    </div>
                @else
                    <div class="consent-block" style="background:#FEF3C7;border:1px solid #FCD34D">
                        <div class="consent-icon">⚠️</div>
                        <div>
                            <div class="consent-title" style="color:#92400E">Not yet generated</div>
                            <div class="consent-body" style="color:#B45309">Will be generated shortly after registration.</div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Verified footer --}}
            <div class="verified-footer">
                <div class="verified-footer-check">&#10003;</div>
                <div>
                    <div class="verified-footer-title">Verified — {{ setting('camp_name','Ogun Conference Youth Congress') }} {{ now()->year }}</div>
                    <div class="verified-footer-sub">This record is authentic and officially registered.</div>
                </div>
            </div>

        </div>{{-- /.body --}}
    </div>{{-- /.card --}}

    <div class="page-footer">
        {{ setting('camp_name','Ogun Conference Youth Congress') }}<br/>
        {{ setting('camp_dates','Aug 16–22, 2026') }} &bull; {{ setting('camp_venue','Abeokuta, Ogun State') }}
    </div>

</div>
</body>
</html>
