<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Ogun Youth Camp') — SDA Ogun Conference</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png" />

    <!-- Tailwind CDN (dev convenience — swap for compiled asset in production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy:  '#1B3A6B',
                        gold:  '#C9A94D',
                        steel: '#2E75B6',
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js for lightweight interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('head')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

<!-- Navigation -->
<nav class="bg-navy shadow-md">
    <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="w-10 h-10 bg-gold rounded-full flex items-center justify-center text-navy font-bold text-sm">OC</div>
            <div class="text-white">
                <div class="font-bold text-sm leading-tight">Ogun Conference</div>
                <div class="text-gold text-xs leading-tight">Youth Camp {{ now()->year }}</div>
            </div>
        </a>

        <div class="hidden md:flex items-center gap-6 text-sm text-white/80">
            <a href="{{ route('home') }}#about"     class="hover:text-gold transition">About</a>
            <a href="{{ route('home') }}#programme" class="hover:text-gold transition">Programme</a>
            <a href="{{ route('home') }}#contact"   class="hover:text-gold transition">Contact</a>
            <a href="{{ route('registration.index') }}"
               class="bg-gold text-navy font-semibold px-4 py-1.5 rounded-full hover:bg-yellow-400 transition">
                Register
            </a>
        </div>

        <!-- Mobile menu toggle -->
        <button class="md:hidden text-white" x-data @click="$dispatch('toggle-menu')">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Mobile menu -->
    <div x-data="{ open: false }" @toggle-menu.window="open = !open" x-show="open"
         class="md:hidden bg-navy border-t border-white/10 px-4 pb-4 space-y-2 text-white text-sm">
        <a href="{{ route('home') }}#about"     class="block py-2 hover:text-gold">About</a>
        <a href="{{ route('home') }}#programme" class="block py-2 hover:text-gold">Programme</a>
        <a href="{{ route('home') }}#contact"   class="block py-2 hover:text-gold">Contact</a>
        <a href="{{ route('registration.index') }}"
           class="block bg-gold text-navy font-semibold text-center py-2 rounded-full">Register</a>
    </div>
</nav>

<!-- Page content -->
@yield('content')

<!-- Footer -->
<footer class="bg-navy text-white/70 text-sm py-8 mt-16">
    <div class="max-w-6xl mx-auto px-4 text-center space-y-2">
        <p class="font-semibold text-white">Seventh-day Adventist Church — Ogun Conference</p>
        <p>Youth Department · Annual Youth Congress {{ now()->year }}</p>
        <p class="text-xs text-white/40 mt-4">
            For registration issues, contact the secretariat:
            <a href="tel:{{ setting('secretariat_phone') }}" class="text-gold hover:underline">
                {{ setting('secretariat_phone', 'TBA') }}
            </a>
        </p>
    </div>
</footer>

@stack('scripts')
</body>
</html>
