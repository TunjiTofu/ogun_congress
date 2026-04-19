<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Complete Form &mdash; {{ $entry->full_name }}</title>
    <link rel="icon" href="{{ asset('images/favicon.svg') }}" type="image/svg+xml"/>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet"/>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config={theme:{extend:{colors:{navy:'#0B2D6B',gold:'#C9A94D',green:'#064E3B',steel:'#2E75B6'}}}}</script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-green-50">

<nav class="py-3 px-4" style="background:#064E3B">
    <div class="max-w-6xl mx-auto flex items-center justify-between">
        <a href="{{ route('coordinator.portal.dashboard') }}"
           class="text-white font-bold text-sm">&#8592; Back to Dashboard</a>
        <span class="text-sm font-semibold" style="color:#6EE7B7">
            Completing form for: {{ $entry->full_name }}
        </span>
    </div>
</nav>

<script>
    const CLUB_RANKS = @json($clubRanks);
    const PREFILL_CATEGORY = '{{ $prefill["prefill_category"] }}';

    function wizard() {
        return {
            step: 1,
            totalSteps: 4,
            labels: ['Personal Details','Parent / Guardian','Health Information','Review & Submit'],
            validationError: '',
            category: PREFILL_CATEGORY,
            districtId: '{{ auth()->user()->church?->district_id ?? old("district_id","") }}',
            churches: [],
            selectedClubRank: '{{ old("club_rank","") }}',

            get needsParent() {
                return this.category === 'adventurer' || this.category === 'pathfinder';
            },
            get availableRanks() {
                if (!this.category || this.category === 'senior_youth') return [];
                return CLUB_RANKS[this.category] ?? [];
            },

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

            async loadChurches() {
                if (!this.districtId) { this.churches = []; return; }
                const res = await fetch('/api/churches?district_id=' + this.districtId);
                this.churches = await res.json();
            },

            noHealthIssues: false,

            validateStep1() {
                this.validationError = '';
                if (!document.querySelector('input[name="gender"]:checked')) {
                    this.validationError = 'Please select gender.'; return false;
                }
                if (!document.querySelector('select[name="church_id"]')?.value) {
                    this.validationError = 'Please select a church.'; return false;
                }
                if (this.availableRanks.length > 0 && !this.selectedClubRank) {
                    this.validationError = 'Please select a club rank.'; return false;
                }
                if (this.category === 'senior_youth') {
                    if (!document.querySelector('select[name="club_rank"]')?.value) {
                        this.validationError = 'Please select the Senior Youth group.'; return false;
                    }
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
                    this.validationError = 'Please enter the parent/guardian phone.'; return false;
                }
                return true;
            },

            stepLabel() { return this.labels[this.step - 1] ?? ''; },
            next() {
                const v = {1:()=>this.validateStep1(),2:()=>this.validateStep2()};
                if (v[this.step] && !v[this.step]()) { window.scrollTo(0,0); return; }
                this.validationError = '';
                if (this.step === 1 && !this.needsParent) this.step = 3;
                else if (this.step < this.totalSteps) this.step++;
                window.scrollTo(0,0);
            },
            prev() {
                this.validationError = '';
                if (this.step === 3 && !this.needsParent) this.step = 1;
                else if (this.step > 1) this.step--;
                window.scrollTo(0,0);
            },
            init() { if (this.districtId) this.loadChurches(); },
        };
    }
</script>

