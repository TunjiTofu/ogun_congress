<x-filament-panels::page>
    @include('partials.dashboard-vars')

    @php
        $regIsClosed = setting('registration_open', '1') !== '1'
            || (setting('registration_closes_at') && now()->gt(\Illuminate\Support\Carbon::parse(setting('registration_closes_at'))));
    @endphp
    @if($regIsClosed)
        <div style="background:var(--d-closed-bg);border:1.5px solid var(--d-closed-bc);border-radius:12px;padding:0.75rem 1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.65rem">
            <span>🔴</span>
            <p style="font-size:0.82rem;font-weight:600;color:var(--d-closed-tc)">Registration is currently <strong>CLOSED</strong>. No new registrations can be submitted.</p>
        </div>
    @endif

    {{-- Header --}}
    <div style="background:linear-gradient(135deg,#020617,#0F172A,#1E293B);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;border:1px solid #1E293B">
        <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#FBBF24;margin-bottom:0.2rem">Accountant Dashboard — Payment Management</p>
        <h1 style="font-size:1.4rem;font-weight:900;color:#F1F5F9;line-height:1.2">Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, {{ auth()->user()->name }}</h1>
        <p style="font-size:0.75rem;color:rgba(255,255,255,0.55);margin-top:0.2rem">{{ now()->format('l, d F Y') }} · Ogun Conference Youth Congress 2026</p>
    </div>

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.85rem;margin-bottom:1.25rem">
        <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">⏳ Pending Offline</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ $pendingOffline }}</p>
        </div>
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">✅ Confirmed Offline</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ $confirmedOffline }}</p>
        </div>
        <div style="background:var(--d-stat-4-bg);border:1px solid var(--d-stat-4-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-4-tc);margin-bottom:0.35rem">📦 Pending Batches</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-4-vc);line-height:1">{{ $pendingBatches }}</p>
        </div>
        <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">💰 Total Revenue</p>
            <p style="font-size:1.4rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">₦{{ number_format($totalRevenue) }}</p>
        </div>
    </div>

    {{-- Revenue breakdown --}}
    <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.25rem">
        <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.85rem">
            <span style="width:8px;height:8px;border-radius:50%;background:#FBBF24;display:inline-block"></span>
            <span style="font-size:0.85rem;font-weight:700;color:var(--d-text)">Revenue Breakdown</span>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
            <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:10px;padding:0.85rem 1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-2-tc);margin-bottom:0.25rem">Offline Payments</p>
                <p style="font-size:1.3rem;font-weight:800;color:var(--d-stat-2-vc)">₦{{ number_format($totalOfflineCollected) }}</p>
                <p style="font-size:0.68rem;color:#4ADE80;margin-top:2px">{{ $confirmedOffline }} confirmed payments</p>
            </div>
            <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:10px;padding:0.85rem 1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-1-tc);margin-bottom:0.25rem">Batch Registrations</p>
                <p style="font-size:1.3rem;font-weight:800;color:var(--d-stat-1-vc)">₦{{ number_format($totalBatchCollected) }}</p>
                <p style="font-size:0.68rem;color:#818CF8;margin-top:2px">{{ $confirmedBatches }} confirmed batches</p>
            </div>
        </div>
    </div>

    {{-- Pending offline payments --}}
    <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;margin-bottom:1.25rem">
        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <div style="display:flex;align-items:center;gap:0.6rem">
                <span style="width:8px;height:8px;border-radius:50%;background:#FBBF24;display:inline-block"></span>
                <span style="font-size:0.85rem;font-weight:700;color:var(--d-text)">Pending Offline Payments</span>
            </div>
            <a href="{{ url('/admin/offline-payments?tableFilters[status][value]=pending') }}" style="font-size:0.68rem;color:#818CF8;font-weight:600;text-decoration:none">View all →</a>
        </div>
        <div style="overflow-y:auto;max-height:300px">
            @forelse($recentPendingPayments as $payment)
                <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;gap:1rem">
                    <div>
                        <p style="font-size:0.82rem;font-weight:700;color:var(--d-text)">{{ $payment->payer_name ?? $payment->registrationCode?->prefill_name ?? '—' }}</p>
                        <p style="font-size:0.68rem;color:var(--d-text-3);margin-top:1px">{{ $payment->phone ?? '—' }} @if($payment->created_at) · {{ $payment->created_at->format('d M Y') }} @endif</p>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:0.82rem;font-weight:700;color:#FBBF24">₦{{ number_format($payment->amount) }}</p>
                        <a href="{{ url('/admin/offline-payments/' . $payment->id . '/edit') }}"
                           style="font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:100px;background:#1E1B4B;color:var(--d-stat-1-tc);text-decoration:none;display:inline-block;margin-top:3px">Review</a>
                    </div>
                </div>
            @empty
                <div style="padding:2rem;text-align:center;color:var(--d-text-3);font-size:0.82rem;font-style:italic">✓ No pending offline payments.</div>
            @endforelse
        </div>
    </div>

    {{-- Pending batch registrations --}}
    <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <div style="display:flex;align-items:center;gap:0.6rem">
                <span style="width:8px;height:8px;border-radius:50%;background:#EF4444;display:inline-block"></span>
                <span style="font-size:0.85rem;font-weight:700;color:var(--d-text)">Pending Bulk Batches</span>
            </div>
            <a href="{{ url('/admin/bulk-registration-batches?tableFilters[status][value]=pending_payment') }}" style="font-size:0.68rem;color:#818CF8;font-weight:600;text-decoration:none">View all →</a>
        </div>
        <div style="overflow-y:auto;max-height:300px">
            @forelse($recentPendingBatches as $batch)
                <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;gap:1rem">
                    <div>
                        <p style="font-size:0.82rem;font-weight:700;color:var(--d-text)">{{ $batch->church?->name ?? '—' }}</p>
                        <p style="font-size:0.68rem;color:var(--d-text-3);margin-top:1px">{{ $batch->entries()->count() }} camper(s) · ₦{{ number_format($batch->expected_total) }} · {{ $batch->createdBy?->name ?? '—' }}</p>
                    </div>
                    <a href="{{ url('/admin/bulk-registration-batches/' . $batch->id . '/edit') }}"
                       style="font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:100px;background:#1E1B4B;color:var(--d-stat-1-tc);text-decoration:none;flex-shrink:0">Review</a>
                </div>
            @empty
                <div style="padding:2rem;text-align:center;color:var(--d-text-3);font-size:0.82rem;font-style:italic">✓ No pending batch registrations.</div>
            @endforelse
        </div>
    </div>

</x-filament-panels::page>
