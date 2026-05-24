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
    <div style="background:linear-gradient(135deg,#020617,#0F172A,#1E1B4B);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.25rem;color:#fff;border:1px solid #1E293B">
        <p style="font-size:0.6rem;font-weight:700;text-transform:uppercase;letter-spacing:0.15em;color:#818CF8;margin-bottom:0.25rem">Camp Director — Read Only</p>
        <h1 style="font-size:1.4rem;font-weight:900;color:#F1F5F9;line-height:1.2">Ogun Conference Youth Congress 2026</h1>
        <p style="font-size:0.75rem;color:rgba(255,255,255,0.55);margin-top:0.25rem">Abeokuta &bull; Aug 16–22, 2026 &bull; Acts 1:8</p>
    </div>

    {{-- Row 1 stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.85rem;margin-bottom:0.85rem">
        <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">👥 Total Registered</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">{{ $totalCampers }}</p>
        </div>
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">✅ Currently In Camp</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ $totalCheckedIn }}</p>
        </div>
        <div style="background:var(--d-stat-4-bg);border:1px solid var(--d-stat-4-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-4-tc);margin-bottom:0.35rem">🚪 Checked Out</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-4-vc);line-height:1">{{ $totalCheckedOut }}</p>
        </div>
    </div>

    {{-- Row 2 stats --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.85rem;margin-bottom:1.25rem">
        <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">⚠️ Consent Pending</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ $consentPending }}</p>
        </div>
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">📸 Photos Pending</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ $photosPending }}</p>
        </div>
        <div style="background:var(--d-stat-4-bg);border:1px solid var(--d-stat-4-bc);border-radius:14px;padding:1rem 1.1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-4-tc);margin-bottom:0.35rem">❌ Photos Rejected</p>
            <p style="font-size:2rem;font-weight:900;color:var(--d-stat-4-vc);line-height:1">{{ $photosRejected }}</p>
        </div>
    </div>

    {{-- Dept + Sessions --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1rem">

        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border)">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">Department Breakdown</p>
            </div>
            <div style="padding:1rem;display:grid;gap:0.75rem">
                @foreach([['Adventurers','adventurers','#818CF8'],['Pathfinders','pathfinders','#34D399'],['Senior Youth','senior_youth','#FBBF24']] as [$label,$key,$color])
                    @php $count = $categoryBreakdown[$key]; $pct = $totalCampers > 0 ? round($count/$totalCampers*100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                            <span style="font-size:0.78rem;color:var(--d-muted)">{{ $label }}</span>
                            <span style="font-size:0.78rem;font-weight:700;color:{{ $color }}">{{ $count }} <span style="color:var(--d-text-3);font-weight:400">({{ $pct }}%)</span></span>
                        </div>
                        <div style="background:var(--d-bg-hover);border-radius:100px;height:5px">
                            <div style="background:{{ $color }};width:{{ $pct }}%;height:5px;border-radius:100px"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">📅 Today's Sessions</p>
            </div>
            <div style="overflow-y:auto;max-height:220px">
                @forelse($todaySessions as $session)
                    <div style="padding:0.65rem 1rem;border-bottom:1px solid var(--d-border)">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px">
                            <span style="font-size:0.8rem;font-weight:600;color:var(--d-text)">{{ $session["title"] }}</span>
                            <span style="background:var(--d-stat-1-bg);color:var(--d-stat-1-tc);font-size:0.62rem;font-weight:700;padding:1px 7px;border-radius:100px">{{ $session["attendance"] }} attended</span>
                        </div>
                        <p style="font-size:0.68rem;color:var(--d-text-3)">🕐 {{ $session["start_time"] }} – {{ $session["end_time"] }} · 📍 {{ $session["venue"] }}</p>
                    </div>
                @empty
                    <div style="padding:1.5rem;text-align:center;color:var(--d-text-3);font-style:italic;font-size:0.8rem">No active sessions today.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- District table --}}
    <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
        <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
            <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">🗺 District Breakdown</p>
        </div>
        <div style="overflow-y:auto;max-height:340px">
            <table style="width:100%;border-collapse:collapse">
                <thead style="position:sticky;top:0;background:#0F172A">
                <tr style="border-bottom:1px solid var(--d-border)">
                    @foreach(['District','Churches','Total','Adv','PF','SY','In Camp','Consent ⚠'] as $h)
                        <th style="padding:0.5rem 0.85rem;text-align:{{ $loop->first ? 'left' : 'center' }};font-size:0.6rem;font-weight:700;color:var(--d-text-3);text-transform:uppercase;letter-spacing:0.08em;white-space:nowrap">{{ $h }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @forelse($districtStats as $i => $stat)
                    <tr style="border-bottom:1px solid var(--d-border);{{ $i % 2 ? 'background:var(--d-bg-hover)' : '' }}">
                        <td style="padding:0.6rem 0.85rem;font-weight:600;font-size:0.82rem;color:var(--d-text-2)">{{ $stat["district"]->name }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-size:0.78rem;color:var(--d-muted)">{{ $stat["churches"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-size:0.9rem;font-weight:800;color:var(--d-text)">{{ $stat["total"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-size:0.78rem;color:#818CF8;font-weight:600">{{ $stat["adventurers"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-size:0.78rem;color:#34D399;font-weight:600">{{ $stat["pathfinders"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center;font-size:0.78rem;color:#FBBF24;font-weight:600">{{ $stat["senior_youth"] }}</td>
                        <td style="padding:0.6rem 0.85rem;text-align:center">
                            @if($stat["checked_in"] > 0)
                                <span style="background:#052E16;color:var(--d-stat-2-tc);font-size:0.68rem;font-weight:700;padding:1px 7px;border-radius:100px">✅ {{ $stat["checked_in"] }}</span>
                            @else<span style="color:var(--d-muted)">—</span>@endif
                        </td>
                        <td style="padding:0.6rem 0.85rem;text-align:center">
                            @if($stat["consent_pending"] > 0)
                                <span style="background:#422006;color:var(--d-stat-3-tc);font-size:0.68rem;font-weight:700;padding:1px 7px;border-radius:100px">{{ $stat["consent_pending"] }}</span>
                            @else<span style="color:var(--d-muted)">—</span>@endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:2rem;text-align:center;color:var(--d-text-3);font-style:italic">No data available.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr style="background:var(--d-stat-1-bg)">
                    <td style="padding:0.6rem 0.85rem;font-weight:700;font-size:0.8rem">TOTAL</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $districtStats->sum("churches") }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:800">{{ $totalCampers }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $categoryBreakdown["adventurers"] }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $categoryBreakdown["pathfinders"] }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $categoryBreakdown["senior_youth"] }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $totalCheckedIn }}</td>
                    <td style="padding:0.6rem 0.85rem;text-align:center;font-weight:700">{{ $consentPending }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

</x-filament-panels::page>
