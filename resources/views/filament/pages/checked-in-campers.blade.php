<x-filament-panels::page>
    @php
        // Currently in camp = last event is check_in
        $currentlyIn = \App\Models\CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->count();

        $checkedOut = \App\Models\CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_out')
            ->count();

        $totalRegistered   = \App\Models\Camper::count();
        $consentPending    = \App\Models\Camper::where('consent_collected', false)
            ->whereIn('category', ['adventurer', 'pathfinder'])->count();
    @endphp

    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
        <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#065F46;margin-bottom:0.4rem">✅ Currently In Camp</p>
            <p style="font-size:2rem;font-weight:900;color:#022C22;line-height:1">{{ $currentlyIn }}</p>
        </div>
        <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#991B1B;margin-bottom:0.4rem">🚪 Checked Out</p>
            <p style="font-size:2rem;font-weight:900;color:#7F1D1D;line-height:1">{{ $checkedOut }}</p>
        </div>
        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#3730A3;margin-bottom:0.4rem">👥 Total Registered</p>
            <p style="font-size:2rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $totalRegistered }}</p>
        </div>
        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#92400E;margin-bottom:0.4rem">⚠️ Consent Pending</p>
            <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $consentPending }}</p>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
