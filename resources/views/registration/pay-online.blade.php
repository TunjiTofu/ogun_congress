<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{ csrf_token() }" />
    <title>Pay Online — { setting('camp_name', 'Ogun Youth Camp') }</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#1B3A6B',gold:'#C9A94D',steel:'#2E75B6'}}}}</script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body class="bg-gray-50">
<nav class="bg-navy py-3 px-4">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="{ route('home') }" class="text-white font-bold text-sm">&#8592; Back to Camp Home</a>
        <span class="text-gold text-sm font-semibold">{ setting('camp_name', 'Ogun Youth Camp') }</span>
    </div>
</nav>
<div class="min-h-screen bg-gray-50 py-12 px-4">
    <div class="max-w-md mx-auto">

        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">💳</span>
            </div>
            <h1 class="text-2xl font-bold text-navy">Pay Online</h1>
            <p class="text-gray-500 text-sm mt-2">
                Pay securely via Paystack. Your registration code will be sent to you via SMS immediately after payment.
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-8" x-data="payForm()">

            @if($errors->any())
                <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('payment.initiate-web') }}" @submit="submitting = true">
                @csrf

                {{-- Name --}}
                <div class="space-y-1 mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           placeholder="As it will appear on your ID card"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-navy
                                  @error('name') border-red-400 @enderror" />
                    @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Phone --}}
                <div class="space-y-1 mb-4">
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           placeholder="08012345678"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                  focus:outline-none focus:ring-2 focus:ring-navy
                                  @error('phone') border-red-400 @enderror" />
                    <p class="text-xs text-gray-400">Your registration code will be sent to this number.</p>
                    @error('phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Category --}}
                <div class="space-y-1 mb-6">
                    <label for="category" class="block text-sm font-medium text-gray-700">Category</label>
                    <select id="category" name="category" required x-model="category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy
                                   @error('category') border-red-400 @enderror">
                        <option value="">— Select your category —</option>
                        <option value="adventurer"   {{ old('category') === 'adventurer'   ? 'selected' : '' }}>Adventurer (Ages 6–9) — ₦{{ number_format((int) setting('fee_adventurer', 5000)) }}</option>
                        <option value="pathfinder"   {{ old('category') === 'pathfinder'   ? 'selected' : '' }}>Pathfinder (Ages 10–15) — ₦{{ number_format((int) setting('fee_pathfinder', 5000)) }}</option>
                        <option value="senior_youth" {{ old('category') === 'senior_youth' ? 'selected' : '' }}>Senior Youth (Ages 16+) — ₦{{ number_format((int) setting('fee_senior_youth', 7000)) }}</option>
                    </select>
                    @error('category')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Amount display --}}
                <div class="bg-navy/5 rounded-xl p-4 mb-6 text-center" x-show="category">
                    <p class="text-sm text-gray-500">Amount to pay</p>
                    <p class="text-2xl font-extrabold text-navy">
                        ₦<span x-text="amount()"></span>
                    </p>
                </div>

                <button type="submit"
                        :disabled="submitting || !category"
                        class="w-full bg-green-600 text-white font-bold py-3 rounded-xl
                               hover:bg-green-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!submitting">Proceed to Payment →</span>
                    <span x-show="submitting">Redirecting to Paystack…</span>
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Payments are processed securely by <strong>Paystack</strong>.
            Your card details are never stored on our servers.
        </p>
    </div>
</div>

<script>
    <script>
        function payForm() {
        const fees = {
        adventurer:   {{ (int) setting('fee_adventurer', 5000) }},
        pathfinder:   {{ (int) setting('fee_pathfinder', 5000) }},
        senior_youth: {{ (int) setting('fee_senior_youth', 7000) }},
    };
        return {
        category:   '{{ old('category') }}',
        submitting: false,
        amount() {
        return this.category ? fees[this.category]?.toLocaleString() ?? '—' : '—';
    }
    }
    }
</script>
</script>
</body>
</html>
