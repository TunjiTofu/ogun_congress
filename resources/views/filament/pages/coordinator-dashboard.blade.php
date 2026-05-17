<x-filament-panels::page>

    @php
        $user     = auth()->user();
        $church   = $user->church()->with('district')->first();
        $batches  = \App\Models\BulkRegistrationBatch::where('created_by', $user->id)
            ->with('entries')->latest()->get();
        $campers  = \App\Models\Camper::where('church_id', $user->church_id)->get();
        $checkedInIds = \App\Models\CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function($sub) {
                $sub->selectRaw('MAX(id)')->from('checkin_events')
                    ->whereIn('event_type', ['check_in','check_out'])->groupBy('camper_id');
            })->where('event_type','check_in')->pluck('camper_id');
    @endphp

    {{-- Church header --}}
    @if($church)
        <div style="background:linear-gradient(135deg,#0B2455,#1B3A8F);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;color:#fff">
            <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.55);margin-bottom:0.25rem">Church Coordinator</p>
            <h1 style="font-size:1.5rem;font-weight:900;line-height:1.2;color:#fff">{{ $church->name }}</h1>
            <p style="font-size:0.78rem;color:rgba(255,255,255,0.65);margin-top:0.25rem">{{ $church->district?->name }} &bull; Ogun Conference Youth Congress 2026</p>
        </div>
    @endif

    {{-- Stats grid --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:1rem;margin-bottom:1.5rem">

        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#3730A3;margin-bottom:0.35rem">👥 Total Registered</p>
            <p style="font-size:2rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $campers->count() }}</p>
            <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}" style="font-size:0.68rem;color:#4338CA;text-decoration:underline;display:inline-block;margin-top:0.4rem">View all →</a>
        </div>

        <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#065F46;margin-bottom:0.35rem">✅ Checked In</p>
            <p style="font-size:2rem;font-weight:900;color:#022C22;line-height:1">{{ $campers->whereIn('id', $checkedInIds->toArray())->count() }}</p>
        </div>

        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#92400E;margin-bottom:0.35rem">⚠️ Consent Pending</p>
            <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $campers->filter(fn($c) => $c->requiresConsentForm() && !$c->consent_collected)->count() }}</p>
        </div>

        <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#991B1B;margin-bottom:0.35rem">📸 Photos Rejected</p>
            <p style="font-size:2rem;font-weight:900;color:#7F1D1D;line-height:1">{{ $campers->where('photo_status','rejected')->count() }}</p>
            @if($campers->where('photo_status','rejected')->count() > 0)
                <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}" style="font-size:0.68rem;color:#DC2626;text-decoration:underline;display:inline-block;margin-top:0.4rem">Upload replacements →</a>
            @endif
        </div>

    </div>

    {{-- Recent batches --}}
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden;margin-bottom:1.5rem">
        <div style="padding:0.85rem 1.1rem;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between">
            <p style="font-weight:700;font-size:0.88rem;color:#0B2455">My Registration Batches</p>
            <a href="{{ route('filament.admin.resources.bulk-registration-batches.index') }}" style="font-size:0.72rem;color:#1B3A8F;text-decoration:underline">View all</a>
        </div>
        @forelse($batches->take(5) as $batch)
            <div style="padding:0.75rem 1.1rem;border-bottom:1px solid #F8FAFF;display:flex;align-items:center;gap:0.75rem">
                <div style="flex:1">
                    <p style="font-size:0.82rem;font-weight:600;color:#1C2340">Batch #{{ $batch->id }}</p>
                    <p style="font-size:0.7rem;color:#64748B">{{ $batch->entries->count() }} campers &bull; {{ $batch->created_at->format('d M Y') }}</p>
                </div>
                <span style="font-size:0.68rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px;
            background:{{ match($batch->status) { 'confirmed'=>'#D1FAE5','rejected'=>'#FEE2E2','pending_payment'=>'#FEF3C7',default=>'#F1F5F9' } }};
            color:{{ match($batch->status) { 'confirmed'=>'#065F46','rejected'=>'#991B1B','pending_payment'=>'#92400E',default=>'#475569' } }}">
            {{ ucwords(str_replace('_',' ',$batch->status)) }}
        </span>
            </div>
        @empty
            <div style="padding:2rem;text-align:center;color:#94A3B8;font-style:italic;font-size:0.82rem">No batches created yet.</div>
        @endforelse
    </div>

</x-filament-panels::page>
