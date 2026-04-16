@extends('layouts.public')

@section('title', setting('camp_name', 'Ogun Youth Camp'))

@section('content')

    {{-- ── Hero ──────────────────────────────────────────────────────────────── --}}
    <section class="bg-navy text-white py-20 px-4 text-center relative overflow-hidden">
        {{-- Background pattern --}}
        <div class="absolute inset-0 opacity-5"
             style="background-image: repeating-linear-gradient(45deg, #fff 0, #fff 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>

        <div class="relative max-w-3xl mx-auto space-y-6">
            <div class="inline-block bg-gold/20 border border-gold/40 text-gold text-sm font-semibold px-4 py-1 rounded-full">
                {{ setting('camp_dates', 'Date TBA') }}
            </div>

            <h1 class="text-4xl md:text-5xl font-extrabold leading-tight">
                {{ setting('camp_name', 'Ogun Conference Youth Camp') }}
            </h1>

            @if(setting('camp_theme'))
                <p class="text-gold text-xl font-semibold italic">"{{ setting('camp_theme') }}"</p>
            @endif

            <p class="text-white/70 text-lg">
                📍 {{ setting('camp_venue', 'Venue TBA') }}
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
                <a href="{{ route('registration.index') }}"
                   class="bg-gold text-navy font-bold px-8 py-3 rounded-full text-lg hover:bg-yellow-400 transition shadow-lg">
                    Register Now
                </a>
                <a href="#about"
                   class="border border-white/40 text-white px-8 py-3 rounded-full text-lg hover:bg-white/10 transition">
                    Learn More
                </a>
            </div>

            {{-- Countdown --}}
            @if(setting('camp_start_date'))
                <div x-data="countdown('{{ setting('camp_start_date') }}')" class="pt-6">
                    <p class="text-white/60 text-sm mb-3">Registration closes in</p>
                    <div class="flex justify-center gap-4">
                        <template x-for="(unit, label) in {days, hours, minutes, seconds}" :key="label">
                            <div class="text-center">
                                <div class="bg-white/10 rounded-lg px-4 py-2 text-2xl font-bold" x-text="unit"></div>
                                <div class="text-white/50 text-xs mt-1" x-text="label"></div>
                            </div>
                        </template>
                        <div class="text-center">
                            <div class="bg-white/10 rounded-lg px-4 py-2 text-2xl font-bold" x-text="days"></div>
                            <div class="text-white/50 text-xs mt-1">Days</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white/10 rounded-lg px-4 py-2 text-2xl font-bold" x-text="hours"></div>
                            <div class="text-white/50 text-xs mt-1">Hours</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white/10 rounded-lg px-4 py-2 text-2xl font-bold" x-text="minutes"></div>
                            <div class="text-white/50 text-xs mt-1">Mins</div>
                        </div>
                        <div class="text-center">
                            <div class="bg-white/10 rounded-lg px-4 py-2 text-2xl font-bold" x-text="seconds"></div>
                            <div class="text-white/50 text-xs mt-1">Secs</div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </section>

    {{-- ── How to Register ──────────────────────────────────────────────────── --}}
    <section class="py-16 px-4 bg-white">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-navy text-center mb-10">How to Register</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                @foreach([
                    ['step' => '1', 'icon' => '🏦', 'title' => 'Pay Registration Fee', 'desc' => 'Transfer the fee to our bank account or pay online via Paystack.'],
                    ['step' => '2', 'icon' => '📲', 'title' => 'Send Proof',            'desc' => 'Send your payment receipt via WhatsApp to ' . setting('whatsapp_number', 'the number below') . '.'],
                    ['step' => '3', 'icon' => '🔑', 'title' => 'Receive Your Code',     'desc' => 'The accountant confirms your payment and sends a unique registration code via SMS.'],
                    ['step' => '4', 'icon' => '✅', 'title' => 'Complete Registration', 'desc' => 'Enter your code below and fill in the registration form to secure your spot.'],
                ] as $item)
                    <div class="text-center space-y-3">
                        <div class="w-12 h-12 bg-navy text-white rounded-full flex items-center justify-center text-xl font-bold mx-auto">
                            {{ $item['step'] }}
                        </div>
                        <div class="text-2xl">{{ $item['icon'] }}</div>
                        <h3 class="font-bold text-navy">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Bank details --}}
            @if(setting('bank_account_number'))
                <div class="mt-10 bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
                    <h3 class="font-bold text-navy mb-3">Bank Transfer Details</h3>
                    <div class="space-y-1 text-sm">
                        <p><span class="text-gray-500">Bank:</span> <strong>{{ setting('bank_name') }}</strong></p>
                        <p><span class="text-gray-500">Account Number:</span>
                            <strong class="font-mono text-lg">{{ setting('bank_account_number') }}</strong></p>
                        <p><span class="text-gray-500">Account Name:</span> <strong>{{ setting('bank_account_name') }}</strong></p>
                    </div>
                </div>
            @endif

            {{-- Code entry box --}}
            <div class="mt-10 bg-navy rounded-2xl p-8 text-white text-center">
                <h3 class="text-xl font-bold mb-2">Already have a code?</h3>
                <p class="text-white/70 text-sm mb-6">Enter your registration code to begin filling your form.</p>
                <form action="{{ route('registration.index') }}" method="GET"
                      class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    <input type="text" name="code" placeholder="e.g. OGN-2026-A3F7K2"
                           class="flex-1 px-4 py-3 rounded-xl text-gray-800 font-mono text-sm text-center uppercase
                              focus:outline-none focus:ring-2 focus:ring-gold"
                           maxlength="14" />
                    <button type="submit"
                            class="bg-gold text-navy font-bold px-6 py-3 rounded-xl hover:bg-yellow-400 transition whitespace-nowrap">
                        Continue →
                    </button>
                </form>
            </div>
        </div>
    </section>

    {{-- ── Registration Fees ────────────────────────────────────────────────── --}}
    <section class="py-16 px-4 bg-gray-50">
        <div class="max-w-3xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-navy mb-8">Registration Fees</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['cat' => 'Adventurers', 'age' => 'Ages 6–9',   'key' => 'fee_adventurer',   'color' => 'blue'],
                    ['cat' => 'Pathfinders', 'age' => 'Ages 10–15', 'key' => 'fee_pathfinder',   'color' => 'green'],
                    ['cat' => 'Senior Youth','age' => 'Ages 16+',   'key' => 'fee_senior_youth', 'color' => 'yellow'],
                ] as $f)
                    <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-{{ $f['color'] }}-500">
                        <h3 class="font-bold text-gray-800">{{ $f['cat'] }}</h3>
                        <p class="text-gray-500 text-sm mb-4">{{ $f['age'] }}</p>
                        <p class="text-3xl font-extrabold text-navy">
                            ₦{{ number_format((int) setting($f['key'], 5000)) }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── About ────────────────────────────────────────────────────────────── --}}
    <section id="about" class="py-16 px-4 bg-white">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-2xl font-bold text-navy text-center mb-8">About the Camp</h2>
            <div class="prose max-w-none text-gray-600 text-center">
                <p class="text-lg">
                    The Ogun Conference Annual Youth Congress brings together Adventurers, Pathfinders,
                    and Senior Youth from churches across the Ogun Conference for a week of spiritual growth,
                    fellowship, and ministry training.
                </p>
                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4 text-center not-prose">
                    @foreach([
                        ['label' => 'Venue',    'value' => setting('camp_venue', 'TBA')],
                        ['label' => 'Dates',    'value' => setting('camp_dates', 'TBA')],
                        ['label' => 'Theme',    'value' => setting('camp_theme', 'TBA')],
                        ['label' => 'Category', 'value' => 'Ages 6 and above'],
                    ] as $info)
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $info['label'] }}</p>
                            <p class="font-semibold text-navy text-sm">{{ $info['value'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── Programme ────────────────────────────────────────────────────────── --}}
    <section id="programme" class="py-16 px-4 bg-gray-50">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-navy mb-4">Camp Programme</h2>
            <p class="text-gray-500 mb-8">The detailed programme will be published closer to the camp date.</p>
            <div class="bg-white rounded-xl shadow-sm p-8 text-gray-400 italic">
                Programme details coming soon. Check back here or follow our official channels.
            </div>
        </div>
    </section>

    {{-- ── Rules ────────────────────────────────────────────────────────────── --}}
    <section class="py-16 px-4 bg-white">
        <div class="max-w-3xl mx-auto">
            <h2 class="text-2xl font-bold text-navy text-center mb-8">Camp Rules & Information</h2>
            <div class="space-y-3 text-sm text-gray-700">
                @foreach([
                    'All campers must carry their printed ID card at all times during the camp.',
                    'Campers under 18 must submit a signed parental consent form at check-in.',
                    'Participants must wear the official camp uniform during all formal sessions.',
                    'Mobile phones should be kept on silent during services and meetings.',
                    'No camper may leave the camp venue without prior permission from camp officials.',
                    'All campers are expected to participate in the camp programme respectfully.',
                ] as $rule)
                    <div class="flex gap-3 bg-gray-50 rounded-lg p-3">
                        <span class="text-navy font-bold">✓</span>
                        <span>{{ $rule }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Contact ──────────────────────────────────────────────────────────── --}}
    <section id="contact" class="py-16 px-4 bg-gray-50">
        <div class="max-w-2xl mx-auto text-center">
            <h2 class="text-2xl font-bold text-navy mb-4">Contact Us</h2>
            <p class="text-gray-500 mb-8">
                For registration issues, payment enquiries, or general questions, reach us via:
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                @if(setting('whatsapp_number'))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp_number')) }}"
                       target="_blank"
                       class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 hover:bg-green-100 transition">
                        <span class="text-2xl">💬</span>
                        <div class="text-left">
                            <div class="font-bold text-gray-800">WhatsApp</div>
                            <div class="text-gray-500">{{ setting('whatsapp_number') }}</div>
                        </div>
                    </a>
                @endif
                @if(setting('secretariat_phone'))
                    <a href="tel:{{ setting('secretariat_phone') }}"
                       class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4 hover:bg-blue-100 transition">
                        <span class="text-2xl">📞</span>
                        <div class="text-left">
                            <div class="font-bold text-gray-800">Secretariat</div>
                            <div class="text-gray-500">{{ setting('secretariat_phone') }}</div>
                        </div>
                    </a>
                @endif
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        function countdown(targetDate) {
            return {
                days: '00', hours: '00', minutes: '00', seconds: '00',
                init() {
                    this.update();
                    setInterval(() => this.update(), 1000);
                },
                update() {
                    const diff = new Date(targetDate) - new Date();
                    if (diff <= 0) { this.days = this.hours = this.minutes = this.seconds = '00'; return; }
                    this.days    = String(Math.floor(diff / 86400000)).padStart(2, '0');
                    this.hours   = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
                    this.minutes = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
                    this.seconds = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
                }
            }
        }
    </script>
@endpush
