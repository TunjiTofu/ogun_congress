<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 10pt;
            color: #1A1A1A;
            padding: 20mm 20mm 15mm 20mm;
            line-height: 1.5;
        }

        /* Header */
        .letterhead {
            text-align: center;
            border-bottom: 2pt solid #1B3A6B;
            padding-bottom: 6mm;
            margin-bottom: 6mm;
        }

        .org-name {
            font-size: 13pt;
            font-weight: bold;
            color: #1B3A6B;
            text-transform: uppercase;
            letter-spacing: 0.5pt;
        }

        .form-title {
            font-size: 11pt;
            font-weight: bold;
            margin-top: 2mm;
            color: #333333;
        }

        .camp-meta {
            font-size: 9pt;
            color: #666666;
            margin-top: 1mm;
        }

        /* Sections */
        h2 {
            font-size: 10pt;
            font-weight: bold;
            color: #1B3A6B;
            border-bottom: 0.5pt solid #CCCCCC;
            padding-bottom: 1mm;
            margin-top: 5mm;
            margin-bottom: 3mm;
            text-transform: uppercase;
            letter-spacing: 0.3pt;
        }

        /* Info table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }

        .info-table td {
            padding: 1.5mm 2mm;
            vertical-align: top;
        }

        .info-table .label {
            width: 38%;
            color: #666666;
            font-weight: bold;
        }

        .info-table .value {
            border-bottom: 0.5pt solid #DDDDDD;
        }

        /* Declaration text */
        .declaration {
            font-size: 9.5pt;
            line-height: 1.7;
            text-align: justify;
            margin: 3mm 0;
        }

        /* Clause blocks */
        .clause {
            margin: 3mm 0;
            padding: 2.5mm 3mm;
            background: #F8F8F8;
            border-left: 2pt solid #1B3A6B;
            font-size: 9pt;
        }

        .clause strong {
            display: block;
            margin-bottom: 1mm;
            color: #1B3A6B;
        }

        /* Opt-out */
        .opt-out {
            font-size: 8.5pt;
            margin-top: 1.5mm;
            display: flex;
            align-items: flex-start;
            gap: 2mm;
        }

        .checkbox {
            width: 4mm;
            height: 4mm;
            border: 1pt solid #555555;
            display: inline-block;
            flex-shrink: 0;
            margin-top: 0.5mm;
        }

        /* Signature block */
        .signature-block {
            margin-top: 8mm;
            display: table;
            width: 100%;
        }

        .sig-row {
            display: table-row;
        }

        .sig-cell {
            display: table-cell;
            width: 48%;
            padding-right: 4%;
            vertical-align: bottom;
        }

        .sig-line {
            border-bottom: 1pt solid #333333;
            height: 12mm;
            margin-bottom: 1mm;
        }

        .sig-label {
            font-size: 8pt;
            color: #666666;
        }

        /* Footer */
        .page-footer {
            position: fixed;
            bottom: 8mm;
            left: 20mm;
            right: 20mm;
            border-top: 0.5pt solid #CCCCCC;
            padding-top: 2mm;
            font-size: 7.5pt;
            color: #AAAAAA;
            text-align: center;
        }

        /* Print instructions box */
        .print-note {
            background: #FFF9E6;
            border: 1pt solid #E6C200;
            border-radius: 2mm;
            padding: 2.5mm 3mm;
            font-size: 8.5pt;
            color: #7A5F00;
            margin-bottom: 4mm;
        }
    </style>
