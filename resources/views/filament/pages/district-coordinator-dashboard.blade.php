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

    @if(! $district)
        <div style="text-align:center;padding:3rem;color:var(--d-text-3);background:#0F172A;border-radius:14px">
            <p style="font-size:1.5rem;margin-bottom:0.5rem">⚠️</p>
            <p style="color:var(--d-muted)">No district assigned to your account. Contact the super admin.</p>
        </div>
    @else

        {{-- Header --}}
        <div style="background:linear-gradient(135deg,#020617,#0F172A,#1E293B);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;border:1px solid #1E293B">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:var(--d-muted);margin-bottom:0.2rem">District Coordinator</p>
            <h1 style="font-size:1.4rem;font-weight:900;color:#F1F5F9;line-height:1.2">{{ $district->name }}</h1>
            <p style="font-size:0.75rem;color:rgba(255,255,255,0.55);margin-top:0.2rem">{{ $churches->count() }} {{ Str::plural('church', $churches->count()) }} · Ogun Conference Youth Congress 2026</p>
        </div>

        {{-- Stats --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.85rem;margin-bottom:1.25rem">
            <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:1rem 1.1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">👥 Total Registered</p>
                <p style="font-size:2rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">{{ $totalRegistered }}</p>
            </div>
            <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:1rem 1.1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">✅ In Camp</p>
                <p style="font-size:2rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ $totalCheckedIn }}</p>
            </div>
            <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:1rem 1.1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">⚠️ Consent Pending</p>
                <p style="font-size:2rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ $consentPending }}</p>
            </div>
            <div style="background:#0F172A;border:1px solid #334155;border-radius:14px;padding:1rem 1.1rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-muted);margin-bottom:0.35rem">🏛 Churches</p>
                <p style="font-size:2rem;font-weight:900;color:var(--d-text);line-height:1">{{ $churches->count() }}</p>
            </div>
        </div>

        {{-- Dept breakdown --}}
        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.25rem">
            <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-text-3);margin-bottom:0.75rem">Department Breakdown</p>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem">
                @foreach([['Adventurers','adventurers','#818CF8'],['Pathfinders','pathfinders','#34D399'],['Senior Youth','senior_youth','#FBBF24']] as [$lbl,$key,$color])
                    @php $count = $categoryBreakdown[$key]; $pct = $totalRegistered > 0 ? round($count/$totalRegistered*100) : 0; @endphp
                    <div style="text-align:center">
                        <p style="font-size:1.6rem;font-weight:900;color:{{ $color }}">{{ $count }}</p>
                        <p style="font-size:0.7rem;color:var(--d-muted);margin-bottom:4px">{{ $lbl }}</p>
                        <div style="background:var(--d-bg-hover);border-radius:100px;height:4px"><div style="background:{{ $color }};width:{{ $pct }}%;height:4px;border-radius:100px"></div></div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Church table --}}
        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between;flex-shrink:0">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">Church Breakdown</p>
                <a href="{{ route('exports.campers', ['district_id' => $district->id]) }}" target="_blank"
                   style="font-size:0.68rem;background:#3730A3;color:#E0E7FF;padding:0.3rem 0.75rem;border-radius:100px;text-decoration:none;font-weight:700">⬇ Export PDF</a>
            </div>
            <div style="overflow-y:auto;max-height:400px">
                <table style="width:100%;border-collapse:collapse">
                    <thead style="position:sticky;top:0;background:#0F172A">
                    <tr style="border-bottom:1px solid var(--d-border)">
                        @foreach(['Church','Total','Adv','PF','SY','Checked In','Consent ⚠',''] as $h)
                            <th style="padding:0.5rem 0.85rem;text-align:{{ $loop->first ? 'left' : ($loop->last ? 'left' : 'center') }};font-size:0.6rem;font-weight:700;color:var(--d-text-3);text-transform:uppercase;white-space:nowrap">{{ $h }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($churchStats as $i => $stat)
                        <tr style="border-bottom:1px solid var(--d-border);{{ $i%2 ? 'background:var(--d-bg-hover)' : '' }}">
                            <td style="padding:0.6rem 0.85rem;font-weight:600;font-size:0.8rem;color:var(--d-text-2)">{{ $stat['church']->name }}</td>
                            <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:800;color:var(--d-text)">{{ $stat['total'] }}</td>
                            <td style="padding:0.6rem 0.85rem;text-align:center;color:#818CF8;font-weight:600;font-size:0.78rem">{{ $stat['adventurers'] }}</td>
                            <td style="padding:0.6rem 0.85rem;text-align:center;color:#34D399;font-weight:600;font-size:0.78rem">{{ $stat['pathfinders'] }}</td>
                            <td style="padding:0.6rem 0.85rem;text-align:center;color:#FBBF24;font-weight:600;font-size:0.78rem">{{ $stat['senior_youth'] }}</td>
                            <td style="padding:0.6rem 0.85rem;text-align:center">
                                @if($stat['checked_in'] > 0)<span style="background:#052E16;color:var(--d-stat-2-tc);font-size:0.68rem;font-weight:700;padding:1px 7px;border-radius:100px">✅ {{ $stat['checked_in'] }}</span>
                                @else<span style="color:var(--d-muted)">—</span>@endif
                            </td>
                            <td style="padding:0.6rem 0.85rem;text-align:center">
                                @if($stat['consent_pending'] > 0)<span style="background:#422006;color:var(--d-stat-3-tc);font-size:0.68rem;font-weight:700;padding:1px 7px;border-radius:100px">{{ $stat['consent_pending'] }}</span>
                                @else<span style="color:var(--d-muted)">—</span>@endif
                            </td>
                            <td style="padding:0.6rem 0.85rem">
                                <a href="{{ route('exports.campers', ['church_id' => $stat['church']->id]) }}" target="_blank" style="font-size:0.65rem;color:#818CF8;text-decoration:underline;margin-right:0.65rem">Export</a>
                                <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}" style="font-size:0.65rem;color:#34D399;text-decoration:underline">Campers</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" style="padding:2rem;text-align:center;color:var(--d-text-3);font-style:italic">No campers registered yet.</td></tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr style="background:#1E1B4B">
                        <td style="padding:0.6rem 0.85rem;font-weight:700;font-size:0.8rem;color:#fff">TOTAL</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:800;color:#fff">{{ $totalRegistered }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700;color:var(--d-stat-1-tc)">{{ $categoryBreakdown["adventurers"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700;color:var(--d-stat-2-tc)">{{ $categoryBreakdown["pathfinders"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700;color:var(--d-stat-3-tc)">{{ $categoryBreakdown["senior_youth"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700;color:#fff">{{ $totalCheckedIn }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700;color:#fff">{{ $consentPending }}</td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
