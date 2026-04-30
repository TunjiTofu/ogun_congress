<x-filament-panels::page>
    <div class="space-y-5">

        {{-- Header card --}}
        <div class="rounded-2xl p-5 text-white relative overflow-hidden"
             style="background:linear-gradient(135deg,#0B2D6B 0%,#1E5FAD 60%,#2E75B6 100%);">
            <div style="position:absolute;top:-20px;right:-20px;width:120px;height:120px;
                    border-radius:50%;background:rgba(255,255,255,0.05);"></div>
            <div style="position:absolute;top:10px;right:40px;width:60px;height:60px;
                    border-radius:50%;background:rgba(255,255,255,0.07);"></div>
            <p style="font-size:0.7rem;letter-spacing:0.15em;color:rgba(168,200,240,0.8);
                  text-transform:uppercase;margin-bottom:0.3rem;">Accountant Dashboard</p>
            <h1 style="font-size:1.4rem;font-weight:800;margin-bottom:0.2rem;">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                {{ auth()->user()->name }}
            </h1>
            <p style="font-size:0.82rem;color:rgba(255,255,255,0.6);">
                {{ now()->format('l, d F Y') }} &bull; Payment Management Centre
            </p>
        </div>

        {{-- Stat cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach([
                ['Pending Offline',   $pendingOffline,          '#F59E0B','#FEF3C7','#92400E', 'heroicon-o-clock'],
                ['Confirmed Offline', $confirmedOffline,         '#10B981','#D1FAE5','#065F46', 'heroicon-o-check-badge'],
                ['Pending Batches',   $pendingBatches,           '#EF4444','#FEE2E2','#991B1B', 'heroicon-o-rectangle-stack'],
                ['Total Revenue',     '&#8358;'.number_format($totalRevenue), '#6366F1','#EEF2FF','#3730A3','heroicon-o-banknotes'],
            ] as [$label,$value,$accent,$bg,$text,$icon])
                <div class="rounded-2xl p-4 border"
                     style="background:{{ $bg }};border-color:{{ $accent }}33;">
                    <div class="flex items-center justify-between mb-3">
                <span style="font-size:0.7rem;font-weight:700;color:{{ $text }};
                             text-transform:uppercase;letter-spacing:0.08em;">{{ $label }}</span>
                        <x-filament::icon :icon="$icon" style="width:1.1rem;height:1.1rem;color:{{ $accent }};opacity:0.7;"/>
                    </div>
                    <p style="font-size:1.6rem;font-weight:900;color:{{ $text }};line-height:1;">{!! $value !!}</p>
                </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

            {{-- Pending Offline Payments --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between"
                     style="background:#FAFAFA;">
                    <div class="flex items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:#F59E0B;display:inline-block;"></span>
                        <h3 style="font-size:0.85rem;font-weight:700;color:#111827;">Pending Offline Payments</h3>
                    </div>
                    <a href="{{ \App\Filament\Resources\OfflinePaymentResource::getUrl('index') }}"
                       style="font-size:0.72rem;color:#6366F1;font-weight:600;text-decoration:none;">View all &rarr;</a>
                </div>
                @forelse($recentPendingPayments as $pmt)
                    <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between last:border-0">
                        <div>
                            <p style="font-size:0.82rem;font-weight:600;color:#1F2937;">{{ $pmt->submitted_name }}</p>
                            <p style="font-size:0.72rem;color:#9CA3AF;">
                                {{ $pmt->submitted_phone }} &bull; &#8358;{{ number_format($pmt->amount) }}
                                &bull; {{ $pmt->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <a href="{{ \App\Filament\Resources\OfflinePaymentResource::getUrl('edit',['record'=>$pmt]) }}"
                           style="font-size:0.72rem;background:#D1FAE5;color:#065F46;font-weight:700;
                          padding:0.3rem 0.8rem;border-radius:100px;text-decoration:none;">
                            Confirm
                        </a>
                    </div>
                @empty
                    <div style="padding:2.5rem;text-align:center;color:#9CA3AF;font-size:0.82rem;font-style:italic;">
                        &#10003; No pending offline payments.
                    </div>
                @endforelse
            </div>

            {{-- Pending Bulk Batches --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between"
                     style="background:#FAFAFA;">
                    <div class="flex items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:#EF4444;display:inline-block;"></span>
                        <h3 style="font-size:0.85rem;font-weight:700;color:#111827;">Pending Bulk Batches</h3>
                    </div>
                    <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('index') }}"
                       style="font-size:0.72rem;color:#6366F1;font-weight:600;text-decoration:none;">View all &rarr;</a>
                </div>
                @forelse($recentPendingBatches as $batch)
                    <div class="px-5 py-3 border-b border-gray-50 flex items-center justify-between last:border-0">
                        <div>
                            <p style="font-size:0.82rem;font-weight:600;color:#1F2937;">{{ $batch->church?->name }}</p>
                            <p style="font-size:0.72rem;color:#9CA3AF;">
                                {{ $batch->entries()->count() }} campers &bull;
                                &#8358;{{ number_format($batch->expected_total) }} expected &bull;
                                {{ $batch->createdBy?->name }}
                            </p>
                        </div>
                        <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('edit',['record'=>$batch]) }}"
                           style="font-size:0.72rem;background:#EEF2FF;color:#3730A3;font-weight:700;
                          padding:0.3rem 0.8rem;border-radius:100px;text-decoration:none;">
                            Review
                        </a>
                    </div>
                @empty
                    <div style="padding:2.5rem;text-align:center;color:#9CA3AF;font-size:0.82rem;font-style:italic;">
                        &#10003; No pending batches.
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-filament-panels::page>
