<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Complete Registration &mdash; {{ setting('camp_name','Ogun Youth Camp') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#0B2D6B',gold:'#C9A94D',steel:'#2E75B6'}}}}</script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50">

<nav class="bg-navy py-3 px-4">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-white font-bold text-sm">&#8592; Camp Home</a>
        <span class="text-gold text-sm font-semibold">{{ setting('camp_name','Ogun Youth Camp') }}</span>
    </div>
</nav>

<script>
    const CLUB_RANKS = @json($clubRanks);

    function wizard() {
        const prefillCategory = '{{ $prefill["prefill_category"] ?? "" }}';
        return {
            step: 1,
            totalSteps: 4,
            labels: ['Personal & Church Details','Parent / Guardian','Health Information','Review & Submit'],
            validationError: '',
            category: prefillCategory,
            lockedChurchId: '{{ $prefill["prefill_church_id"] ?? "" }}',
            districtId: '{{ old("district_id","") }}',
            churches: [],
            selectedClubRank: '{{ old("club_rank","") }}',
            seniorYouthRank: '{{ old("club_rank","") }}',

            get computedClubRank() {
                if (this.category === 'senior_youth') return this.seniorYouthRank;
                return this.selectedClubRank;
            },

            get categoryLabel() {
                return {adventurer:'Adventurer',pathfinder:'Pathfinder',senior_youth:'Senior Youth'}[this.category] ?? '';
            },
            get needsParent() {
                return this.category === 'adventurer' || this.category === 'pathfinder';
            },
            get availableRanks() {
                if (!this.category || this.category === 'senior_youth') return [];
                return CLUB_RANKS[this.category] ?? [];
            },
            get seniorYouthGroup() {
                return this.category === 'senior_youth' ? '{{ old("club_rank","") }}' || '' : '';
            },

            // ── Photo / Camera state ──────────────────────────────────────────
            photoPreview:    null,
            capturedDataUrl: null,
            cameraOpen:      false,
            cameraAvailable: false,
            hasFrontAndBack: false,
            _stream:         null,
            _facingMode:     'user',

            handleFileInput(event) {
                const file = event.target.files[0];
                if (!file) return;
                this.capturedDataUrl = null;
                const reader = new FileReader();
                reader.onload = e => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            },
            handlePhoto(event) { this.handleFileInput(event); },
            clearPhoto() {
                this.photoPreview    = null;
                this.capturedDataUrl = null;
                this.cameraOpen      = false;
                const inp = document.getElementById('photo-input');
                if (inp) inp.value = '';
                if (this._stream) { this._stream.getTracks().forEach(t => t.stop()); this._stream = null; }
            },
            removePhoto() { this.clearPhoto(); },
            async openCamera() {
                this.cameraOpen = true;
                await this.$nextTick();

                // Stop any existing stream first
                if (this._stream) {
                    this._stream.getTracks().forEach(t => t.stop());
                    this._stream = null;
                }

                // Fallback chain: exact facingMode → ideal facingMode → any camera
                // 'exact' is required to actually force the rear camera on most phones;
                // without it, browsers often ignore facingMode and stay on front camera.
                const attempts = [
                    { video: { facingMode: { exact: this._facingMode }, width: { ideal: 640 }, height: { ideal: 640 } } },
                    { video: { facingMode: this._facingMode,            width: { ideal: 640 }, height: { ideal: 640 } } },
                    { video: true },
                ];

                let opened = false;
                for (const attempt of attempts) {
                    try {
                        this._stream = await navigator.mediaDevices.getUserMedia({ ...attempt, audio: false });
                        this.$refs.videoEl.srcObject = this._stream;
                        opened = true;
                        break;
                    } catch (e) { /* try next */ }
                }

                if (!opened) {
                    this.cameraOpen      = false;
                    this.cameraAvailable = false;
                    alert('Camera unavailable. Please use the file upload option instead.');
                    return;
                }

                // Re-enumerate after permission granted — labels now populated
                navigator.mediaDevices.enumerateDevices().then(devices => {
                    this.hasFrontAndBack = devices.filter(d => d.kind === 'videoinput').length > 1;
                }).catch(() => {});
            },
            closeCamera() {
                this.cameraOpen = false;
                if (this._stream) { this._stream.getTracks().forEach(t => t.stop()); this._stream = null; }
            },
            capture() {
                const video = this.$refs.videoEl, canvas = this.$refs.canvasEl;
                const size  = 480;
                canvas.width = canvas.height = size;
                const ctx = canvas.getContext('2d');
                const vw = video.videoWidth, vh = video.videoHeight;
                const side = Math.min(vw, vh);
                ctx.drawImage(video, (vw-side)/2, (vh-side)/2, side, side, 0, 0, size, size);
                const dataUrl        = canvas.toDataURL('image/jpeg', 0.92);
                this.capturedDataUrl = dataUrl;
                this.photoPreview    = dataUrl;
                if (this.$refs.photoData) this.$refs.photoData.value = dataUrl;
                this.closeCamera();
            },
            async flipCamera() {
                this._facingMode = this._facingMode === 'user' ? 'environment' : 'user';
                this.closeCamera();
                await this.$nextTick();
                await this.openCamera();
            },
            initCamera() {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    this.cameraAvailable = true;
                    if (navigator.mediaDevices.enumerateDevices) {
                        navigator.mediaDevices.enumerateDevices().then(devices => {
                            this.hasFrontAndBack = devices.filter(d => d.kind === 'videoinput').length > 1;
                        }).catch(() => {});
                    }
                }
            },



            async loadChurches() {
                if (!this.districtId) { this.churches = []; return; }
                const res = await fetch('/api/churches?district_id=' + this.districtId);
                this.churches = await res.json();
            },

            noHealthIssues: false,

            validateStep1() {
                this.validationError = '';
                if (!document.querySelector('input[name="gender"]:checked')) {
                    this.validationError = 'Please select your gender.'; return false;
                }
                if (!document.querySelector('input[name="tshirt_size"]:checked')) {
                    this.validationError = 'Please select your T-shirt size.'; return false;
                }
                if (!this.photoPreview && !this.capturedDataUrl) {
                    this.validationError = 'Please upload or capture a passport photo.'; return false;
                }
                // Only validate district/church if church is not locked by the code
                if (!this.lockedChurchId) {
                    if (!this.districtId) {
                        this.validationError = 'Please select your district.'; return false;
                    }
                    if (!document.querySelector('select[name="church_id"]')?.value) {
                        this.validationError = 'Please select your church.'; return false;
                    }
                }
                if (this.availableRanks.length > 0 && !this.selectedClubRank) {
                    this.validationError = 'Please select your club rank / class.'; return false;
                }
                if (this.category === 'senior_youth') {
                    if (!this.seniorYouthRank) { this.validationError = 'Please select your Senior Youth group.'; return false; }
                }
                return true;
            },
            validateStep2() {
                this.validationError = '';
                if (!this.needsParent) return true;
                if (!document.querySelector('input[name="parent_name"]')?.value.trim()) {
                    this.validationError = 'Please enter the parent/guardian full name.'; return false;
                }
                if (!document.querySelector('input[name="parent_phone"]')?.value.trim()) {
                    this.validationError = 'Please enter the parent/guardian phone number.'; return false;
                }
                return true;
            },

            stepLabel() { return this.labels[this.step - 1] ?? ''; },
            next() {
                const validators = {1:()=>this.validateStep1(), 2:()=>this.validateStep2()};
                if (validators[this.step] && !validators[this.step]()) { window.scrollTo(0,0); return; }
                this.validationError = '';
                if (this.step === 1 && !this.needsParent) { this.step = 3; }
                else if (this.step < this.totalSteps) { this.step++; }
                window.scrollTo(0,0);
            },
            prev() {
                this.validationError = '';
                if (this.step === 3 && !this.needsParent) { this.step = 1; }
                else if (this.step > 1) { this.step--; }
                window.scrollTo(0,0);
            },
            init() { if (this.districtId) this.loadChurches(); this.initCamera(); },
        };
    }

    /* Camera methods merged into main wizard component above */

    /* ── T-Shirt size pill selection ── */
    function selectTshirt(label) {
        document.querySelectorAll('#tshirt-grid .tshirt-pill').forEach(function(pill) {
            pill.classList.remove('bg-navy', 'text-white', 'border-navy');
            pill.classList.add('border-gray-200', 'text-gray-600');
        });
        var pill = label.querySelector('.tshirt-pill');
        if (pill) {
            pill.classList.add('bg-navy', 'text-white', 'border-navy');
            pill.classList.remove('border-gray-200', 'text-gray-600');
        }
        var radio = label.querySelector('input[type=radio]');
        if (radio) radio.checked = true;
    }
    // Restore on page load (after validation failure with old() value)
    document.addEventListener('DOMContentLoaded', function() {
        var checked = document.querySelector('#tshirt-grid input[type=radio]:checked');
        if (checked) selectTshirt(checked.closest('label'));
    });
</script>

<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-2xl mx-auto" x-data="wizard()">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-navy">Complete Your Registration</h1>
            <p class="text-sm text-gray-500 mt-1">Code: <span class="font-mono font-bold text-navy">{{ $code }}</span></p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="font-semibold text-red-700 text-sm mb-2">Please correct the following:</p>
                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <p class="text-xs text-gray-400 mb-3">Fields marked <span class="text-red-500 font-bold">*</span> are compulsory.</p>

        {{-- Front-end validation error --}}
        <div x-show="validationError" style="display:none"
             class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm flex items-start gap-2">
            <span class="font-bold flex-shrink-0">&#9888;</span>
            <span x-text="validationError"></span>
        </div>

        {{-- Progress bar --}}
        <div class="mb-6">
            <div class="flex justify-between text-xs text-gray-400 mb-2">
                <span>Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                <span x-text="stepLabel()"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2">
                <div class="bg-navy h-2 rounded-full transition-all duration-300"
                     :style="'width:' + (step/totalSteps*100) + '%'"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('registration.submit-web') }}"
              enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="code" value="{{ $code }}"/>
            <input type="hidden" name="category_locked" value="{{ $prefill['prefill_category'] ?? '' }}"/>

            {{-- ══ STEP 1 ══ --}}
            <div x-show="step === 1" class="space-y-5">

                {{-- Payment & Category Summary --}}
                <div class="bg-white rounded-2xl shadow-sm p-5">
                    <h2 class="font-bold text-navy text-base mb-3">Your Registration Details</h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['Full Name',      $prefill['prefill_name']],
                            ['Phone',          $prefill['prefill_phone']],
                            ['Department',     match($prefill['prefill_category'] ?? '') {
                                'adventurer'   => 'Adventurer Club',
                                'pathfinder'   => 'Pathfinder Club',
                                'senior_youth' => 'Senior Youth (SYL)',
                                default        => '—'
                            }],
                            ['Amount Paid',    '&#8358;'.number_format((float)($prefill['amount_paid']??0))],
                        ] as [$l,$v])
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">{{ $l }}</p>
                                <p class="font-semibold text-sm text-gray-800">{!! $v !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                    <h2 class="font-bold text-navy text-base">Personal Information</h2>

                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="flex gap-6">
                            @foreach(['male'=>'Male','female'=>'Female'] as $v=>$l)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $v }}"
                                           {{ old('gender')===$v ? 'checked' : '' }} class="text-navy"/>
                                    <span class="text-sm">{{ $l }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('gender')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- T-Shirt Size --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            T-Shirt Size <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-4 gap-2 sm:grid-cols-7" id="tshirt-grid">
                            @foreach(['XS','S','M','L','XL','XXL','XXXL'] as $size)
                                <label class="cursor-pointer" onclick="selectTshirt(this)">
                                    <input type="radio" name="tshirt_size" value="{{ $size }}"
                                           {{ old('tshirt_size')===$size ? 'checked' : '' }} required
                                           class="sr-only"/>
                                    <span class="tshirt-pill flex items-center justify-center h-10 rounded-xl
                                             border-2 font-semibold text-sm transition-all
                                             {{ old('tshirt_size')===$size
                                                ? 'bg-navy text-white border-navy'
                                                : 'border-gray-200 text-gray-600 hover:border-navy' }}">
                                    {{ $size }}
                                </span>
                                </label>
                            @endforeach
                        </div>
                        @error('tshirt_size')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Passport Photo with Upload + Live Camera --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Passport Photo <span class="text-red-500">*</span>
                        </label>

                        {{-- Preview --}}
                        <div x-show="photoPreview" class="mb-3">
                            <div class="relative inline-block">
                                <img :src="photoPreview"
                                     class="w-28 h-28 object-cover rounded-xl border-2 border-navy shadow-sm"/>
                                <button type="button" @click="clearPhoto()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white
                                           rounded-full text-xs flex items-center justify-center hover:bg-red-600">
                                    &#10005;
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <button type="button" @click="$refs.photoInput.click()"
                                        class="text-navy underline mr-2">Change photo</button>
                                <template x-if="cameraAvailable">
                                    <button type="button" @click="openCamera()"
                                            class="text-navy underline">Retake with camera</button>
                                </template>
                            </p>
                        </div>

                        {{-- Upload + Camera chooser (shown when no preview) --}}
                        <div x-show="!photoPreview && !cameraOpen" class="space-y-2">
                            {{-- Upload drop zone --}}
                            <div @click="$refs.photoInput.click()"
                                 class="border-2 border-dashed border-gray-300 rounded-xl p-5 text-center
                                    cursor-pointer hover:border-navy transition">
                                <div class="text-3xl mb-1">&#128247;</div>
                                <p class="text-sm font-medium text-gray-700">Click to upload photo</p>
                                <p class="text-xs text-gray-400 mt-0.5">JPG, PNG or WEBP &middot; max 2 MB &middot; appears on ID card</p>
                            </div>

                            {{-- Camera button --}}
                            <template x-if="cameraAvailable">
                                <button type="button" @click="openCamera()"
                                        class="w-full flex items-center justify-center gap-2 border border-navy
                                           text-navy rounded-xl py-3 text-sm font-medium hover:bg-navy
                                           hover:text-white transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    Use live camera
                                </button>
                            </template>
                        </div>

                        {{-- Live camera panel --}}
                        <div x-show="cameraOpen" x-cloak class="space-y-3">
                            <div class="relative rounded-xl overflow-hidden bg-black aspect-square max-w-xs mx-auto">
                                <video x-ref="videoEl" autoplay playsinline muted
                                       class="w-full h-full object-cover"></video>
                                {{-- Mirror overlay guide --}}
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-24 h-28 border-2 border-white border-opacity-50 rounded-lg"></div>
                                </div>
                            </div>
                            <canvas x-ref="canvasEl" class="hidden"></canvas>
                            <div class="flex gap-2 max-w-xs mx-auto">
                                <button type="button" @click="capture()"
                                        class="flex-1 bg-navy text-white rounded-xl py-3 text-sm font-semibold
                                           hover:opacity-90 transition flex items-center justify-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="3"/><path d="M20 7h-3.5l-1-2h-7l-1 2H4a2 2 0 00-2 2v10a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/>
                                    </svg>
                                    Capture
                                </button>
                                <button type="button" @click="closeCamera()"
                                        class="px-4 border border-gray-300 rounded-xl text-sm text-gray-600
                                           hover:border-gray-400 transition">
                                    Cancel
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 text-center">
                                <button type="button" @click="flipCamera()" class="underline text-navy text-sm font-medium">
                                    ↻ Flip camera
                                </button>
                            </p>
                        </div>

                        {{-- Hidden file input (for upload mode) --}}
                        <input type="file" name="photo" id="photo-input" x-ref="photoInput"
                               accept="image/jpeg,image/png,image/webp" class="hidden"
                               @change="handleFileInput($event)"/>
                        {{-- Hidden input carrying the captured image data for form submission --}}
                        <input type="hidden" name="photo_data_url" x-ref="photoData" x-model="capturedDataUrl"/>

                        @error('photo')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Home Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                        <textarea name="home_address" rows="2" placeholder="Optional"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-navy">{{ old('home_address') }}</textarea>
                    </div>
                </div>

                {{-- Church & Ministry --}}
                <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                    <h2 class="font-bold text-navy text-base">Church &amp; Ministry</h2>

                    @php
                        $lockedChurch = !empty($prefill['prefill_church_id'])
                            ? \App\Models\Church::with('district')->find($prefill['prefill_church_id'])
                            : null;
                    @endphp

                    @if($lockedChurch)
                        {{-- Church is locked from the batch registration code --}}
                        <input type="hidden" name="church_id" value="{{ $lockedChurch->id }}"/>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                            <p class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700">
                                {{ $lockedChurch->district?->name ?? '—' }}
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Church</label>
                            <p class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700">
                                {{ $lockedChurch->name }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">&#128274; Your church is set from your registration code and cannot be changed.</p>
                        </div>
                    @else
                        {{-- Free selection for individually-issued codes --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">District <span class="text-red-500">*</span></label>
                            <select x-model="districtId" @change="loadChurches()"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                       focus:outline-none focus:ring-2 focus:ring-navy">
                                <option value="">— Select District —</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id')==$district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Church <span class="text-red-500">*</span></label>
                            <select name="church_id"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                       focus:outline-none focus:ring-2 focus:ring-navy
                                       @error('church_id') border-red-400 @enderror">
                                <option value="">— Select District first —</option>
                                <template x-for="church in churches" :key="church.id">
                                    <option :value="church.id" x-text="church.name"
                                            :selected="church.id == {{ (int) old('church_id',0) }}"></option>
                                </template>
                            </select>
                            @error('church_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    @endif

                    {{-- Ministry (auto from category — read only) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ministry / Club</label>
                        <input type="text" name="ministry"
                               :value="category === 'adventurer' ? 'Adventurers' :
                                   category === 'pathfinder'  ? 'Pathfinders'  : 'Senior Youth'"
                               readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                                  bg-gray-50 text-gray-600"/>
                    </div>

                    {{-- Single hidden input carries the actual club_rank value --}}
                    <input type="hidden" name="club_rank" :value="computedClubRank"/>

                    {{-- Club Rank — Adventurers & Pathfinders --}}
                    <div x-show="availableRanks.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Club Rank / Class <span class="text-red-500">*</span>
                        </label>
                        <select x-model="selectedClubRank"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select your rank —</option>
                            <template x-for="rank in availableRanks" :key="rank">
                                <option :value="rank" x-text="rank"
                                        :selected="rank === selectedClubRank"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Senior Youth Group --}}
                    <div x-show="category === 'senior_youth'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Senior Youth Group <span class="text-red-500">*</span>
                        </label>
                        <select x-model="seniorYouthRank"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select —</option>
                            <option value="Ambassador" {{ old('club_rank')==='Ambassador' ? 'selected' : '' }}>
                                Ambassador (Ages 16–21)
                            </option>
                            <option value="Young Adults" {{ old('club_rank')==='Young Adults' ? 'selected' : '' }}>
                                Young Adults (Ages 22+)
                            </option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Ambassador (16–21 years) &bull; Young Adults (22 years and above)</p>
                    </div>
                </div>
            </div>

            {{-- ══ STEP 2: Parent / Guardian ══ --}}
            <div x-show="step === 2" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-navy text-base">Parent / Guardian Information</h2>
                <p class="text-xs text-gray-500">Required for Adventurers and Pathfinders (ages 6–15).</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="parent_name" value="{{ old('parent_name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy @error('parent_name') border-red-400 @enderror"/>
                    @error('parent_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship <span class="text-red-500">*</span></label>
                    <select name="parent_relationship"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-navy">
                        <option value="">— Select —</option>
                        @foreach(['Mother','Father','Guardian','Uncle','Aunt','Grandparent','Pastor'] as $rel)
                            <option value="{{ $rel }}" {{ old('parent_relationship')===$rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                    <input type="tel" name="parent_phone" value="{{ old('parent_phone') }}"
                           placeholder="08012345678"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy @error('parent_phone') border-red-400 @enderror"/>
                    @error('parent_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 text-xs">(optional)</span></label>
                    <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy"/>
                </div>
            </div>

            {{-- ══ STEP 3: Health ══ --}}
            <div x-show="step === 3" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-navy text-base">Health &amp; Medical Information</h2>
                <p class="text-xs text-gray-500">Optional — helps us keep your child safe at camp.</p>
                <label class="flex items-start gap-3 cursor-pointer bg-green-50 border border-green-200 rounded-xl p-4">
                    <input type="checkbox" x-model="noHealthIssues" class="mt-0.5 text-green-600 rounded"/>
                    <div>
                        <p class="text-sm font-medium text-gray-800">No known medical conditions, medications, or allergies.</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tick to skip the health section.</p>
                    </div>
                </label>
                <div x-show="!noHealthIssues" class="space-y-4">
                    @foreach([
                        ['medical_conditions','Medical Conditions','e.g. Asthma, Diabetes, Epilepsy, Sickle Cell'],
                        ['medications','Current Medications','Medication name and dosage'],
                        ['allergies','Allergies','e.g. Penicillin, Peanuts, Dust'],
                    ] as [$name,$label,$ph])
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
                            <textarea name="{{ $name }}" rows="2" placeholder="{{ $ph }}"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-navy">{{ old($name) }}</textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ STEP 4: Review & Submit ══ --}}
            <div x-show="step === 4" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-navy text-base">Review &amp; Submit</h2>
                <p class="text-sm text-gray-600">Please confirm your information is accurate before submitting.</p>
                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                    After submitting, your <strong>ID card</strong> and (if under 18) <strong>consent form</strong>
                    will be generated and available for download.
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="confirm_accuracy" required class="mt-1 text-navy"/>
                    <span class="text-sm text-gray-700">
                    I confirm that all information provided is accurate and complete.
                </span>
                </label>
                <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-4 rounded-xl text-lg
                           hover:bg-green-700 transition">
                    Submit Registration &#10003;
                </button>
            </div>

            {{-- Navigation --}}
            <div class="flex gap-3 mt-4">
                <button type="button" @click="prev()" x-show="step > 1"
                        class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3
                           rounded-xl hover:bg-gray-100 transition">
                    &larr; Back
                </button>
                <button type="button" @click="next()" x-show="step < totalSteps"
                        class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-steel transition">
                    Next &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
