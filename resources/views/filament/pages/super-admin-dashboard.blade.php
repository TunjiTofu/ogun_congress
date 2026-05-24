<x-filament-panels::page>
    @include('partials.dashboard-vars')

    {{-- Registration closed alert --}}
    @php
        $regClosed = setting('registration_open','1') !== '1'
            || (setting('registration_closes_at') && now()->gt(\Illuminate\Support\Carbon::parse(setting('registration_closes_at'))));
    @endphp
    @if($regClosed)
        <div style="background:var(--d-closed-bg);border:1.5px solid var(--d-closed-bc);border-radius:12px;padding:0.85rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.75rem">
            <span>🔴</span>
            <div style="flex:1">
                <p style="font-weight:700;color:var(--d-closed-tc);font-size:0.88rem">Registration is currently CLOSED</p>
                @if($isSuperAdmin)
                    <p style="color:var(--d-closed-tc);font-size:0.75rem;margin-top:2px;opacity:0.8">
                        <a href="{{ route('filament.admin.pages.registration-control-page') }}" style="font-weight:700;text-decoration:underline">Open Registration Control →</a>
                    </p>
                @endif
            </div>
        </div>
    @endif

    @if($pendingOffline > 0 || $photosPending > 0 || $unreadMessages > 0)
        <div style="background:var(--d-stat-3-bg);border:1.5px solid var(--d-stat-3-bc);border-radius:12px;padding:0.85rem 1.1rem;margin-bottom:1.25rem;display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap">
            <span>⚠️</span>
            <p style="font-weight:600;color:var(--d-stat-3-tc);font-size:0.85rem;display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                Action needed:
                @if($pendingOffline > 0) <span style="background:var(--d-stat-3-bc);color:var(--d-stat-3-vc);padding:2px 10px;border-radius:100px;font-size:0.72rem;font-weight:700">{{ $pendingOffline }} offline payment{{ $pendingOffline > 1 ? 's' : '' }}</span> @endif
                @if($photosPending > 0)  <span style="background:var(--d-stat-3-bc);color:var(--d-stat-3-vc);padding:2px 10px;border-radius:100px;font-size:0.72rem;font-weight:700">{{ $photosPending }} photo{{ $photosPending > 1 ? 's' : '' }} to review</span> @endif
                @if($unreadMessages > 0) <span style="background:var(--d-stat-3-bc);color:var(--d-stat-3-vc);padding:2px 10px;border-radius:100px;font-size:0.72rem;font-weight:700">{{ $unreadMessages }} unread message{{ $unreadMessages > 1 ? 's' : '' }}</span> @endif
            </p>
        </div>
    @endif

    {{-- Row 1: Key stats --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.85rem;margin-bottom:0.85rem">
        <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">👥 Total Registered</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">{{ number_format($total) }}</p>
        </div>
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">✅ In Camp Now</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ number_format($checkedIn) }}</p>
        </div>
        <div style="background:var(--d-stat-1-bg);border:1px solid var(--d-stat-1-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-1-tc);margin-bottom:0.35rem">💳 Active Codes</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-1-vc);line-height:1">{{ number_format($activeCodes) }}</p>
        </div>
        <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.12em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">💰 Offline Pending</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ number_format($pendingOffline) }}</p>
        </div>
    </div>

    {{-- Row 2 --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:0.85rem;margin-bottom:1.25rem">
        <div style="background:var(--d-stat-2-bg);border:1px solid var(--d-stat-2-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-2-tc);margin-bottom:0.35rem">📸 Photos Pending</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-2-vc);line-height:1">{{ number_format($photosPending) }}</p>
            @if($photosRejected)<p style="font-size:0.65rem;color:var(--d-stat-4-tc);margin-top:3px">{{ $photosRejected }} rejected</p>@endif
        </div>
        <div style="background:var(--d-stat-3-bg);border:1px solid var(--d-stat-3-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-3-tc);margin-bottom:0.35rem">📋 Consent Pending</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-3-vc);line-height:1">{{ number_format($consentPending) }}</p>
        </div>
        <div style="background:var(--d-stat-5-bg);border:1px solid var(--d-stat-5-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-5-tc);margin-bottom:0.35rem">🛡 Camp Officials</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-5-vc);line-height:1">{{ number_format($officials) }}</p>
        </div>
        <div style="background:var(--d-stat-6-bg);border:1px solid var(--d-stat-6-bc);border-radius:14px;padding:0.9rem 1rem">
            <p style="font-size:0.58rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--d-stat-6-tc);margin-bottom:0.35rem">✉️ Unread Messages</p>
            <p style="font-size:1.9rem;font-weight:900;color:var(--d-stat-6-vc);line-height:1">{{ number_format($unreadMessages) }}</p>
        </div>
    </div>

    {{-- Chart + Dept --}}
    <div style="display:grid;grid-template-columns:2fr 1fr;gap:1rem;margin-bottom:1rem">
        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);display:flex;justify-content:space-between;align-items:center">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">Registrations — Last 14 Days</p>
                <span style="font-size:0.7rem;color:var(--d-muted)">{{ array_sum($chartData) }} total</span>
            </div>
            <div style="padding:0.85rem 1rem">
                @php $max = max(array_merge($chartData, [1])); @endphp
                <div style="display:flex;align-items:flex-end;gap:3px;height:72px">
                    @foreach($chartData as $i => $val)
                        @php $h = max(4, round($val / $max * 100)); @endphp
                        <div style="flex:1;height:100%;display:flex;align-items:flex-end">
                            <div title="{{ $chartLabels[$i] }}: {{ $val }}" style="width:100%;height:{{ $h }}%;background:{{ $val > 0 ? 'var(--d-bar-adv)' : 'var(--d-bar-bg)' }};border-radius:2px 2px 0 0;min-height:3px"></div>
                        </div>
                    @endforeach
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:4px">
                    <span style="font-size:0.55rem;color:var(--d-muted)">{{ $chartLabels[0] ?? '' }}</span>
                    <span style="font-size:0.55rem;color:var(--d-muted)">{{ $chartLabels[13] ?? '' }}</span>
                </div>
            </div>
        </div>

        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border)">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">Departments</p>
            </div>
            <div style="padding:0.85rem 1rem;display:grid;gap:0.65rem">
                @foreach([['Adventurers',$adventurers,'var(--d-bar-adv)'],['Pathfinders',$pathfinders,'var(--d-bar-pf)'],['Senior Youth',$seniorYouth,'var(--d-bar-sy)']] as [$dept,$count,$color])
                    @php $pct = $total > 0 ? round($count / $total * 100) : 0; @endphp
                    <div>
                        <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                            <span style="font-size:0.75rem;color:var(--d-text-2)">{{ $dept }}</span>
                            <span style="font-size:0.75rem;font-weight:700;color:{{ $color }}">{{ number_format($count) }} <span style="color:var(--d-muted);font-weight:400">({{ $pct }}%)</span></span>
                        </div>
                        <div style="background:var(--d-bar-bg);border-radius:100px;height:5px">
                            <div style="background:{{ $color }};width:{{ $pct }}%;height:5px;border-radius:100px"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Programme + District --}}
    <div style="display:grid;grid-template-columns:{{ $isSuperAdmin ? '1fr 1.5fr' : '1fr' }};gap:1rem;margin-bottom:1rem">
        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">📅 Today's Programme</p>
            </div>
            <div style="overflow-y:auto;max-height:240px">
                @forelse($todaySessions as $s)
                    <div style="padding:0.65rem 1rem;border-bottom:1px solid var(--d-border)">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2px">
                            <span style="font-size:0.8rem;font-weight:600;color:var(--d-text)">{{ $s['title'] }}</span>
                            <span style="background:var(--d-stat-1-bg);color:var(--d-stat-1-tc);font-size:0.62rem;font-weight:700;padding:1px 7px;border-radius:100px">{{ $s['attendance'] }} attended</span>
                        </div>
                        <p style="font-size:0.68rem;color:var(--d-muted)">🕐 {{ $s['start_time'] }} · 📍 {{ $s['venue'] }}</p>
                    </div>
                @empty
                    <div style="padding:1.5rem;text-align:center;color:var(--d-muted);font-style:italic;font-size:0.8rem">No sessions scheduled for today.</div>
                @endforelse
            </div>
        </div>

        @if($isSuperAdmin && $districtStats->isNotEmpty())
            <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column">
                <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
                    <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">🗺 District Breakdown</p>
                </div>
                <div style="overflow-y:auto;max-height:240px">
                    <table style="width:100%;border-collapse:collapse">
                        <thead style="position:sticky;top:0;background:var(--d-thead)">
                        <tr style="border-bottom:1px solid var(--d-border)">
                            <th style="padding:0.5rem 0.85rem;text-align:left;color:var(--d-muted);font-size:0.6rem;text-transform:uppercase;letter-spacing:0.08em">District</th>
                            <th style="padding:0.5rem 0.85rem;text-align:center;color:var(--d-muted);font-size:0.6rem;text-transform:uppercase">Total</th>
                            <th style="padding:0.5rem 0.85rem;text-align:center;color:var(--d-muted);font-size:0.6rem;text-transform:uppercase">In Camp</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($districtStats as $i => $stat)
                            <tr style="border-bottom:1px solid var(--d-border);{{ $i % 2 ? 'background:var(--d-bg-hover)' : '' }}">
                                <td style="padding:0.5rem 0.85rem;font-weight:600;color:var(--d-text-2);font-size:0.8rem">{{ $stat['name'] }}</td>
                                <td style="padding:0.5rem 0.85rem;text-align:center;font-weight:800;color:var(--d-text)">{{ $stat['total'] }}</td>
                                <td style="padding:0.5rem 0.85rem;text-align:center">
                                    @if($stat['checked_in'] > 0)
                                        <span style="background:var(--d-in-bg);color:var(--d-in-tc);font-size:0.68rem;font-weight:700;padding:1px 7px;border-radius:100px">✅ {{ $stat['checked_in'] }}</span>
                                    @else<span style="color:var(--d-muted)">—</span>@endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Recent Registrations + Check-ins --}}
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">🆕 Recent Registrations</p>
            </div>
            <div style="overflow-y:auto;max-height:320px">
                @forelse($recentRegistrations as $camper)
                    @php
                        $cat = $camper->category?->value ?? '';
                        $cc = ['adventurer'=>'var(--d-bar-adv)','pathfinder'=>'var(--d-bar-pf)','senior_youth'=>'var(--d-bar-sy)'][$cat] ?? 'var(--d-muted)';
                    @endphp
                    <div style="padding:0.55rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;gap:0.65rem">
                        <div style="width:32px;height:32px;border-radius:50%;background:var(--d-bg-hover);display:flex;align-items:center;justify-content:center;color:{{ $cc }};font-weight:800;font-size:0.65rem;flex-shrink:0">
                            {{ strtoupper(substr($camper->full_name,0,2)) }}
                        </div>
                        <div style="flex:1;min-width:0">
                            <p style="font-size:0.78rem;font-weight:600;color:var(--d-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $camper->full_name }}</p>
                            <p style="font-size:0.65rem;color:var(--d-muted)">{{ $camper->church?->name }} · {{ $camper->created_at->diffForHumans() }}</p>
                        </div>
                        <span style="background:var(--d-bg-hover);color:{{ $cc }};font-size:0.6rem;font-weight:700;padding:2px 7px;border-radius:100px;flex-shrink:0">{{ $camper->category?->label() }}</span>
                    </div>
                @empty
                    <div style="padding:1.5rem;text-align:center;color:var(--d-muted);font-style:italic;font-size:0.8rem">No registrations yet.</div>
                @endforelse
            </div>
        </div>

        <div style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;display:flex;flex-direction:column">
            <div style="padding:0.75rem 1rem;border-bottom:1px solid var(--d-border);flex-shrink:0">
                <p style="font-weight:700;font-size:0.85rem;color:var(--d-text)">🚪 Recent Check-In Activity</p>
            </div>
            <div style="overflow-y:auto;max-height:320px">
                @forelse($recentCheckins as $event)
                    @php $type = is_string($event->event_type) ? $event->event_type : $event->event_type?->value; $isIn = $type === 'check_in'; @endphp
                    <div style="padding:0.55rem 1rem;border-bottom:1px solid var(--d-border);display:flex;align-items:center;gap:0.65rem">
                        <span style="font-size:1rem;flex-shrink:0">{{ $isIn ? '✅' : '🚪' }}</span>
                        <div style="flex:1;min-width:0">
                            <p style="font-size:0.78rem;font-weight:600;color:var(--d-text);overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $event->camper?->full_name ?? '—' }}</p>
                            <p style="font-size:0.65rem;color:var(--d-muted)">{{ $event->camper?->church?->name }}</p>
                        </div>
                        <span style="font-size:0.65rem;color:var(--d-muted);flex-shrink:0">{{ \Illuminate\Support\Carbon::parse($event->occurred_at)->format('g:i A') }}</span>
                    </div>
                @empty
                    <div style="padding:1.5rem;text-align:center;color:var(--d-muted);font-style:italic;font-size:0.8rem">No check-in activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>

</x-filament-panels::page>
