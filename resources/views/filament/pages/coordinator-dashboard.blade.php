<x-filament-panels::page>
    <div style="font-family:'DM Sans',ui-sans-serif,system-ui,sans-serif;color:#111827">

        @if(!$church)
            <div style="background:#FEF9C3;border:1px solid #FDE047;border-radius:16px;padding:2rem;text-align:center;margin-bottom:1.5rem">
                <p style="font-size:1.1rem;font-weight:800;color:#713F12;margin-bottom:0.4rem">&#9888; No Church Assigned</p>
                <p style="font-size:0.84rem;color:#92400E">Your account has not been linked to a church. Please contact the super admin.</p>
            </div>
        @else

            {{-- ── Hero ─────────────────────────────────────────────────────── --}}
            <div style="border-radius:20px;background:linear-gradient(135deg,#0B2455 0%,#1B3A8F 60%,#2E5FAD 100%);
            padding:1.75rem 2rem;position:relative;overflow:hidden;margin-bottom:1.5rem">
                <div style="position:absolute;top:-40px;right:-40px;width:180px;height:180px;border-radius:50%;
                background:rgba(255,255,255,0.04);pointer-events:none"></div>
                <p style="font-size:0.62rem;font-weight:700;letter-spacing:0.2em;text-transform:uppercase;
              color:rgba(232,194,85,0.85);margin-bottom:0.4rem">Church Coordinator — Ogun Conference Youth Congress 2026</p>
                <h1 style="font-size:1.5rem;font-weight:800;color:#fff;line-height:1.2;margin-bottom:0.3rem">
                    {{ $church->name }}
                </h1>
                <p style="font-size:0.78rem;color:rgba(255,255,255,0.55)">
                    <span style="color:rgba(255,255,255,0.85);font-weight:600">{{ $church->district?->name }}</span>
                    &bull; {{ now()->format('l, d F Y') }}
                </p>
            </div>

            {{-- ── Stats ────────────────────────────────────────────────────── --}}
            @php
                $totalCampers   = $batches->sum(fn($b) => $b->entries->count());
                $formsCompleted = $confirmedCampers->count();
                $formsPending   = max(0, $totalCampers - $formsCompleted);
                $pct = $totalCampers > 0 ? round($formsCompleted / $totalCampers * 100) : 0;
            @endphp

            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:1.5rem">
                <div style="background:#EEF2FF;border:1px solid #C7D2FE;border-radius:16px;padding:1.25rem 1.4rem">
                    <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
                  color:#3730A3;margin-bottom:0.5rem">Registered Campers</p>
                    <p style="font-size:1.9rem;font-weight:900;color:#1E1B4B;line-height:1">{{ $formsCompleted }}</p>
                </div>
                <div style="background:#FEF3C7;border:1px solid #FDE68A;border-radius:16px;padding:1.25rem 1.4rem">
                    <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
                  color:#92400E;margin-bottom:0.5rem">Total Paid</p>
                    <p style="font-size:1.5rem;font-weight:900;color:#78350F;line-height:1">
                        &#8358;{{ number_format($totalPaid) }}
                    </p>
                </div>
                <div style="background:#D1FAE5;border:1px solid #6EE7B7;border-radius:16px;padding:1.25rem 1.4rem">
                    <p style="font-size:0.62rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;
                  color:#065F46;margin-bottom:0.5rem">Batches</p>
                    <p style="font-size:1.9rem;font-weight:900;color:#022C22;line-height:1">{{ $batches->count() }}</p>
                </div>
            </div>

            {{-- ── Progress bar ─────────────────────────────────────────────── --}}
            @if($totalCampers > 0)
                <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            padding:1.1rem 1.4rem;margin-bottom:1.5rem;
            box-shadow:0 2px 16px rgba(11,36,85,0.05)">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.5rem">
                        <span style="font-size:0.82rem;font-weight:700;color:#0B2455">Registration Forms Completed</span>
                        <span style="font-size:0.75rem;font-weight:700;color:#0B2455">
            {{ $formsCompleted }}/{{ $totalCampers }} &bull; {{ $pct }}%
        </span>
                    </div>
                    <div style="height:6px;background:#E2E8F0;border-radius:3px;overflow:hidden">
                        <div style="width:{{ $pct }}%;height:100%;border-radius:3px;
                    background:linear-gradient(90deg,#0B2455,#1B3A8F);transition:width 0.4s ease"></div>
                    </div>
                    @if($formsPending > 0)
                        <p style="font-size:0.72rem;color:#F59E0B;margin-top:0.5rem;font-weight:500">
                            &#9888; {{ $formsPending }} camper{{ $formsPending !== 1 ? 's' : '' }} still
                            need{{ $formsPending === 1 ? 's' : '' }} to complete their registration form.
                        </p>
                    @else
                        <p style="font-size:0.72rem;color:#10B981;margin-top:0.5rem;font-weight:600">
                            &#10003; All campers have completed their registration forms.
                        </p>
                    @endif
                </div>
            @endif

            {{-- ── Registered Campers ───────────────────────────────────────── --}}
            <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            overflow:hidden;box-shadow:0 2px 16px rgba(11,36,85,0.05);margin-bottom:1.5rem">
                <div style="padding:1rem 1.4rem;border-bottom:1px solid #F1F5F9;background:#FAFBFF;
                display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:0.6rem">
                        <span style="width:8px;height:8px;border-radius:50%;background:#10B981;display:inline-block"></span>
                        <span style="font-size:0.88rem;font-weight:700;color:#0B2455">Registered Campers</span>
                    </div>
                    <span style="font-size:0.72rem;color:#94A3B8">{{ $confirmedCampers->count() }} total</span>
                </div>

                @forelse($confirmedCampers as $camper)
                    <div style="padding:0.9rem 1.4rem;display:flex;align-items:center;justify-content:space-between;
                gap:1rem;border-bottom:1px solid #F8FAFF">
                        <div style="display:flex;align-items:center;gap:0.85rem;flex:1;min-width:0">
                            @if($camper->getFirstMedia('photo'))
                                <img src="{{ route('camper.photo', $camper->id) }}" alt="Photo"
                                     style="width:40px;height:40px;border-radius:50%;object-fit:cover;
                        border:2px solid rgba(11,36,85,0.08);flex-shrink:0"
                                     onerror="this.outerHTML='<div style=\'width:40px;height:40px;border-radius:50%;background:#EEF2FF;display:flex;align-items:center;justify-content:center;color:#6366F1;font-size:1.1rem;flex-shrink:0\'>&#128100;</div>'"/>
                            @else
                                <div style="width:40px;height:40px;border-radius:50%;background:#EEF2FF;
                        display:flex;align-items:center;justify-content:center;
                        color:#6366F1;font-size:1.1rem;flex-shrink:0">&#128100;</div>
                            @endif
                            <div style="min-width:0">
                                <p style="font-size:0.84rem;font-weight:700;color:#111827;
                           white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    {{ $camper->full_name }}
                                </p>
                                <p style="font-size:0.7rem;color:#94A3B8;margin-top:1px">
                                    <span style="font-family:monospace;color:#6366F1;font-weight:700">{{ $camper->camper_number }}</span>
                                    &bull; {{ $camper->category->label() }}
                                    @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
                                </p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.4rem;flex-shrink:0">
            <span style="font-size:0.65rem;font-weight:700;padding:0.22rem 0.65rem;border-radius:100px;
                         white-space:nowrap;{{ $camper->id_card_path
                            ? 'background:#D1FAE5;color:#065F46'
                            : 'background:#FEF3C7;color:#92400E' }}">
                {{ $camper->id_card_path ? 'ID Ready' : 'Generating' }}
            </span>
                            @if($camper->id_card_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->id_card_path) }}" target="_blank"
                                   style="font-size:0.68rem;font-weight:700;padding:0.3rem 0.75rem;border-radius:100px;
                      background:#0B2455;color:#fff;text-decoration:none">ID Card</a>
                            @endif
                            @if($camper->consent_form_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->consent_form_path) }}" target="_blank"
                                   style="font-size:0.68rem;font-weight:700;padding:0.3rem 0.75rem;border-radius:100px;
                      background:#475569;color:#fff;text-decoration:none">Consent</a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding:3rem 1.5rem;text-align:center;color:#94A3B8;font-size:0.84rem;font-style:italic">
                        No registered campers yet. Campers appear here once they complete their form.
                    </div>
                @endforelse
            </div>

            {{-- ── Registration Batches ─────────────────────────────────────── --}}
            <div style="background:#fff;border:1px solid rgba(11,36,85,0.08);border-radius:18px;
            overflow:hidden;box-shadow:0 2px 16px rgba(11,36,85,0.05)">
                <div style="padding:1rem 1.4rem;border-bottom:1px solid #F1F5F9;background:#FAFBFF;
                display:flex;align-items:center;justify-content:space-between">
                    <div style="display:flex;align-items:center;gap:0.6rem">
                        <span style="width:8px;height:8px;border-radius:50%;background:#6366F1;display:inline-block"></span>
                        <span style="font-size:0.88rem;font-weight:700;color:#0B2455">Registration Batches</span>
                    </div>
                    <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('create') }}"
                       style="font-size:0.72rem;font-weight:700;padding:0.38rem 0.9rem;border-radius:100px;
                  background:#0B2455;color:#fff;text-decoration:none">+ New Batch</a>
                </div>

                @forelse($batches as $batch)
                    @php
                        $done  = $batch->entries->filter(fn($e) => $e->status === 'registered')->count();
                        $btotal = $batch->entries->count();
                        $statusStyle = match($batch->status) {
                            'confirmed'       => 'background:#D1FAE5;color:#065F46;border:1px solid #6EE7B7',
                            'pending_payment' => 'background:#FEF3C7;color:#92400E;border:1px solid #FCD34D',
                            'draft'           => 'background:#F1F5F9;color:#475569;border:1px solid #E2E8F0',
                            'rejected'        => 'background:#FEE2E2;color:#991B1B;border:1px solid #FCA5A5',
                            default           => 'background:#F1F5F9;color:#475569;border:1px solid #E2E8F0',
                        };
                    @endphp
                    <div style="padding:1.1rem 1.4rem;border-bottom:1px solid #F8FAFF">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:1rem;margin-bottom:0.5rem">
                            <div>
                                <p style="font-size:0.85rem;font-weight:700;color:#111827">Batch #{{ $batch->id }}</p>
                                <p style="font-size:0.72rem;color:#94A3B8;margin-top:0.15rem;line-height:1.4">
                                    {{ $btotal }} camper{{ $btotal !== 1 ? 's' : '' }} &bull;
                                    &#8358;{{ number_format($batch->expected_total) }} expected
                                    @if($batch->amount_paid) &bull; &#8358;{{ number_format($batch->amount_paid) }} paid @endif
                                    &bull; {{ $batch->created_at->format('d M Y') }}
                                </p>
                                @if($batch->status === 'confirmed' && $btotal > 0)
                                    <p style="font-size:0.72rem;color:#10B981;margin-top:0.2rem;font-weight:600">
                                        {{ $done }}/{{ $btotal }} forms completed
                                    </p>
                                @endif
                                @if($batch->status === 'rejected' && $batch->rejection_reason)
                                    <p style="font-size:0.72rem;color:#DC2626;margin-top:0.2rem">
                                        Rejected: {{ $batch->rejection_reason }}
                                    </p>
                                @endif
                            </div>
                            <span style="font-size:0.62rem;font-weight:700;padding:0.2rem 0.65rem;border-radius:100px;
                         white-space:nowrap;flex-shrink:0;{{ $statusStyle }}">
                {{ ucwords(str_replace('_',' ',$batch->status)) }}
            </span>
                        </div>

                        @if($batch->status === 'confirmed' && $batch->entries->count())
                            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-top:0.5rem">
                                @foreach($batch->entries->take(8) as $entry)
                                    @if($entry->registrationCode)
                                        <span style="font-family:monospace;font-size:0.63rem;font-weight:700;
                         background:#EEF2FF;color:#3730A3;
                         padding:0.18rem 0.5rem;border-radius:6px">
                {{ $entry->registrationCode->code }}
            </span>
                                    @endif
                                @endforeach
                                @if($batch->entries->count() > 8)
                                    <span style="font-size:0.65rem;color:#94A3B8;line-height:1.6">
                +{{ $batch->entries->count()-8 }} more
            </span>
                                @endif
                            </div>
                        @endif

                        <div style="display:flex;justify-content:flex-end;margin-top:0.7rem">
                            <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('edit',['record'=>$batch]) }}"
                               style="font-size:0.68rem;font-weight:700;padding:0.3rem 0.8rem;border-radius:100px;
                      background:#F1F5F9;color:#475569;text-decoration:none">View Batch &rarr;</a>
                        </div>
                    </div>
                @empty
                    <div style="padding:3rem 1.5rem;text-align:center;color:#94A3B8;font-size:0.84rem;font-style:italic">
                        No batches yet. Create your first batch above.
                    </div>
                @endforelse
            </div>

        @endif
    </div>
</x-filament-panels::page>
