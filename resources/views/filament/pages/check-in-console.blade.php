<x-filament-panels::page>

    <div class="space-y-6">

        {{-- ── Scanner / Result area ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Camera panel --}}
            <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-section-content p-6 space-y-4">
                    <h2 class="text-base font-semibold text-gray-950 dark:text-white">QR Scanner</h2>

                    {{-- Camera feed --}}
                    <div class="relative overflow-hidden rounded-lg bg-black aspect-video">
                        <video id="qr-video" class="w-full h-full object-cover" playsinline></video>
                        <canvas id="qr-canvas" class="hidden"></canvas>

                        {{-- Scanning indicator --}}
                        <div class="absolute inset-0 flex items-center justify-center" id="scan-overlay">
                            <div class="border-2 border-white/70 rounded-lg w-48 h-48 flex items-center justify-center">
                                <span class="text-white/70 text-sm">Align QR code here</span>
                            </div>
                        </div>
                    </div>

                    {{-- Camera controls --}}
                    <div class="flex gap-3">
                        <button
                            onclick="startCamera()"
                            class="fi-btn fi-btn-color-primary fi-btn-size-md fi-color-primary flex-1"
                        >
                            Start Camera
                        </button>
                        <button
                            onclick="stopCamera()"
                            class="fi-btn fi-btn-color-gray fi-btn-size-md fi-color-gray"
                        >
                            Stop
                        </button>
                    </div>

                    {{-- Manual search fallback --}}
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p class="text-sm text-gray-500 mb-2">Manual search (if QR scan fails)</p>
                        <div class="flex gap-2">
                            <input
                                type="text"
                                wire:model="manualSearch"
                                wire:keydown.enter="searchManually"
                                placeholder="Enter camper number or name"
                                class="fi-input flex-1 block w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-2 text-sm"
                            />
                            <button
                                wire:click="searchManually"
                                class="fi-btn fi-btn-color-primary fi-btn-size-md fi-color-primary"
                            >
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Result panel --}}
            <div class="fi-section rounded-xl shadow-sm ring-1 ring-gray-950/5
                @if($scanStatus === 'idle')      bg-gray-50 dark:bg-gray-800
                @elseif($scanStatus === 'found') bg-green-50 dark:bg-green-950 ring-green-500/30
                @elseif($scanStatus === 'already_in') bg-amber-50 dark:bg-amber-950 ring-amber-500/30
                @elseif($scanStatus === 'not_found')  bg-red-50 dark:bg-red-950 ring-red-500/30
                @endif
            ">
                <div class="fi-section-content p-6">

                    @if($scanStatus === 'idle')
                        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                            <x-heroicon-o-qr-code class="w-16 h-16 mb-3" />
                            <p class="text-sm">Awaiting scan…</p>
                        </div>

                    @elseif($scanStatus === 'not_found')
                        <div class="flex flex-col items-center justify-center h-64 text-red-500">
                            <x-heroicon-o-x-circle class="w-16 h-16 mb-3" />
                            <p class="font-semibold text-lg">Not Found</p>
                            <p class="text-sm mt-1 text-center">
                                No camper found for: <strong>{{ $scanResult }}</strong>
                            </p>
                            <p class="text-xs mt-2 text-center text-gray-500">
                                They may have registered after the last sync. Please verify manually.
                            </p>
                            <button wire:click="resetScan" class="mt-4 fi-btn fi-btn-color-gray fi-btn-size-sm fi-color-gray">
                                Scan Again
                            </button>
                        </div>

                    @elseif($camperData)
                        {{-- Camper card --}}
                        <div class="space-y-4">

                            {{-- Status banner --}}
                            @if($scanStatus === 'already_in')
                                <div class="rounded-lg bg-amber-100 dark:bg-amber-900 border border-amber-400 px-4 py-2 text-amber-800 dark:text-amber-200 text-sm font-medium">
                                    ⚠ Already checked in. Confirm again only if re-entry is authorised.
                                </div>
                            @endif

                            {{-- Consent warning --}}
                            @if($camperData['consent_required'] && !$camperData['consent_collected'])
                                <div class="rounded-lg bg-red-100 dark:bg-red-900 border border-red-400 px-4 py-2 text-red-800 dark:text-red-200 text-sm font-medium">
                                    📋 CONSENT FORM REQUIRED — please collect from parent/guardian before confirming.
                                </div>
                            @endif

                            {{-- Camper details --}}
                            <div class="flex gap-4">
                                @if($camperData['photo_url'])
                                    <img src="{{ $camperData['photo_url'] }}"
                                         class="w-20 h-20 rounded-full object-cover ring-4
                                             {{ $scanStatus === 'found' ? 'ring-green-400' : 'ring-amber-400' }}"
                                         alt="Photo" />
                                @else
                                    <div class="w-20 h-20 rounded-full bg-gray-200 flex items-center justify-center">
                                        <x-heroicon-o-user class="w-10 h-10 text-gray-400" />
                                    </div>
                                @endif

                                <div class="flex-1">
                                    <p class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $camperData['full_name'] }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 font-mono">
                                        {{ $camperData['camper_number'] }}
                                    </p>
                                    <div class="flex gap-2 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                            @if($camperData['badge_color']) bg-opacity-20 @endif
                                            bg-blue-100 text-blue-700">
                                            {{ $camperData['category'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Church:</span>
                                    <span class="font-medium ml-1">{{ $camperData['church'] ?? '—' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">District:</span>
                                    <span class="font-medium ml-1">{{ $camperData['district'] ?? '—' }}</span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            @if(auth()->user()->hasAnyRole(['secretariat', 'super_admin']))
                                <div class="flex gap-3 pt-2 border-t border-gray-200 dark:border-gray-700">

                                    @if($camperData['consent_required'] && !$camperData['consent_collected'])
                                        {{-- Consent not yet collected — offer combined action --}}
                                        <button
                                            wire:click="confirmCheckIn(true)"
                                            wire:confirm="Confirm check-in AND mark consent form as collected?"
                                            class="fi-btn fi-btn-color-success fi-btn-size-md fi-color-success flex-1"
                                        >
                                            ✓ Check In + Collect Consent
                                        </button>
                                    @else
                                        <button
                                            wire:click="confirmCheckIn(false)"
                                            class="fi-btn fi-btn-color-success fi-btn-size-md fi-color-success flex-1"
                                        >
                                            ✓ Confirm Check In
                                        </button>
                                    @endif

                                    <button
                                        wire:click="confirmCheckOut"
                                        wire:confirm="Record a check-out for {{ $camperData['full_name'] }}?"
                                        class="fi-btn fi-btn-color-warning fi-btn-size-md fi-color-warning"
                                    >
                                        Check Out
                                    </button>

                                    <button wire:click="resetScan" class="fi-btn fi-btn-color-gray fi-btn-size-md fi-color-gray">
                                        Reset
                                    </button>
                                </div>
                            @else
                                {{-- Security role — view only, no action buttons --}}
                                <button wire:click="resetScan" class="fi-btn fi-btn-color-gray fi-btn-size-md fi-color-gray w-full">
                                    Scan Next
                                </button>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- ── jsQR scanner script ──────────────────────────────────────────────── --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
    <script>
        let videoStream = null;
        let scanInterval = null;

        async function startCamera() {
            const video  = document.getElementById('qr-video');
            const canvas = document.getElementById('qr-canvas');

            try {
                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' }
                });
                video.srcObject = videoStream;
                video.play();

                scanInterval = setInterval(() => {
                    if (video.readyState !== video.HAVE_ENOUGH_DATA) return;

                    canvas.width  = video.videoWidth;
                    canvas.height = video.videoHeight;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code      = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: 'dontInvert',
                    });

                    if (code) {
                        stopCamera();
                        // Emit to Livewire
                        Livewire.dispatch('qr-scanned', { code: code.data });
                    }
                }, 200); // Check every 200ms

            } catch (err) {
                console.error('Camera error:', err);
                alert('Could not access camera. Please check permissions or use manual search.');
            }
        }

        function stopCamera() {
            clearInterval(scanInterval);
            if (videoStream) {
                videoStream.getTracks().forEach(t => t.stop());
                videoStream = null;
            }
        }

        // Auto-restart camera when Livewire resets scan state
        document.addEventListener('livewire:navigated', () => stopCamera());
        Livewire.on('scan-reset', () => startCamera());
    </script>
    @endpush

</x-filament-panels::page>
