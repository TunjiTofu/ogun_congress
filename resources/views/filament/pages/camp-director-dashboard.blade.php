<x-filament-panels::page>

    {{-- ── Header ── --}}
    <div style="background:linear-gradient(135deg,#0B2455,#1B3A8F);border-radius:16px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;color:#fff">
        <p style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:rgba(255,255,255,0.55);margin-bottom:0.25rem">Camp Director — Read Only</p>
        <h1 style="font-size:1.5rem;font-weight:900;color:#fff;line-height:1.2">Ogun Conference Youth Congress 2026</h1>
        <p style="font-size:0.78rem;color:rgba(255,255,255,0.65);margin-top:0.25rem">Abeokuta &bull; Aug 16–22, 2026 &bull; Acts 1:8</p>
    </div>

    {{-- ── Top stats ── --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.25rem">
        <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#3730A3;margin-bottom:0.35rem">👥 Total Registered</p>
            <p style="font-size:2rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $totalCampers }}</p>
        </div>
        <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#065F46;margin-bottom:0.35rem">✅ Currently In Camp</p>
            <p style="font-size:2rem;font-weight:900;color:#022C22;line-height:1">{{ $totalCheckedIn }}</p>
        </div>
        <div style="background:#FEE2E2;border:1px solid #FCA5A5;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#991B1B;margin-bottom:0.35rem">🚪 Checked Out</p>
            <p style="font-size:2rem;font-weight:900;color:#7F1D1D;line-height:1">{{ $totalCheckedOut }}</p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
        <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#92400E;margin-bottom:0.35rem">⚠️ Consent Pending</p>
            <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $consentPending }}</p>
        </div>
        <div style="background:#F0FDF4;border:1px solid #BBF7D0;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#14532D;margin-bottom:0.35rem">📸 Photos Pending Review</p>
            <p style="font-size:2rem;font-weight:900;color:#052E16;line-height:1">{{ $photosPending }}</p>
        </div>
        <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:14px;padding:1rem 1.25rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:#92400E;margin-bottom:0.35rem">❌ Photos Rejected</p>
            <p style="font-size:2rem;font-weight:900;color:#78350F;line-height:1">{{ $photosRejected }}</p>
        </div>
    </div>

    {{-- ── Category + Today's sessions ── --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">

        {{-- Category breakdown --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden">
            <div style="padding:0.85rem 1.1rem;border-bottom:1px solid #F1F5F9">
                <p style="font-weight:700;font-size:0.88rem;color:#0B2455">Department Breakdown</p>
            </div>
            <div style="padding:1rem 1.1rem;display:grid;gap:0.75rem">
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <div style="width:10px;height:10px;border-radius:50%;background:#1B3A8F"></div>
                        <span style="font-size:0.82rem;color:#475569">Adventurers</span>
                    </div>
                    <span style="font-size:1.1rem;font-weight:800;color:#1B3A8F">{{ $categoryBreakdown["adventurers"] }}</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <div style="width:10px;height:10px;border-radius:50%;background:#2D7A3A"></div>
                        <span style="font-size:0.82rem;color:#475569">Pathfinders</span>
                    </div>
                    <span style="font-size:1.1rem;font-weight:800;color:#2D7A3A">{{ $categoryBreakdown["pathfinders"] }}</span>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        <div style="width:10px;height:10px;border-radius:50%;background:#C9A94D"></div>
                        <span style="font-size:0.82rem;color:#475569">Senior Youth</span>
                    </div>
                    <span style="font-size:1.1rem;font-weight:800;color:#C9A94D">{{ $categoryBreakdown["senior_youth"] }}</span>
                </div>
            </div>
        </div>

        {{-- Today's sessions --}}
        <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden">
            <div style="padding:0.85rem 1.1rem;border-bottom:1px solid #F1F5F9">
                <p style="font-weight:700;font-size:0.88rem;color:#0B2455">Today's Programme Sessions</p>
            </div>
            @forelse($todaySessions as $session)
                <div style="padding:0.75rem 1.1rem;border-bottom:1px solid #F8FAFF">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.2rem">
                        <span style="font-size:0.82rem;font-weight:600;color:#1C2340">{{ $session["title"] }}</span>
                        <span style="background:#EEF2FF;color:#3730A3;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:100px">{{ $session["attendance"] }} attended</span>
                    </div>
                    <p style="font-size:0.7rem;color:#64748B">🕐 {{ $session["start_time"] }} – {{ $session["end_time"] }} &bull; 📍 {{ $session["venue"] }}</p>
                </div>
            @empty
                <div style="padding:1.5rem;text-align:center;color:#94A3B8;font-style:italic;font-size:0.82rem">No active sessions today.</div>
            @endforelse
        </div>

    </div>

    {{-- ── District breakdown table ── --}}
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden;margin-bottom:1.5rem">
        <div style="padding:0.85rem 1.1rem;border-bottom:1px solid #F1F5F9">
            <p style="font-weight:700;font-size:0.88rem;color:#0B2455">District Breakdown</p>
        </div>
        <div style="overflow-x:auto">
            <table style="width:100%;border-collapse:collapse">
                <thead>
                <tr style="background:#F8FAFF;border-bottom:1px solid #E2E8F0">
                    <th style="padding:0.65rem 1rem;text-align:left;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase;letter-spacing:0.08em">District</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase">Churches</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#64748B;text-transform:uppercase">Total</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#1B3A8F;text-transform:uppercase">Adv</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#2D7A3A;text-transform:uppercase">PF</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#C9A94D;text-transform:uppercase">SY</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#065F46;text-transform:uppercase">In Camp</th>
                    <th style="padding:0.65rem 1rem;text-align:center;font-size:0.65rem;font-weight:700;color:#92400E;text-transform:uppercase">Consent ⚠</th>
                </tr>
                </thead>
                <tbody>
                @forelse($districtStats as $i => $stat)
                    <tr style="border-bottom:1px solid #F1F5F9;{{ $i % 2 === 1 ? 'background:#FAFBFF' : '' }}">
                        <td style="padding:0.75rem 1rem;font-weight:600;font-size:0.85rem;color:#1C2340">{{ $stat["district"]->name }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#475569">{{ $stat["churches"] }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-size:0.95rem;font-weight:800;color:#0B2455">{{ $stat["total"] }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#1B3A8F;font-weight:600">{{ $stat["adventurers"] }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#2D7A3A;font-weight:600">{{ $stat["pathfinders"] }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center;font-size:0.82rem;color:#C9A94D;font-weight:600">{{ $stat["senior_youth"] }}</td>
                        <td style="padding:0.75rem 1rem;text-align:center">
                            @if($stat["checked_in"] > 0)
                                <span style="background:#D1FAE5;color:#065F46;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px">✅ {{ $stat["checked_in"] }}</span>
                            @else
                                <span style="color:#CBD5E1;font-size:0.78rem">—</span>
                            @endif
                        </td>
                        <td style="padding:0.75rem 1rem;text-align:center">
                            @if($stat["consent_pending"] > 0)
                                <span style="background:#FEF3C7;color:#92400E;font-size:0.75rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px">{{ $stat["consent_pending"] }}</span>
                            @else
                                <span style="color:#CBD5E1;font-size:0.78rem">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="padding:2rem;text-align:center;color:#94A3B8;font-style:italic">No data available.</td></tr>
                @endforelse
                </tbody>
                <tfoot>
                <tr style="background:#0B2455;color:#fff">
                    <td style="padding:0.65rem 1rem;font-weight:700;font-size:0.82rem">TOTAL</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $districtStats->sum("churches") }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:800">{{ $totalCampers }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["adventurers"] }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["pathfinders"] }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $categoryBreakdown["senior_youth"] }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $totalCheckedIn }}</td>
                    <td style="padding:0.65rem 1rem;text-align:center;font-weight:700">{{ $consentPending }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ── Recent check-in activity ── --}}
    <div style="background:#fff;border:1px solid #E2E8F0;border-radius:14px;overflow:hidden">
        <div style="padding:0.85rem 1.1rem;border-bottom:1px solid #F1F5F9">
            <p style="font-weight:700;font-size:0.88rem;color:#0B2455">Recent Check-In Activity</p>
        </div>
        @forelse($recentActivity as $event)
            @php
                $type  = is_string($event->event_type) ? $event->event_type : $event->event_type?->value;
                $isIn  = $type === 'check_in';
            @endphp
            <div style="padding:0.65rem 1.1rem;border-bottom:1px solid #F8FAFF;display:flex;align-items:center;gap:0.75rem">
                <span style="font-size:1rem;flex-shrink:0">{{ $isIn ? '✅' : '🚪' }}</span>
                <div style="flex:1">
                    <span style="font-size:0.82rem;font-weight:600;color:#1C2340">{{ $event->camper?->full_name ?? '—' }}</span>
                    <span style="font-size:0.72rem;color:#94A3B8;margin-left:0.5rem">{{ $event->camper?->church?->name }}</span>
                </div>
                <div style="text-align:right">
                    <span style="font-size:0.7rem;color:#64748B">{{ \Illuminate\Support\Carbon::parse($event->occurred_at)->format('g:i A') }}</span>
                    <div style="font-size:0.65rem;color:#94A3B8">{{ \Illuminate\Support\Carbon::parse($event->occurred_at)->format('d M') }}</div>
                </div>
            </div>
        @empty
            <div style="padding:2rem;text-align:center;color:#94A3B8;font-style:italic;font-size:0.82rem">No check-in activity yet.</div>
        @endforelse
    </div>

</x-filament-panels::page>
