<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>ID Card Export</title>
    <meta http-equiv="refresh" content="{{ $data['status'] === 'processing' || $data['status'] === 'queued' ? '4' : '0' }}">
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#F7F3EA;
            display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;color:#1A2238}
        .card{background:#fff;border-radius:22px;padding:48px 40px;max-width:480px;width:100%;text-align:center;
            box-shadow:0 16px 48px -8px rgba(10,24,50,.1)}
        .icon{font-size:3rem;margin-bottom:16px}
        h1{font-size:20px;font-weight:700;margin-bottom:10px;color:#0A1832}
        p{font-size:14px;color:#6B7280;line-height:1.6;margin-bottom:24px}
        .badge{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
            padding:4px 12px;border-radius:100px;margin-bottom:20px}
        .badge-queued,.badge-processing{background:#FEF3C7;color:#92400E}
        .badge-done{background:#F0FDF4;color:#15803D}
        .badge-failed{background:#FEF2F2;color:#B91C1C}
        .spinner{width:40px;height:40px;border:3px solid #E5E7EB;border-top-color:#0A1832;
            border-radius:50%;animation:spin 1s linear infinite;margin:0 auto 20px}
        @keyframes spin{to{transform:rotate(360deg)}}
        .btn{display:inline-flex;align-items:center;gap:8px;background:#0A1832;color:#fff;font-size:13.5px;
            font-weight:600;padding:12px 24px;border-radius:100px;text-decoration:none;margin-top:4px}
        .btn:hover{background:#142547}
        .btn-gold{background:#B8924A}
        .btn-gold:hover{background:#D4B26E;color:#0A1832}
        .meta{font-size:12px;color:#9CA3AF;margin-top:16px}
    </style>
</head>
<body>
<div class="card">
    @if($data['status'] === 'done')
        <div class="icon">&#128196;</div>
        <span class="badge badge-done">&#10003; Ready to download</span>
        <h1>Your ID Cards are ready</h1>
        <p>{{ $data['total'] }} ID card(s) generated successfully.<br/>Your download will begin automatically.</p>
        <a href="{{ route('exports.id-cards.download', ['key' => $key]) }}" class="btn btn-gold">
            &#11123; Download PDF
        </a>
        <script>window.location="{{ route('exports.id-cards.download', ['key' => $key]) }}"</script>

    @elseif($data['status'] === 'failed')
        <div class="icon">&#10060;</div>
        <span class="badge badge-failed">Export failed</span>
        <h1>Something went wrong</h1>
        <p>{{ $data['error'] ?? 'An unexpected error occurred.' }}</p>
        <a href="javascript:history.back()" class="btn">&#8592; Go back and try again</a>

    @else
        <div class="spinner"></div>
        <span class="badge badge-processing">
        {{ $data['status'] === 'queued' ? 'Queued' : 'Generating...' }}
    </span>
        <h1>Generating ID Cards</h1>
        <p>
            Preparing {{ $data['total'] }} ID card(s).<br/>
            This page refreshes automatically. Please wait.
        </p>
        <p style="font-size:12px;color:#9CA3AF">Do not close this tab.</p>
    @endif

    <div class="meta">
        &#10022; Ogun Conference Youth Congress &middot; ID Card Export
    </div>
</div>
</body>
</html>
