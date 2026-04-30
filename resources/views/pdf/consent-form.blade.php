<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9.5pt;
            color: #1A1A1A;
            padding: 18mm 18mm 12mm 18mm;
            line-height: 1.5;
        }
        .letterhead {
            text-align: center;
            border-bottom: 2pt solid #0B2D6B;
            padding-bottom: 5mm;
            margin-bottom: 5mm;
        }
        .org-name { font-size: 12pt; font-weight: bold; color: #0B2D6B; text-transform: uppercase; letter-spacing: 0.5pt; }
        .form-title { font-size: 10pt; font-weight: bold; margin-top: 2mm; color: #333; }
        .camp-meta { font-size: 8.5pt; color: #666; margin-top: 1mm; }
        h2 {
            font-size: 9.5pt; font-weight: bold; color: #0B2D6B;
            border-bottom: 0.5pt solid #CCCCCC;
            padding-bottom: 1mm; margin-top: 5mm; margin-bottom: 3mm;
            text-transform: uppercase; letter-spacing: 0.3pt;
        }
        .info-table { width: 100%; border-collapse: collapse; font-size: 9pt; }
        .info-table td { padding: 1.2mm 2mm; vertical-align: top; }
        .info-table .label { width: 35%; color: #555; font-weight: bold; }
        .info-table .value { border-bottom: 0.5pt solid #DDD; }
        .declaration { font-size: 9pt; line-height: 1.7; text-align: justify; margin: 3mm 0; }
        .clause {
            margin: 3mm 0; padding: 2mm 3mm;
            background: #F8F8F8; border-left: 2pt solid #0B2D6B; font-size: 8.5pt;
        }
        .clause strong { display: block; margin-bottom: 0.8mm; color: #0B2D6B; }
        .opt-out { font-size: 8pt; margin-top: 1.5mm; display: flex; align-items: flex-start; gap: 2mm; }
        .checkbox { width: 3.5mm; height: 3.5mm; border: 1pt solid #555; display: inline-block; flex-shrink: 0; margin-top: 0.5mm; }
        .signature-block { margin-top: 7mm; }
        .sig-row { display: flex; gap: 8%; margin-bottom: 5mm; }
        .sig-cell { flex: 1; }
        .sig-line { border-bottom: 1pt solid #333; height: 10mm; margin-bottom: 1mm; }
        .sig-label { font-size: 7.5pt; color: #666; }
        .page-footer {
            position: fixed; bottom: 6mm; left: 18mm; right: 18mm;
            border-top: 0.5pt solid #CCC; padding-top: 1.5mm;
            font-size: 7pt; color: #AAA; text-align: center;
        }
        .print-note {
            background: #FFF9E6; border: 1pt solid #E6C200;
            border-radius: 1.5mm; padding: 2mm 3mm;
            font-size: 8pt; color: #7A5F00; margin-bottom: 4mm;
        }
        .health-row { font-size: 8.5pt; margin-bottom: 1.5mm; }
        .health-none { font-style: italic; color: #888; font-size: 8.5pt; }
    </style>
</head>
<body>

{{-- Letterhead --}}
<div class="letterhead">
    @php $logoPath = public_path('images/congress_logo.png'); @endphp
    @if(file_exists($logoPath))
        <div style="margin-bottom:3mm;">
            <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents($logoPath)) }}"
                 style="width:16mm;height:16mm;"/>
        </div>
    @endif
    <div class="org-name">Seventh-day Adventist Church</div>
    <div class="org-name" style="font-size:10pt;">Ogun Conference Youth Department</div>
    <div class="form-title">Parental / Guardian Consent Form</div>
    <div class="camp-meta">{{ $campName }} &bull; {{ $campDates }} &bull; {{ $campVenue }}</div>
</div>

<div class="print-note">
    &#128203; Print this form, sign it, and present at check-in. Required for all participants under 18.
</div>

{{-- Section A: Camper Details --}}
<h2>Section A &mdash; Camper Details</h2>
<table class="info-table">
    <tr>
        <td class="label">Full Name:</td>
        <td class="value">{{ $camper->full_name }}</td>
        <td class="label" style="padding-left:5mm;">Reg. Code:</td>
        <td class="value">{{ $camper->camper_number }}</td>
    </tr>
    <tr>
        <td class="label">Category:</td>
        <td class="value">{{ $camper->category->label() }}</td>
        <td class="label" style="padding-left:5mm;">Rank:</td>
        <td class="value">{{ $camper->club_rank ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Church:</td>
        <td class="value">{{ $camper->church?->name ?? '—' }}</td>
        <td class="label" style="padding-left:5mm;">District:</td>
        <td class="value">{{ $camper->church?->district?->name ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Gender:</td>
        <td class="value">{{ $camper->gender?->label() ?? '—' }}</td>
        <td class="label" style="padding-left:5mm;">Date of Birth:</td>
        <td class="value">
            {{ $camper->date_of_birth ? $camper->date_of_birth->format('d F Y') : '—' }}
        </td>
    </tr>
</table>

{{-- Section B: Parent/Guardian --}}
<h2>Section B &mdash; Parent / Guardian Information</h2>
@php
    // Load contacts fresh to avoid lazy-loading issues
    $contacts = $camper->contacts()->get();
    $parent   = $contacts->first(function($c) {
        // Handle both enum and string type values
        $typeVal = is_object($c->type) ? $c->type->value : (string) $c->type;
        return $typeVal === 'parent_guardian';
    });
@endphp
<table class="info-table">
    <tr>
        <td class="label">Full Name:</td>
        <td class="value" style="min-width:55mm;">{{ $parent?->full_name ?? '' }}&nbsp;</td>
        <td class="label" style="padding-left:5mm;">Relationship:</td>
        <td class="value">{{ $parent?->relationship ?? '' }}&nbsp;</td>
    </tr>
    <tr>
        <td class="label">Phone:</td>
        <td class="value">{{ $parent?->phone ?? '' }}&nbsp;</td>
        <td class="label" style="padding-left:5mm;">Email:</td>
        <td class="value">{{ $parent?->email ?? '' }}&nbsp;</td>
    </tr>
</table>
@if(!$parent)
    <p style="font-size:7.5pt;color:#888;margin-top:1mm;">
        &#9998; Please complete parent/guardian details above and submit at check-in if not captured during registration.
    </p>
@endif

{{-- Section C: Medical --}}
<h2>Section C &mdash; Medical Information</h2>
@php $health = $camper->health; @endphp
<table class="info-table">
    <tr>
        <td class="label">Medical Conditions:</td>
        <td class="value" colspan="3">
            @if($health && $health->medical_conditions)
                <strong>{{ $health->medical_conditions }}</strong>
            @else
                <span class="health-none">None declared</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Current Medications:</td>
        <td class="value" colspan="3">
            @if($health && $health->medications)
                <strong>{{ $health->medications }}</strong>
            @else
                <span class="health-none">None declared</span>
            @endif
        </td>
    </tr>
    <tr>
        <td class="label">Allergies:</td>
        <td class="value" colspan="3">
            @if($health && $health->allergies)
                <strong>{{ $health->allergies }}</strong>
            @else
                <span class="health-none">None declared</span>
            @endif
        </td>
    </tr>
</table>

{{-- Section D: Consent Declarations --}}
<h2>Section D &mdash; Consent Declarations</h2>
<p class="declaration">I, the undersigned parent or legal guardian of the above-named participant, hereby:</p>

<div class="clause">
    <strong>1. Camp Attendance Consent</strong>
    Give my full consent for my child / ward to attend <em>{{ $campName }}</em>
    ({{ $campDates }}, {{ $campVenue }}). I acknowledge that the camp involves physical activities,
    outdoor programmes, and spiritual sessions.
</div>

<div class="clause">
    <strong>2. Medical Consent</strong>
    Authorise the camp medical team and designated officials to administer first aid,
    over-the-counter medication, or emergency medical treatment if I cannot be reached in a timely manner.
</div>

<div class="clause">
    <strong>3. Media Consent</strong>
    Give consent for photographs / video of my child taken during camp to be used for official
    SDA Ogun Conference publications and social media.
    <div class="opt-out">
        <span class="checkbox"></span>
        <span><strong>Opt-Out:</strong> Tick if you do NOT consent to your child's image being published.</span>
    </div>
</div>

<div class="clause">
    <strong>4. Rules &amp; Emergency Contact</strong>
    Confirm the emergency contact details on record are accurate and agree that my child
    will abide by all camp rules. I understand my child may only be released to an authorised adult on record.
</div>

{{-- Section E: Signature --}}
<h2>Section E &mdash; Declaration &amp; Signature</h2>
<p class="declaration">
    I confirm that all information provided is true and accurate, and I agree to the above declarations.
</p>

<div class="signature-block">
    <div class="sig-row">
        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">Signature of Parent / Guardian</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">Full Name (Print)</div>
        </div>
    </div>
    <div class="sig-row">
        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">Date</div>
        </div>
        <div class="sig-cell">
            <div class="sig-line"></div>
            <div class="sig-label">Relationship to Camper</div>
        </div>
    </div>
</div>

<div class="page-footer">
    Seventh-day Adventist Church &mdash; Ogun Conference &bull;
    {{ $campName }} &bull; Code: {{ $camper->camper_number }}
</div>

</body>
</html>
