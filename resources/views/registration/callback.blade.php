<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Confirming Payment — {{ setting('camp_name', 'Ogun Youth Camp') }}</title>
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

<div class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="max-w-md w-full text-center" x-data="callbackPage()" x-init="poll()">

        {{-- Checking --}}
        <div x-show="status === 'checking'">
            <div class="w-20 h-20 border-4 border-navy border-t-transparent rounded-full animate-spin mx-auto mb-6"></div>
            <h1 class="text-2xl font-bold text-navy mb-2">Confirming Your Payment</h1>
            <p class="text-gray-500 text-sm">Please wait while we verify your payment with Paystack&hellip;</p>
            <p class="text-xs text-gray-400 mt-4">This usually takes less than 30 seconds.</p>
        </div>

        {{-- Success --}}
        <div x-show="status === 'active'" class="space-y-4">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto">
                <span class="text-4xl">&#10003;</span>
            </div>
            <h1 class="text-2xl font-bold text-green-700">Payment Confirmed!</h1>
            <p class="text-gray-600 text-sm">Your registration code has been sent to your phone via SMS.</p>
            <div class="bg-gray-100 rounded-xl p-4 font-mono text-2xl font-bold text-navy tracking-widest" x-text="code"></div>
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-800">
                &#9888; <strong>Save this code.</strong> You will need it to complete your registration.
            </div>
            <a :href="'/registration/form/' + code"
               class="block w-full bg-navy text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition">
                Complete Registration &rarr;
            </a>
        </div>

        {{-- Still pending --}}
        <div x-show="status === 'pending'" class="space-y-4">
            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto">
                <span class="text-4xl">&#8987;</span>
            </div>
            <h1 class="text-2xl font-bold text-yellow-700">Payment Being Processed</h1>
            <p class="text-gray-600 text-sm">
                Your payment is still being confirmed. <strong>Check your phone for an SMS with your registration code.</strong>
            </p>
            <div class="bg-gray-100 rounded-xl p-4 font-mono text-xl font-bold text-navy tracking-widest" x-text="code"></div>
            <div class="flex gap-3">
                <button @click="poll()" class="flex-1 border border-navy text-navy font-bold py-3 rounded-xl hover:bg-navy/5 transition text-sm">Check Again</button>
                <a href="{{ route('registration.index') }}" class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition text-sm text-center">Enter Code Manually</a>
            </div>
        </div>

        {{-- Error --}}
        <div x-show="status === 'error'" class="space-y-4">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto">
                <span class="text-4xl">&#10007;</span>
            </div>
            <h1 class="text-2xl font-bold text-red-700">Something Went Wrong</h1>
            <p class="text-gray-600 text-sm" x-text="errorMsg"></p>
            <a href="{{ route('registration.index') }}" class="block w-full bg-navy text-white font-bold py-3 rounded-xl hover:bg-blue-800 transition">Try Again</a>
        </div>

    </div>
</div>

<script>
    function callbackPage() {
        const params    = new URLSearchParams(window.location.search);
        const code      = params.get('reference') || '';
        const maxPolls  = 10;

        return {
            status:   'checking',
            code:     code,
            errorMsg: '',
            polls:    0,

            async poll() {
                if (!code) { this.status = 'error'; this.errorMsg = 'No payment reference found.'; return; }
                this.status = 'checking';
                this.polls  = 0;
                this.doPoll();
            },

            async doPoll() {
                if (this.polls >= maxPolls) { this.status = 'pending'; return; }
                this.polls++;
                try {
                    const res  = await fetch('/api/v1/payment/status/' + code);
                    const data = await res.json();
                    if (data.is_active) { this.status = 'active'; return; }
                    setTimeout(() => this.doPoll(), 3000);
                } catch {
                    this.status  = 'error';
                    this.errorMsg = 'Could not reach the server. Please check your connection.';
                }
            }
        }
    }
</script>
</body>
</html>
