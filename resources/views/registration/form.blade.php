@extends('layouts.public')

@section('title', 'Complete Registration — ' . setting('camp_name', 'Ogun Youth Camp'))

@section('content')
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="max-w-2xl mx-auto" x-data="wizard()">

            {{-- Header --}}
            <div class="text-center mb-6">
                <h1 class="text-xl font-bold text-navy">Complete Your Registration</h1>
                <p class="text-sm text-gray-500 mt-1">Code: <span class="font-mono font-bold text-navy">{{ $code }}</span></p>
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

            {{-- Progress bar --}}
            <div class="mb-6">
                <div class="flex justify-between text-xs text-gray-400 mb-2">
                    <span>Step <span x-text="step"></span> of <span x-text="totalSteps"></span></span>
                    <span x-text="stepLabel()"></span>
                </div>
                <div class="bg-gray-200 rounded-full h-2">
                    <div class="bg-navy h-2 rounded-full transition-all duration-300"
                         :style="'width: ' + (step / totalSteps * 100) + '%'"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('registration.submit-web') }}"
                  enctype="multipart/form-data" id="reg-form">
                @csrf
                {{-- Hidden code — server reads prefill_name/phone from DB, not POST --}}
                <input type="hidden" name="code" value="{{ $code }}" />

                {{-- ── Step 1: Pre-filled details (read-only) ──────────────────────── --}}
                <div x-show="step === 1" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Your Payment Details</h2>
                    <p class="text-xs text-gray-400">These details are from your payment record and cannot be changed.</p>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Full Name</p>
                            <p class="font-semibold text-gray-800">{{ $prefill['prefill_name'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Phone Number</p>
                            <p class="font-semibold text-gray-800">{{ $prefill['prefill_phone'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Amount Paid</p>
                            <p class="font-semibold text-gray-800">₦{{ number_format($prefill['amount_paid']) }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">Payment Method</p>
                            <p class="font-semibold text-gray-800">{{ $prefill['payment_type'] }}</p>
                        </div>
                    </div>

                    <div class="pt-2">
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="date_of_birth" id="date_of_birth"
                               value="{{ old('date_of_birth') }}"
                               max="{{ now()->subYears(6)->format('Y-m-d') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('date_of_birth') border-red-400 @enderror" />
                        <p class="text-xs text-gray-400 mt-1">Determines your camp category (Adventurer / Pathfinder / Senior Youth).</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Gender <span class="text-red-500">*</span></label>
                        <div class="flex gap-4">
                            @foreach(['male' => 'Male', 'female' => 'Female'] as $val => $label)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="gender" value="{{ $val }}"
                                           {{ old('gender') === $val ? 'checked' : '' }} required
                                           class="text-navy focus:ring-navy" />
                                    <span class="text-sm">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="home_address" class="block text-sm font-medium text-gray-700 mb-1">Home Address</label>
                        <textarea name="home_address" id="home_address" rows="2"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy"
                                  placeholder="Optional">{{ old('home_address') }}</textarea>
                    </div>

                    <div>
                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-1">
                            Passport Photo <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('photo') border-red-400 @enderror" />
                        <p class="text-xs text-gray-400 mt-1">JPG or PNG, max 2MB. Will appear on your ID card.</p>
                    </div>
                </div>

                {{-- ── Step 2: Church & Ministry ───────────────────────────────────── --}}
                <div x-show="step === 2" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Church & Ministry</h2>

                    <div x-data="districtChurch()">
                        <div class="mb-4">
                            <label for="district_id" class="block text-sm font-medium text-gray-700 mb-1">
                                District <span class="text-red-500">*</span>
                            </label>
                            <select id="district_id" x-model="districtId" @change="loadChurches()" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy">
                                <option value="">— Select District —</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                        {{ $district->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="church_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Church <span class="text-red-500">*</span>
                            </label>
                            <select name="church_id" id="church_id" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy @error('church_id') border-red-400 @enderror">
                                <option value="">— Select District first —</option>
                                <template x-for="church in churches" :key="church.id">
                                    <option :value="church.id" x-text="church.name"
                                            :selected="church.id == {{ old('church_id', 0) }}"></option>
                                </template>
                            </select>
                            @error('church_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div>
                        <label for="ministry" class="block text-sm font-medium text-gray-700 mb-1">Ministry / Club</label>
                        <input type="text" name="ministry" id="ministry" value="{{ old('ministry') }}"
                               placeholder="e.g. Pathfinders, Adventurers"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                    </div>

                    <div>
                        <label for="club_rank" class="block text-sm font-medium text-gray-700 mb-1">Club Rank / Class</label>
                        <input type="text" name="club_rank" id="club_rank" value="{{ old('club_rank') }}"
                               placeholder="e.g. Friend, Companion, Explorer"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                    </div>

                    <div>
                        <label for="volunteer_role" class="block text-sm font-medium text-gray-700 mb-1">
                            Volunteer Role <span class="text-xs text-gray-400">(Senior Youth only — optional)</span>
                        </label>
                        <input type="text" name="volunteer_role" id="volunteer_role" value="{{ old('volunteer_role') }}"
                               placeholder="e.g. Worship Leader, Security Volunteer"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                    </div>
                </div>

                {{-- ── Step 3: Parent / Guardian ───────────────────────────────────── --}}
                <div x-show="step === 3" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Parent / Guardian Information</h2>
                    <p class="text-xs text-gray-500">Required for Adventurers and Pathfinders (under 16).</p>

                    <div>
                        <label for="parent_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="parent_name" id="parent_name" value="{{ old('parent_name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('parent_name') border-red-400 @enderror" />
                        @error('parent_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="parent_relationship" class="block text-sm font-medium text-gray-700 mb-1">
                            Relationship <span class="text-red-500">*</span>
                        </label>
                        <select name="parent_relationship" id="parent_relationship"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select —</option>
                            @foreach(['Mother','Father','Guardian','Uncle','Aunt','Grandparent','Pastor'] as $rel)
                                <option value="{{ $rel }}" {{ old('parent_relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="parent_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="parent_phone" id="parent_phone" value="{{ old('parent_phone') }}"
                               placeholder="08012345678"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('parent_phone') border-red-400 @enderror" />
                        @error('parent_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="parent_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address <span class="text-gray-400 text-xs">(optional)</span></label>
                        <input type="email" name="parent_email" id="parent_email" value="{{ old('parent_email') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                    </div>
                </div>

                {{-- ── Step 4: Health ───────────────────────────────────────────────── --}}
                <div x-show="step === 4" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Health & Medical Information</h2>
                    <p class="text-xs text-gray-500">All fields are optional but help us keep your child safe at camp.</p>

                    @foreach([
                        ['name' => 'medical_conditions',   'label' => 'Medical Conditions',      'placeholder' => 'e.g. Asthma, Diabetes, Epilepsy'],
                        ['name' => 'medications',          'label' => 'Current Medications',      'placeholder' => 'Medication name and dosage'],
                        ['name' => 'allergies',            'label' => 'Allergies',                'placeholder' => 'e.g. Penicillin, Peanuts, Dust'],
                        ['name' => 'dietary_restrictions', 'label' => 'Dietary Restrictions',     'placeholder' => 'e.g. Vegetarian, Nut-free'],
                    ] as $field)
                        <div>
                            <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $field['label'] }}</label>
                            <textarea name="{{ $field['name'] }}" id="{{ $field['name'] }}" rows="2"
                                      placeholder="{{ $field['placeholder'] }}"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy">{{ old($field['name']) }}</textarea>
                        </div>
                    @endforeach

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="doctor_name" class="block text-sm font-medium text-gray-700 mb-1">Doctor's Name</label>
                            <input type="text" name="doctor_name" id="doctor_name" value="{{ old('doctor_name') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                        </div>
                        <div>
                            <label for="doctor_phone" class="block text-sm font-medium text-gray-700 mb-1">Doctor's Phone</label>
                            <input type="tel" name="doctor_phone" id="doctor_phone" value="{{ old('doctor_phone') }}"
                                   placeholder="08012345678"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy" />
                        </div>
                    </div>
                </div>

                {{-- ── Step 5: Emergency Contact ────────────────────────────────────── --}}
                <div x-show="step === 5" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Emergency Contact</h2>
                    <p class="text-xs text-gray-500">Who should we call in case of an emergency?</p>

                    <div>
                        <label for="emergency_name" class="block text-sm font-medium text-gray-700 mb-1">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="emergency_name" id="emergency_name" value="{{ old('emergency_name') }}"
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('emergency_name') border-red-400 @enderror" />
                        @error('emergency_name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="emergency_relationship" class="block text-sm font-medium text-gray-700 mb-1">
                            Relationship <span class="text-red-500">*</span>
                        </label>
                        <select name="emergency_relationship" id="emergency_relationship" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-navy">
                            <option value="">— Select —</option>
                            @foreach(['Mother','Father','Guardian','Uncle','Aunt','Grandparent','Sibling','Friend','Pastor'] as $rel)
                                <option value="{{ $rel }}" {{ old('emergency_relationship') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="emergency_phone" class="block text-sm font-medium text-gray-700 mb-1">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="emergency_phone" id="emergency_phone" value="{{ old('emergency_phone') }}"
                               placeholder="08012345678" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-navy @error('emergency_phone') border-red-400 @enderror" />
                        @error('emergency_phone')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- ── Step 6: Review & Submit ──────────────────────────────────────── --}}
                <div x-show="step === 6" class="bg-white rounded-2xl shadow-sm p-6 space-y-4">
                    <h2 class="font-bold text-navy text-lg">Review & Submit</h2>
                    <p class="text-sm text-gray-600">
                        Please review your information. By submitting, you confirm that all details are accurate.
                    </p>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-800">
                        ℹ After submitting, your <strong>ID card</strong> and
                        <strong>consent form</strong> (if applicable) will be generated and available for download.
                    </div>

                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox" name="confirm_accuracy" required class="mt-1 text-navy focus:ring-navy" />
                        <span class="text-sm text-gray-700">
                    I confirm that all information provided is accurate and complete.
                    I understand that my registration cannot be changed after submission.
                </span>
                    </label>

                    <button type="submit" id="submit-btn"
                            class="w-full bg-green-600 text-white font-bold py-4 rounded-xl text-lg hover:bg-green-700 transition">
                        Submit Registration ✓
                    </button>
                </div>

                {{-- Navigation buttons --}}
                <div class="flex gap-3 mt-4">
                    <button type="button" @click="prev()" x-show="step > 1"
                            class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 rounded-xl hover:bg-gray-100 transition">
                        ← Back
                    </button>
                    <button type="button" @click="next()" x-show="step < totalSteps"
                            class="flex-1 bg-navy text-white font-bold py-3 rounded-xl hover:bg-steel transition">
                        Next →
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function wizard() {
            return {
                step: {{ $errors->any() ? 1 : 1 }},
                totalSteps: 6,
                steps: ['Payment Details', 'Church & Ministry', 'Parent / Guardian', 'Health Info', 'Emergency Contact', 'Review & Submit'],

                stepLabel() { return this.steps[this.step - 1] ?? ''; },
                next() { if (this.step < this.totalSteps) this.step++; window.scrollTo(0, 0); },
                prev() { if (this.step > 1) this.step--; window.scrollTo(0, 0); },
            }
        }

        function districtChurch() {
            return {
                districtId: '{{ old('district_id', '') }}',
                churches: [],

                async loadChurches() {
                    if (!this.districtId) { this.churches = []; return; }
                    const res = await fetch(`/api/churches?district_id=${this.districtId}`);
                    this.churches = await res.json();
                },

                init() { if (this.districtId) this.loadChurches(); }
            }
        }
    </script>
@endpush
