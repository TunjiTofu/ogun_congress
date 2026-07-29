<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Skill Selection — {{ $camper->full_name }}</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png"/>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,700;1,9..144,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --navy:#0B2D6B;--navy2:#142547;--gold:#B8924A;--gold2:#D4B26E;
            --cream:#F7F3EA;--paper:#FBF8F1;--text:#1A2238;--muted:#7A8499;
            --border:#E7DFC9;--red:#DC2626;--green:#059669;--amber:#D97706;
        }
        body{font-family:'DM Sans',system-ui,sans-serif;background:var(--paper);color:var(--text);min-height:100vh}
        .nav{background:var(--navy);padding:.85rem 1.25rem;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:.65rem}
        .nav img{width:34px;height:34px;border-radius:50%;border:1.5px solid rgba(184,146,74,.4)}
        .nav-title{font-family:'Fraunces',Georgia,serif;font-size:.82rem;color:var(--gold2)}
        .nav-sub{font-size:.62rem;color:rgba(255,255,255,.45)}
        .btn-logout{font-size:.72rem;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.18);color:rgba(255,255,255,.7);padding:.35rem .9rem;border-radius:100px;cursor:pointer;text-decoration:none;transition:.2s}
        .btn-logout:hover{border-color:var(--gold);color:var(--gold)}
        .container{max-width:960px;margin:0 auto;padding:1.5rem 1.25rem}

        /* Alert banners */
        .alert{padding:.85rem 1.1rem;border-radius:10px;font-size:.83rem;margin-bottom:1.25rem;display:flex;align-items:flex-start;gap:.65rem}
        .alert-success{background:#F0FDF4;border:1px solid #86EFAC;color:#14532D}
        .alert-error{background:#FEF2F2;border:1px solid #FECACA;color:var(--red)}
        .alert-warning{background:#FFFBEB;border:1px solid #FDE68A;color:#78350F}

        /* Registration closed banner */
        .closed-banner{background:var(--navy);color:#fff;border-radius:12px;padding:1rem 1.25rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:.75rem}
        .closed-banner p{font-size:.85rem;line-height:1.5}
        .closed-banner strong{color:var(--gold2)}

        /* Profile card */
        .profile-card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;gap:1.25rem}
        .profile-photo{width:72px;height:90px;border-radius:10px;object-fit:cover;object-position:top center;border:2px solid var(--border);flex-shrink:0}
        .profile-photo-placeholder{width:72px;height:90px;border-radius:10px;background:var(--cream);display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:1.8rem;border:2px solid var(--border);flex-shrink:0}
        .profile-name{font-family:'Fraunces',Georgia,serif;font-size:1.2rem;font-weight:700;color:var(--navy);margin-bottom:.2rem}
        .profile-num{font-family:monospace;font-size:.75rem;color:var(--muted);margin-bottom:.5rem}
        .profile-tags{display:flex;flex-wrap:wrap;gap:.35rem}
        .tag{font-size:.65rem;font-weight:700;padding:.2rem .7rem;border-radius:100px;display:inline-block}
        .tag-navy{background:var(--navy);color:#fff}
        .tag-gold{background:var(--cream);color:var(--gold);border:1px solid var(--gold)}
        .tag-gray{background:var(--cream);color:var(--muted);border:1px solid var(--border)}

        /* Current skill */
        .current-skill{background:linear-gradient(135deg,#F0FDF4,#DCFCE7);border:1.5px solid #86EFAC;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem}
        .current-skill-label{font-size:.62rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--green);margin-bottom:.35rem}
        .current-skill-name{font-family:'Fraunces',Georgia,serif;font-size:1.1rem;font-weight:700;color:#14532D}
        .current-skill-facilitator{font-size:.78rem;color:#166534;margin-top:.2rem}

        /* Skills grid */
        .section-head{font-size:.65rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-bottom:.85rem;display:flex;align-items:center;gap:.5rem}
        .section-head::after{content:'';flex:1;height:1px;background:var(--border)}
        .skill-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:1rem;margin-bottom:2rem}
        .skill-card{background:#fff;border:1.5px solid var(--border);border-radius:14px;padding:1.25rem;display:flex;flex-direction:column;transition:.2s;position:relative;overflow:hidden}
        .skill-card.selected{border-color:#86EFAC;background:linear-gradient(160deg,#fff,#F0FDF4)}
        .skill-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px}
        .skill-card.general::before{background:linear-gradient(90deg,var(--gold),var(--gold2))}
        .skill-card.category::before{background:linear-gradient(90deg,var(--navy),#1F3360)}
        .skill-name{font-family:'Fraunces',Georgia,serif;font-size:1rem;font-weight:700;color:var(--navy);margin-bottom:.65rem;line-height:1.2}
        .skill-detail{font-size:.75rem;color:var(--muted);line-height:1.55;margin-bottom:.5rem;display:flex;align-items:flex-start;gap:.4rem}
        .skill-detail-icon{flex-shrink:0;margin-top:.05rem}
        .skill-slots{display:inline-flex;align-items:center;gap:.35rem;font-size:.72rem;font-weight:700;padding:.3rem .8rem;border-radius:100px;margin-bottom:.85rem;margin-top:.15rem}
        .slots-ok{background:#F0FDF4;color:var(--green);border:1px solid #BBF7D0}
        .slots-low{background:#FFFBEB;color:var(--amber);border:1px solid #FDE68A}
        .skill-btn{margin-top:auto;padding:.7rem;border-radius:8px;font-size:.8rem;font-weight:700;border:none;cursor:pointer;transition:.2s;width:100%}
        .skill-btn-select{background:var(--navy);color:#fff}
        .skill-btn-select:hover{background:var(--navy2)}
        .skill-btn-change{background:var(--cream);color:var(--navy);border:1.5px solid var(--border)}
        .skill-btn-change:hover{border-color:var(--navy)}
        .skill-btn-selected{background:var(--green);color:#fff;cursor:default}
        .skill-badge-selected{position:absolute;top:.75rem;right:.75rem;font-size:.6rem;font-weight:700;background:var(--green);color:#fff;padding:.15rem .5rem;border-radius:100px}
        .empty{text-align:center;padding:3rem;color:var(--muted)}
        .empty-icon{font-size:2rem;margin-bottom:.75rem}

        @media(max-width:600px){
            .profile-card{flex-direction:column;align-items:flex-start}
            .skill-grid{grid-template-columns:1fr}
        }
    </style>
</head>
<body>

<nav class="nav">
    <div class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Logo"/>
        <div>
            <div class="nav-title">Skill Acquisition</div>
            <div class="nav-sub">Congress 2026</div>
        </div>
    </div>
    <form method="POST" action="{{ route('skills.logout') }}" style="display:inline">
        @csrf
        <button type="submit" class="btn-logout">Log Out</button>
    </form>
</nav>

<div class="container">

    @if(session('success'))
        <div class="alert alert-success">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">&#9888; {{ session('error') }}</div>
    @endif

    {{-- Registration closed banner --}}
    @if(! $registrationOpen)
        <div class="closed-banner">
            <span style="font-size:1.2rem;flex-shrink:0">🔒</span>
            <p>
                <strong>Skill registration is closed.</strong><br/>
                @if($existing)
                    Your current selection is shown below. No further changes can be made.
                @else
                    You did not complete your skill selection before registration closed.
                @endif
            </p>
        </div>
    @endif

    {{-- Camper profile --}}
    <div class="profile-card">
        @php $media = $camper->getFirstMedia('photo'); @endphp
        @if($media)
            <img src="{{ route('camper.photo', $camper->id) }}?v={{ $media->updated_at->timestamp }}" class="profile-photo" alt="Photo"/>
        @else
            <div class="profile-photo-placeholder">&#128100;</div>
        @endif
        <div>
            <div class="profile-name">{{ $camper->full_name }}</div>
            <div class="profile-num">{{ $camper->camper_number }}</div>
            <div class="profile-tags">
                <span class="tag tag-navy">{{ $camper->category?->label() ?? '—' }}</span>
                @if($camper->club_rank)
                    <span class="tag tag-gold">{{ $camper->club_rank }}</span>
                @endif
                @if($camper->church)
                    <span class="tag tag-gray">{{ $camper->church->name }}</span>
                    <span class="tag tag-gray">{{ $camper->church->district?->name }}</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Current selection --}}
    @if($existing)
        <div class="current-skill">
            <div class="current-skill-label">&#10003; Your Current Skill Selection</div>
            <div class="current-skill-name">{{ $existing->skill->name }}</div>
            @if($existing->skill->facilitator)
                <div class="current-skill-facilitator">Facilitator: {{ $existing->skill->facilitator }}</div>
            @endif
            @if($registrationOpen)
                <p style="font-size:.73rem;color:#166534;margin-top:.5rem">You can change your selection below while registration is open.</p>
            @endif
        </div>
    @endif

    {{-- Skills list (only when registration is open) --}}
    @if($registrationOpen)

        @if($skills->isEmpty())
            <div class="empty">
                <div class="empty-icon">&#128372;</div>
                <p style="font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:.4rem">No skills available</p>
                <p style="font-size:.8rem">No skills are currently available for your category. Please contact the organizers.</p>
            </div>
        @else
            {{-- General skills --}}
            @php $general = $skills->whereNull('category'); $specific = $skills->whereNotNull('category'); @endphp

            @if($specific->isNotEmpty())
                <div class="section-head">Category Skills</div>
                <div class="skill-grid">
                    @foreach($specific as $skill)
                        @php $isSelected = $existing?->skill_id === $skill->id; $isLow = $skill->remainingSlots() <= 5; @endphp
                        <div class="skill-card category {{ $isSelected ? 'selected' : '' }}">
                            @if($isSelected)<span class="skill-badge-selected">&#10003; Selected</span>@endif
                            <div class="skill-name">{{ $skill->name }}</div>
                            @if($skill->facilitator)
                                <div class="skill-detail"><span class="skill-detail-icon">&#128100;</span><span>{{ $skill->facilitator }}</span></div>
                            @endif
                            @if($skill->requirement)
                                <div class="skill-detail"><span class="skill-detail-icon">&#128196;</span><span>{{ $skill->requirement }}</span></div>
                            @endif
                            <div class="skill-slots {{ $isLow ? 'slots-low' : 'slots-ok' }}">
                                &#128065; {{ $skill->remainingSlots() }} slot{{ $skill->remainingSlots() !== 1 ? 's' : '' }} remaining
                            </div>
                            @if($isSelected)
                                <button class="skill-btn skill-btn-selected" disabled>&#10003; Selected</button>
                            @else
                                <form method="POST" action="{{ route('skills.register') }}">
                                    @csrf
                                    <input type="hidden" name="skill_id" value="{{ $skill->id }}"/>
                                    <button type="submit" class="skill-btn {{ $existing ? 'skill-btn-change' : 'skill-btn-select' }}">
                                        {{ $existing ? 'Switch to This' : 'Select' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            @if($general->isNotEmpty())
                <div class="section-head">General Skills <span style="font-size:.6rem;font-weight:400;letter-spacing:0;text-transform:none;color:var(--muted)">&nbsp;— open to all departments</span></div>
                <div class="skill-grid">
                    @foreach($general as $skill)
                        @php $isSelected = $existing?->skill_id === $skill->id; $isLow = $skill->remainingSlots() <= 5; @endphp
                        <div class="skill-card general {{ $isSelected ? 'selected' : '' }}">
                            @if($isSelected)<span class="skill-badge-selected">&#10003; Selected</span>@endif
                            <div class="skill-name">{{ $skill->name }}</div>
                            @if($skill->facilitator)
                                <div class="skill-detail"><span class="skill-detail-icon">&#128100;</span><span>{{ $skill->facilitator }}</span></div>
                            @endif
                            @if($skill->requirement)
                                <div class="skill-detail"><span class="skill-detail-icon">&#128196;</span><span>{{ $skill->requirement }}</span></div>
                            @endif
                            <div class="skill-slots {{ $isLow ? 'slots-low' : 'slots-ok' }}">
                                &#128065; {{ $skill->remainingSlots() }} slot{{ $skill->remainingSlots() !== 1 ? 's' : '' }} remaining
                            </div>
                            @if($isSelected)
                                <button class="skill-btn skill-btn-selected" disabled>&#10003; Selected</button>
                            @else
                                <form method="POST" action="{{ route('skills.register') }}">
                                    @csrf
                                    <input type="hidden" name="skill_id" value="{{ $skill->id }}"/>
                                    <button type="submit" class="skill-btn {{ $existing ? 'skill-btn-change' : 'skill-btn-select' }}">
                                        {{ $existing ? 'Switch to This' : 'Select' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        @endif

    @elseif(! $existing)
        {{-- Closed, no selection made --}}
        <div class="empty">
            <div class="empty-icon">&#128274;</div>
            <p style="font-size:.9rem;font-weight:600;color:var(--text);margin-bottom:.4rem">Skill registration has closed</p>
            <p style="font-size:.8rem">You did not complete your skill selection.</p>
        </div>
    @endif

</div>

</body>
</html>
