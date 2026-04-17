<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Complete Registration &mdash; {{ setting('camp_name', 'Ogun Youth Camp') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#1B3A6B',gold:'#C9A94D',steel:'#2E75B6'}}}}</script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-50">

<nav class="bg-navy py-3 px-4">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="{{ route('home') }}" class="text-white font-bold text-sm">&#8592; Camp Home</a>
        <span class="text-gold text-sm font-semibold">{{ setting('camp_name', 'Ogun Youth Camp') }}</span>
    </div>
</nav>

{{-- Alpine component functions defined before DOM so they are available on init --}}
<script>
    // ── Club ranks from DB (passed from controller) ───────────────────────────────
    const CLUB_RANKS = @json($clubRanks);

    // ── Age/category computation ──────────────────────────────────────────────────
    function computeCategory(dob) {
        if (!dob) return null;
        const today = new Date();
        const birth = new Date(dob);
        let age = today.getFullYear() - birth.getFullYear();
        const m = today.getMonth() - birth.getMonth();
        if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) age--;
        if (age < 6)               return null;
        if (age >= 6 && age <= 9)  return 'adventurer';
        if (age >= 10 && age <= 15) return 'pathfinder';
        return 'senior_youth';
    }

    function categoryLabel(cat) {
        return { adventurer: 'Adventurer', pathfinder: 'Pathfinder', senior_youth: 'Senior Youth' }[cat] ?? '';
    }

    // ── Wizard ────────────────────────────────────────────────────────────────────
    function wizard() {
        return {
            // Steps: 1=Personal+Church, 2=Parent/Guardian, 3=Health, 4=Review
            step: 1,
            totalSteps: 4,
            labels: ['Personal & Church Details', 'Parent / Guardian', 'Health Information', 'Review & Submit'],

            // DOB / category reactive state
            dob: '{{ old("date_of_birth", "") }}',
            get age() {
                if (!this.dob) return null;
                const today = new Date(), birth = new Date(this.dob);
                let a = today.getFullYear() - birth.getFullYear();
                const m = today.getMonth() - birth.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) a--;
                return a;
            },
            get category() { return computeCategory(this.dob); },
            get seniorYouthGroup() {
                // Ambassador: 16-21, Young Adults: 22+
                if (this.category !== 'senior_youth' || this.age === null) return null;
                return this.age <= 21 ? 'Ambassador' : 'Young Adults';
            },
            get categoryDisplay() {
                if (!this.dob) return '';
                const cat = this.category;
                if (!cat) return '<span style="color:#DC2626">&#9888; Age does not meet camp requirements (minimum age: 6).</span>';
                const label = categoryLabel(cat);
                if (cat === 'senior_youth') {
                    const group = this.seniorYouthGroup;
                    return `You will be registered as <strong>Senior Youth &mdash; ${group}</strong> (age ${this.age}).`;
                }
                //return `You will be registered as a <strong>${label}</strong> (age ${this.age}).`;
                return `You will be registered as a <strong>${label}</strong>.`;
            },
            get needsParent() {
                const cat = this.category;
                return cat === 'adventurer' || cat === 'pathfinder';
            },
            get availableRanks() {
                if (this.category === 'senior_youth') return [];  // auto-set from age
                return CLUB_RANKS[this.category] ?? [];
            },

            // Photo preview
            photoPreview: null,
            handlePhoto(event) {
                const file = event.target.files[0];
                if (!file) { this.photoPreview = null; return; }
                const reader = new FileReader();
                reader.onload = e => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            },
            removePhoto() {
                this.photoPreview = null;
                document.getElementById('photo-input').value = '';
            },

            // Health toggle
            noHealthIssues: false,

            // Navigation
            stepLabel() { return this.labels[this.step - 1] ?? ''; },
            next() {
                // Skip parent step if senior youth
                if (this.step === 1 && !this.needsParent) {
                    this.step = 3;
                } else if (this.step < this.totalSteps) {
                    this.step++;
                }
                window.scrollTo(0, 0);
            },
            prev() {
                // Skip parent step backwards if senior youth
                if (this.step === 3 && !this.needsParent) {
                    this.step = 1;
                } else if (this.step > 1) {
                    this.step--;
                }
                window.scrollTo(0, 0);
            },
        };
    }

    // ── Cascading district/church dropdown ────────────────────────────────────────
    function districtChurch(selectedDistrictId) {
        return {
            districtId: selectedDistrictId || '',
            churches: [],
            async loadChurches() {
                if (!this.districtId) { this.churches = []; return; }
                const res = await fetch('/api/churches?district_id=' + this.districtId);
                this.churches = await res.json();
            },
            init() { if (this.districtId) this.loadChurches(); }
        };
    }
</script>

