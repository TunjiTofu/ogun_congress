<x-filament-panels::page>
    @include('partials.dashboard-vars')
    <style>
        /* Responsive campers grid */
        .hidden { display:none !important }
        .campers-grid { display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1rem }
        .camper-card { background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;cursor:pointer;text-decoration:none;display:block }
        .camper-card:hover { transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,0.12) }
        .camper-card-top { display:flex;align-items:center;gap:0.75rem;padding:0.85rem 0.9rem }
        .camper-photo { width:52px;height:64px;border-radius:8px;object-fit:cover;object-position:top center;border:1px solid var(--d-border);flex-shrink:0 }
        .camper-no-photo { width:52px;height:64px;border-radius:8px;background:var(--d-bg-hover);display:flex;align-items:center;justify-content:center;font-size:1.5rem;flex-shrink:0;border:1px solid var(--d-border) }
        .camper-name { font-weight:700;font-size:0.88rem;color:var(--d-text);line-height:1.2;margin-bottom:2px }
        .camper-code { font-family:monospace;font-size:0.7rem;color:var(--d-muted);margin-bottom:4px }
        .dept-badge { display:inline-block;font-size:0.62rem;font-weight:700;padding:2px 8px;border-radius:100px;color:#fff }
        .rank-tag { display:inline-block;font-size:0.6rem;font-weight:500;padding:2px 7px;border-radius:100px;background:var(--d-bg-hover);color:var(--d-text-3);margin-left:4px }
        .camper-card-footer { padding:0.55rem 0.9rem;border-top:1px solid var(--d-border);display:flex;align-items:center;justify-content:space-between }
        .camper-church { font-size:0.7rem;color:var(--d-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;max-width:140px }
        .consent-icon { font-size:0.75rem }
        .status-dot { width:8px;height:8px;border-radius:50%;flex-shrink:0 }

        /* Table view for wider screens */
        @media(max-width:767px) { .table-view { display:none } }
        @media(min-width:768px) { .card-view  { display:none } .table-view { display:block } }

        /* Table */
        .campers-table { width:100%;border-collapse:collapse;font-size:0.82rem }
        .campers-table th { padding:0.6rem 0.85rem;text-align:left;font-size:0.62rem;text-transform:uppercase;letter-spacing:0.08em;color:var(--d-muted);border-bottom:1px solid var(--d-border);background:var(--d-thead);white-space:nowrap }
        .campers-table td { padding:0.6rem 0.85rem;border-bottom:1px solid var(--d-border);color:var(--d-text-2);vertical-align:middle }
        .campers-table tr:hover td { background:var(--d-bg-hover) }
        .tbl-photo { width:36px;height:44px;border-radius:6px;object-fit:cover;object-position:top center;border:1px solid var(--d-border) }
        .tbl-no-photo { width:36px;height:44px;border-radius:6px;background:var(--d-bg-hover);display:flex;align-items:center;justify-content:center;font-size:1rem }
        .tbl-name { font-weight:600;color:var(--d-text) }
        .tbl-code { font-family:monospace;font-size:0.72rem;color:var(--d-muted) }
        .badge-sm { font-size:0.62rem;font-weight:700;padding:2px 8px;border-radius:100px }
        .action-link { font-size:0.72rem;color:var(--d-bar-adv);text-decoration:none;font-weight:600 }
        .action-link:hover { text-decoration:underline }
        .pagination-bar { display:flex;align-items:center;justify-content:space-between;margin-top:1rem;flex-wrap:wrap;gap:0.5rem }
        .page-info { font-size:0.78rem;color:var(--d-muted) }
    </style>

    @php
        $catColors = ['adventurer'=>'#3b82f6','pathfinder'=>'#22c55e','senior_youth'=>'#f59e0b'];
        $catDark   = ['adventurer'=>'#818cf8','pathfinder'=>'#34d399','senior_youth'=>'#fbbf24'];
        function camperCatColor($cat) {
            $colors = ['adventurer'=>'#3b82f6','pathfinder'=>'#22c55e','senior_youth'=>'#f59e0b'];
            return $colors[$cat] ?? '#64748b';
        }
    @endphp

    {{-- ── Toolbar ── --}}
    @if($isSuperAdmin)
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;margin-bottom:1rem">
            <button onclick="openMod('modal-id-cards')"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.1rem;background:var(--d-stat-1-bg);color:var(--d-stat-1-tc);border:1px solid var(--d-stat-1-bc);border-radius:8px;font-size:0.82rem;font-weight:700;cursor:pointer">
                🪪 Export ID Cards
            </button>
            <button onclick="openMod('modal-camper-list')"
                    style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.55rem 1.1rem;background:var(--d-bg-card);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:8px;font-size:0.82rem;font-weight:700;cursor:pointer">
                📄 Export Camper List
            </button>
        </div>
    @endif

    <form method="GET" action="{{ $baseUrl }}" style="margin-bottom:1rem">
        {{-- Search pill --}}
        <div style="display:flex;align-items:center;background:var(--d-bg-card);border:1.5px solid var(--d-border);border-radius:12px;padding:0 0.75rem;gap:0.5rem;flex-wrap:wrap;transition:border-color 0.2s"
             onfocusin="this.style.borderColor='#6366f1'" onfocusout="this.style.borderColor=''">
            <svg style="width:16px;height:16px;color:var(--d-muted);flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search name, code or phone…"
                   style="flex:1;min-width:200px;padding:0.65rem 0.25rem;background:transparent;border:none;outline:none;color:var(--d-text);font-size:0.88rem"/>
            <div style="display:flex;align-items:center;gap:0.5rem;padding:0.35rem 0;flex-wrap:wrap">
                <select name="filter_category" onchange="this.form.submit()"
                        style="padding:0.35rem 0.65rem;background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:8px;font-size:0.78rem;outline:none;cursor:pointer">
                    <option value="">All Depts</option>
                    @foreach(\App\Enums\CamperCategory::cases() as $cat)
                        <option value="{{ $cat->value }}" {{ request('filter_category')===$cat->value ? 'selected' : '' }}>{{ $cat->label() }}</option>
                    @endforeach
                </select>
                @if($isSuperAdmin)
                    <select name="filter_district" onchange="this.form.submit()"
                            style="padding:0.35rem 0.65rem;background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:8px;font-size:0.78rem;outline:none;cursor:pointer">
                        <option value="">All Districts</option>
                        @foreach($districts as $d)
                            <option value="{{ $d->id }}" {{ request('filter_district')==$d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                        @endforeach
                    </select>
                    <select name="filter_photo" onchange="this.form.submit()"
                            style="padding:0.35rem 0.65rem;background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:8px;font-size:0.78rem;outline:none;cursor:pointer">
                        <option value="">All Photos</option>
                        <option value="pending"  {{ request('filter_photo')==='pending'  ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="approved" {{ request('filter_photo')==='approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="rejected" {{ request('filter_photo')==='rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
                @endif
                <button type="submit"
                        style="padding:0.35rem 1rem;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-size:0.78rem;font-weight:700;cursor:pointer">Search</button>
                @if(request()->hasAny(['q','filter_category','filter_district','filter_photo']))
                    <a href="{{ $baseUrl }}"
                       style="font-size:0.72rem;color:var(--d-muted);text-decoration:underline;padding:0.35rem 0.25rem">Clear</a>
                @endif
            </div>
        </div>
    </form>

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.75rem;flex-wrap:wrap;gap:0.5rem">
        <p style="font-size:0.78rem;color:var(--d-muted)">{{ $campers->total() }} campers found</p>
        <div style="display:flex;align-items:center;gap:0.5rem">
            <span style="font-size:0.75rem;color:var(--d-muted)">Show:</span>
            @foreach([12, 24, 48, 100] as $n)
                @php
                    $ppParams = array_merge(array_filter(request()->only(['q','filter_category','filter_district','filter_photo'])), ['per_page' => $n]);
                    $ppUrl    = $baseUrl . '?' . http_build_query($ppParams);
                    $active   = (int) request('per_page', 24) === $n;
                @endphp
                <a href="{{ $ppUrl }}"
                   style="font-size:0.75rem;padding:3px 10px;border-radius:6px;text-decoration:none;{{ $active ? 'background:#4f46e5;color:#fff;font-weight:700' : 'background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border)' }}">
                    {{ $n }}
                </a>
            @endforeach
        </div>
    </div>

    {{-- Export modals (simple display toggle — no class juggling) --}}
    @if($isSuperAdmin)
        <style>
            .export-modal-backdrop { display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.65);padding:2rem 1rem;overflow-y:auto }
            .export-modal-backdrop.open { display:block }
            .export-modal-box { background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:16px;padding:1.5rem;max-width:440px;width:100%;margin:0 auto }
        </style>

        {{-- ID Cards modal --}}
        <div id="modal-id-cards" class="export-modal-backdrop" onclick="if(event.target===this)closeMod('modal-id-cards')">
            <div class="export-modal-box">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                    <p style="font-weight:700;font-size:1rem;color:var(--d-text)">🪪 Export ID Cards PDF</p>
                    <button onclick="closeMod('modal-id-cards')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--d-muted);line-height:1">&times;</button>
                </div>
                <p style="font-size:0.78rem;color:var(--d-muted);margin-bottom:1rem">Filter by district or department. Leave blank to export all.</p>
                <form action="{{ route('exports.id-cards') }}" method="GET" target="_blank">
                    <div style="display:grid;gap:0.75rem;margin-bottom:1.25rem">
                        <div>
                            <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--d-muted);display:block;margin-bottom:4px">District</label>
                            <select name="district_id" style="width:100%;padding:0.55rem 0.75rem;background:var(--d-bg-hover);color:var(--d-text);border:1px solid var(--d-border);border-radius:8px;font-size:0.85rem;outline:none">
                                <option value="">All districts</option>
                                @foreach($districts as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--d-muted);display:block;margin-bottom:4px">Department</label>
                            <select name="category" style="width:100%;padding:0.55rem 0.75rem;background:var(--d-bg-hover);color:var(--d-text);border:1px solid var(--d-border);border-radius:8px;font-size:0.85rem;outline:none">
                                <option value="">All departments</option>
                                @foreach(\App\Enums\CamperCategory::cases() as $cat)<option value="{{ $cat->value }}">{{ $cat->label() }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:0.75rem">
                        <button type="submit" style="flex:1;padding:0.65rem;background:#4f46e5;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:0.88rem;cursor:pointer">Generate & Download</button>
                        <button type="button" onclick="closeMod('modal-id-cards')" style="padding:0.65rem 1rem;background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:10px;font-size:0.85rem;cursor:pointer">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Camper list modal --}}
        <div id="modal-camper-list" class="export-modal-backdrop" onclick="if(event.target===this)closeMod('modal-camper-list')">
            <div class="export-modal-box">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem">
                    <p style="font-weight:700;font-size:1rem;color:var(--d-text)">📄 Export Camper List PDF</p>
                    <button onclick="closeMod('modal-camper-list')" style="background:none;border:none;font-size:1.4rem;cursor:pointer;color:var(--d-muted);line-height:1">&times;</button>
                </div>
                <p style="font-size:0.78rem;color:var(--d-muted);margin-bottom:1rem">Filter by district or department. Leave blank to export all.</p>
                <form action="{{ route('exports.campers') }}" method="GET" target="_blank">
                    <div style="display:grid;gap:0.75rem;margin-bottom:1.25rem">
                        <div>
                            <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--d-muted);display:block;margin-bottom:4px">District</label>
                            <select name="district_id" style="width:100%;padding:0.55rem 0.75rem;background:var(--d-bg-hover);color:var(--d-text);border:1px solid var(--d-border);border-radius:8px;font-size:0.85rem;outline:none">
                                <option value="">All districts</option>
                                @foreach($districts as $d)<option value="{{ $d->id }}">{{ $d->name }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;color:var(--d-muted);display:block;margin-bottom:4px">Department</label>
                            <select name="category" style="width:100%;padding:0.55rem 0.75rem;background:var(--d-bg-hover);color:var(--d-text);border:1px solid var(--d-border);border-radius:8px;font-size:0.85rem;outline:none">
                                <option value="">All departments</option>
                                @foreach(\App\Enums\CamperCategory::cases() as $cat)<option value="{{ $cat->value }}">{{ $cat->label() }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div style="display:flex;gap:0.75rem">
                        <button type="submit" style="flex:1;padding:0.65rem;background:#4f46e5;color:#fff;border:none;border-radius:10px;font-weight:700;font-size:0.88rem;cursor:pointer">Generate & Download</button>
                        <button type="button" onclick="closeMod('modal-camper-list')" style="padding:0.65rem 1rem;background:var(--d-bg-hover);color:var(--d-text-2);border:1px solid var(--d-border);border-radius:10px;font-size:0.85rem;cursor:pointer">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function openMod(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
            function closeMod(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
        </script>
    @endif

    {{-- ── MOBILE: Card Grid ── --}}
    <div class="card-view campers-grid">
        @forelse($campers as $camper)
            @php $cat = $camper->category?->value ?? ''; $color = camperCatColor($cat); @endphp
            <a href="{{ \App\Filament\Resources\CamperResource::getUrl('view', ['record' => $camper]) }}" class="camper-card">
                <div class="camper-card-top">
                    @if($camper->getFirstMedia('photo'))
                        <img src="{{ route('camper.photo', $camper->id) }}" class="camper-photo" alt=""/>
                    @else
                        <div class="camper-no-photo">👤</div>
                    @endif
                    <div style="min-width:0;flex:1">
                        <div class="camper-name">{{ $camper->full_name }}</div>
                        <div class="camper-code">{{ $camper->camper_number }}</div>
                        <div>
                            <span class="dept-badge" style="background:{{ $color }}">{{ $camper->category?->label() }}</span>
                            @if($camper->club_rank)<span class="rank-tag">{{ $camper->club_rank }}</span>@endif
                        </div>
                        @if($camper->is_official && $camper->campRole)
                            <div style="margin-top:3px"><span style="font-size:0.58rem;font-weight:700;background:#450a0a;color:#fca5a5;padding:1px 6px;border-radius:100px">🛡 {{ $camper->campRole->name }}</span></div>
                        @endif
                    </div>
                </div>
                <div class="camper-card-footer">
                    <span class="camper-church">{{ $camper->church?->name }}</span>
                    <div style="display:flex;align-items:center;gap:0.5rem">
                        @if($camper->photo_status === 'approved')<span title="Photo approved">✅</span>
                        @elseif($camper->photo_status === 'rejected')<span title="Photo rejected">❌</span>
                        @else<span title="Photo pending" style="opacity:0.4">📷</span>@endif
                        @if($camper->consent_collected)<span title="Consent collected">📋</span>@endif
                    </div>
                </div>
            </a>
        @empty
            <div style="grid-column:1/-1;padding:3rem;text-align:center;color:var(--d-muted);font-style:italic">No campers found.</div>
        @endforelse
    </div>

    {{-- ── DESKTOP: Compact Table ── --}}
    <div class="table-view" style="background:var(--d-bg-card);border:1px solid var(--d-border);border-radius:14px;overflow:hidden">
        <div style="overflow-x:auto">
            <table class="campers-table">
                <thead>
                <tr>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Department · Rank</th>
                    <th>Church</th>
                    @if($isSuperAdmin)<th>Photo</th>@endif
                    <th>Consent</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($campers as $camper)
                    @php $cat = $camper->category?->value ?? ''; $color = camperCatColor($cat); @endphp
                    <tr>
                        <td>
                            @if($camper->getFirstMedia('photo'))
                                <img src="{{ route('camper.photo', $camper->id) }}" class="tbl-photo" alt=""/>
                            @else
                                <div class="tbl-no-photo">👤</div>
                            @endif
                        </td>
                        <td>
                            <div class="tbl-name">{{ $camper->full_name }}</div>
                            <div class="tbl-code">{{ $camper->camper_number }}</div>
                            @if($camper->is_official && $camper->campRole)
                                <span style="font-size:0.58rem;font-weight:700;background:var(--d-stat-4-bg);color:var(--d-stat-4-tc);padding:1px 5px;border-radius:100px">🛡 {{ $camper->campRole->name }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-sm" style="background:{{ $color }};color:#fff">{{ $camper->category?->label() }}</span>
                            @if($camper->club_rank) <span style="font-size:0.7rem;color:var(--d-muted)">{{ $camper->club_rank }}</span>@endif
                        </td>
                        <td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $camper->church?->name }}</td>
                        @if($isSuperAdmin)
                            <td>
                                @if($camper->photo_status==='approved') <span class="badge-sm" style="background:var(--d-stat-2-bg);color:var(--d-stat-2-tc)">✅</span>
                                @elseif($camper->photo_status==='rejected') <span class="badge-sm" style="background:var(--d-stat-4-bg);color:var(--d-stat-4-tc)">❌</span>
                                @else <span class="badge-sm" style="background:var(--d-stat-3-bg);color:var(--d-stat-3-tc)">⏳</span>@endif
                            </td>
                        @endif
                        <td>{{ $camper->consent_collected ? '✅' : '—' }}</td>
                        <td style="white-space:nowrap">
                            <a href="{{ \App\Filament\Resources\CamperResource::getUrl('view',['record'=>$camper]) }}" class="action-link">View</a>
                            &nbsp;
                            <a href="{{ \App\Filament\Resources\CamperResource::getUrl('edit',['record'=>$camper]) }}" class="action-link">Edit</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:3rem;text-align:center;color:var(--d-muted);font-style:italic">No campers found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="pagination-bar">
        <span class="page-info">Showing {{ $campers->firstItem() }}–{{ $campers->lastItem() }} of {{ $campers->total() }}</span>
        <div style="display:flex;gap:0.5rem">
            @php
                // Build clean URLs using the Filament route — avoids /admin/admin doubling
                $qBase = array_filter(request()->only(['q','filter_category','filter_district','filter_photo','per_page']));
                $currentPage = $campers->currentPage();
                $lastPage    = $campers->lastPage();
                $pageUrl     = fn($p) => $baseUrl . '?' . http_build_query(array_merge($qBase, ['page' => $p]));
            @endphp

            @if($currentPage > 1)
                <a href="{{ $pageUrl($currentPage - 1) }}" style="padding:0.4rem 0.85rem;border:1px solid var(--d-border);border-radius:8px;color:var(--d-text);text-decoration:none;font-size:0.8rem;background:var(--d-bg-card)">← Prev</a>
            @else
                <span style="padding:0.4rem 0.85rem;border:1px solid var(--d-border);border-radius:8px;color:var(--d-muted);font-size:0.8rem">← Prev</span>
            @endif

            @for($p = max(1, $currentPage - 2); $p <= min($lastPage, $currentPage + 2); $p++)
                @if($p === $currentPage)
                    <span style="padding:0.4rem 0.85rem;background:#4f46e5;color:#fff;border-radius:8px;font-size:0.8rem;font-weight:700">{{ $p }}</span>
                @else
                    <a href="{{ $pageUrl($p) }}" style="padding:0.4rem 0.85rem;border:1px solid var(--d-border);border-radius:8px;color:var(--d-text);text-decoration:none;font-size:0.8rem;background:var(--d-bg-card)">{{ $p }}</a>
                @endif
            @endfor

            @if($currentPage < $lastPage)
                <a href="{{ $pageUrl($currentPage + 1) }}" style="padding:0.4rem 0.85rem;border:1px solid var(--d-border);border-radius:8px;color:var(--d-text);text-decoration:none;font-size:0.8rem;background:var(--d-bg-card)">Next →</a>
            @else
                <span style="padding:0.4rem 0.85rem;border:1px solid var(--d-border);border-radius:8px;color:var(--d-muted);font-size:0.8rem">Next →</span>
            @endif
        </div>
    </div>



</x-filament-panels::page>
