<x-filament-panels::page>

    <div class="space-y-6">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-navy to-blue-600 rounded-2xl p-6 text-white"
             style="background:linear-gradient(135deg,#0B2D6B,#1E5FAD)">
            <h1 class="text-xl font-bold">Accountant Dashboard</h1>
            <p class="text-blue-200 text-sm mt-1">Payment confirmation and revenue overview &mdash; {{ now()->format('l, d F Y') }}</p>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['Pending Offline',  $pendingOffline,         'heroicon-o-clock',             'bg-yellow-50 border-yellow-200 text-yellow-700'],
                ['Confirmed Offline',$confirmedOffline,        'heroicon-o-check-circle',       'bg-green-50 border-green-200 text-green-700'],
                ['Pending Batches',  $pendingBatches,          'heroicon-o-rectangle-stack',    'bg-orange-50 border-orange-200 text-orange-700'],
                ['Total Revenue',    '₦'.number_format($totalRevenue), 'heroicon-o-banknotes', 'bg-blue-50 border-blue-200 text-blue-700'],
            ] as [$label,$value,$icon,$cls])
                <div class="rounded-xl border p-4 {{ $cls }}">
                    <x-filament::icon :icon="$icon" class="w-6 h-6 mb-2 opacity-70"/>
                    <p class="text-2xl font-bold">{{ $value }}</p>
                    <p class="text-xs font-medium mt-0.5">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Pending Offline Payments --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Pending Offline Payments</h2>
                    <a href="{{ \App\Filament\Resources\OfflinePaymentResource::getUrl('index') }}"
                       class="text-xs text-blue-600 hover:underline font-semibold">View All &rarr;</a>
                </div>
                @forelse($recentPendingPayments as $payment)
                    <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $payment->submitted_name }}</p>
                            <p class="text-xs text-gray-500">{{ $payment->submitted_phone }} &bull; ₦{{ number_format($payment->amount) }}</p>
                        </div>
                        <a href="{{ \App\Filament\Resources\OfflinePaymentResource::getUrl('edit', ['record' => $payment]) }}"
                           class="text-xs bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full hover:bg-green-200">
                            Confirm
                        </a>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm italic">No pending payments.</div>
                @endforelse
            </div>

            {{-- Pending Bulk Batches --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Pending Bulk Batches</h2>
                    <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('index') }}"
                       class="text-xs text-blue-600 hover:underline font-semibold">View All &rarr;</a>
                </div>
                @forelse($recentPendingBatches as $batch)
                    <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $batch->church?->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $batch->entries()->count() }} campers &bull;
                                ₦{{ number_format($batch->expected_total) }} expected &bull;
                                by {{ $batch->createdBy?->name }}
                            </p>
                        </div>
                        <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('edit', ['record' => $batch]) }}"
                           class="text-xs bg-green-100 text-green-700 font-bold px-3 py-1 rounded-full hover:bg-green-200">
                            Review
                        </a>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 text-sm italic">No pending batches.</div>
                @endforelse
            </div>

        </div>

    </div>
</x-filament-panels::page>
