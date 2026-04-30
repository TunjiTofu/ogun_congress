<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Coordinator Portal &mdash; {{ $church?->name ?? 'Dashboard' }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        :root{--green:#064E3B;--green2:#065F46;--green3:#10B981;--gold:#C9A94D;--navy:#0B2D6B}
        body{font-family:'Lato',sans-serif;background:#F0FAF6;min-height:100vh}

        nav{background:var(--green);padding:0.75rem 1.5rem;display:flex;align-items:center;justify-content:space-between}
        .nav-brand{display:flex;align-items:center;gap:0.75rem}
        .nav-brand img{width:38px;height:38px;border-radius:50%;border:1px solid rgba(16,185,129,0.4)}
        .nav-title{font-family:'Cinzel',serif;font-size:0.78rem;color:#6EE7B7;letter-spacing:0.06em}
        .nav-sub{font-size:0.65rem;color:rgba(255,255,255,0.6)}
        .btn-logout{background:transparent;border:1px solid rgba(255,255,255,0.25);color:rgba(255,255,255,0.7);
            font-size:0.75rem;padding:0.35rem 0.9rem;border-radius:100px;cursor:pointer;
            transition:0.2s;text-decoration:none;display:inline-block}
        .btn-logout:hover{border-color:var(--gold);color:var(--gold)}

        .container{max-width:960px;margin:0 auto;padding:2rem 1.5rem}

        .header-card{background:linear-gradient(135deg,var(--green) 0%,#047857 100%);
            border-radius:20px;padding:1.75rem 2rem;color:#fff;margin-bottom:1.5rem;
            position:relative;overflow:hidden}
        .header-card::before{content:'';position:absolute;top:-20px;right:-20px;width:120px;height:120px;
            border-radius:50%;background:rgba(255,255,255,0.05)}
        .header-label{font-size:0.65rem;letter-spacing:0.15em;color:rgba(110,231,183,0.9);
            text-transform:uppercase;margin-bottom:0.3rem}
        .header-church{font-family:'Cinzel',serif;font-size:1.4rem;font-weight:700}
        .header-dist{font-size:0.8rem;color:rgba(255,255,255,0.65);margin-top:0.2rem}

        .stats{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem}
        .stat{background:#fff;border-radius:16px;padding:1.2rem 1.5rem;
            box-shadow:0 2px 12px rgba(6,78,59,0.08);border:1px solid rgba(16,185,129,0.1)}
        .stat-label{font-size:0.68rem;font-weight:700;color:#6B7280;text-transform:uppercase;
            letter-spacing:0.08em;margin-bottom:0.4rem}
        .stat-value{font-size:1.8rem;font-weight:900;color:var(--green)}

        .flash-success{background:#D1FAE5;border:1px solid #6EE7B7;border-radius:12px;
            padding:0.9rem 1.2rem;color:#065F46;font-size:0.85rem;margin-bottom:1.2rem}
        .flash-info{background:#DBEAFE;border:1px solid #93C5FD;border-radius:12px;
            padding:0.9rem 1.2rem;color:#1E40AF;font-size:0.85rem;margin-bottom:1.2rem}

        /* Batch card */
        .batch-card{background:#fff;border-radius:20px;overflow:hidden;margin-bottom:1.5rem;
            box-shadow:0 2px 16px rgba(6,78,59,0.08);border:1px solid rgba(16,185,129,0.1)}
        .batch-header{padding:1rem 1.5rem;border-bottom:1px solid #F0FDF4;display:flex;
            align-items:center;justify-content:space-between;background:#F9FFFE}
        .batch-title{font-family:'Cinzel',serif;font-size:0.92rem;color:var(--green);font-weight:700}
        .batch-meta{font-size:0.72rem;color:#9CA3AF;margin-top:0.15rem}
        .badge{display:inline-block;font-size:0.65rem;font-weight:700;padding:0.2rem 0.6rem;
            border-radius:100px;border:1px solid}
        .badge-confirmed{background:#D1FAE5;border-color:#6EE7B7;color:#065F46}
        .badge-pending{background:#FEF3C7;border-color:#FCD34D;color:#92400E}

        /* Camper rows */
        .camper-row{padding:1rem 1.5rem;border-bottom:1px solid #F9FAFB;display:flex;
            align-items:center;justify-content:space-between;gap:1rem}
        .camper-row:last-child{border:none}
        .camper-avatar{width:40px;height:40px;border-radius:50%;object-fit:cover;
            border:2px solid #E5E7EB;flex-shrink:0}
        .camper-avatar-placeholder{width:40px;height:40px;border-radius:50%;background:#F0FDF4;
            display:flex;align-items:center;justify-content:center;
            color:#10B981;font-size:1.1rem;flex-shrink:0}
        .camper-name{font-size:0.85rem;font-weight:700;color:#111827}
        .camper-detail{font-size:0.7rem;color:#9CA3AF;margin-top:1px}
        .camper-code{font-family:monospace;font-size:0.72rem;color:#6366F1;font-weight:700}

        .btn-fill{font-size:0.72rem;background:var(--green);color:#fff;font-weight:700;
            padding:0.4rem 0.9rem;border-radius:100px;text-decoration:none;transition:0.2s}
        .btn-fill:hover{background:var(--green2)}
        .btn-done{font-size:0.72rem;background:#D1FAE5;color:#065F46;font-weight:700;
            padding:0.4rem 0.9rem;border-radius:100px;cursor:default}
        .btn-doc{font-size:0.72rem;background:var(--navy);color:#fff;font-weight:700;
            padding:0.4rem 0.9rem;border-radius:100px;text-decoration:none;transition:0.2s}
        .btn-doc:hover{background:#1E5FAD}

        .empty{padding:3rem;text-align:center;color:#9CA3AF;font-style:italic;font-size:0.85rem}

        @media(max-width:600px){.stats{grid-template-columns:1fr 1fr}.stat-value{font-size:1.4rem}}
    </style>
</head>
<body>

<nav>
    <div class="nav-brand">
        <img src="{{ asset('images/logo.svg') }}" alt="Logo"/>
        <div>
            <div class="nav-title">Coordinator Portal</div>
            <div class="nav-sub">{{ $user->name }}</div>
        </div>
    </div>
    <form method="POST" action="{{ route('coordinator.portal.logout') }}" style="display:inline">
        @csrf
        @method('POST')
        <button type="submit" class="btn-logout">Log Out</button>
    </form>
</nav>

<div class="container">

    @if(session('success'))
        <div class="flash-success">&#10003; {{ session('success') }}</div>
    @endif
    @if(session('info'))
        <div class="flash-info">&#x2139; {{ session('info') }}</div>
    @endif

    {{-- Church header --}}
    <div class="header-card">
        <div class="header-label">Church Coordinator Dashboard</div>
        <div class="header-church">{{ $church?->name ?? 'No Church Assigned' }}</div>
        <div class="header-dist">{{ $church?->district?->name }} &bull; {{ now()->format('d F Y') }}</div>
    </div>

    @if(! $church)
        <div style="background:#FEF3C7;border:1px solid #FCD34D;border-radius:16px;padding:1.5rem;
                text-align:center;color:#92400E;font-size:0.85rem;">
            Your account has not been linked to a local church. Please contact the super admin.
        </div>
    @else

        {{-- Stats --}}
        @php
            $totalCampers = $batches->sum(fn($b) => $b->entries->count());
            $totalForms   = $batches->sum(fn($b) => $b->entries->where('status','registered')->count());
            $totalPaid    = $batches->sum('amount_paid');
        @endphp
        <div class="stats">
            <div class="stat">
                <div class="stat-label">Total Campers</div>
                <div class="stat-value">{{ $totalCampers }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Forms Completed</div>
                <div class="stat-value">{{ $totalForms }}</div>
            </div>
            <div class="stat">
                <div class="stat-label">Total Paid</div>
                <div class="stat-value" style="font-size:1.3rem">&#8358;{{ number_format($totalPaid) }}</div>
            </div>
        </div>

        @forelse($batches as $batch)
            <div class="batch-card">
                <div class="batch-header">
                    <div>
                        <div class="batch-title">Batch #{{ $batch->id }}</div>
                        <div class="batch-meta">
                            {{ $batch->entries->count() }} campers &bull;
                            &#8358;{{ number_format($batch->expected_total) }} &bull;
                            {{ $batch->confirmed_at?->format('d M Y') }}
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        @php
                            $done = $batch->entries->where('status','registered')->count();
                            $total = $batch->entries->count();
                        @endphp
                        <span style="font-size:0.75rem;color:#6B7280;">{{ $done }}/{{ $total }} forms done</span>
                        <span class="badge badge-confirmed">Confirmed</span>
                    </div>
                </div>

                @forelse($batch->entries as $entry)
                    <div class="camper-row">
                        <div style="display:flex;align-items:center;gap:0.75rem;flex:1;">
                            @if($entry->registrationCode?->camper?->getFirstMedia('photo'))
                                <img src="{{ route('camper.photo', $entry->registrationCode->camper->id) }}"
                                     class="camper-avatar" alt="Photo"
                                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'"/>
                                <div class="camper-avatar-placeholder" style="display:none">&#128100;</div>
                            @else
                                <div class="camper-avatar-placeholder">&#128100;</div>
                            @endif
                            <div>
                                <div class="camper-name">{{ $entry->full_name }}</div>
                                <div class="camper-detail">
                                    {{ $entry->category->label() }} &bull;
                                    @if($entry->registrationCode)
                                        <span class="camper-code">{{ $entry->registrationCode->code }}</span>
                                    @else
                                        <span style="color:#EF4444;">No code yet</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.5rem;flex-shrink:0;">
                            @if($entry->status === 'registered')
                                <span class="btn-done">&#10003; Registered</span>
                                @if($entry->registrationCode?->camper?->id_card_path)
                                    @php $docService = app(\App\Services\DocumentGenerationService::class); @endphp
                                    <a href="{{ $docService->getDownloadUrl($entry->registrationCode->camper->id_card_path) }}"
                                       target="_blank" class="btn-doc">ID Card</a>
                                @endif
                                @if($entry->registrationCode?->camper?->consent_form_path)
                                    <a href="{{ $docService->getDownloadUrl($entry->registrationCode->camper->consent_form_path) }}"
                                       target="_blank" class="btn-doc">Consent</a>
                                @endif
                            @elseif($entry->registrationCode)
                                <a href="{{ route('coordinator.portal.form', ['batch'=>$batch->id,'entry'=>$entry->id]) }}"
                                   class="btn-fill">Fill Form &rarr;</a>
                            @else
                                <span style="font-size:0.72rem;color:#9CA3AF;">Awaiting code</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty">No campers in this batch.</div>
                @endforelse
            </div>
        @empty
            <div style="background:#fff;border-radius:20px;padding:3rem;text-align:center;color:#9CA3AF;
                box-shadow:0 2px 12px rgba(6,78,59,0.06);">
                <p style="font-size:1.5rem;margin-bottom:0.5rem;">&#128194;</p>
                <p style="font-size:0.88rem;font-style:italic;">
                    No confirmed batches yet. Create a batch in the admin panel, submit payment, and return here once confirmed.
                </p>
                <a href="{{ url('/admin') }}" style="display:inline-block;margin-top:1rem;font-size:0.8rem;
           background:var(--green);color:#fff;padding:0.5rem 1.2rem;border-radius:100px;
           text-decoration:none;font-weight:700;">Go to Admin Panel &rarr;</a>
            </div>
        @endforelse

    @endif
</div>

</body>
</html>
