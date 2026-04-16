@extends('layouts.public')

@section('title', 'Registration Complete — ' . setting('camp_name', 'Ogun Youth Camp'))

@section('content')
    <div class="min-h-screen bg-gray-50 py-12 px-4">
        <div class="max-w-lg mx-auto space-y-6">

            {{-- Success banner --}}
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
                <div class="text-5xl mb-3">🎉</div>
                <h1 class="text-2xl font-bold text-green-700">Registration Complete!</h1>
                <p class="text-green-600 text-sm mt-2">
                    Welcome, <strong>{{ $camper->full_name }}</strong>.
                    Your spot at {{ setting('camp_name', 'Ogun Youth Camp') }} is confirmed.
                </p>
            </div>

            {{-- Camper summary card --}}
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h2 class="font-bold text-navy mb-4">Your Registration Summary</h2>
                <div class="space-y-3 text-sm">
                    @foreach([
                        ['label' => 'Camper Number', 'value' => $camper->camper_number, 'mono' => true],
                        ['label' => 'Full Name',      'value' => $camper->full_name],
                        ['label' => 'Category',       'value' => $camper->category->label()],
                        ['label' => 'Church',         'value' => $camper->church?->name ?? '—'],
                        ['label' => 'District',       'value' => $camper->church?->district?->name ?? '—'],
                        ['label' => 'Camp Dates',     'value' => setting('camp_dates', 'TBA')],
                        ['label' => 'Venue',          'value' => setting('camp_venue', 'TBA')],
                    ] as $row)
                        <div class="flex justify-between border-b border-gray-100 pb-2 last:border-0">
                            <span class="text-gray-500">{{ $row['label'] }}</span>
                            <span class="font-semibold {{ isset($row['mono']) ? 'font-mono' : '' }}">{{ $row['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Document downloads --}}
            <div class="bg-white rounded-2xl shadow-sm p-6" x-data="downloads('{{ $camper->camper_number }}')">
                <h2 class="font-bold text-navy mb-4">Your Documents</h2>

                {{-- ID Card --}}
                <div class="border border-gray-200 rounded-xl p-4 mb-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">🪪</span>
                            <div>
                                <p class="font-semibold text-sm">Camper ID Card</p>
                                <p class="text-xs text-gray-400">Print and bring to camp. Contains your QR code.</p>
                            </div>
                        </div>
                        <div>
                    <span x-show="!urls.id_card && !generating"
                          class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Generating…</span>
                            <a x-show="urls.id_card" :href="urls.id_card" target="_blank"
                               class="text-xs bg-navy text-white px-3 py-1.5 rounded-full hover:bg-steel transition">
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Consent Form --}}
                @if($camper->requiresConsentForm())
                    <div class="border border-gray-200 rounded-xl p-4 mb-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">📋</span>
                                <div>
                                    <p class="font-semibold text-sm">Parental Consent Form</p>
                                    <p class="text-xs text-gray-400">Print, sign, and bring to check-in.</p>
                                </div>
                            </div>
                            <div>
                    <span x-show="!urls.consent_form && !generating"
                          class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Generating…</span>
                                <a x-show="urls.consent_form" :href="urls.consent_form" target="_blank"
                                   class="text-xs bg-navy text-white px-3 py-1.5 rounded-full hover:bg-steel transition">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 text-xs text-red-700">
                        ⚠ <strong>Important:</strong> Your child will not be admitted to camp without a signed consent form.
                        Please print, sign, and present it at the check-in desk.
                    </div>
                @endif

                {{-- Refresh button while generating --}}
                <button x-show="!allReady() && !generating" @click="fetchUrls()"
                        class="w-full mt-4 border border-navy text-navy font-semibold py-2 rounded-xl text-sm hover:bg-navy/5 transition">
                    🔄 Refresh Documents
                </button>

                <p x-show="generating" class="text-center text-xs text-gray-400 mt-4 animate-pulse">
                    Documents are being generated, please wait…
                </p>
            </div>

            {{-- Camp info reminder --}}
            <div class="bg-navy text-white rounded-2xl p-6 text-sm space-y-2">
                <h3 class="font-bold text-gold">Before You Arrive</h3>
                <ul class="space-y-1 text-white/80">
                    <li>✓ Print and laminate your <strong>ID card</strong></li>
                    @if($camper->requiresConsentForm())
                        <li>✓ Print, sign, and bring the <strong>consent form</strong></li>
                    @endif
                    <li>✓ Camp begins: <strong>{{ setting('camp_dates', 'TBA') }}</strong></li>
                    <li>✓ Venue: <strong>{{ setting('camp_venue', 'TBA') }}</strong></li>
                </ul>
            </div>

            <p class="text-center text-xs text-gray-400">
                You can return to this page at any time using your code:
                <strong class="font-mono">{{ $camper->camper_number }}</strong>
            </p>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function downloads(camperNumber) {
            return {
                urls:       { id_card: null, consent_form: null },
                generating: false,
                polls:      0,

                allReady() {
                    const needsConsent = {{ $camper->requiresConsentForm() ? 'true' : 'false' }};
                    return this.urls.id_card && (!needsConsent || this.urls.consent_form);
                },

                async fetchUrls() {
                    this.generating = true;
                    try {
                        const res  = await fetch(`/api/v1/registration/downloads/${camperNumber}`);
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
                    } catch {
                        this.generating = false;
                    }
                },

                init() { this.fetchUrls(); }
            }
        }
    </script>
@endpush
