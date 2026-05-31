<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/><meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>403 — Access Denied</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#F7F3EA;color:#1A2238;
            display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px}
        .card{background:#fff;border-radius:22px;padding:48px 40px;max-width:500px;width:100%;
            text-align:center;box-shadow:0 16px 48px -8px rgba(10,24,50,.1)}
        .code{font-size:80px;font-weight:700;color:#E5E7EB;line-height:1;margin-bottom:8px;font-family:Georgia,serif}
        h1{font-size:22px;font-weight:700;color:#0A1832;margin-bottom:12px}
        p{font-size:14.5px;color:#6B7280;line-height:1.6;margin-bottom:28px}
        a{display:inline-flex;align-items:center;gap:8px;background:#0A1832;color:#fff;
            font-size:13.5px;font-weight:600;padding:12px 24px;border-radius:100px;text-decoration:none}
        a:hover{background:#142547}
        .gold{color:#B8924A}
    </style>
</head>
<body>
<div class="card">
    <div class="code">403</div>
    <h1>Access Denied</h1>
    <p>{{ $message ?? "You don't have permission to view this page. Please contact the secretariat if you believe this is an error." }}</p>
    <a href="/">← Back to home</a>
    <p style="margin-top:20px;font-size:12px;color:#B0B8CB">
        <span class="gold">&#10022;</span>
        Ogun Conference Youth Congress 2026
    </p>
</div>
</body>
</html>
