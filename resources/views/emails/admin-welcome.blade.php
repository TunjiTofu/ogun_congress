<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Welcome to Ogun Conference Youth Congress Admin</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
            background: #F0F4F8; color: #1F2937; line-height: 1.6; }
        .wrapper { max-width: 620px; margin: 32px auto; background: #fff;
            border-radius: 12px; overflow: hidden;
            box-shadow: 0 4px 24px rgba(11,36,85,0.10); }
        .header { background: linear-gradient(135deg, #0B2455 0%, #1B3A8F 100%);
            padding: 36px 40px 28px; text-align: center; }
        .header-logo { width: 64px; height: 64px; border-radius: 50%;
            border: 2px solid rgba(212,178,110,0.6);
            margin: 0 auto 16px; display: block; }
        .header h1 { color: #fff; font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .header p  { color: rgba(255,255,255,0.65); font-size: 13px; }
        .gold-bar  { height: 4px; background: linear-gradient(90deg, #B8924A, #D4B26E, #B8924A); }
        .body      { padding: 36px 40px; }
        .greeting  { font-size: 18px; font-weight: 700; color: #0B2455; margin-bottom: 12px; }
        .intro     { font-size: 14px; color: #374151; margin-bottom: 24px; }
        .section-title { font-size: 11px; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.1em; color: #6B7280; margin-bottom: 10px; }
        .creds-box { background: #F8FAFC; border: 1px solid #E5E7EB; border-radius: 8px;
            padding: 20px 24px; margin-bottom: 24px; }
        .cred-row  { display: flex; align-items: center; justify-content: space-between;
            padding: 8px 0; border-bottom: 1px solid #F1F5F9; }
        .cred-row:last-child { border-bottom: none; padding-bottom: 0; }
        .cred-key  { font-size: 12px; color: #6B7280; font-weight: 600; }
        .cred-val  { font-size: 13px; font-weight: 700; color: #0B2455;
            font-family: 'Courier New', monospace; background: #EEF2FF;
            padding: 3px 10px; border-radius: 4px; letter-spacing: 0.04em; }
        .btn       { display: inline-block; background: #0B2455; color: #fff;
            font-size: 14px; font-weight: 700; padding: 13px 28px;
            border-radius: 100px; text-decoration: none; margin: 4px; }
        .btn-gold  { background: #B8924A; }
        .btn-wrap  { text-align: center; margin: 24px 0; }
        .alert     { background: #FEF3C7; border: 1px solid #F59E0B; border-radius: 8px;
            padding: 14px 18px; margin-bottom: 24px; font-size: 13px; color: #92400E; }
        .alert strong { display: block; margin-bottom: 4px; font-size: 13px; }
        .policy-box { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px;
            padding: 16px 20px; margin-bottom: 24px; }
        .policy-box h4 { font-size: 13px; font-weight: 700; color: #14532D; margin-bottom: 10px; }
        .policy-list { list-style: none; }
        .policy-list li { font-size: 12.5px; color: #166534; padding: 3px 0; }
        .policy-list li::before { content: "✓ "; font-weight: 700; }
        .divider { height: 1px; background: #F1F5F9; margin: 24px 0; }
        .footer { background: #F8FAFC; padding: 24px 40px; text-align: center;
            border-top: 1px solid #E5E7EB; }
        .footer p { font-size: 11.5px; color: #9CA3AF; line-height: 1.7; }
        .footer a { color: #B8924A; text-decoration: none; }
        @media (max-width: 480px) {
            .body, .footer { padding: 24px 20px; }
            .cred-row { flex-direction: column; align-items: flex-start; gap: 4px; }
            .btn { display: block; text-align: center; margin: 6px 0; }
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
{{--        <img src="{{ url('/images/congress_logo.png') }}" alt="Congress Logo" class="header-logo"/>--}}
        <img src="{{ url('https://res.cloudinary.com/dhxz1zt0j/image/upload/v1779953796/congress_logo_chy6td.png') }}" alt="Congress Logo" class="header-logo"/>
        <h1>Ogun Conference Youth Congress 2026</h1>
        <p>Registration & Monitoring System — Admin Portal</p>
    </div>
    <div class="gold-bar"></div>

    <div class="body">
        <p class="greeting">Welcome, {{ $user->name }}! 👋</p>
        <p class="intro">
            An admin account has been created for you on the
            <strong>Ogun Conference Youth Congress 2026 Registration & Monitoring System</strong>.
            Your login credentials are below. Please log in and change your password immediately.
        </p>

        <p class="section-title">Your Login Credentials</p>
        <div class="creds-box">
            <div class="cred-row">
                <span class="cred-key">Email Address</span>
                <span class="cred-val">{{ $user->email }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-key">Temporary Password</span>
                <span class="cred-val">{{ $plainPassword }}</span>
            </div>
            <div class="cred-row">
                <span class="cred-key">Your Role</span>
                <span class="cred-val">{{ $roleName }}</span>
            </div>
        </div>

        <div class="alert">
            <strong>⚠️ You must change your password on first login.</strong>
            You will not be able to access any features until your password has been updated.
            Do not share this email with anyone.
        </div>

        <div class="btn-wrap">
            <a href="{{ $adminUrl }}/login" class="btn btn-gold" style="color: #FFFFFF">Log In to Admin Panel →</a>
            <a href="{{ $landingUrl }}" class="btn" style="color: #FFFFFF">Visit Congress Website</a>
        </div>

        <div class="divider"></div>

        <div class="policy-box">
            <h4>Password Requirements</h4>
            <ul class="policy-list">
                <li>At least <strong>8 characters</strong> long</li>
                <li>Contains at least one <strong>uppercase letter</strong> (A–Z)</li>
                <li>Contains at least one <strong>lowercase letter</strong> (a–z)</li>
                <li>Contains at least one <strong>number</strong> (0–9)</li>
                <li>Contains at least one <strong>special character</strong> (!@#$%^&amp;*)</li>
                <li>Must be <strong>different from your temporary password</strong></li>
                <li>Must not be a commonly used password</li>
            </ul>
        </div>

        <div class="divider"></div>

        <p style="font-size:13px;color:#6B7280;">
            If you did not expect this email, please contact the system administrator immediately.
            This account was created as part of the Ogun Conference Youth Congress admin setup.
        </p>
    </div>

    <div class="footer">
        <p>
            <strong style="color:#0B2455">Seventh-day Adventist Church — Ogun Conference Youth Department</strong><br/>
            {{ setting('camp_name') }} · Abeokuta · {{ setting('camp_dates') }}<br/>
            <em>"{{ setting('camp_theme') }}" — Acts 1:8</em><br/><br/>
            This is an automated message. Please do not reply directly to this email.
        </p>
    </div>
</div>
</body>
</html>
