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

    @php
        $user        = auth()->user();
        $church      = $user->church()->with('district')->first();
        $campers     = \App\Models\Camper::where('church_id', $user->church_id)->get();
        $checkedInIds = \App\Models\CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', fn($sub) => $sub->selectRaw('MAX(id)')->from('checkin_events')
                ->whereIn('event_type',['check_in','check_out'])->groupBy('camper_id'))
            ->where('event_type','check_in')->pluck('camper_id');
        $batches = \App\Models\BulkRegistrationBatch::where('created_by', $user->id)
            ->with('entries')->latest()->get();
    @endphp

    {{-- Church header --}}
    @if($church)
        <div style="background:linear-gradient(135deg,#020617,#0F172A,#1E293B);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;border:1px solid #1E293B">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:var(--d-muted);margin-bottom:0.2rem">Church Coordinator</p>
            <h1 style="font-size:1.4rem;font-weight:900;color:#F1F5F9;line-height:1.2">{{ $church->name }}</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.55);margin-top:0.2rem">{{ $church->district?->name }} · Ogun Conference Youth Congress 2026</p>
        </div>
    @endif

    {{-- Stats --}}
    <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.85rem;margin-bottom:1.25rem">
        <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">👥 Total Registered</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">{{ $campers->count() }}</p>
            <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}" style="font-size:0.65rem;color:#818CF8;text-decoration:underline;display:inline-block;margin-top:0.35rem">View all →</a>
        </div>
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">✅ Checked In</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ $campers->whereIn('id',$checkedInIds->toArray())->count() }}</p>
        </div>
        <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">⚠️ Consent Pending</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ $campers->filter(fn($c) => $c->requiresConsentForm() && !$c->consent_collected)->count() }}</p>
        </div>
        <div style="background:var(--d-stat-4-bg);border:1px solid var(--d-stat-4-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-4-tc);margin-bottom:0.35rem">📸 Photos Rejected</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-4-vc);line-height:1">{{ $campers->where('photo_status','rejected')->count() }}</p>
            @if($campers->where('photo_status','rejected')->count() > 0)
                <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}" style="font-size:0.65rem;color:var(--d-stat-4-tc);text-decoration:underline;display:inline-block;margin-top:0.35rem">Upload replacements →</a>
            @endif
        </div>
    </div>

    {{-- Recent batches --}}
    <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
            <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">My Registration Batches</p>
            <a href="{{ route('filament.admin.resources.bulk-registration-batches.index') }}" style="font-size:0.68rem;color:#818CF8;text-decoration:underline">View all</a>
        </div>
        <div style="overflow-y:auto;max-height:320px">
            @forelse($batches->take(8) as $batch)
                @php
                    $bgs = ['confirmed'=>'#052E16','rejected'=>'#450A0A','pending_payment'=>'#422006'];
                    $tcs = ['confirmed'=>'#86EFAC','rejected'=>'#FCA5A5','pending_payment'=>'#FDE68A'];
                    $bg = $bgs[$batch->status] ?? '#1E293B';
                    $tc = $tcs[$batch->status] ?? '#94A3B8';
                @endphp
                <div style="padding:0.65rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;gap:0.75rem">
                    <div style="flex:1">
                        <p style="font-size:0.8rem;font-weight:600;color:var(--d-text)">Batch #{{ $batch->id }}</p>
                        <p style="font-size:0.68rem;color:var(--d-text-3)">{{ $batch->entries->count() }} campers · {{ $batch->created_at->format('d M Y') }}</p>
                    </div>
                    <span style="font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:100px;background:{{ $bg }};color:{{ $tc }}">
                {{ ucwords(str_replace('_',' ',$batch->status)) }}
            </span>
                </div>
            @empty
                <div style="padding:2rem;text-align:center;color:var(--d-text-3);font-style:italic;font-size:0.8rem">No batches created yet.</div>
            @endforelse
        </div>
    </div>

</x-filament-panels::page>
