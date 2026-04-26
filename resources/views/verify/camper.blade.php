<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Camper Verification — {{ $camper->camper_number }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #0B2455; --navy2: #071640; --blue: #1B3A8F;
            --gold: #C9A94D; --gold2: #E8C255; --light: #F4F6FB;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(160deg, var(--navy2) 0%, var(--blue) 100%);
            min-height: 100vh; display: flex; align-items: flex-start;
            justify-content: center; padding: 2rem 1rem;
        }
        .card {
            background: #fff; border-radius: 24px;
            width: 100%; max-width: 480px;
            overflow: hidden; box-shadow: 0 32px 80px rgba(0,0,0,0.4);
        }
        /* Header */
        .card-head {
            background: linear-gradient(135deg, var(--navy2), var(--blue));
            padding: 1.5rem; display: flex; align-items: center; gap: 1rem;
        }
        .card-head-logo { width: 54px; height: 54px; border-radius: 50%; border: 2px solid rgba(201,169,77,0.5); object-fit: cover; flex-shrink: 0; }
        .card-head-text-title { font-family: 'Playfair Display', serif; font-size: 0.78rem; color: var(--gold2); letter-spacing: 0.06em; line-height: 1.4; }
        .card-head-text-sub   { font-size: 0.62rem; color: rgba(255,255,255,0.5); margin-top: 0.1rem; }

        /* Gold accent line */
        .accent-bar { height: 3px; background: linear-gradient(90deg, var(--navy), var(--gold), var(--navy)); }

        /* Photo */
        .photo-section { padding: 1.5rem 1.5rem 0; }
        .photo-wrap { position: relative; }
        .camper-photo {
            width: 100%; max-height: 280px; object-fit: cover; object-position: top center;
            border-radius: 16px; border: 2px solid rgba(11,36,85,0.1); display: block;
        }
        .photo-placeholder {
            width: 100%; height: 180px; border-radius: 16px;
            background: var(--light); display: flex; align-items: center;
            justify-content: center; font-size: 3.5rem; color: #CBD5E1;
            border: 2px solid rgba(11,36,85,0.08);
        }
        .verified-badge {
            position: absolute; bottom: 10px; right: 10px;
            background: #065F46; color: #fff;
            font-size: 0.65rem; font-weight: 700;
            padding: 0.3rem 0.7rem; border-radius: 100px;
            display: flex; align-items: center; gap: 0.3rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        /* Core info */
        .info { padding: 1.25rem 1.5rem 0; }
        .camper-name { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: var(--navy); line-height: 1.2; margin-bottom: 0.25rem; }
        .camper-code { font-family: monospace; font-size: 0.82rem; color: var(--blue); font-weight: 700; letter-spacing: 0.06em; margin-bottom: 0.75rem; }
        .dept-pill {
            display: inline-flex; gap: 0.4rem; align-items: center;
            background: rgba(201,169,77,0.1); border: 1px solid rgba(201,169,77,0.35);
            color: var(--navy); font-size: 0.72rem; font-weight: 700;
            padding: 0.28rem 0.8rem; border-radius: 100px; margin-bottom: 1.25rem;
        }

        /* Detail grid */
        .section-title {
            font-size: 0.6rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.14em;
            margin: 0 1.5rem 0.75rem; padding-top: 1rem;
            border-top: 1px solid #F1F5F9;
            display: flex; align-items: center; gap: 0.5rem;
        }
        .section-title::after { content: ''; flex: 1; height: 1px; background: #F1F5F9; }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; padding: 0 1.5rem; }
        .detail-cell {
            background: var(--light); border-radius: 10px;
            padding: 0.7rem 0.85rem; border: 1px solid rgba(11,36,85,0.07);
        }
        .detail-cell.full { grid-column: 1 / -1; }
        .detail-lbl { font-size: 0.58rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 0.2rem; }
        .detail-val { font-size: 0.82rem; font-weight: 600; color: var(--navy); line-height: 1.4; }

        /* Health */
        .health-item { display: flex; gap: 0.5rem; align-items: flex-start; margin-bottom: 0.4rem; }
        .health-dot  { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); flex-shrink: 0; margin-top: 5px; }
        .health-text { font-size: 0.8rem; color: #4B5563; line-height: 1.5; }
        .no-issues   { font-size: 0.8rem; color: #10B981; font-weight: 600; }

        /* Contacts */
        .contact-card {
            background: var(--light); border-radius: 10px;
            padding: 0.75rem 0.85rem; border: 1px solid rgba(11,36,85,0.07);
            margin: 0 1.5rem 0.5rem;
        }
        .contact-type { font-size: 0.58rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 0.2rem; }
        .contact-name { font-size: 0.82rem; font-weight: 700; color: var(--navy); }
        .contact-meta { font-size: 0.73rem; color: #6B7280; margin-top: 0.1rem; }

        /* Consent status */
        .consent-banner {
            margin: 0.75rem 1.5rem 0; border-radius: 10px; padding: 0.85rem 1rem;
            display: flex; align-items: center; gap: 0.75rem;
        }
        .consent-icon { font-size: 1.3rem; flex-shrink: 0; }
        .consent-title { font-size: 0.78rem; font-weight: 700; }
        .consent-sub   { font-size: 0.7rem; margin-top: 0.1rem; }

        /* Verified strip */
        .verified-strip {
            margin: 1rem 1.5rem 0;
            background: #D1FAE5; border: 1px solid #6EE7B7; border-radius: 12px;
            padding: 0.85rem 1rem; display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.78rem; font-weight: 700; color: #065F46;
        }

        /* Footer */
        .card-foot {
            background: var(--light); border-top: 1px solid #E2E8F0;
            padding: 1rem 1.5rem; text-align: center;
            font-size: 0.68rem; color: #94A3B8; margin-top: 1rem;
        }
        .card-foot strong { color: var(--navy); }
    </style>
</head>
<body>
<div class="card">

    <div class="card-head">
        <img src="{{ asset('images/congress_logo.png') }}" class="card-head-logo" alt="Logo"/>
        <div>
            <div class="card-head-text-title">Ogun Conference Youth Congress 2026</div>
            <div class="card-head-text-sub">Official Camper Verification Record</div>
        </div>
    </div>
    <div class="accent-bar"></div>

    {{-- Photo --}}
    <div class="photo-section">
        <div class="photo-wrap">
            @if($camper->getFirstMedia('photo'))
                <img src="{{ route('camper.photo', $camper->id) }}" class="camper-photo" alt="Photo"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                <div class="photo-placeholder" style="display:none">&#128100;</div>
            @else
                <div class="photo-placeholder">&#128100;</div>
            @endif
            <div class="verified-badge">&#10003; VERIFIED</div>
        </div>
    </div>

    {{-- Name & Category --}}
    <div class="info">
        <div class="camper-name">{{ $camper->full_name }}</div>
        <div class="camper-code">{{ $camper->camper_number }}</div>
        <div class="dept-pill">
            &#127775; {{ $camper->category->label() }}
            @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
        </div>
    </div>

    {{-- Personal Details --}}
    <div class="section-title">Personal Information</div>
    <div class="detail-grid">
        <div class="detail-cell">
            <div class="detail-lbl">Church</div>
            <div class="detail-val">{{ $camper->church?->name ?? '—' }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-lbl">District</div>
            <div class="detail-val">{{ $camper->church?->district?->name ?? '—' }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-lbl">Gender</div>
            <div class="detail-val" style="text-transform:capitalize">{{ $camper->gender?->value ?? '—' }}</div>
        </div>
        <div class="detail-cell">
            <div class="detail-lbl">Registered</div>
            <div class="detail-val">{{ $camper->created_at->format('d M Y') }}</div>
        </div>
        @if($camper->home_address)
            <div class="detail-cell full">
                <div class="detail-lbl">Home Address</div>
                <div class="detail-val">{{ $camper->home_address }}</div>
            </div>
        @endif
    </div>

    {{-- Health & Medical --}}
    @php $health = $camper->health; @endphp
    @if($health)
        <div class="section-title">Health &amp; Medical</div>
        <div style="padding: 0 1.5rem;">
            @if(!$health->medical_conditions && !$health->medications && !$health->allergies)
                <p class="no-issues">&#10003; No known medical conditions, medications, or allergies.</p>
            @else
                @if($health->medical_conditions)
                    <div class="health-item">
                        <div class="health-dot"></div>
                        <div class="health-text"><strong>Conditions:</strong> {{ $health->medical_conditions }}</div>
                    </div>
                @endif
                @if($health->medications)
                    <div class="health-item">
                        <div class="health-dot"></div>
                        <div class="health-text"><strong>Medications:</strong> {{ $health->medications }}</div>
                    </div>
                @endif
                @if($health->allergies)
                    <div class="health-item">
                        <div class="health-dot"></div>
                        <div class="health-text"><strong>Allergies:</strong> {{ $health->allergies }}</div>
                    </div>
                @endif
            @endif
        </div>
    @endif

    {{-- Contacts --}}
    @php $contacts = $camper->contacts()->get(); @endphp
    @if($contacts->count())
        <div class="section-title">Emergency &amp; Guardian Contacts</div>
        @foreach($contacts as $contact)
            <div class="contact-card">
                <div class="contact-type">{{ ucwords(str_replace('_',' ',$contact->type?->value ?? '')) }}</div>
                <div class="contact-name">{{ $contact->full_name }}</div>
                <div class="contact-meta">
                    @if($contact->relationship) {{ $contact->relationship }} &bull; @endif
                    {{ $contact->phone ?? '' }}
                    @if($contact->email) &bull; {{ $contact->email }} @endif
                </div>
            </div>
        @endforeach
    @endif

    {{-- Consent form status --}}
    @if($camper->requiresConsentForm())
        <div class="consent-banner"
             style="{{ $camper->consent_collected
            ? 'background:#D1FAE5;border:1px solid #6EE7B7;'
            : ($camper->consent_form_path
                ? 'background:#DBEAFE;border:1px solid #93C5FD;'
                : 'background:#FEF3C7;border:1px solid #FCD34D;') }}">
            <div class="consent-icon">
                @if($camper->consent_collected) ✅
                @elseif($camper->consent_form_path) 📋
                @else ⚠️
                @endif
            </div>
            <div>
                <div class="consent-title" style="{{ $camper->consent_collected
                ? 'color:#065F46'
                : ($camper->consent_form_path ? 'color:#1E40AF' : 'color:#92400E') }}">
                    @if($camper->consent_collected)
                        Consent Form: Physically Submitted ✓
                    @elseif($camper->consent_form_path)
                        Consent Form: Generated — Not Yet Submitted
                    @else
                        Consent Form: Not Yet Generated
                    @endif
                </div>
                <div class="consent-sub" style="{{ $camper->consent_collected
                ? 'color:#047857'
                : ($camper->consent_form_path ? 'color:#1D4ED8' : 'color:#B45309') }}">
                    @if($camper->consent_collected)
                        Physical consent form has been collected by the secretariat.
                        This camper is cleared for camp activities.
                    @elseif($camper->consent_form_path)
                        The PDF has been generated. The signed physical copy must be submitted at check-in.
                    @else
                        Consent form has not been generated yet. Will be created shortly after registration.
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Verified strip --}}
    <div class="verified-strip">
        <span style="font-size:1.2rem">&#10003;</span>
        <div>
            <div>Verified — {{ setting('camp_name','Ogun Conference Youth Congress') }} {{ now()->year }}</div>
            <div style="font-weight:400;font-size:0.7rem;color:#047857;margin-top:0.1rem">
                This record is authentic and has been officially registered.
            </div>
        </div>
    </div>

    <div class="card-foot">
        <strong>{{ setting('camp_name','Ogun Conference Youth Congress') }}</strong><br/>
        {{ setting('camp_dates','Aug 16–22, 2026') }} &bull; {{ setting('camp_venue','Abeokuta, Ogun State') }}
    </div>
</div>
</body>
</html>