<div class="min-h-screen bg-gray-50 py-8 px-4">
    <div class="max-w-2xl mx-auto" x-data="wizard()">

        <div class="text-center mb-6">
            <h1 class="text-xl font-bold text-navy">Complete Your Registration</h1>
            <p class="text-sm text-gray-500 mt-1">
                Code: <span class="font-mono font-bold text-navy">{{ $code }}</span>
            </p>
        </div>

        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="font-semibold text-red-700 text-sm mb-2">Please correct the following:</p>
                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Required fields note --}}
        <p class="text-xs text-gray-400 mb-4">Fields marked <span class="text-red-500 font-bold">*</span> are compulsory.</p>

        {{-- Progress bar --}}
        <div class="mb-6">
            <div class="flex justify-between text-xs text-gray-400 mb-2">
                <span>Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                <span x-text="stepLabel()"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2">
                <div class="bg-navy h-2 rounded-full transition-all duration-300"
                     :style="'width:' + (step / totalSteps * 100) + '%'"></div>
            </div>
        </div>

        <form method="POST" action="{{ route('registration.submit-web') }}"
              enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="code" value="{{ $code }}" />

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- STEP 1 — Personal Details + Church & Ministry (merged)          --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 1" class="space-y-6">

                {{-- Payment summary (read-only) --}}
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h2 class="font-bold text-navy text-lg mb-4">Your Payment Details</h2>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach([
                            ['Full Name',      $prefill['prefill_name']],
                            ['Phone Number',   $prefill['prefill_phone']],
                            ['Amount Paid',    '&#8358;' . number_format((float)($prefill['amount_paid'] ?? 0))],
                            ['Payment Method', $prefill['payment_type']],
                        ] as [$label, $value])
                            <div class="bg-gray-50 rounded-xl p-3">
                                <p class="text-xs text-gray-400 uppercase tracking-wide mb-0.5">{{ $label }}</p>
                                <p class="font-semibold text-gray-800 text-sm">{!! $value !!}</p>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                    <h2 class="font-bold text-navy text-lg">Personal Information</h2>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="date_of_birth"
                            id="date_of_birth"
                            value="{{ old('date_of_birth') }}"
                            max="{{ now()->subYears(6)->format('Y-m-d') }}"
                            min="{{ now()->subYears(100)->format('Y-m-d') }}"
                            required
                            x-model="dob"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                               focus:outline-none focus:ring-2 focus:ring-navy
                               @error('date_of_birth') border-red-400 @enderror"
                        />
                        <p class="text-xs text-gray-400 mt-1">You can type the date directly or use the calendar picker.</p>
                        {{-- Live category display --}}
                        <p class="text-sm mt-2 font-medium text-navy"
                           x-show="dob" x-html="categoryDisplay"></p>
                        @error('date_of_birth')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-6">
                            @foreach(['male' => 'Male', 'female' => 'Female'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $val }}"
                                           {{ old('gender') === $val ? 'checked' : '' }} required
                                           class="text-navy focus:ring-navy" />
                                    <span class="text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('gender')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Passport Photo with preview --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Passport Photo <span class="text-red-500">*</span>
                        </label>

                        {{-- Preview area --}}
                        <div x-show="photoPreview" class="mb-3">
                            <div class="relative inline-block">
                                <img :src="photoPreview" alt="Photo preview"
                                     class="w-32 h-32 object-cover rounded-xl border-2 border-navy shadow-sm" />
                                <button type="button" @click="removePhoto()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full
                                           text-xs flex items-center justify-center hover:bg-red-600 shadow">
                                    &#10005;
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Looking good! <button type="button" @click="$refs.photoInput.click()"
                                                      class="text-navy underline hover:no-underline">Change photo</button>
                            </p>
                        </div>

                        {{-- Upload button --}}
                        <div x-show="!photoPreview"
                             class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center
                                hover:border-navy transition cursor-pointer"
                             @click="$refs.photoInput.click()">
                            <div class="text-3xl mb-2">&#128247;</div>
                            <p class="text-sm font-medium text-gray-700">Click to upload photo</p>
                            <p class="text-xs text-gray-400 mt-1">JPG or PNG &middot; max 2MB &middot; will appear on ID card</p>
                        </div>

                        <input type="file"
                               name="photo"
                               id="photo-input"
                               x-ref="photoInput"
                               accept="image/jpeg,image/png,image/webp"
                               required
                               class="hidden"
                               @change="handlePhoto($event)"
                        />
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
                <div class="bg-white rounded-2xl shadow-sm p-6 space-y-5"
                     x-data="districtChurch('{{ old('district_id') }}')">
                    <h2 class="font-bold text-navy text-lg">Church &amp; Ministry</h2>

                    {{-- District --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            District <span class="text-red-500">*</span>
                        </label>
                        <select x-model="districtId" @change="loadChurches()" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select District —</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Church --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Church <span class="text-red-500">*</span>
                        </label>
                        <select name="church_id" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy
                                   @error('church_id') border-red-400 @enderror">
                            <option value="">— Select District first —</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name"
                                        :selected="church.id == {{ (int) old('church_id', 0) }}"></option>
                            </template>
                        </select>
                        @error('church_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Ministry (auto-set from DOB) --}}
                    <div x-show="$root.category && $root.category !== 'senior_youth'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ministry / Club</label>
                        <input type="text" name="ministry"
                               :value="$root.category === 'adventurer' ? 'Adventurers' :
                                   $root.category === 'pathfinder'  ? 'Pathfinders'  : ''"
                               readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600" />
                        <p class="text-xs text-gray-400 mt-1">Automatically set based on date of birth.</p>
                    </div>

                    {{-- Senior Youth Group (auto-set from age) --}}
                    <div x-show="$root.category === 'senior_youth'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Senior Youth Group</label>
                        <input type="text" name="ministry" value="Senior Youth" readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600" />
                    </div>
                    <div x-show="$root.category === 'senior_youth'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Group / Rank</label>
                        <input type="text" name="club_rank"
                               :value="$root.seniorYouthGroup"
                               readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600" />
                        <p class="text-xs text-gray-400 mt-1">
                            Ambassador (ages 16–21) &bull; Young Adults (ages 22+) &mdash; set automatically from date of birth.
                        </p>
                    </div>

                    {{-- Club Rank dropdown (Adventurers & Pathfinders only) --}}
                    <div x-show="$root.availableRanks.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Club Rank / Class</label>
                        <select name="club_rank"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                                   focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select Rank —</option>
                            <template x-for="rank in $root.availableRanks" :key="rank">
                                <option :value="rank" x-text="rank"
                                        :selected="rank === '{{ old('club_rank') }}'"></option>
                            </template>
                        </select>
                    </div>

                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- STEP 2 — Parent / Guardian (Adventurers & Pathfinders only)     --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 2" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-navy text-lg">Parent / Guardian Information</h2>
                <p class="text-xs text-gray-500">Required for Adventurers and Pathfinders (ages 6&ndash;15).</p>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="parent_name" value="{{ old('parent_name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy
                              @error('parent_name') border-red-400 @enderror" />
                    @error('parent_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Relationship <span class="text-red-500">*</span>
                    </label>
                    <select name="parent_relationship"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white
                               focus:outline-none focus:ring-2 focus:ring-navy">
                        <option value="">— Select —</option>
                        @foreach(['Mother','Father','Guardian','Uncle','Aunt','Grandparent','Pastor'] as $rel)
                            <option value="{{ $rel }}" {{ old('parent_relationship') === $rel ? 'selected' : '' }}>
                                {{ $rel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="parent_phone" value="{{ old('parent_phone') }}"
                           placeholder="08012345678"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy
                              @error('parent_phone') border-red-400 @enderror" />
                    @error('parent_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-gray-400 text-xs">(optional)</span>
                    </label>
                    <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                              focus:outline-none focus:ring-2 focus:ring-navy" />
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- STEP 3 — Health & Medical (fully optional)                      --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 3" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-navy text-lg">Health &amp; Medical Information</h2>
                <p class="text-xs text-gray-500">
                    This section is optional. Providing accurate health information helps us keep your child safe at camp.
                </p>

                {{-- Skip toggle --}}
                <label class="flex items-start gap-3 cursor-pointer bg-green-50 border border-green-200 rounded-xl p-4">
                    <input type="checkbox" x-model="noHealthIssues"
                           class="mt-0.5 text-green-600 focus:ring-green-500 rounded" />
                    <div>
                        <p class="text-sm font-medium text-gray-800">
                            I have no known medical conditions, medications, or allergies.
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Tick this box to skip the health section. You can still leave all fields blank.
                        </p>
                    </div>
                </label>

                {{-- Health fields (hidden when box is ticked) --}}
                <div x-show="!noHealthIssues" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Medical Conditions</label>
                        <textarea name="medical_conditions" rows="2"
                                  placeholder="e.g. Asthma, Diabetes, Epilepsy, Sickle Cell"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-navy">{{ old('medical_conditions') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Medications</label>
                        <textarea name="medications" rows="2"
                                  placeholder="Medication name and dosage"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-navy">{{ old('medications') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                        <textarea name="allergies" rows="2"
                                  placeholder="e.g. Penicillin, Peanuts, Dust, Latex"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2 focus:ring-navy">{{ old('allergies') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ════════════════════════════════════════════════════════════════ --}}
            {{-- STEP 4 — Review & Submit                                        --}}
            {{-- ════════════════════════════════════════════════════════════════ --}}
            <div x-show="step === 4" class="bg-white rounded-2xl shadow-sm p-6 space-y-5">
                <h2 class="font-bold text-navy text-lg">Review &amp; Submit</h2>
                <p class="text-sm text-gray-600">
                    Please review your information. Once submitted, your registration cannot be changed.
                </p>

                <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                    After submitting, your <strong>ID card</strong> and
                    (if under 18) <strong>consent form</strong> will be generated and available for download.
                </div>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="confirm_accuracy" required class="mt-1 text-navy" />
                    <span class="text-sm text-gray-700">
                    I confirm that all information I have provided is accurate and complete,
                    and that I understand the camp rules and registration terms.
                </span>
                </label>

                <button type="submit"
                        class="w-full bg-green-600 text-white font-bold py-4 rounded-xl text-lg
                           hover:bg-green-700 transition">
                    Submit Registration &#10003;
                </button>
            </div>

            {{-- Navigation buttons --}}
            <div class="flex gap-3 mt-4">
                <button type="button" @click="prev()" x-show="step > 1"
                        class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl
                           hover:bg-gray-100 transition">
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
