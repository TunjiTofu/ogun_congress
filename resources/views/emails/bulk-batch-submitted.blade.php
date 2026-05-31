<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1"/>
    <title>New Batch Submitted</title>
    <style>
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Arial,sans-serif;background:#F0F4F8;color:#1F2937;line-height:1.6}
        .wrap{max-width:600px;margin:32px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(11,36,85,.10)}
        .hd{background:linear-gradient(135deg,#0B2455 0%,#1B3A8F 100%);padding:32px 40px 24px;text-align:center}
        .hd h1{color:#fff;font-size:20px;font-weight:700;margin-bottom:4px}
        .hd p{color:rgba(255,255,255,.65);font-size:13px}
        .gold-bar{height:4px;background:linear-gradient(90deg,#B8924A,#D4B26E,#B8924A)}
        .body{padding:32px 40px}
        .alert{background:#FEF3C7;border:1px solid #F59E0B;border-radius:8px;padding:14px 18px;margin-bottom:24px;font-size:13px;color:#92400E}
        .alert strong{display:block;font-size:14px;margin-bottom:4px}
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:1px;background:#E5E7EB;border:1px solid #E5E7EB;border-radius:8px;overflow:hidden;margin-bottom:24px}
        .info-cell{background:#fff;padding:14px 16px}
        .info-lbl{font-size:10px;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:#6B7280;margin-bottom:3px}
        .info-val{font-size:15px;font-weight:600;color:#0B2455}
        .info-val-lg{font-size:22px;color:#B8924A}
        .btn{display:inline-block;background:#0B2455;color:#fff;font-size:14px;font-weight:700;padding:13px 28px;border-radius:100px;text-decoration:none;margin:8px 0}
        .btn-wrap{text-align:center;margin:24px 0}
        .divider{height:1px;background:#F1F5F9;margin:24px 0}
        .footer{background:#F8FAFC;padding:20px 40px;text-align:center;border-top:1px solid #E5E7EB}
        .footer p{font-size:11.5px;color:#9CA3AF;line-height:1.7}
        @media(max-width:480px){.body,.footer{padding:20px} .info-grid{grid-template-columns:1fr}}
    </style>
</head>
<body>
<div class="wrap">
    <div class="hd">
        <h1>&#8358; New Batch Payment Submitted</h1>
        <p>Ogun Conference Youth Congress 2026 &mdash; Registration System</p>
    </div>
    <div class="gold-bar"></div>
    <div class="body">
        <div class="alert">
            <strong>&#8358; Action Required</strong>
            A youth leader has submitted a new bulk registration batch and is awaiting payment confirmation.
        </div>

        <div class="info-grid">
            <div class="info-cell">
                <div class="info-lbl">Church</div>
                <div class="info-val">{{ $church?->name ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">District</div>
                <div class="info-val">{{ $district?->name ?? '—' }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Campers in Batch</div>
                <div class="info-val">{{ $count }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Expected Total</div>
                <div class="info-val info-val-lg">&#8358;{{ $total }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Batch Reference</div>
                <div class="info-val" style="font-family:monospace;font-size:13px">{{ $batch->id }}</div>
            </div>
            <div class="info-cell">
                <div class="info-lbl">Submitted At</div>
                <div class="info-val" style="font-size:13px">{{ $batch->updated_at?->format('d M Y · H:i') }}</div>
            </div>
        </div>

        <p style="font-size:14px;color:#374151;margin-bottom:16px">
            Please log in to the admin dashboard, verify the bank transfer teller from the coordinator,
            and confirm the payment to generate registration codes for all campers in this batch.
        </p>

        <div class="btn-wrap">
            <a href="{{ $adminUrl }}" style="color: #FFFFFF" class="btn">Review in Admin Dashboard &rarr;</a>
        </div>

        <div class="divider"></div>

        <p style="font-size:12.5px;color:#6B7280">
            This notification was sent automatically when the church coordinator clicked
            <em>"Submit for Payment"</em> on their dashboard. If you have already processed this batch,
            please disregard this email.
        </p>
    </div>
    <div class="footer">
        <p>
            <strong style="color:#0B2455">Seventh-day Adventist Church — Ogun Conference Youth Department</strong><br/>
            Youth Congress 2026 &middot; Registration & Monitoring System<br/>
            This is an automated notification. Do not reply.
        </p>
    </div>
</div>
</body>
</html>