<div class="min-h-screen py-8 px-4" style="background:#F0FAF6">
    <div class="max-w-2xl mx-auto" x-data="wizard()">

        {{-- Camper summary --}}
        <div class="rounded-2xl p-5 mb-6 text-white" style="background:linear-gradient(135deg,#064E3B,#047857)">
            <p class="text-xs uppercase tracking-widest mb-1" style="color:rgba(110,231,183,0.85)">Completing Registration For</p>
            <h1 class="text-xl font-bold font-serif">{{ $entry->full_name }}</h1>
            <p class="text-sm mt-1" style="color:rgba(255,255,255,0.65)">
                {{ $entry->category->label() }} &bull;
                Code: <span class="font-mono font-bold">{{ $code }}</span> &bull;
                &#8358;{{ number_format($entry->fee) }} paid
            </p>
        </div>

        @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl p-4">
                <p class="font-semibold text-red-700 text-sm mb-2">Please correct the following:</p>
                <ul class="list-disc list-inside text-red-600 text-sm space-y-1">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        {{-- Validation error --}}
        <div x-show="validationError" style="display:none"
             class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-sm">
            <span class="font-bold">&#9888;</span>
            <span x-text="validationError"></span>
        </div>

        <p class="text-xs text-gray-400 mb-3">Fields marked <span class="text-red-500 font-bold">*</span> are compulsory.</p>

        {{-- Progress --}}
        <div class="mb-6">
            <div class="flex justify-between text-xs text-gray-400 mb-2">
                <span>Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                <span x-text="stepLabel()"></span>
            </div>
            <div class="bg-gray-200 rounded-full h-2">
                <div class="h-2 rounded-full transition-all duration-300"
                     style="background:#064E3B"
                     :style="'width:' + (step/totalSteps*100) + '%'"></div>
            </div>
        </div>

        <form method="POST"
              action="{{ route('coordinator.portal.submit', ['batch'=>$batch->id,'entry'=>$entry->id]) }}"
              enctype="multipart/form-data" novalidate>
            @csrf

            {{-- ══ STEP 1: Personal + Church ══ --}}
            <div x-show="step === 1" class="space-y-5">

                <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                    <h2 class="font-bold text-lg" style="color:#064E3B">Personal Information</h2>

                    {{-- Department (locked) --}}
                    <div class="rounded-xl p-3 border" style="background:#F0FDF4;border-color:rgba(16,185,129,0.2)">
                        <p class="text-xs font-bold uppercase tracking-wide mb-0.5" style="color:#065F46">Department (Locked)</p>
                        <p class="font-semibold text-sm text-gray-800">{{ $entry->category->label() }}</p>
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="flex gap-6">
                            @foreach(['male'=>'Male','female'=>'Female'] as $v=>$l)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $v }}"
                                           {{ old('gender')===$v ? 'checked' : '' }} class="text-green-700"/>
                                    <span class="text-sm">{{ $l }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Photo --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Passport Photo</label>
                        <div x-show="photoPreview" class="mb-3">
                            <div class="relative inline-block">
                                <img :src="photoPreview" class="w-28 h-28 object-cover rounded-xl border-2 shadow-sm"
                                     style="border-color:#064E3B"/>
                                <button type="button" @click="removePhoto()"
                                        class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full
                                           text-xs flex items-center justify-center">&#10005;</button>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <button type="button" @click="$refs.photoInput.click()"
                                        class="underline" style="color:#064E3B">Change photo</button>
                            </p>
                        </div>
                        <div x-show="!photoPreview" @click="$refs.photoInput.click()"
                             class="border-2 border-dashed rounded-xl p-5 text-center cursor-pointer transition"
                             style="border-color:#D1FAE5" onmouseover="this.style.borderColor='#064E3B'"
                             onmouseout="this.style.borderColor='#D1FAE5'">
                            <div class="text-2xl mb-1">&#128247;</div>
                            <p class="text-sm font-medium text-gray-700">Click to upload photo</p>
                            <p class="text-xs text-gray-400 mt-0.5">JPG or PNG &middot; max 2MB</p>
                        </div>
                        <input type="file" name="photo" id="photo-input" x-ref="photoInput"
                               accept="image/jpeg,image/png,image/webp" class="hidden"
                               @change="handlePhoto($event)"/>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                        <textarea name="home_address" rows="2" placeholder="Optional"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm
                                     focus:outline-none focus:ring-2"
                                  style="--tw-ring-color:#064E3B">{{ old('home_address') }}</textarea>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                    <h2 class="font-bold text-lg" style="color:#064E3B">Church &amp; Ministry</h2>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District <span class="text-red-500">*</span></label>
                        <select x-model="districtId" @change="loadChurches()"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none">
                            <option value="">— Select District —</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ (auth()->user()->church?->district_id == $district->id || old('district_id') == $district->id) ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Church <span class="text-red-500">*</span></label>
                        <select name="church_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none @error('church_id') border-red-400 @enderror">
                            <option value="">— Select church —</option>
                            <template x-for="church in churches" :key="church.id">
                                <option :value="church.id" x-text="church.name"
                                        :selected="church.id == {{ (int)(auth()->user()->church_id ?? old('church_id', 0)) }}"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Ministry (auto from category) --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ministry / Club</label>
                        <input type="text" name="ministry"
                               :value="category === 'adventurer' ? 'Adventurers' :
                                   category === 'pathfinder'  ? 'Pathfinders'  : 'Senior Youth'"
                               readonly
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm bg-gray-50 text-gray-600"/>
                    </div>

                    {{-- Club rank (Adventurers & Pathfinders) --}}
                    <div x-show="availableRanks.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Club Rank <span class="text-red-500">*</span></label>
                        <select name="club_rank" x-model="selectedClubRank"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none">
                            <option value="">— Select rank —</option>
                            <template x-for="rank in availableRanks" :key="rank">
                                <option :value="rank" x-text="rank" :selected="rank === selectedClubRank"></option>
                            </template>
                        </select>
                    </div>

                    {{-- Senior Youth group --}}
                    <div x-show="category === 'senior_youth'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Senior Youth Group <span class="text-red-500">*</span></label>
                        <select name="club_rank"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none">
                            <option value="">— Select —</option>
                            <option value="Ambassador" {{ old('club_rank')==='Ambassador' ? 'selected' : '' }}>Ambassador (Ages 16–21)</option>
                            <option value="Young Adults" {{ old('club_rank')==='Young Adults' ? 'selected' : '' }}>Young Adults (Ages 22+)</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- ══ STEP 2: Parent / Guardian ══ --}}
            <div x-show="step === 2" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-lg" style="color:#064E3B">Parent / Guardian</h2>
                <p class="text-xs text-gray-500">Required for Adventurers and Pathfinders.</p>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="parent_name" value="{{ old('parent_name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                    <select name="parent_relationship"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none">
                        <option value="">— Select —</option>
                        @foreach(['Mother','Father','Guardian','Uncle','Aunt','Grandparent','Pastor'] as $rel)
                            <option value="{{ $rel }}" {{ old('parent_relationship')===$rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone <span class="text-red-500">*</span></label>
                    <input type="tel" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="08012345678"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email <span class="text-gray-400 text-xs">(optional)</span></label>
                    <input type="email" name="parent_email" value="{{ old('parent_email') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none"/>
                </div>
            </div>

            {{-- ══ STEP 3: Health ══ --}}
            <div x-show="step === 3" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-lg" style="color:#064E3B">Health &amp; Medical</h2>
                <p class="text-xs text-gray-500">Optional — helps keep the camper safe.</p>
                <label class="flex items-start gap-3 cursor-pointer rounded-xl p-4 border"
                       style="background:#F0FDF4;border-color:rgba(16,185,129,0.2)">
                    <input type="checkbox" x-model="noHealthIssues" class="mt-0.5"/>
                    <div>
                        <p class="text-sm font-medium text-gray-800">No known medical conditions, medications, or allergies.</p>
                        <p class="text-xs text-gray-500 mt-0.5">Tick to skip this section.</p>
                    </div>
                </label>
                <div x-show="!noHealthIssues" class="space-y-4">
                    @foreach([['medical_conditions','Medical Conditions','e.g. Asthma, Sickle Cell'],['medications','Medications','Name and dosage'],['allergies','Allergies','e.g. Penicillin, Peanuts']] as [$n,$l,$p])
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $l }}</label>
                            <textarea name="{{ $n }}" rows="2" placeholder="{{ $p }}"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none">{{ old($n) }}</textarea>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ STEP 4: Review & Submit ══ --}}
            <div x-show="step === 4" class="bg-white rounded-2xl shadow-sm p-5 space-y-4">
                <h2 class="font-bold text-lg" style="color:#064E3B">Review &amp; Submit</h2>
                <p class="text-sm text-gray-600">Submit this form to register <strong>{{ $entry->full_name }}</strong>.</p>
                <div class="rounded-xl p-4 text-sm border" style="background:#F0FDF4;border-color:rgba(16,185,129,0.2);color:#065F46">
                    After submitting, the ID card and (if applicable) consent form will be generated and available in your dashboard.
                </div>
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="confirm_accuracy" required class="mt-1"/>
                    <span class="text-sm text-gray-700">I confirm the information above is accurate.</span>
                </label>
                <button type="submit"
                        class="w-full font-bold py-4 rounded-xl text-lg text-white transition"
                        style="background:#064E3B" onmouseover="this.style.background='#047857'"
                        onmouseout="this.style.background='#064E3B'">
                    Register {{ $entry->full_name }} &#10003;
                </button>
            </div>

            {{-- Navigation --}}
            <div class="flex gap-3 mt-4">
                <button type="button" @click="prev()" x-show="step > 1"
                        class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-100 transition">
                    &larr; Back
                </button>
                <button type="button" @click="next()" x-show="step < totalSteps"
                        class="flex-1 text-white font-bold py-3 rounded-xl transition"
                        style="background:#064E3B" onmouseover="this.style.background='#047857'"
                        onmouseout="this.style.background='#064E3B'">
                    Next &rarr;
                </button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
