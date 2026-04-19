<x-filament-panels::page>
    <div class="space-y-5">

        @if(!$church)
            <div class="rounded-2xl p-6 text-center"
                 style="background:#FEF9C3;border:1px solid #FDE047;">
                <p style="font-size:1.2rem;font-weight:800;color:#713F12;margin-bottom:0.3rem;">&#9888; No Church Assigned</p>
                <p style="font-size:0.85rem;color:#92400E;">Your account has not been linked to a church. Contact the super admin.</p>
            </div>
        @else

            {{-- Header --}}
            <div class="rounded-2xl p-5 text-white relative overflow-hidden"
                 style="background:linear-gradient(135deg,#064E3B 0%,#065F46 50%,#047857 100%);">
                <div style="position:absolute;top:-15px;right:-15px;width:100px;height:100px;
                    border-radius:50%;background:rgba(255,255,255,0.06);"></div>
                <p style="font-size:0.68rem;letter-spacing:0.15em;color:rgba(167,243,208,0.85);
                  text-transform:uppercase;margin-bottom:0.3rem;">Church Coordinator</p>
                <h1 style="font-size:1.3rem;font-weight:900;margin-bottom:0.15rem;">
                    {{ $church->name }}
                </h1>
                <p style="font-size:0.8rem;color:rgba(255,255,255,0.65);">
                    {{ $church->district?->name }} &bull; {{ now()->format('d F Y') }}
                </p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['Registered Campers', $totalRegistered,             '#10B981','#D1FAE5','#065F46'],
                    ['Total Paid',         '&#8358;'.number_format($totalPaid), '#6366F1','#EEF2FF','#3730A3'],
                    ['Batches Created',    $batches->count(),            '#F59E0B','#FEF3C7','#92400E'],
                ] as [$lbl,$val,$accent,$bg,$text])
                    <div class="rounded-2xl p-4 border"
                         style="background:{{ $bg }};border-color:{{ $accent }}33;">
                        <p style="font-size:0.68rem;font-weight:700;color:{{ $text }};
                      text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.6rem;">{{ $lbl }}</p>
                        <p style="font-size:1.7rem;font-weight:900;color:{{ $text }};line-height:1;">{!! $val !!}</p>
                    </div>
                @endforeach
            </div>

            {{-- Registered Campers --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between"
                     style="background:#F9FAFB;">
                    <div class="flex items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:#10B981;display:inline-block;"></span>
                        <h3 style="font-size:0.85rem;font-weight:700;color:#111827;">
                            Registered Campers &mdash; {{ $church->name }}
                        </h3>
                    </div>
                    <span style="font-size:0.72rem;color:#9CA3AF;">{{ $confirmedCampers->count() }} total</span>
                </div>

                @forelse($confirmedCampers as $camper)
                    <div class="px-5 py-3.5 border-b border-gray-50 flex items-center justify-between gap-4 last:border-0">
                        <div class="flex items-center gap-3">
                            @if($camper->getFirstMediaUrl('photo','thumb'))
                                <img src="{{ $camper->getFirstMediaUrl('photo','thumb') }}"
                                     style="width:38px;height:38px;border-radius:50%;object-fit:cover;
                            border:2px solid #E5E7EB;flex-shrink:0;"/>
                            @else
                                <div style="width:38px;height:38px;border-radius:50%;background:#F3F4F6;
                            display:flex;align-items:center;justify-content:center;
                            color:#9CA3AF;font-size:1rem;flex-shrink:0;">&#128100;</div>
                            @endif
                            <div>
                                <p style="font-size:0.82rem;font-weight:700;color:#111827;line-height:1.2;">
                                    {{ $camper->full_name }}
                                </p>
                                <p style="font-size:0.7rem;color:#9CA3AF;margin-top:1px;">
                                    <span style="font-family:monospace;">{{ $camper->camper_number }}</span>
                                    &bull; {{ $camper->category->label() }}
                                    @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
                                </p>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:0.4rem;flex-shrink:0;">
                <span style="font-size:0.68rem;padding:0.2rem 0.6rem;border-radius:100px;font-weight:700;
                    {{ $camper->id_card_path
                        ? 'background:#D1FAE5;color:#065F46;'
                        : 'background:#FEF3C7;color:#92400E;' }}">
                    {{ $camper->id_card_path ? '&#10003; Ready' : '&#8987; Generating' }}
                </span>
                            @if($camper->id_card_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->id_card_path) }}"
                                   target="_blank"
                                   style="font-size:0.72rem;background:#0B2D6B;color:#fff;font-weight:700;
                          padding:0.3rem 0.8rem;border-radius:100px;text-decoration:none;">
                                    ID Card
                                </a>
                            @endif
                            @if($camper->consent_form_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->consent_form_path) }}"
                                   target="_blank"
                                   style="font-size:0.72rem;background:#4B5563;color:#fff;font-weight:700;
                          padding:0.3rem 0.8rem;border-radius:100px;text-decoration:none;">
                                    Consent
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div style="padding:3rem;text-align:center;color:#9CA3AF;font-style:italic;font-size:0.85rem;">
                        No registered campers yet. Once a batch is confirmed and campers complete their forms, they appear here.
                    </div>
                @endforelse
            </div>

            {{-- Batch History --}}
            <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between"
                     style="background:#F9FAFB;">
                    <div class="flex items-center gap-2">
                        <span style="width:8px;height:8px;border-radius:50%;background:#6366F1;display:inline-block;"></span>
                        <h3 style="font-size:0.85rem;font-weight:700;color:#111827;">Registration Batches</h3>
                    </div>
                    <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('create') }}"
                       style="font-size:0.72rem;background:#0B2D6B;color:#fff;font-weight:700;
                      padding:0.35rem 0.9rem;border-radius:100px;text-decoration:none;">
                        + New Batch
                    </a>
                </div>
                @forelse($batches as $batch)
                    <div class="px-5 py-3.5 border-b border-gray-50 last:border-0">
                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.75rem;">
                            <div style="flex:1;">
                                <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:0.25rem;">
                                    <p style="font-size:0.82rem;font-weight:700;color:#111827;">
                                        Batch #{{ $batch->id }}
                                    </p>
                                    <span style="font-size:0.65rem;padding:0.15rem 0.5rem;border-radius:100px;font-weight:700;border:1px solid;
                            {{ match($batch->status){
                                'draft'           => 'background:#F9FAFB;border-color:#D1D5DB;color:#6B7280;',
                                'pending_payment' => 'background:#FEF3C7;border-color:#FCD34D;color:#92400E;',
                                'confirmed'       => 'background:#D1FAE5;border-color:#6EE7B7;color:#065F46;',
                                'rejected'        => 'background:#FEE2E2;border-color:#FCA5A5;color:#991B1B;',
                                default           => ''
                            } }}">
                            {{ ucwords(str_replace('_',' ',$batch->status)) }}
                        </span>
                                </div>
                                <p style="font-size:0.72rem;color:#9CA3AF;">
                                    {{ $batch->entries->count() }} campers &bull;
                                    &#8358;{{ number_format($batch->expected_total) }} expected
                                    @if($batch->amount_paid)
                                        &bull; &#8358;{{ number_format($batch->amount_paid) }} paid
                                    @endif
                                    &bull; {{ $batch->created_at->format('d M Y') }}
                                </p>
                                @if($batch->status === 'rejected' && $batch->rejection_reason)
                                    <p style="font-size:0.72rem;color:#DC2626;margin-top:0.2rem;">
                                        Rejected: {{ $batch->rejection_reason }}
                                    </p>
                                @endif
                                @if($batch->status === 'confirmed' && $batch->entries->count() > 0)
                                    <div style="margin-top:0.6rem;display:flex;flex-wrap:wrap;gap:0.3rem;">
                                        @foreach($batch->entries->take(6) as $entry)
                                            @if($entry->registrationCode)
                                                <span style="font-size:0.65rem;background:#EEF2FF;color:#3730A3;
                                     padding:0.15rem 0.5rem;border-radius:6px;font-family:monospace;font-weight:700;">
                            {{ $entry->registrationCode->code }}
                        </span>
                                            @endif
                                        @endforeach
                                        @if($batch->entries->count() > 6)
                                            <span style="font-size:0.65rem;color:#9CA3AF;">+{{ $batch->entries->count()-6 }} more</span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('edit',['record'=>$batch]) }}"
                               style="font-size:0.72rem;color:#6366F1;font-weight:600;text-decoration:none;white-space:nowrap;flex-shrink:0;">
                                View &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <div style="padding:3rem;text-align:center;color:#9CA3AF;font-style:italic;font-size:0.85rem;">
                        No batches yet. Create your first bulk registration above.
                    </div>
                @endforelse
            </div>

        @endif
    </div>
</x-filament-panels::page>
