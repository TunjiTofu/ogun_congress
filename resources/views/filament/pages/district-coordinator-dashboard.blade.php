<x-filament-panels::page>

    @if(! $district)
        <div style="text-align:center;padding:3rem;color:#94A3B8">
            <p style="font-size:1.5rem;margin-bottom:0.5rem">⚠️</p>
            <p>No district assigned to your account. Contact the super admin.</p>
        </div>
    @else

        {{-- ── Header ── --}}
        <div style="margin-bottom:1.5rem">
            <p style="font-size:0.75rem;color:#94A3B8;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:0.25rem">
                District Coordinator
            </p>
            <h1 style="font-size:1.6rem;font-weight:900;color:#F0FDF4;line-height:1.2">
                {{ $district->name }}
            </h1>
            <p style="font-size:0.82rem;color:#64748B;margin-top:0.25rem">
                {{ $churches->count() }} {{ Str::plural('church', $churches->count()) }} &bull;
                Ogun Conference Youth Congress 2026
            </p>
        </div>

        {{-- ── District stats ── --}}
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.5rem">
            <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:14px;padding:1rem 1.25rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#3730A3;margin-bottom:0.4rem">👥 Total Registered</p>
                <p style="font-size:2rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $totalRegistered }}</p>
            </div>
            <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:14px;padding:1rem 1.25rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#065F46;margin-bottom:0.4rem">✅ Currently In Camp</p>
                <p style="font-size:2rem;font-weight:900;color:#022C22;line-height:1">{{ $totalCheckedIn }}</p>
            </div>
            <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:14px;padding:1rem 1.25rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#92400E;margin-bottom:0.4rem">⚠️ Consent Pending</p>
                <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $consentPending }}</p>
            </div>
            <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:14px;padding:1rem 1.25rem">
                <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#14532D;margin-bottom:0.4rem">🏛 Churches</p>
                <p style="font-size:2rem;font-weight:900;color:#052E16;line-height:1">{{ $churches->count() }}</p>
            </div>
        </div>

        {{-- ── Category breakdown ── --}}
        <div style="background:#F8FAFF;border:1px solid #E2E8F0;border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem">
            <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#64748B;margin-bottom:0.75rem">Department Breakdown</p>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.75rem">
                <div style="text-align:center">
                    <p style="font-size:1.5rem;font-weight:900;color:#0B2455">{{ $categoryBreakdown["adventurers"] }}</p>
                    <p style="font-size:0.72rem;color:#64748B">Adventurers</p>
                </div>
                <div style="text-align:center">
                    <p style="font-size:1.5rem;font-weight:900;color:#1B3A8F">{{ $categoryBreakdown["pathfinders"] }}</p>
                    <p style="font-size:0.72rem;color:#64748B">Pathfinders</p>
                </div>
                <div style="text-align:center">
                    <p style="font-size:1.5rem;font-weight:900;color:#9F1239">{{ $categoryBreakdown["senior_youth"] }}</p>
                    <p style="font-size:0.72rem;color:#64748B">Senior Youth</p>
                </div>
            </div>
        </div>

        {{-- ── Church-by-church table ── --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #F1F5F9;display:flex;align-items:center;justify-content:space-between">
                <p style="font-weight:700;font-size:0.9rem;color:#0B2455">Church Breakdown</p>
                <a href="{{ route('exports.campers', ['district_id' => $district->id]) }}"
                   target="_blank"
                   style="font-size:0.72rem;background:#0B2455;color:#fff;padding:0.35rem 0.85rem;border-radius:100px;text-decoration:none;font-weight:700">
                    ⬇ Export List PDF
                </a>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead>
                    <tr style="background:#F8FAFF;border-bottom:1px solid #E2E8F0">
                        <th style="padding:0.65rem 1rem;text-align:left;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.08em">Church</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.08em">Total</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#065F46;text-transform:uppercase;letter-spacing:0.08em">Adv</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#1B3A8F;text-transform:uppercase;letter-spacing:0.08em">PF</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#9F1239;text-transform:uppercase;letter-spacing:0.08em">SY</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#065F46;text-transform:uppercase;letter-spacing:0.08em">Checked In</th>
                        <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#92400E;text-transform:uppercase;letter-spacing:0.08em">Consent ⚠</th>
                        <th style="padding:0.65rem 1rem;text-align:left;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.08em">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($churchStats as $i => $stat)
                        <tr style="border-bottom:1px solid #F1F5F9;{{ $i % 2 === 1 ? 'background:#FAFBFF' : '' }}">
                            <td style="padding:0.75rem 1rem;font-weight:600;font-size:0.83rem;color:#1C2340">
                                {{ $stat['church']->name }}
                            </td>
                            <td style="padding:0.75rem 1rem;text-align:center;font-size:0.9rem;font-weight:800;color:#0B2455">
                                {{ $stat['total'] }}
                            </td>
                            <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#065F46">{{ $stat['adventurers'] }}</td>
                            <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#1B3A8F">{{ $stat['pathfinders'] }}</td>
                            <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#9F1239">{{ $stat['senior_youth'] }}</td>
                            <td style="padding:0.75rem 1rem;text-align:center">
                                @if($stat['checked_in'] > 0)
                                    <span style="background:#D1FAE5;color:#065F46;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px">
                                ✅ {{ $stat['checked_in'] }}
                            </span>
                                @else
                                    <span style="color:#CBD5E1;font-size:0.78rem">—</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem;text-align:center">
                                @if($stat['consent_pending'] > 0)
                                    <span style="background:#FEF3C7;color:#92400E;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px">
                                ⚠️ {{ $stat['consent_pending'] }}
                            </span>
                                @else
                                    <span style="color:#CBD5E1;font-size:0.78rem">—</span>
                                @endif
                            </td>
                            <td style="padding:0.75rem 1rem">
                                <a href="{{ route('exports.campers', ['church_id' => $stat['church']->id]) }}"
                                   target="_blank"
                                   style="font-size:0.68rem;color:#0B2455;text-decoration:underline;margin-right:0.75rem">
                                    Export
                                </a>
                                <a href="{{ route('filament.admin.pages.coordinator-campers-page') }}"
                                   style="font-size:0.68rem;color:#1B3A8F;text-decoration:underline">
                                    View Campers
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="padding:2rem;text-align:center;color:#94A3B8;font-style:italic">
                                No campers registered in this district yet.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                    <tfoot>
                    <tr style="background:#0B2455;color:#fff">
                        <td style="padding:0.65rem 1rem;font-weight:700;font-size:0.82rem">TOTAL</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:800">{{ $totalRegistered }}</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["adventurers"] }}</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["pathfinders"] }}</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["senior_youth"] }}</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $totalCheckedIn }}</td>
                        <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $consentPending }}</td>
                        <td></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @endif
</x-filament-panels::page>
