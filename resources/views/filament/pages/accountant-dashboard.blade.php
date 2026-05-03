<x-filament-panels::page>
    <div style="font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;color:#111827">

        {{-- ── Hero ─────────────────────────────────────────────────────── --}}
        <div style="border-radius:20px;background:linear-gradient(135deg,#0B2455 0%,#1B3A8F 60%,#2E5FAD 100%);
            padding:1.75rem 2rem;position:relative;overflow:hidden;margin-bottom:1.5rem">
            <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;
                background:rgba(255,255,255,0.04);pointer-events:none"></div>
            <p style="font-size:0.62rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;
              color:rgba(232,194,85,0.85);margin-bottom:0.4rem">Accountant Dashboard — Payment Management Centre</p>
            <h1 style="font-size:1.5rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:0.3rem">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                {{ auth()->user()->name }}
            </h1>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.55)">
                {{ now()->format('l, d F Y') }} &bull; Ogun Conference Youth Congress 2026
            </p>
        </div>

        {{-- ── Stats ────────────────────────────────────────────────────── --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
            <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:16px;padding:1.1rem 1.2rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#92400E;margin-bottom:0.4rem">Pending Offline</p>
                <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $pendingOffline }}</p>
            </div>
            <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:16px;padding:1.1rem 1.2rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#065F46;margin-bottom:0.4rem">Confirmed Offline</p>
                <p style="font-size:2rem;font-weight:900;color:#022C22;line-height:1">{{ $confirmedOffline }}</p>
            </div>
            <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:16px;padding:1.1rem 1.2rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#991B1B;margin-bottom:0.4rem">Pending Batches</p>
                <p style="font-size:2rem;font-weight:900;color:#7F1D1D;line-height:1">{{ $pendingBatches }}</p>
            </div>
            <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:16px;padding:1.1rem 1.2rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#3730A3;margin-bottom:0.4rem">Total Revenue</p>
                <p style="font-size:1.35rem;font-weight:900;color:#1E1B4B;line-height:1">&#8358;{{ number_format($totalRevenue) }}</p>
            </div>
        </div>

        {{-- ── Revenue breakdown ───────────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            padding:1.25rem 1.5rem;margin-bottom:1.5rem;
            box-shadow:0 2px 16px rgba(11,36,85,0.05)">
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem">
                <span style="width:8px;height:8px;border-radius:50%;background:#C9A94D;display:inline-block"></span>
                <span style="font-size:0.88rem;font-weight:700;color:#0B2455">Revenue Breakdown</span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                <div>
                    <p style="font-size:0.6rem;color:#94A3B8;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:0.2rem">Offline Payments Collected</p>
                    <p style="font-size:1.2rem;font-weight:800;color:#065F46">&#8358;{{ number_format($totalOfflineCollected) }}</p>
                    <p style="font-size:0.7rem;color:#94A3B8;margin-top:0.1rem">{{ $confirmedOffline }} confirmed payments</p>
                </div>
                <div>
                    <p style="font-size:0.6rem;color:#94A3B8;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:0.2rem">Batch Registrations Collected</p>
                    <p style="font-size:1.2rem;font-weight:800;color:#1E40AF">&#8358;{{ number_format($totalBatchCollected) }}</p>
                    <p style="font-size:0.7rem;color:#94A3B8;margin-top:0.1rem">{{ $confirmedBatches }} confirmed batches</p>
                </div>
            </div>
        </div>

        {{-- ── Pending offline payments ─────────────────────────────────── --}}
        <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            overflow:hidden;box-shadow:0 2px 16px rgba(11,36,85,0.05);margin-bottom:1.5rem">
            <div style="padding:1rem 1.5rem;border-bottom:1px solid #F1F5F9;background:#FAFBFF;
                display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:0.6rem">
                    <span style="width:8px;height:8px;border-radius:50%;background:#F59E0B;display:inline-block"></span>
                    <span style="font-size:0.88rem;font-weight:700;color:#0B2455">Pending Offline Payments</span>
                </div>
                <a href="{{ url('/admin/offline-payments?tableFilters[status][value]=pending') }}"
                   style="font-size:0.72rem;color:#6366F1;font-weight:600;text-decoration:none">View all &rarr;</a>
            </div>
            @forelse($recentPendingPayments as $payment)
                <div style="padding:0.9rem 1.5rem;border-bottom:1px solid #F8FAFF;display:flex;align-items:center;justify-content:space-between;gap:1rem">
                    <div>
                        <p style="font-size:0.84rem;font-weight:700;color:#111827">{{ $payment->payer_name ?? $payment->registrationCode?->prefill_name ?? '—' }}</p>
                        <p style="font-size:0.7rem;color:#94A3B8;margin-top:1px">
                            {{ $payment->phone ?? '—' }}
                            @if($payment->created_at) &bull; {{ $payment->created_at->format('d M Y') }} @endif
                        </p>
                    </div>
                    <div style="text-align:right;flex-shrink:0">
                        <p style="font-size:0.84rem;font-weight:700;color:#0B2455">&#8358;{{ number_format($payment->amount) }}</p>
                        <a href="{{ url('/admin/offline-payments/' . $payment->id . '/edit') }}"
                           style="font-size:0.68rem;font-weight:700;padding:0.25rem 0.65rem;border-radius:100px;
                      background:#0B2455;color:#fff;text-decoration:none;display:inline-block;margin-top:3px">
                            Review
                        </a>
                    </div>
                </div>
            @empty
                <div style="padding:2.5rem 1.5rem;text-align:center;color:#94A3B8;font-size:0.84rem;font-style:italic">
                    &#10003; No pending offline payments.
                </div>
            @endforelse
        </div>

        {{-- ── Pending batch registrations ─────────────────────────────── --}}
        <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            overflow:hidden;box-shadow:0 2px 16px rgba(11,36,85,0.05)">
            <div style="padding:1rem 1.5rem;border-bottom:1px solid #F1F5F9;background:#FAFBFF;
                display:flex;align-items:center;justify-content:space-between">
                <div style="display:flex;align-items:center;gap:0.6rem">
                    <span style="width:8px;height:8px;border-radius:50%;background:#EF4444;display:inline-block"></span>
                    <span style="font-size:0.88rem;font-weight:700;color:#0B2455">Pending Bulk Batches</span>
                </div>
                <a href="{{ url('/admin/bulk-registration-batches?tableFilters[status][value]=pending_payment') }}"
                   style="font-size:0.72rem;color:#6366F1;font-weight:600;text-decoration:none">View all &rarr;</a>
            </div>
            @forelse($recentPendingBatches as $batch)
                <div style="padding:0.9rem 1.5rem;border-bottom:1px solid #F8FAFF;display:flex;align-items:center;justify-content:space-between;gap:1rem">
                    <div>
                        <p style="font-size:0.84rem;font-weight:700;color:#111827">{{ $batch->church?->name ?? '—' }}</p>
                        <p style="font-size:0.7rem;color:#94A3B8;margin-top:1px">
                            {{ $batch->entries()->count() }} camper{{ $batch->entries()->count() !== 1 ? 's' : '' }}
                            &bull; &#8358;{{ number_format($batch->expected_total) }} expected
                            &bull; {{ $batch->createdBy?->name ?? '—' }}
                        </p>
                    </div>
                    <a href="{{ url('/admin/bulk-registration-batches/' . $batch->id . '/edit') }}"
                       style="font-size:0.68rem;font-weight:700;padding:0.3rem 0.75rem;border-radius:100px;
                  background:#0B2455;color:#fff;text-decoration:none;flex-shrink:0">
                        Review
                    </a>
                </div>
            @empty
                <div style="padding:2.5rem 1.5rem;text-align:center;color:#94A3B8;font-size:0.84rem;font-style:italic">
                    &#10003; No pending batch registrations.
                </div>
            @endforelse
        </div>

    </div>
</x-filament-panels::page>
