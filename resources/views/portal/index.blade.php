<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Camper Portal &mdash; {{ setting('camp_name', 'Ogun Youth Camp') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Lato',sans-serif;background:linear-gradient(135deg,#0B2D6B 0%,#1E5FAD 100%);min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:2rem}
        .card{background:#fff;border-radius:24px;padding:2.5rem 2rem;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.25);text-align:center}
        .logo{width:80px;height:80px;border-radius:50%;margin:0 auto 1.2rem;display:block;border:2px solid rgba(201,169,77,0.3)}
        h1{font-family:'Cinzel',serif;font-size:1.3rem;color:#0B2D6B;margin-bottom:0.3rem}
        p{color:#888;font-size:0.85rem;margin-bottom:1.5rem;line-height:1.6}
        .alert{padding:0.75rem 1rem;border-radius:10px;font-size:0.85rem;margin-bottom:1rem;text-align:left}
        .alert-error{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C}
        .alert-success{background:#F0FDF4;border:1px solid #BBF7D0;color:#15803D}
        label{display:block;text-align:left;font-size:0.78rem;font-weight:700;color:#555;margin-bottom:0.4rem;letter-spacing:0.06em;text-transform:uppercase}
        input{width:100%;padding:0.9rem 1rem;border:1.5px solid #E5E7EB;border-radius:12px;font-family:monospace;font-size:1rem;text-align:center;letter-spacing:0.1em;text-transform:uppercase;outline:none;transition:border-color 0.2s;margin-bottom:1.2rem}
        input:focus{border-color:#1B3A6B;box-shadow:0 0 0 3px rgba(27,58,107,0.1)}
        button{width:100%;padding:0.9rem;background:#0B2D6B;color:#fff;font-family:'Cinzel',serif;font-size:0.9rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;letter-spacing:0.08em;transition:background 0.2s}
        button:hover{background:#1E5FAD}
        .back{display:block;margin-top:1.2rem;font-size:0.8rem;color:#888;text-decoration:none}
        .back:hover{color:#0B2D6B}
    </style>
</head>
<body>
<div class="card">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo"/>
    <h1>Camper Portal</h1>
    <p>Enter your registration code to view your ID card, consent form, and camp announcements.</p>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('portal.login') }}">
        @csrf
        <label for="code">Your Registration Code</label>
        <input type="text" name="code" id="code" placeholder="OGN-2026-XXXXXX"
               value="{{ old('code') }}" maxlength="15" autocomplete="off" spellcheck="false"
               oninput="this.value=this.value.toUpperCase()"/>
        @error('code')<p style="color:#B91C1C;font-size:0.8rem;margin:-0.8rem 0 0.8rem;text-align:left">{{ $message }}</p>@enderror
        <button type="submit">Access My Portal &rarr;</button>
    </form>
    <a href="{{ route('home') }}" class="back">&#8592; Back to Camp Home</a>
</div>
</body>
</html>
