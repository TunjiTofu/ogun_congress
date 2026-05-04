<x-filament-panels::page>
    <div style="display:flex;gap:1rem;margin-bottom:1rem;flex-wrap:wrap">
        <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:12px;padding:0.9rem 1.25rem;flex:1;min-width:120px">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#065F46;margin-bottom:0.25rem">Currently In Camp</p>
            <p style="font-size:1.8rem;font-weight:900;color:#022C22;line-height:1">{{ \App\Models\CheckinEvent::where('event_type','check_in')->whereDate('occurred_at',today())->distinct('camper_id')->count('camper_id') }}</p>
        </div>
        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:12px;padding:0.9rem 1.25rem;flex:1;min-width:120px">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#3730A3;margin-bottom:0.25rem">Total Registered</p>
            <p style="font-size:1.8rem;font-weight:900;color:#1E1B4B;line-height:1">{{ \App\Models\Camper::count() }}</p>
        </div>
        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:12px;padding:0.9rem 1.25rem;flex:1;min-width:120px">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:#92400E;margin-bottom:0.25rem">Consent Pending</p>
            <p style="font-size:1.8rem;font-weight:900;color:#78350F;line-height:1">{{ \App\Models\Camper::where('consent_collected',false)->whereIn('category',['adventurer','pathfinder'])->count() }}</p>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
