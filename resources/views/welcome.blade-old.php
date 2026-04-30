<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ setting('camp_name', 'Ogun Youth Camp') }} — SDA Ogun Conference</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#1B3A6B',gold:'#C9A94D',steel:'#2E75B6'}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

{{-- Nav --}}
<nav class="bg-navy shadow-md" x-data="{ open: false }">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gold rounded-full flex items-center justify-center text-navy font-bold text-sm">OC</div>
            <div class="text-white">
                <div class="font-bold text-sm">Ogun Conference</div>
                <div class="text-gold text-xs">Youth Camp {{ now()->year }}</div>
            </div>
        </a>
        <div class="hidden md:flex items-center gap-6 text-sm text-white/80">
            <a href="#about" class="hover:text-gold transition">About</a>
            <a href="#programme" class="hover:text-gold transition">Programme</a>
            <a href="#contact" class="hover:text-gold transition">Contact</a>
            <a href="{{ route('registration.index') }}" class="bg-gold text-navy font-semibold px-4 py-1.5 rounded-full hover:bg-yellow-400 transition">Register</a>
        </div>
        <button @click="open = !open" class="md:hidden text-white">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>
    <div x-show="open" class="md:hidden bg-navy border-t border-white/10 px-4 pb-4 space-y-2 text-white text-sm">
        <a href="#about" class="block py-2 hover:text-gold">About</a>
        <a href="#programme" class="block py-2 hover:text-gold">Programme</a>
        <a href="#contact" class="block py-2 hover:text-gold">Contact</a>
        <a href="{{ route('registration.index') }}" class="block bg-gold text-navy font-semibold text-center py-2 rounded-full">Register</a>
    </div>
</nav>

{{-- Hero --}}
<section class="bg-navy text-white py-20 px-4 text-center">
    <div class="max-w-3xl mx-auto space-y-6">
        <div class="inline-block bg-gold/20 border border-gold/40 text-gold text-sm font-semibold px-4 py-1 rounded-full">
            {{ setting('camp_dates', 'Date TBA') }}
        </div>
        <h1 class="text-4xl md:text-5xl font-extrabold">{{ setting('camp_name', 'Ogun Conference Youth Camp') }}</h1>
        @if(setting('camp_theme'))
            <p class="text-gold text-xl italic">"{{ setting('camp_theme') }}"</p>
        @endif
        <p class="text-white/70 text-lg">{{ setting('camp_venue', 'Venue TBA') }}</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center pt-4">
            <a href="{{ route('registration.index') }}" class="bg-gold text-navy font-bold px-8 py-3 rounded-full text-lg hover:bg-yellow-400 transition">Register Now</a>
            <a href="#about" class="border border-white/40 text-white px-8 py-3 rounded-full text-lg hover:bg-white/10 transition">Learn More</a>
        </div>
    </div>
</section>

{{-- How to Register --}}
<section id="how-to-register" class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-navy text-center mb-10">How to Register</h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-center">
            @foreach([
                ['1','🏦','Pay Registration Fee','Transfer the fee to our bank account or pay online.'],
                ['2','📲','Send Proof','Send your receipt to ' . setting('whatsapp_number','our WhatsApp') . '.'],
                ['3','🔑','Get Your Code','A unique code is sent via SMS once payment is confirmed.'],
                ['4','✅','Complete Form','Enter your code and fill in the registration form.'],
            ] as [$step,$icon,$title,$desc])
                <div class="space-y-3">
                    <div class="w-12 h-12 bg-navy text-white rounded-full flex items-center justify-center font-bold mx-auto">{{ $step }}</div>
                    <div class="text-2xl">{{ $icon }}</div>
                    <h3 class="font-bold text-navy">{{ $title }}</h3>
                    <p class="text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>

        @if(setting('bank_account_number'))
            <div class="mt-10 bg-blue-50 border border-blue-200 rounded-xl p-6 text-center">
                <h3 class="font-bold text-navy mb-3">Bank Transfer Details</h3>
                <p class="text-sm"><span class="text-gray-500">Bank:</span> <strong>{{ setting('bank_name') }}</strong></p>
                <p class="text-sm"><span class="text-gray-500">Account Number:</span> <strong class="font-mono text-lg">{{ setting('bank_account_number') }}</strong></p>
                <p class="text-sm"><span class="text-gray-500">Account Name:</span> <strong>{{ setting('bank_account_name') }}</strong></p>
            </div>
        @endif

        <div class="mt-10 bg-navy rounded-2xl p-8 text-white text-center">
            <h3 class="text-xl font-bold mb-2">Already have a code?</h3>
            <p class="text-white/70 text-sm mb-6">Enter your registration code to begin filling your form.</p>
            <form action="{{ route('registration.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                <input type="text" name="code" placeholder="OGN-2026-XXXXXX"
                       class="flex-1 px-4 py-3 rounded-xl text-gray-800 font-mono text-sm text-center uppercase focus:outline-none focus:ring-2 focus:ring-gold" maxlength="14" />
                <button type="submit" class="bg-gold text-navy font-bold px-6 py-3 rounded-xl hover:bg-yellow-400 transition whitespace-nowrap">Continue →</button>
            </form>
        </div>
    </div>
