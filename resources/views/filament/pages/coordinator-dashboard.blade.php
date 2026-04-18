<x-filament-panels::page>

    <div class="space-y-6">

        @if(!$church)
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 text-yellow-800 text-center">
                <p class="font-bold text-lg mb-1">&#9888; No Church Assigned</p>
                <p class="text-sm">Your account has not been linked to a local church. Please contact the super admin.</p>
            </div>
        @else

            {{-- Header --}}
            <div class="rounded-2xl p-6 text-white" style="background:linear-gradient(135deg,#0B2D6B,#1E5FAD)">
                <p class="text-blue-200 text-xs uppercase tracking-widest mb-1">Church Coordinator</p>
                <h1 class="text-xl font-bold">{{ $church->name }}</h1>
                <p class="text-blue-200 text-sm">{{ $church->district?->name }} &bull; {{ now()->format('d F Y') }}</p>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach([
                    ['Total Registered',  $totalRegistered,           'bg-green-50 border-green-200 text-green-700'],
                    ['Total Paid',        '₦'.number_format($totalPaid), 'bg-blue-50 border-blue-200 text-blue-700'],
                    ['Active Batches',    $batches->count(),           'bg-purple-50 border-purple-200 text-purple-700'],
                ] as [$label,$value,$cls])
                    <div class="rounded-xl border p-4 {{ $cls }}">
                        <p class="text-2xl font-bold">{{ $value }}</p>
                        <p class="text-xs font-medium mt-0.5">{{ $label }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Registered Campers Table --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Registered Campers — {{ $church->name }}</h2>
                    <span class="text-xs text-gray-500">{{ $confirmedCampers->count() }} camper(s)</span>
                </div>

                @forelse($confirmedCampers as $camper)
                    <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            @if($camper->getFirstMediaUrl('photo','thumb'))
                                <img src="{{ $camper->getFirstMediaUrl('photo','thumb') }}"
                                     class="w-10 h-10 rounded-full object-cover border border-gray-200"/>
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 text-lg">&#128100;</div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $camper->full_name }}</p>
                                <p class="text-xs text-gray-500">
                                    {{ $camper->camper_number }} &bull;
                                    {{ $camper->category->label() }}
                                    @if($camper->club_rank) &bull; {{ $camper->club_rank }} @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            {{-- Status badge --}}
                            <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $camper->id_card_path ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $camper->id_card_path ? 'Docs Ready' : 'Generating...' }}
                </span>
                            {{-- Downloads --}}
                            @if($camper->id_card_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->id_card_path) }}"
                                   target="_blank"
                                   class="text-xs bg-navy text-white px-3 py-1 rounded-full hover:bg-blue-800 transition"
                                   style="background:#0B2D6B">
                                    ID Card
                                </a>
                            @endif
                            @if($camper->consent_form_path && $documentService)
                                <a href="{{ $documentService->getDownloadUrl($camper->consent_form_path) }}"
                                   target="_blank"
                                   class="text-xs bg-gray-600 text-white px-3 py-1 rounded-full hover:bg-gray-700 transition">
                                    Consent
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-10 text-center text-gray-400 italic text-sm">
                        No registered campers yet. Once a batch is confirmed and campers complete their forms, they will appear here.
                    </div>
                @endforelse
            </div>

            {{-- Batch History --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-bold text-gray-800">Registration Batches</h2>
                    <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('create') }}"
                       class="text-xs font-bold px-3 py-1.5 rounded-full text-white hover:opacity-90 transition"
                       style="background:#0B2D6B">+ New Batch</a>
                </div>
                @forelse($batches as $batch)
                    <div class="px-5 py-3 border-b border-gray-50">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-gray-800">
                                    Batch #{{ $batch->id }} &mdash; {{ $batch->entries->count() }} campers
                                </p>
                                <p class="text-xs text-gray-500">
                                    Expected: ₦{{ number_format($batch->expected_total) }} &bull;
                                    Created: {{ $batch->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-0.5 rounded-full border font-semibold
                        {{ match($batch->status) {
                            'draft'           => 'bg-gray-100 border-gray-300 text-gray-600',
                            'pending_payment' => 'bg-yellow-100 border-yellow-300 text-yellow-700',
                            'confirmed'       => 'bg-green-100 border-green-300 text-green-700',
                            'rejected'        => 'bg-red-100 border-red-300 text-red-700',
                            default           => ''
                        } }}">
                        {{ ucwords(str_replace('_',' ',$batch->status)) }}
                    </span>
                                <a href="{{ \App\Filament\Resources\BulkRegistrationBatchResource::getUrl('edit',['record'=>$batch]) }}"
                                   class="text-xs text-blue-600 hover:underline">View</a>
                            </div>
                        </div>
                        @if($batch->status === 'confirmed')
                            <div class="mt-2 grid grid-cols-2 md:grid-cols-4 gap-1.5">
                                @foreach($batch->entries as $entry)
                                    @if($entry->registrationCode)
                                        <div class="bg-green-50 rounded-lg px-2 py-1.5 text-xs">
                                            <p class="font-semibold text-gray-700 truncate">{{ $entry->full_name }}</p>
                                            <p class="font-mono text-green-700 text-xs">{{ $entry->registrationCode->code }}</p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                        @if($batch->status === 'rejected' && $batch->rejection_reason)
                            <p class="mt-1 text-xs text-red-600">Reason: {{ $batch->rejection_reason }}</p>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-gray-400 italic text-sm">No batches yet. Create your first bulk registration.</div>
                @endforelse
            </div>

        @endif
    </div>
</x-filament-panels::page>