</head>
<body>

    {{-- Letterhead --}}
    <div class="letterhead">
        <div class="org-name">Seventh-day Adventist Church</div>
        <div class="org-name" style="font-size: 11pt;">Ogun Conference Youth Department</div>
        <div class="form-title">Parental / Guardian Consent Form</div>
        <div class="camp-meta">{{ $campName }} &bull; {{ $campDates }} &bull; {{ $campVenue }}</div>
    </div>

    {{-- Print instruction --}}
    <div class="print-note">
        📋 Please print this form, sign it, and present it at the check-in desk on arrival day.
        This form is required for all participants under 18 years of age.
    </div>

    {{-- Camper Details --}}
    <h2>Section A — Camper Details</h2>
    <table class="info-table">
        <tr>
            <td class="label">Full Name:</td>
            <td class="value">{{ $camper->full_name }}</td>
            <td class="label" style="padding-left: 6mm;">Registration Code:</td>
            <td class="value">{{ $camper->camper_number }}</td>
        </tr>
        <tr>
            <td class="label">Date of Birth:</td>
            <td class="value">{{ $camper->date_of_birth->format('d F Y') }}</td>
            <td class="label" style="padding-left: 6mm;">Age:</td>
            <td class="value">{{ $camper->age }} years</td>
        </tr>
        <tr>
            <td class="label">Category:</td>
            <td class="value">{{ $camper->category->label() }}</td>
            <td class="label" style="padding-left: 6mm;">Church:</td>
            <td class="value">{{ $camper->church?->name }}</td>
        </tr>
        <tr>
            <td class="label">District:</td>
            <td class="value">{{ $camper->church?->district?->name }}</td>
            <td class="label" style="padding-left: 6mm;">Gender:</td>
            <td class="value">{{ $camper->gender->label() }}</td>
        </tr>
    </table>

    @if($camper->contacts->isNotEmpty())
        @php $parent = $camper->contacts->where('type.value', 'parent_guardian')->first(); @endphp
        @if($parent)
        <h2>Section B — Parent / Guardian Details</h2>
        <table class="info-table">
            <tr>
                <td class="label">Full Name:</td>
                <td class="value">{{ $parent->full_name }}</td>
                <td class="label" style="padding-left: 6mm;">Relationship:</td>
                <td class="value">{{ $parent->relationship }}</td>
            </tr>
            <tr>
                <td class="label">Phone Number:</td>
                <td class="value">{{ $parent->phone }}</td>
                <td class="label" style="padding-left: 6mm;">Email:</td>
                <td class="value">{{ $parent->email ?? '—' }}</td>
            </tr>
        </table>
        @endif
    @endif

    {{-- Consent declarations --}}
    <h2>Section C — Consent Declarations</h2>

    <p class="declaration">
        I, the undersigned parent or legal guardian of the above-named participant, hereby:
    </p>

    <div class="clause">
        <strong>1. Camp Attendance Consent</strong>
        Give my full consent for my child / ward to attend the <em>{{ $campName }}</em>
        taking place from {{ $campDates }} at {{ $campVenue }}.
        I acknowledge that the camp involves physical activities, outdoor programmes, and spiritual sessions.
    </div>

    <div class="clause">
        <strong>2. Medical Consent</strong>
        Authorise the camp medical team and any designated camp official to administer first aid,
        over-the-counter medication, or emergency medical treatment if I cannot be reached in a timely manner.
        I understand that all reasonable efforts will be made to contact me first.
    </div>

    <div class="clause">
        <strong>3. Media Consent</strong>
        Give my consent for photographs and video footage of my child / ward taken during the camp
        to be used for official SDA Church — Ogun Conference publications, social media platforms,
        and internal communications.

        <div class="opt-out">
            <span class="checkbox"></span>
            <span>
                <strong>Opt-Out:</strong> Tick this box if you do NOT consent to your child's image
                being used in any published material.
            </span>
        </div>
    </div>

    <div class="clause">
        <strong>4. Emergency Contact &amp; Departure</strong>
        Confirm that the emergency contact details provided during online registration are accurate.
        I understand that my child may only be released to an authorised adult whose details are on record.
    </div>

    {{-- Signature --}}
    <h2>Section D — Declaration &amp; Signature</h2>

    <p class="declaration">
        I confirm that all information provided above and during online registration is true and accurate.
        I understand the camp rules and agree that my child / ward will abide by them.
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
        <div class="sig-row" style="margin-top: 6mm; display: table-row;">
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

    {{-- Page footer --}}
    <div class="page-footer">
        Seventh-day Adventist Church &mdash; Ogun Conference &bull;
        {{ $campName }} &bull; Camper: {{ $camper->camper_number }}
    </div>

</body>
</html>