</section>

{{-- Fees --}}
<section class="py-16 px-4 bg-gray-50">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-navy mb-8">Registration Fees</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach([['Adventurers','Ages 6-9','fee_adventurer','blue'],['Pathfinders','Ages 10-15','fee_pathfinder','green'],['Senior Youth','Ages 16+','fee_senior_youth','yellow']] as [$cat,$age,$key,$color])
                <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-{{ $color }}-500">
                    <h3 class="font-bold text-gray-800">{{ $cat }}</h3>
                    <p class="text-gray-500 text-sm mb-4">{{ $age }}</p>
                    <p class="text-3xl font-extrabold text-navy">&#8358;{{ number_format((int) setting($key, 5000)) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- About --}}
<section id="about" class="py-16 px-4 bg-white">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-navy mb-6">About the Camp</h2>
        <p class="text-gray-600 text-lg max-w-2xl mx-auto">
            The Ogun Conference Annual Youth Congress brings together Adventurers, Pathfinders, and Senior Youth
            from churches across the conference for a week of spiritual growth, fellowship, and ministry training.
        </p>
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([['Venue',setting('camp_venue','TBA')],['Dates',setting('camp_dates','TBA')],['Theme',setting('camp_theme','TBA')],['Category','Ages 6 and above']] as [$label,$value])
                <div class="bg-gray-50 rounded-xl p-4">
                    <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">{{ $label }}</p>
                    <p class="font-semibold text-navy text-sm">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Programme --}}
<section id="programme" class="py-16 px-4 bg-gray-50">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-navy mb-4">Camp Programme</h2>
        <p class="text-gray-500 mb-8">The detailed programme will be published closer to the camp date.</p>
        <div class="bg-white rounded-xl shadow-sm p-8 text-gray-400 italic">Programme details coming soon.</div>
    </div>
</section>

{{-- Rules --}}
<section class="py-16 px-4 bg-white">
    <div class="max-w-3xl mx-auto">
        <h2 class="text-2xl font-bold text-navy text-center mb-8">Camp Rules</h2>
        <div class="space-y-3 text-sm text-gray-700">
            @foreach(['All campers must carry their printed ID card at all times.','Campers under 18 must submit a signed parental consent form at check-in.','Participants must wear the official camp uniform during formal sessions.','Mobile phones should be kept on silent during services and meetings.','No camper may leave the venue without prior permission from officials.','All campers are expected to participate in the programme respectfully.'] as $rule)
                <div class="flex gap-3 bg-gray-50 rounded-lg p-3">
                    <span class="text-navy font-bold">&#10003;</span>
                    <span>{{ $rule }}</span>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- Contact --}}
<section id="contact" class="py-16 px-4 bg-gray-50">
    <div class="max-w-2xl mx-auto text-center">
        <h2 class="text-2xl font-bold text-navy mb-8">Contact Us</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            @if(setting('whatsapp_number'))
                <a href="https://wa.me/{{ preg_replace('/\D/', '', setting('whatsapp_number')) }}" target="_blank"
                   class="flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 hover:bg-green-100 transition">
                    <span class="text-2xl">&#x1F4AC;</span>
                    <div class="text-left"><div class="font-bold text-gray-800">WhatsApp</div><div class="text-gray-500">{{ setting('whatsapp_number') }}</div></div>
                </a>
            @endif
            @if(setting('secretariat_phone'))
                <a href="tel:{{ setting('secretariat_phone') }}"
                   class="flex items-center gap-3 bg-blue-50 border border-blue-200 rounded-xl p-4 hover:bg-blue-100 transition">
                    <span class="text-2xl">&#x1F4DE;</span>
                    <div class="text-left"><div class="font-bold text-gray-800">Secretariat</div><div class="text-gray-500">{{ setting('secretariat_phone') }}</div></div>
                </a>
            @endif
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-navy text-white/70 text-sm py-8">
    <div class="max-w-6xl mx-auto px-4 text-center space-y-2">
        <p class="font-semibold text-white">Seventh-day Adventist Church — Ogun Conference</p>
        <p>Youth Department · Annual Youth Congress {{ now()->year }}</p>
    </div>
</footer>

</body>
</html>
