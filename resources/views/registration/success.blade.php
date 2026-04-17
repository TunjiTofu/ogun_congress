<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Registration Complete — {{ setting('camp_name', 'Ogun Youth Camp') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#1B3A6B',gold:'#C9A94D'}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">

<nav class="bg-navy py-3 px-4">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-white font-bold text-sm">&#8592; Back to Camp Home</a>
        <span class="text-gold text-sm font-semibold">{{ setting('camp_name', 'Ogun Youth Camp') }}</span>
    </div>
</nav>

<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-lg mx-auto space-y-6">

        {{-- Success banner --}}
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
            <div class="text-5xl mb-3">&#127881;</div>
            <h1 class="text-2xl font-bold text-green-700">Registration Complete!</h1>
            <p class="text-green-600 text-sm mt-2">
                Welcome, <strong>{{ $camper->full_name }}</strong>.
                Your spot at {{ setting('camp_name', 'Ogun Youth Camp') }} is confirmed.
            </p>
        </div>

        {{-- Summary --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="font-bold text-navy mb-4">Your Registration Summary</h2>
            <div class="space-y-3 text-sm">
                @foreach([
                    ['Camper Number', $camper->camper_number, true],
                    ['Full Name',     $camper->full_name,     false],
                    ['Category',      $camper->category->label(), false],
                    ['Church',        $camper->church?->name ?? '—', false],
                    ['District',      $camper->church?->district?->name ?? '—', false],
                    ['Camp Dates',    setting('camp_dates', 'TBA'), false],
                    ['Venue',         setting('camp_venue', 'TBA'), false],
                ] as [$label, $value, $mono])
                    <div class="flex justify-between border-b border-gray-100 pb-2 last:border-0">
                        <span class="text-gray-500">{{ $label }}</span>
                        <span class="font-semibold {{ $mono ? 'font-mono' : '' }}">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Documents --}}
        <div class="bg-white rounded-2xl shadow-sm p-6"
             x-data="{
            urls: { id_card: null, consent_form: null },
            polls: 0,
            generating: true,
            needsConsent: {{ $camper->requiresConsentForm() ? 'true' : 'false' }},
            allReady() {
                return this.urls.id_card && (!this.needsConsent || this.urls.consent_form);
            },
            async fetchUrls() {
                this.generating = true;
                try {
                    const res  = await fetch('/api/v1/registration/downloads/{{ $camper->camper_number }}');
                    const data = await res.json();
                    if (data.status === 'ready') {
                        this.urls      = data.urls;
                        this.generating = false;
                    } else if (this.polls < 8) {
                        this.polls++;
                        setTimeout(() => this.fetchUrls(), 5000);
                    } else {
                        this.generating = false;
                    }
                } catch { this.generating = false; }
            }
         }"
             x-init="fetchUrls()">

            <h2 class="font-bold text-navy mb-4">Your Documents</h2>

            {{-- ID Card --}}
            <div class="border border-gray-200 rounded-xl p-4 mb-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">&#x1F4D7;</span>
                    <div>
                        <p class="font-semibold text-sm">Camper ID Card</p>
                        <p class="text-xs text-gray-400">Print and bring to camp. Contains your QR code.</p>
                    </div>
                </div>
                <div>
                <span x-show="!urls.id_card && generating"
                      class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Generating&hellip;</span>
                    <a x-show="urls.id_card" :href="urls.id_card" target="_blank"
                       class="text-xs bg-navy text-white px-3 py-1.5 rounded-full hover:bg-blue-800 transition">
                        Download PDF
                    </a>
                </div>
            </div>

            @if($camper->requiresConsentForm())
                {{-- Consent Form --}}
                <div class="border border-gray-200 rounded-xl p-4 mb-3 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">&#x1F4CB;</span>
                        <div>
                            <p class="font-semibold text-sm">Parental Consent Form</p>
                            <p class="text-xs text-gray-400">Print, sign, and bring to check-in.</p>
                        </div>
                    </div>
                    <div>
                <span x-show="!urls.consent_form && generating"
                      class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Generating&hellip;</span>
                        <a x-show="urls.consent_form" :href="urls.consent_form" target="_blank"
                           class="text-xs bg-navy text-white px-3 py-1.5 rounded-full hover:bg-blue-800 transition">
                            Download PDF
                        </a>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-700">
                    &#9888; <strong>Important:</strong> You will not be admitted without a signed consent form.
                    Print, sign, and present it at check-in.
                </div>
            @endif

            <button x-show="!allReady() && !generating" @click="fetchUrls()"
                    class="w-full mt-4 border border-navy text-navy font-semibold py-2 rounded-xl text-sm hover:bg-navy/5 transition">
                &#8635; Refresh Documents
            </button>
            <p x-show="generating" class="text-center text-xs text-gray-400 mt-4 animate-pulse">
                Documents are being generated, please wait&hellip;
            </p>
        </div>

        {{-- Reminder --}}
        <div class="bg-navy text-white rounded-2xl p-6 text-sm space-y-2">
            <h3 class="font-bold text-gold">Before You Arrive</h3>
            <ul class="space-y-1 text-white/80">
                <li>&#10003; Print and laminate your <strong>ID card</strong></li>
                @if($camper->requiresConsentForm())
                    <li>&#10003; Print, sign, and bring the <strong>consent form</strong></li>
                @endif
                <li>&#10003; Camp begins: <strong>{{ setting('camp_dates', 'TBA') }}</strong></li>
                <li>&#10003; Venue: <strong>{{ setting('camp_venue', 'TBA') }}</strong></li>
            </ul>
        </div>

        <p class="text-center text-xs text-gray-400">
            Return to this page any time using your code:
            <strong class="font-mono">{{ $camper->camper_number }}</strong>
        </p>

    </div>
</div>

</body>
</html>
