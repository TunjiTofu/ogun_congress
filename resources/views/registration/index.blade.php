@extends('layouts.public')

@section('title', 'Register — ' . setting('camp_name', 'Ogun Youth Camp'))

@section('content')
    <div class="min-h-screen bg-gray-50 py-12 px-4">
        <div class="max-w-md mx-auto">

            {{-- Header --}}
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-gold text-2xl">🔑</span>
                </div>
                <h1 class="text-2xl font-bold text-navy">Enter Your Registration Code</h1>
                <p class="text-gray-500 text-sm mt-2">
                    You should have received this code via SMS after your payment was confirmed.
                </p>
            </div>

            {{-- Error from session --}}
            @if(session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Code entry form --}}
            <div class="bg-white rounded-2xl shadow-sm p-8">
                <form method="POST" action="{{ route('registration.validate-code-web') }}" x-data="codeForm()">
                    @csrf

                    <div class="space-y-2">
                        <label for="code" class="block text-sm font-medium text-gray-700">
                            Registration Code
                        </label>
                        <input
                            type="text"
                            id="code"
                            name="code"
                            value="{{ old('code', request('code')) }}"
                            placeholder="OGN-2026-XXXXXX"
                            maxlength="14"
                            x-model="code"
                            @input="code = code.toUpperCase()"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl font-mono text-center
                               text-lg tracking-widest uppercase focus:outline-none focus:ring-2
                               focus:ring-navy focus:border-transparent
                               @error('code') border-red-400 bg-red-50 @enderror"
                            autocomplete="off"
                            spellcheck="false"
                        />
                        @error('code')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit"
                            :disabled="code.length < 6"
                            class="w-full mt-6 bg-navy text-white font-bold py-3 rounded-xl
                               hover:bg-steel transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Continue to Registration →
                    </button>
                </form>

                <div class="mt-6 pt-6 border-t border-gray-100 text-center text-sm text-gray-500">
                    <p>Don't have a code yet?</p>
                    <a href="{{ route('home') }}#how-to-register" class="text-navy font-semibold hover:underline">
                        See how to get one →
                    </a>
                </div>
            </div>

            {{-- Online payment option --}}
            @if(setting('paystack_enabled', '1') === '1')
                <div class="mt-6 bg-white rounded-2xl shadow-sm p-6 text-center">
                    <p class="text-sm text-gray-500 mb-4">Or pay online and get your code instantly</p>
                    <a href="{{ route('registration.pay-online') }}"
                       class="inline-block bg-green-600 text-white font-bold px-6 py-3 rounded-xl
                      hover:bg-green-700 transition text-sm">
                        💳 Pay Online via Paystack
                    </a>
                </div>
            @endif

            <p class="text-center text-xs text-gray-400 mt-6">
                Having trouble? Contact the secretariat on
                <a href="tel:{{ setting('secretariat_phone') }}" class="text-navy hover:underline">
                    {{ setting('secretariat_phone', 'TBA') }}
                </a>
            </p>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function codeForm() {
            return {
                code: '{{ old('code', request('code')) }}'
            }
        }
    </script>
@endpush
