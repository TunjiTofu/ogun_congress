<x-filament-panels::page>

    @php
        $registrationOpen    = setting('skill_registration_open', '0') === '1';
        $totalSkills         = \App\Models\Skill::count();
        $activeSkills        = \App\Models\Skill::where('status', 'active')->count();
        $totalRegistrations  = \App\Models\CamperSkillRegistration::count();

        $skillStats = \App\Models\Skill::withCount('registrations')
            ->orderByDesc('registrations_count')
            ->get()
            ->map(fn ($s) => [
                'name'       => $s->name,
                'category'   => $s->categoryLabel(),
                'registered' => $s->registrations_count,
                'capacity'   => $s->maximum_attendees,
                'remaining'  => max(0, $s->maximum_attendees - $s->registrations_count),
                'pct'        => $s->maximum_attendees > 0
                    ? round($s->registrations_count / $s->maximum_attendees * 100)
                    : 0,
            ]);

        $mostPopular  = $skillStats->first();
        $leastPopular = $skillStats->where('registered', '>', 0)->sortBy('registered')->first();
    @endphp

    {{-- Status banner --}}
    <div style="display:flex;align-items:center;gap:.75rem;padding:.85rem 1.1rem;border-radius:12px;margin-bottom:1.25rem;
        {{ $registrationOpen ? 'background:#052E16;border:1px solid #166534' : 'background:#450A0A;border:1px solid #991B1B' }}">
        <span style="font-size:1rem">{{ $registrationOpen ? '🟢' : '🔴' }}</span>
        <p style="font-size:.85rem;font-weight:600;color:{{ $registrationOpen ? '#86EFAC' : '#FCA5A5' }}">
            Skill Registration is currently <strong>{{ $registrationOpen ? 'OPEN' : 'CLOSED' }}</strong>.
            {{ $registrationOpen ? 'Campers can register and change their skill selection.' : 'No new registrations or changes are possible.' }}
        </p>
    </div>

    {{-- KPI cards --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:.85rem;margin-bottom:1.25rem">
        @foreach([
            ['Total Skills',        $totalSkills,        '#818CF8', '#1E1B4B'],
            ['Active Skills',       $activeSkills,       '#34D399', '#052E16'],
            ['Total Registrations', $totalRegistrations, '#FBBF24', '#422006'],
            ['Registration',        $registrationOpen ? 'OPEN' : 'CLOSED',
                                    $registrationOpen ? '#86EFAC' : '#FCA5A5',
                                    $registrationOpen ? '#052E16' : '#450A0A'],
        ] as [$label, $value, $vc, $bg])
            <div style="background:{{ $bg }};border-radius:14px;padding:1rem 1.1rem">
                <p style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.55);margin-bottom:.35rem">{{ $label }}</p>
                <p style="font-size:1.8rem;font-weight:900;color:{{ $vc }};line-height:1">{{ $value }}</p>
            </div>
        @endforeach
    </div>

    {{-- Most / Least popular --}}
    @if($mostPopular || $leastPopular)
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.85rem;margin-bottom:1.25rem">
            @if($mostPopular)
                <div style="background:var(--fi-color-gray-800,#1E293B);border-radius:14px;padding:1rem 1.25rem">
                    <p style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.4);margin-bottom:.35rem">🔥 Most Popular</p>
                    <p style="font-size:1rem;font-weight:700;color:#F1F5F9">{{ $mostPopular['name'] }}</p>
                    <p style="font-size:.75rem;color:rgba(255,255,255,.5)">{{ $mostPopular['registered'] }} / {{ $mostPopular['capacity'] }} registered</p>
                </div>
            @endif
            @if($leastPopular)
                <div style="background:var(--fi-color-gray-800,#1E293B);border-radius:14px;padding:1rem 1.25rem">
                    <p style="font-size:.58rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:rgba(255,255,255,.4);margin-bottom:.35rem">📊 Least Popular</p>
                    <p style="font-size:1rem;font-weight:700;color:#F1F5F9">{{ $leastPopular['name'] }}</p>
                    <p style="font-size:.75rem;color:rgba(255,255,255,.5)">{{ $leastPopular['registered'] }} / {{ $leastPopular['capacity'] }} registered</p>
                </div>
            @endif
        </div>
    @endif

    {{-- Skill breakdown table --}}
    <div style="background:var(--fi-color-gray-800,#1E293B);border-radius:14px;overflow:hidden">
        <div style="padding:.75rem 1rem;border-bottom:1px solid rgba(255,255,255,.06)">
            <p style="font-weight:700;font-size:.85rem;color:var(--fi-color-gray-100,#F1F5F9)">Registrations by Skill</p>
        </div>
        <div style="overflow-y:auto;max-height:480px">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                <tr style="background:rgba(0,0,0,.25)">
                    @foreach(['Skill','For','Registered','Capacity','Remaining','Fill Rate'] as $h)
                        <th style="padding:.55rem .85rem;text-align:{{ $loop->first ? 'left' : 'center' }};font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.4)">{{ $h }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @forelse($skillStats as $i => $stat)
                    <tr style="{{ $i%2 ? 'background:rgba(255,255,255,.03)' : '' }};border-bottom:1px solid rgba(255,255,255,.04)">
                        <td style="padding:.6rem .85rem;font-weight:600;font-size:.82rem;color:var(--fi-color-gray-100,#F1F5F9)">{{ $stat['name'] }}</td>
                        <td style="padding:.6rem .85rem;text-align:center;font-size:.72rem;color:rgba(255,255,255,.5)">{{ $stat['category'] }}</td>
                        <td style="padding:.6rem .85rem;text-align:center;font-weight:700;color:#FBBF24">{{ $stat['registered'] }}</td>
                        <td style="padding:.6rem .85rem;text-align:center;color:rgba(255,255,255,.45)">{{ $stat['capacity'] }}</td>
                        <td style="padding:.6rem .85rem;text-align:center;font-weight:700;color:{{ $stat['remaining'] <= 5 ? '#FCA5A5' : ($stat['remaining'] <= 15 ? '#FDE68A' : '#86EFAC') }}">{{ $stat['remaining'] }}</td>
                        <td style="padding:.6rem 1rem;text-align:center">
                            <div style="background:rgba(255,255,255,.08);border-radius:100px;height:6px;overflow:hidden;width:80px;margin:0 auto">
                                <div style="background:{{ $stat['pct'] >= 90 ? '#FCA5A5' : ($stat['pct'] >= 60 ? '#FBBF24' : '#34D399') }};width:{{ $stat['pct'] }}%;height:100%;border-radius:100px"></div>
                            </div>
                            <span style="font-size:.65rem;color:rgba(255,255,255,.35)">{{ $stat['pct'] }}%</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="padding:2rem;text-align:center;color:rgba(255,255,255,.35);font-style:italic">No skills created yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-filament-panels::page>
