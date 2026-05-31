<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Photo Album — {{ setting('camp_name','Ogun Conference Youth Congress') }}</title>
    <link rel="icon" href="{{ asset('images/congress_logo.png') }}" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;1,9..144,500&family=Instrument+Serif:ital@0;1&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        html{scroll-behavior:smooth;font-size:16px}
        :root{
            --ink:#0A1832;--gold:#B8924A;--gold-2:#D4B26E;--gold-soft:rgba(184,146,74,.10);
            --cream:#F7F3EA;--cream-2:#EFE8D7;--paper:#FBF8F1;--paper-2:#FDFBF6;
            --text:#1A2238;--text-2:#4A5468;--muted:#7A8499;
            --hairline:rgba(10,24,50,.08);--rule:#E7DFC9;
            --sh-2:0 4px 16px rgba(10,24,50,.06),0 1px 2px rgba(10,24,50,.04);
            --sh-3:0 16px 48px -8px rgba(10,24,50,.12),0 4px 12px rgba(10,24,50,.06);
            --r:14px;--r-lg:22px;
            --font-display:'Fraunces','Playfair Display',Georgia,serif;
            --font-script:'Instrument Serif',Georgia,serif;
            --font-body:'DM Sans',-apple-system,BlinkMacSystemFont,system-ui,sans-serif;
        }
        body{font-family:var(--font-body);color:var(--text);background:var(--cream);overflow-x:hidden;-webkit-font-smoothing:antialiased;line-height:1.55}
        img{max-width:100%;display:block}
        a{color:inherit}

        /* Nav */
        .nav{position:sticky;top:0;z-index:100;display:flex;align-items:center;padding:10px 32px;background:rgba(247,243,234,.92);backdrop-filter:blur(20px);border-bottom:1px solid var(--hairline)}
        .nav-brand{display:flex;align-items:center;gap:10px;text-decoration:none;margin-right:auto}
        .nav-logo{width:36px;height:36px;border-radius:50%;border:1.5px solid rgba(184,146,74,.35)}
        .nav-name{font-family:var(--font-display);font-size:14px;font-weight:600;color:var(--ink)}
        .nav-back{font-size:13px;color:var(--text-2);text-decoration:none;display:flex;align-items:center;gap:6px;font-weight:500;transition:color .2s}
        .nav-back:hover{color:var(--ink)}
        .nav-cta{display:flex;align-items:center;gap:8px}
        .btn-sm{font-size:12.5px;font-weight:600;padding:8px 16px;border-radius:100px;text-decoration:none;background:var(--ink);color:#fff;transition:background .2s}
        .btn-sm:hover{background:#142547}

        /* Album shell */
        .album-shell{max-width:1300px;margin:0 auto;padding:48px 32px 100px;position:relative}

        /* Back */
        .album-back{display:inline-flex;align-items:center;gap:8px;font-size:12.5px;font-weight:500;color:var(--text-2);text-decoration:none;margin-bottom:32px;transition:color .2s,gap .2s}
        .album-back:hover{color:var(--ink);gap:12px}

        /* Header */
        .album-head{display:grid;grid-template-columns:1.4fr 1fr;gap:48px;align-items:end;padding-bottom:36px;margin-bottom:48px;border-bottom:1px solid var(--rule)}
        .album-eyebrow{display:inline-flex;align-items:center;gap:10px;font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);margin-bottom:18px}
        .album-eyebrow-dot{width:6px;height:6px;border-radius:50%;background:var(--gold);box-shadow:0 0 0 4px rgba(184,146,74,.12)}
        .album-title{font-family:var(--font-display);font-size:clamp(2.4rem,4.6vw,3.8rem);font-weight:400;letter-spacing:-.025em;line-height:1.02;color:var(--ink);margin-bottom:16px}
        .album-title em{font-family:var(--font-script);font-style:italic;color:var(--gold)}
        .album-lede{font-size:1.05rem;color:var(--text-2);line-height:1.65;max-width:540px}
        .album-meta{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:var(--rule);border:1px solid var(--rule);border-radius:var(--r);overflow:hidden}
        .album-meta-cell{background:var(--paper-2);padding:14px 18px}
        .album-meta-lbl{font-size:10px;font-weight:600;letter-spacing:.16em;text-transform:uppercase;color:var(--muted);margin-bottom:4px}
        .album-meta-val{font-family:var(--font-display);font-size:16px;font-weight:500;color:var(--ink)}

        /* District tabs */
        .album-tabs{display:flex;align-items:center;gap:8px;margin-bottom:36px;flex-wrap:wrap}
        .album-tab{display:inline-flex;align-items:center;gap:8px;background:transparent;border:1px solid var(--hairline);color:var(--text-2);font-family:var(--font-body);font-size:13px;font-weight:500;padding:9px 18px;border-radius:100px;cursor:pointer;transition:all .2s}
        .album-tab:hover{color:var(--ink);border-color:var(--ink)}
        .album-tab.active{background:var(--ink);color:#fff;border-color:var(--ink)}
        .album-tab-count{font-size:10.5px;font-weight:600;color:var(--muted);background:var(--cream-2);padding:2px 8px;border-radius:100px}
        .album-tab.active .album-tab-count{background:rgba(255,255,255,.15);color:var(--gold-2)}

        /* Photo grid */
        .district-section{display:none}
        .district-section.active{display:block}
        .photo-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:12px}
        .photo-card{position:relative;border-radius:var(--r);overflow:hidden;background:var(--paper-2);border:1px solid var(--hairline);cursor:pointer;transition:transform .35s cubic-bezier(.2,.7,.2,1),box-shadow .35s,border-color .3s;aspect-ratio:4/3}
        .photo-card:hover{transform:translateY(-3px);box-shadow:var(--sh-3);border-color:var(--gold)}
        .photo-card:nth-child(5n+1){aspect-ratio:3/2}
        .photo-card:nth-child(7n+3){aspect-ratio:1/1}
        .photo-card img{width:100%;height:100%;object-fit:cover;transition:transform .4s}
        .photo-card:hover img{transform:scale(1.04)}
        .photo-card-tape{position:absolute;top:-10px;left:50%;transform:translateX(-50%) rotate(-2deg);width:60px;height:20px;background:rgba(247,243,234,.75);border:1px dashed rgba(184,146,74,.4);backdrop-filter:blur(4px);z-index:2;border-radius:2px;opacity:.8}
        .photo-card:nth-child(3n) .photo-card-tape{transform:translateX(-50%) rotate(3deg)}
        .photo-card:nth-child(4n) .photo-card-tape{display:none}
        .photo-cap{position:absolute;left:0;right:0;bottom:0;padding:14px 16px;color:#fff;background:linear-gradient(180deg,transparent,rgba(10,24,50,.78));z-index:1;opacity:0;transform:translateY(8px);transition:opacity .3s,transform .3s}
        .photo-card:hover .photo-cap{opacity:1;transform:translateY(0)}
        .photo-cap-district{font-size:9.5px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--gold-2);margin-bottom:3px}
        .photo-cap-title{font-family:var(--font-display);font-size:13.5px;font-weight:500;line-height:1.3}

        /* Empty state */
        .album-empty{text-align:center;padding:80px 0}
        .album-empty-icon{font-size:3rem;margin-bottom:16px}

        /* Footer strip */
        .album-foot{margin-top:56px;padding:24px 32px;background:var(--ink);color:rgba(255,255,255,.7);border-radius:var(--r-lg);display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:wrap;position:relative;overflow:hidden}
        .album-foot::before{content:'';position:absolute;top:-50%;left:-10%;width:60%;height:200%;background:radial-gradient(ellipse,rgba(184,146,74,.18) 0%,transparent 60%);filter:blur(40px);pointer-events:none}
        .album-foot-text{display:flex;align-items:center;gap:14px;font-size:13.5px;position:relative}
        .album-foot-mark{width:36px;height:36px;border-radius:50%;background:var(--gold-soft);border:1px solid rgba(184,146,74,.3);color:var(--gold-2);display:grid;place-items:center;font-size:16px;flex-shrink:0}
        .album-foot-cta{display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:var(--ink);font-size:13px;font-weight:600;padding:11px 18px;border-radius:100px;text-decoration:none;position:relative;transition:background .2s,transform .2s}
        .album-foot-cta:hover{background:var(--gold-2);transform:translateY(-1px)}

        /* Lightbox */
        .lb{position:fixed;inset:0;z-index:2000;background:rgba(5,12,28,.95);backdrop-filter:blur(12px);display:none;align-items:center;justify-content:center;padding:60px 40px}
        .lb.open{display:flex}
        .lb-close{position:fixed;top:20px;right:24px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff;font-size:24px;width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background .2s}
        .lb-close:hover{background:rgba(255,255,255,.2)}
        .lb-frame{display:flex;flex-direction:column;align-items:center;gap:16px;max-width:90vw}
        .lb-img{max-width:90vw;max-height:80vh;object-fit:contain;border-radius:8px}
        .lb-caption{font-size:13px;color:rgba(255,255,255,.7);text-align:center}

        @media(max-width:960px){.album-head{grid-template-columns:1fr}.album-shell{padding:32px 20px 80px}}
        @media(max-width:640px){.photo-grid{grid-template-columns:1fr 1fr}.nav{padding:10px 20px}}
    </style>
</head>
<body>

<!-- Nav -->
<nav class="nav">
    <a href="{{ url('/') }}" class="nav-brand">
        <img src="{{ asset('images/congress_logo.png') }}" alt="Logo" class="nav-logo"/>
        <div class="nav-name">{{ setting('organization_name','Ogun Conference') }}</div>
    </a>
    <a href="{{ url('/') }}" class="nav-back">← Back to home</a>
</nav>

<div class="album-shell">

    <!-- Header -->
    <div class="album-head">
        <div>
            <div class="album-eyebrow"><span class="album-eyebrow-dot"></span>&nbsp; Congress {{ now()->year }}</div>
            <h1 class="album-title">Moments from the<br/><em>Congress</em></h1>
            <p class="album-lede">Official and community photos from the Ogun Conference Youth Congress, grouped by district.</p>
        </div>
        <div class="album-meta">
            <div class="album-meta-cell">
                <div class="album-meta-lbl">Congress</div>
                <div class="album-meta-val">{{ setting('camp_name','Youth Congress 2026') }}</div>
            </div>
            <div class="album-meta-cell">
                <div class="album-meta-lbl">Venue</div>
                <div class="album-meta-val">{{ setting('camp_venue','Abeokuta') }}</div>
            </div>
            <div class="album-meta-cell">
                <div class="album-meta-lbl">Dates</div>
                <div class="album-meta-val">{{ setting('camp_dates','Aug 16–22, 2026') }}</div>
            </div>
            <div class="album-meta-cell">
                <div class="album-meta-lbl">Total Photos</div>
                <div class="album-meta-val">{{ $media->flatten()->count() }}</div>
            </div>
        </div>
    </div>

    @if($media->isEmpty())
        <div class="album-empty">
            <div class="album-empty-icon">📸</div>
            <p style="font-family:var(--font-display);font-size:22px;color:var(--ink);margin-bottom:8px">No photos yet</p>
            <p style="font-size:14px;color:var(--muted)">Photos will appear here once uploaded and approved by the admin.</p>
        </div>
    @else
        <!-- District tabs -->
        <div class="album-tabs">
            <button class="album-tab active" data-target="all" onclick="switchTab(this,'all')">
                All <span class="album-tab-count">{{ $media->flatten()->count() }}</span>
            </button>
            @foreach($media as $districtName => $photos)
                <button class="album-tab" data-target="dist-{{ $loop->index }}" onclick="switchTab(this,'dist-{{ $loop->index }}')">
                    {{ $districtName ?? 'General' }} <span class="album-tab-count">{{ $photos->count() }}</span>
                </button>
            @endforeach
        </div>

        <!-- All photos -->
        <div class="district-section active" id="all">
            <div class="photo-grid">
                @foreach($media->flatten() as $photo)
                    <div class="photo-card" onclick="openLb('{{ $photo->cloudinary_url }}','{{ addslashes($photo->caption ?? $photo->title ?? '') }}','{{ addslashes($photo->district?->name ?? '') }}')">
                        <div class="photo-card-tape"></div>
                        <img src="{{ $photo->cloudinary_url }}" alt="{{ $photo->title ?? 'Congress photo' }}" loading="lazy"/>
                        <div class="photo-cap">
                            @if($photo->district)<div class="photo-cap-district">{{ $photo->district->name }}</div>@endif
                            @if($photo->title ?? $photo->caption)<div class="photo-cap-title">{{ $photo->title ?? $photo->caption }}</div>@endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Per-district photos -->
        @foreach($media as $districtName => $photos)
            <div class="district-section" id="dist-{{ $loop->index }}">
                <div class="photo-grid">
                    @foreach($photos as $photo)
                        <div class="photo-card" onclick="openLb('{{ $photo->cloudinary_url }}','{{ addslashes($photo->caption ?? $photo->title ?? '') }}','{{ addslashes($districtName ?? '') }}')">
                            <div class="photo-card-tape"></div>
                            <img src="{{ $photo->cloudinary_url }}" alt="{{ $photo->title ?? 'Congress photo' }}" loading="lazy"/>
                            <div class="photo-cap">
                                @if($photo->title ?? $photo->caption)<div class="photo-cap-title">{{ $photo->title ?? $photo->caption }}</div>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Footer strip -->
        <div class="album-foot">
            <div class="album-foot-text">
                <div class="album-foot-mark">&#10022;</div>
                <span>These memories belong to all of us. Share the album with your church family.</span>
            </div>
            <a href="{{ url('/') }}" class="album-foot-cta">Back to Congress Site &rarr;</a>
        </div>
    @endif
</div>

<!-- Lightbox -->
<div class="lb" id="lb" onclick="if(event.target===this)closeLb()">
    <button class="lb-close" onclick="closeLb()">×</button>
    <div class="lb-frame">
        <img class="lb-img" id="lb-img" src="" alt=""/>
        <div class="lb-caption" id="lb-caption"></div>
    </div>
</div>

<script>
    function switchTab(btn,target){
        document.querySelectorAll('.album-tab').forEach(b=>b.classList.remove('active'));
        document.querySelectorAll('.district-section').forEach(s=>s.classList.remove('active'));
        btn.classList.add('active');
        document.getElementById(target).classList.add('active');
    }
    function openLb(src,caption,district){
        document.getElementById('lb-img').src=src;
        document.getElementById('lb-caption').textContent=(district?district+' · ':'')+caption;
        document.getElementById('lb').classList.add('open');
        document.body.style.overflow='hidden';
    }
    function closeLb(){
        document.getElementById('lb').classList.remove('open');
        document.getElementById('lb-img').src='';
        document.body.style.overflow='';
    }
    document.addEventListener('keydown',e=>{if(e.key==='Escape')closeLb();});
</script>
</body>
</html>
