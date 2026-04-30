<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Coordinator Portal &mdash; {{ setting('camp_name','Ogun Youth Camp') }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet"/>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Lato',sans-serif;background:linear-gradient(135deg,#064E3B 0%,#065F46 100%);
            min-height:100vh;display:flex;align-items:center;justify-content:center;padding:2rem}
        .card{background:#fff;border-radius:24px;padding:2.5rem 2rem;width:100%;max-width:420px;
            box-shadow:0 24px 64px rgba(0,0,0,0.25);text-align:center}
        .logo{width:80px;height:80px;border-radius:50%;margin:0 auto 1.2rem;display:block;
            border:2px solid rgba(16,185,129,0.3)}
        h1{font-family:'Cinzel',serif;font-size:1.25rem;color:#064E3B;margin-bottom:0.25rem}
        .sub{color:#6B7280;font-size:0.82rem;margin-bottom:1.5rem}
        .alert{padding:0.75rem 1rem;border-radius:10px;font-size:0.82rem;margin-bottom:1rem;text-align:left}
        .alert-error{background:#FEF2F2;border:1px solid #FECACA;color:#B91C1C}
        label{display:block;text-align:left;font-size:0.72rem;font-weight:700;color:#555;
            margin-bottom:0.35rem;letter-spacing:0.06em;text-transform:uppercase}
        input{width:100%;padding:0.85rem 1rem;border:1.5px solid #E5E7EB;border-radius:12px;
            font-size:0.9rem;outline:none;transition:border-color 0.2s;margin-bottom:1rem;
            font-family:'Lato',sans-serif}
        input:focus{border-color:#10B981;box-shadow:0 0 0 3px rgba(16,185,129,0.1)}
        button{width:100%;padding:0.9rem;background:#064E3B;color:#fff;font-family:'Cinzel',serif;
            font-size:0.88rem;font-weight:700;border:none;border-radius:12px;cursor:pointer;
            letter-spacing:0.08em;transition:background 0.2s}
        button:hover{background:#047857}
        .back{display:block;margin-top:1.2rem;font-size:0.78rem;color:#9CA3AF;text-decoration:none}
        .back:hover{color:#064E3B}
    </style>
</head>
<body>
<div class="card">
    <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="logo"/>
    <h1>Coordinator Portal</h1>
    <p class="sub">Log in to manage your church's batch registrations and complete camper forms.</p>

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ route('coordinator.portal.login') }}">
        @csrf
        <label for="email">Email Address</label>
        <input type="email" name="email" id="email" value="{{ old('email') }}"
               placeholder="coordinator@church.org" required autocomplete="email"/>
        @error('email')<p style="color:#B91C1C;font-size:0.75rem;margin-top:-0.75rem;margin-bottom:0.75rem;text-align:left">{{ $message }}</p>@enderror

        <label for="password">Password</label>
        <input type="password" name="password" id="password" placeholder="••••••••" required/>

        <button type="submit">Log In &rarr;</button>
    </form>
    <a href="{{ route('home') }}" class="back">&#8592; Back to Camp Home</a>
</div>
</body>
</html>
