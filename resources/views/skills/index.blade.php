<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Skill Acquisition — Ogun Conference Youth Congress 2026</title>
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png"/>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,700;1,9..144,400&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        :root{
            --navy:#0B2D6B;--navy2:#142547;--gold:#B8924A;--gold2:#D4B26E;
            --cream:#F7F3EA;--paper:#FBF8F1;--text:#1A2238;--muted:#7A8499;
            --border:#E7DFC9;--red:#DC2626;--green:#059669;
        }
        body{font-family:'DM Sans',system-ui,sans-serif;background:var(--paper);color:var(--text);min-height:100vh;display:flex;flex-direction:column}
        .nav{background:var(--navy);padding:1rem 1.5rem;display:flex;align-items:center;gap:.75rem}
        .nav img{width:36px;height:36px;border-radius:50%;border:1.5px solid rgba(184,146,74,.4)}
        .nav-title{font-family:'Fraunces',Georgia,serif;font-size:.85rem;color:var(--gold2);letter-spacing:.04em}
        .nav-sub{font-size:.65rem;color:rgba(255,255,255,.5)}
        .hero{background:linear-gradient(160deg,var(--navy) 0%,#1F3360 100%);padding:4rem 1.5rem 3rem;text-align:center;position:relative;overflow:hidden}
        .hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 60% 80%,rgba(184,146,74,.18),transparent 60%)}
        .hero-tag{font-size:.65rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--gold2);margin-bottom:.75rem;display:block}
        .hero h1{font-family:'Fraunces',Georgia,serif;font-size:clamp(2rem,5vw,3rem);font-weight:700;color:#fff;line-height:1.05;margin-bottom:.75rem}
        .hero h1 em{font-style:italic;color:var(--gold2);font-weight:400}
        .hero p{font-size:.95rem;color:rgba(255,255,255,.65);max-width:520px;margin:0 auto 2rem;line-height:1.6}
        .card{background:#fff;border:1px solid var(--border);border-radius:16px;padding:2rem;box-shadow:0 4px 24px rgba(10,24,50,.07);width:100%;max-width:420px;margin:0 auto}
        .form-label{font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--muted);display:block;margin-bottom:.5rem}
        .form-input{width:100%;padding:.85rem 1rem;border:1.5px solid var(--border);border-radius:10px;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:600;color:var(--text);background:var(--paper);letter-spacing:.06em;text-align:center;transition:.2s;outline:none}
        .form-input:focus{border-color:var(--navy);background:#fff}
        .form-input::placeholder{font-weight:400;letter-spacing:0;color:var(--muted)}
        .btn{display:block;width:100%;padding:.9rem;background:var(--navy);color:#fff;font-family:'DM Sans',sans-serif;font-size:.9rem;font-weight:700;border:none;border-radius:10px;cursor:pointer;margin-top:1rem;transition:.2s}
        .btn:hover{background:var(--navy2)}
        .alert{padding:.75rem 1rem;border-radius:8px;font-size:.82rem;margin-bottom:1rem}
        .alert-error{background:#FEF2F2;border:1px solid #FECACA;color:var(--red)}
        .alert-info{background:#EFF6FF;border:1px solid #BFDBFE;color:#1E40AF}
        .hint{font-size:.72rem;color:var(--muted);text-align:center;margin-top:.75rem;line-height:1.6}
        .hint code{background:var(--cream);padding:.1rem .4rem;border-radius:4px;font-family:monospace;color:var(--text)}
        /* Code entry split layout */
        .code-wrap{display:flex;align-items:center;border:1.5px solid var(--border);border-radius:10px;background:var(--paper);overflow:hidden;transition:.2s}
        .code-wrap:focus-within{border-color:var(--navy);background:#fff}
        .code-prefix{padding:.85rem .75rem .85rem 1rem;font-family:monospace;font-size:.9rem;font-weight:700;color:var(--muted);background:var(--cream);border-right:1.5px solid var(--border);white-space:nowrap;user-select:none;flex-shrink:0}
        .code-suffix-input{border:none!important;border-radius:0!important;background:transparent!important;flex:1;padding:.85rem .85rem .85rem .65rem;font-size:1.05rem;font-weight:700;letter-spacing:.15em;text-align:left;min-width:0;outline:none!important}
        .code-suffix-input::placeholder{font-weight:400;letter-spacing:.08em;color:var(--muted)}
        .main{flex:1;padding:2.5rem 1.5rem;display:flex;align-items:center;justify-content:center}
        footer{text-align:center;padding:1.5rem;font-size:.72rem;color:var(--muted)}
    </style>
</head>
<body>
<nav class="nav">
    <img src="{{ asset('images/congress_logo.png') }}" alt="Logo"/>
    <div>
        <div class="nav-title">Ogun Conference Youth Congress 2026</div>
        <div class="nav-sub">Skill Acquisition Module</div>
    </div>
</nav>

<div class="hero">
    <span class="hero-tag">Skill Acquisition · Congress 2026</span>
    <h1>From the Word<br/><em>to the World</em></h1>
    <p>Enter your registration code to view and enroll in a skill acquisition class for the Congress.</p>
</div>

<main class="main">
    <div class="card">
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        <form method="POST" action="{{ route('skills.login') }}" onsubmit="combineCode(this)">
            @csrf
            {{-- Hidden field that holds the full combined code on submit --}}
            <input type="hidden" name="code" id="code-full"/>

            <label class="form-label" for="code-suffix">Your Registration Code</label>

            <div class="code-wrap">
                <span class="code-prefix">OGN-2026-</span>
                <input
                    id="code-suffix"
                    type="text"
                    class="form-input code-suffix-input"
                    placeholder="XXXXXX"
                    maxlength="6"
                    autocomplete="off"
                    spellcheck="false"
                    inputmode="text"
                    oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')"
                    value="{{ old('code') ? substr(old('code'), 9) : '' }}"
                    required
                />
            </div>

            @error('code')<p style="color:var(--red);font-size:.78rem;margin-top:.4rem">{{ $message }}</p>@enderror
            <button type="submit" class="btn">Continue &rarr;</button>
        </form>

        <p class="hint">Enter the last 6 characters of your registration code.<br/>Format: <code>OGN-2026-<strong>XXXXXX</strong></code></p>

        <script>
            function combineCode(form) {
                var suffix = document.getElementById('code-suffix').value.trim().toUpperCase();
                document.getElementById('code-full').value = 'OGN-2026-' + suffix;
            }
            // Auto-move focus to suffix field on page load
            document.addEventListener('DOMContentLoaded', function() {
                var el = document.getElementById('code-suffix');
                if (el) el.focus();
            });
        </script>
    </div>
</main>

<footer>Ogun Conference Youth Congress 2026 &nbsp;&middot;&nbsp; Skill Acquisition Module</footer>
</body>
</html>
