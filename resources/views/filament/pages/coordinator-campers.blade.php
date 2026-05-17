<x-filament-panels::page>

    {{-- Summary bar —— uses variables passed from getViewData() --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:12px;padding:0.85rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#3730A3;margin-bottom:0.3rem">Total Registered</p>
            <p style="font-size:1.8rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $totalCount }}</p>
        </div>
        <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:12px;padding:0.85rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#065F46;margin-bottom:0.3rem">Photos Approved</p>
            <p style="font-size:1.8rem;font-weight:900;color:#022C22;line-height:1">{{ $approvedCount }}</p>
        </div>
        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:12px;padding:0.85rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#92400E;margin-bottom:0.3rem">Photos Pending</p>
            <p style="font-size:1.8rem;font-weight:900;color:#78350F;line-height:1">{{ $pendingCount }}</p>
        </div>
        <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:12px;padding:0.85rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#991B1B;margin-bottom:0.3rem">Photos Rejected</p>
            <p style="font-size:1.8rem;font-weight:900;color:#7F1D1D;line-height:1">{{ $rejectedCount }}</p>
        </div>
    </div>

    @if($rejectedCount > 0)
        <div style="background:#FEF2F2;border:1px solid #FCA5A5;border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;font-size:0.82rem;color:#991B1B;font-weight:600">
            ⚠️ {{ $rejectedCount }} camper(s) have rejected photos. Use the <strong>Replace Photo</strong> action on their row.
        </div>
    @endif

    {{ $this->table }}

</x-filament-panels::page>
