<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="theme-color" content="#1B3A6B" />
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>Camp Check-In — {{ setting('camp_name', 'Ogun Youth Camp') }}</title>
    <link rel="manifest" href="/checkin-manifest.json" />
    <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
</head>
<body class="bg-gray-900 text-white min-h-screen" x-data="checkinApp()">

{{-- Offline banner --}}
<div x-show="!online"
     class="fixed top-0 inset-x-0 z-50 bg-red-600 text-white text-center py-2 text-sm font-bold">
    ⚠ OFFLINE MODE — Check-ins are being saved locally and will sync when connected.
</div>

{{-- Header --}}
<header class="bg-navy px-4 py-3 flex items-center justify-between"
        :class="!online ? 'mt-9' : ''">
    <div>
        <p class="font-bold text-sm">Camp Check-In</p>
        <p class="text-white/50 text-xs">{{ setting('camp_name', 'Ogun Youth Camp') }}</p>
    </div>
    <div class="flex items-center gap-3">
            <span class="text-xs" :class="online ? 'text-green-400' : 'text-red-400'">
                <span x-text="online ? '● Online' : '● Offline'"></span>
            </span>
        <button @click="showLogin = !authenticated" x-show="!authenticated"
                class="text-xs bg-gold text-navy font-bold px-3 py-1.5 rounded-full">
            Login
        </button>
        <button @click="logout()" x-show="authenticated"
                class="text-xs border border-white/30 text-white/70 px-3 py-1.5 rounded-full">
            Logout
        </button>
    </div>
</header>

{{-- Login panel --}}
<div x-show="!authenticated" class="p-6 max-w-sm mx-auto mt-8">
    <div class="bg-gray-800 rounded-2xl p-6 space-y-4">
        <h2 class="text-lg font-bold text-center">Staff Login</h2>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Email</label>
            <input type="email" x-model="loginEmail" placeholder="your@email.com"
                   class="w-full bg-gray-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold" />
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Password / PIN</label>
            <input type="password" x-model="loginPin" placeholder="••••••••"
                   @keydown.enter="login()"
                   class="w-full bg-gray-700 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold" />
        </div>
        <p x-show="loginError" x-text="loginError" class="text-red-400 text-xs text-center"></p>
        <button @click="login()" :disabled="loggingIn"
                class="w-full bg-gold text-navy font-bold py-3 rounded-xl hover:bg-yellow-400 transition disabled:opacity-50">
            <span x-show="!loggingIn">Login</span>
            <span x-show="loggingIn">Logging in…</span>
        </button>
    </div>

    <div class="mt-6 text-center">
        <button @click="syncData()" x-show="token"
                class="text-xs text-gray-400 hover:text-white underline">
            Sync camper data
        </button>
    </div>
</div>

{{-- Main scanner UI --}}
<div x-show="authenticated" class="p-4 space-y-4">

    {{-- Sync bar --}}
    <div class="flex items-center justify-between bg-gray-800 rounded-xl px-4 py-2 text-xs">
            <span class="text-gray-400">
                <span x-text="cachedCount"></span> campers cached ·
                <span x-text="pendingEvents"></span> events pending sync
            </span>
        <button @click="syncAll()" :disabled="syncing"
                class="text-gold hover:underline disabled:opacity-50">
            <span x-show="!syncing">⟳ Sync</span>
            <span x-show="syncing">Syncing…</span>
        </button>
    </div>

    {{-- Mode selector --}}
    <div class="flex rounded-xl bg-gray-800 p-1 gap-1">
        <button @click="setMode('checkin')"
                :class="mode === 'checkin' ? 'bg-blue-700 text-white' : 'text-gray-400 hover:text-white'"
                class="flex-1 py-2.5 rounded-lg text-sm font-bold transition">
            🚪 Check In / Out
        </button>
        <button @click="setMode('attendance')"
                :class="mode === 'attendance' ? 'bg-blue-700 text-white' : 'text-gray-400 hover:text-white'"
                class="flex-1 py-2.5 rounded-lg text-sm font-bold transition">
            📋 Programme Attendance
        </button>
    </div>

    {{-- Session picker (attendance mode only) --}}
    <div x-show="mode === 'attendance'" class="space-y-2">
        <div x-show="sessions.length === 0"
             class="text-center text-gray-500 text-sm py-3 bg-gray-800 rounded-xl px-4">
            No active sessions today. Ask the super admin to create one in the admin panel.
        </div>
        <div x-show="sessions.length > 0">
            <label class="block text-xs text-gray-400 font-bold uppercase tracking-wider mb-1">
                Select Programme Session
            </label>
            <select x-model="selectedSessionId"
                    class="w-full bg-gray-800 border border-gray-600 rounded-xl px-4 py-3 text-sm text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— Choose a session —</option>
                <template x-for="s in sessions" :key="s.id">
                    <option :value="s.id"
                            x-text="s.title + ' (' + (s.start_time ? s.start_time.substring(0,5) : '') + ')'"></option>
                </template>
            </select>
            <p x-show="!selectedSessionId"
               class="text-xs text-amber-400 mt-1">⚠ Select a session before scanning</p>
        </div>
    </div>

    {{-- Camera scanner --}}
    <div class="relative bg-black rounded-2xl overflow-hidden aspect-video">
        <video id="qr-video" class="w-full h-full object-cover" playsinline></video>
        <canvas id="qr-canvas" class="hidden"></canvas>
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
            <div class="border-2 border-gold/70 rounded-xl w-48 h-48"></div>
        </div>
        <div class="absolute bottom-3 inset-x-0 text-center">
            <button @click="toggleCamera()" class="bg-black/60 text-white text-xs px-4 py-1.5 rounded-full">
                <span x-text="cameraOn ? 'Stop Camera' : 'Start Camera'"></span>
            </button>
        </div>
    </div>

    {{-- Manual search --}}
    <div class="flex gap-2">
        <input type="text" x-model="manualCode" @keydown.enter="lookupManual()"
               placeholder="Enter camper number manually"
               class="flex-1 bg-gray-800 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-gold font-mono uppercase" />
        <button @click="lookupManual()"
                class="bg-gold text-navy font-bold px-4 py-3 rounded-xl text-sm">
            Search
        </button>
    </div>

    {{-- Result card --}}
    <div x-show="result" class="rounded-2xl p-5 space-y-3"
         :class="{
                'bg-green-900 border border-green-500': resultStatus === 'found',
                'bg-yellow-900 border border-yellow-500': resultStatus === 'already_in',
                'bg-red-900 border border-red-500': resultStatus === 'not_found'
             }">

        <div x-show="resultStatus === 'not_found'" class="text-center">
            <p class="text-4xl mb-2">❌</p>
            <p class="font-bold text-red-300">Camper Not Found</p>
            <p class="text-xs text-gray-400 mt-1" x-text="'Code: ' + scannedCode"></p>
            <p class="text-xs text-gray-400">They may have registered after the last sync.</p>
        </div>

        <template x-if="result && resultStatus !== 'not_found'">
            <div class="space-y-3">
                <div x-show="resultStatus === 'already_in'"
                     class="bg-yellow-700/50 rounded-lg px-3 py-2 text-xs text-yellow-200">
                    ⚠️ <strong>Already checked in.</strong> This camper has already been checked in. Use Check Out to remove them, or Reset to scan another.
                </div>

                <div x-show="result?.consent_required && !result?.consent_collected"
                     class="bg-red-700/50 rounded-lg px-3 py-2 text-xs text-red-200">
                    📋 CONSENT FORM REQUIRED — collect before confirming check-in.
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-gray-600 overflow-hidden flex-shrink-0">
                        <img x-show="result?.photo_url" :src="result?.photo_url"
                             class="w-full h-full object-cover" />
                        <div x-show="!result?.photo_url" class="w-full h-full flex items-center justify-center text-2xl">👤</div>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-lg" x-text="result?.full_name"></p>
                        <p class="text-xs font-mono text-gray-300" x-text="result?.camper_number"></p>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-white/20 mt-1 inline-block"
                              x-text="result?.category"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 text-xs text-gray-300">
                    <div><span class="text-gray-500">Church:</span> <span x-text="result?.church ?? '—'"></span></div>
                    <div><span class="text-gray-500">District:</span> <span x-text="result?.district ?? '—'"></span></div>
                    <div x-show="result?.last_event_type">
                        <span class="text-gray-500">Last Event:</span>
                        <span x-text="result?.last_event_type === 'check_in' ? '✅ Checked In' : '🚪 Checked Out'"></span>
                    </div>
                    <div x-show="result?.last_event_by">
                        <span class="text-gray-500">By:</span>
                        <span x-text="result?.last_event_by ?? '—'"></span>
                    </div>
                </div>

                <!-- ── Action buttons ──────────────────────────────── -->
                <div class="flex flex-col gap-3 pt-3" x-show="mode === 'checkin'">

                    <!-- Check In + Collect Consent — only when consent needed & not yet collected & not checked in -->
                    <button @click="checkIn(true)"
                            x-show="result?.consent_required && !result?.consent_collected && !result?.is_checked_in"
                            class="w-full font-bold py-4 rounded-2xl text-base transition"
                            style="background:#D97706;color:#fff;border:none;font-size:1rem;padding:1rem 1.25rem;border-radius:1rem;cursor:pointer;letter-spacing:0.01em">
                        📋 Check In + Collect Consent Form
                    </button>

                    <!-- Check In — always shown when not yet checked in -->
                    <button @click="checkIn(false)"
                            x-show="!result?.is_checked_in"
                            class="w-full font-bold py-4 rounded-2xl text-base transition"
                            style="background:#16A34A;color:#fff;border:none;font-size:1rem;padding:1rem 1.25rem;border-radius:1rem;cursor:pointer">
                        ✅ Check In
                    </button>

                    <!-- Check Out — only when already checked in -->
                    <button @click="doCheckOut()"
                            x-show="result?.is_checked_in"
                            class="w-full font-bold py-4 rounded-2xl text-base transition"
                            style="background:#DC2626;color:#fff;border:none;font-size:1rem;padding:1rem 1.25rem;border-radius:1rem;cursor:pointer">
                        🚪 Check Out
                    </button>

                    <!-- Reset — always visible -->
                    <button @click="reset()"
                            class="w-full font-bold py-3 rounded-2xl text-base transition"
                            style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.15);font-size:0.95rem;padding:0.85rem 1.25rem;border-radius:1rem;cursor:pointer">
                        ↩ Reset
                    </button>
                </div>

                <!-- Attendance mode buttons -->
                <div class="flex flex-col gap-3 pt-3" x-show="mode === 'attendance'">
                    <button @click="markAttendance()"
                            :disabled="!selectedSessionId"
                            class="w-full font-bold py-4 rounded-2xl text-base"
                            style="background:#2563EB;color:#fff;border:none;font-size:1rem;padding:1rem 1.25rem;border-radius:1rem;cursor:pointer">
                        📋 Mark Present
                    </button>
                    <button @click="reset()"
                            class="w-full font-bold py-3 rounded-2xl"
                            style="background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.7);border:1px solid rgba(255,255,255,0.15);font-size:0.95rem;padding:0.85rem 1.25rem;border-radius:1rem;cursor:pointer">
                        ↩ Reset
                    </button>
                </div>
            </div>
        </template>
    </div>

</div>

<script>
    // ── Global API headers helper ──────────────────────────────────────────────
    function apiHeaders(token) {
        return {
            'Content-Type':     'application/json',
            'Accept':           'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN':     document.querySelector('meta[name="csrf-token"]')?.content || '',
            // Standard Bearer token header
            ...(token ? { 'Authorization': 'Bearer ' + token } : {}),
            // Fallback header for hosts that strip Authorization (e.g. cPanel Apache CGI)
            ...(token ? { 'X-Api-Token': token } : {}),
        };
    }

    const DEVICE_ID = (() => {
        let id = localStorage.getItem('checkin_device_id');
        if (!id) { id = crypto.randomUUID(); localStorage.setItem('checkin_device_id', id); }
        return id;
    })();

    function checkinApp() {
        return {
            // Auth
            authenticated: !!localStorage.getItem('checkin_token'),
            token:         localStorage.getItem('checkin_token') ?? '',
            loginEmail:    '',
            loginPin:      '',
            loginError:    '',
            loggingIn:     false,

            // State
            online:          navigator.onLine,
            syncing:         false,
            cameraOn:        false,
            scanInterval:    null,
            videoStream:     null,
            mode:            localStorage.getItem('checkin_mode') || 'checkin',
            sessions:        [],
            selectedSessionId: '',

            // Scan result
            result:        null,
            resultStatus:  'idle',
            scannedCode:   '',
            manualCode:    '',

            // Stats
            cachedCount:   0,
            pendingEvents: 0,

            init() {
                window.addEventListener('online',  () => { this.online = true;  this.syncEvents(); });
                window.addEventListener('offline', () => { this.online = false; });
                this.updateStats();
                if (this.authenticated) this.startCamera();
            },

            // ── Auth ──────────────────────────────────────────────────────────

            async login() {
                this.loggingIn = true; this.loginError = '';
                try {
                    const res  = await fetch('/api/checkin/auth', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        },
                        body: JSON.stringify({ device_id: DEVICE_ID, email: this.loginEmail, pin: this.loginPin }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.token         = data.token;
                        this.authenticated = true;
                        localStorage.setItem('checkin_token', data.token);
                        this.dbg('Login OK. Token length=' + (data.token?.length || 0));
                        // Sync immediately after login then flush any pending events
                        try {
                            await this.syncData();
                            await this.syncEvents();
                        } catch(syncErr) {
                            this.dbg('Post-login sync threw: ' + syncErr.message);
                        }
                        this.startCamera();
                    } else {
                        this.loginError = data.message || 'Invalid credentials. Please try again.';
                    }
                } catch (e) {
                    console.error('Login error:', e);
                    this.loginError = 'Could not connect (' + e.message + '). Check the console for details.';
                }
                this.loggingIn = false;
            },

            logout() {
                localStorage.removeItem('checkin_token');
                this.token = ''; this.authenticated = false;
                this.stopCamera(); this.reset();
            },

            // ── Camera ────────────────────────────────────────────────────────

            async startCamera() {
                if (this.cameraOn) return;
                const video  = document.getElementById('qr-video');
                const canvas = document.getElementById('qr-canvas');
                try {
                    this.videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                    video.srcObject  = this.videoStream;
                    video.play();
                    this.cameraOn    = true;
                    this.scanInterval = setInterval(() => {
                        if (video.readyState !== video.HAVE_ENOUGH_DATA) return;
                        canvas.width  = video.videoWidth;
                        canvas.height = video.videoHeight;
                        const ctx     = canvas.getContext('2d');
                        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                        const img  = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(img.data, img.width, img.height, { inversionAttempts: 'dontInvert' });
                        if (code) this.handleScan(code.data);
                    }, 250);
                } catch { console.warn('Camera unavailable.'); }
            },

            stopCamera() {
                clearInterval(this.scanInterval);
                if (this.videoStream) { this.videoStream.getTracks().forEach(t => t.stop()); this.videoStream = null; }
                this.cameraOn = false;
            },

            toggleCamera() { this.cameraOn ? this.stopCamera() : this.startCamera(); },

            // ── Lookup ────────────────────────────────────────────────────────

            handleScan(raw) {
                let code = raw.trim();
                // Handle full URL: http://...../verify/OGN-2026-XXXX
                if (code.includes('/verify/')) {
                    code = code.split('/verify/').pop().split('?')[0].trim();
                }
                // Handle OGN: prefix
                if (code.startsWith('OGN:')) {
                    code = code.slice(4);
                }
                code = code.toUpperCase();
                this.stopCamera();
                this.lookup(code);
            },

            lookupManual() {
                if (!this.manualCode.trim()) return;
                this.lookup(this.manualCode.trim().toUpperCase());
            },

            async lookup(camperNumber) {
                if (!camperNumber || camperNumber.length < 3) {
                    this.resultStatus = 'not_found';
                    this.result = null;
                    return;
                }

                this.scannedCode = camperNumber;
                this.result      = null;
                this.resultStatus = 'idle';

                // Helper to read from IndexedDB properly
                const readFromCache = async (code) => {
                    const db = await this.getDB();
                    return new Promise((resolve) => {
                        const req = db.transaction('campers', 'readonly').objectStore('campers').get(code);
                        req.onsuccess = () => resolve(req.result || null);
                        req.onerror   = () => resolve(null);
                    });
                };

                // Try online API first
                if (this.online) {
                    try {
                        const res  = await fetch(`/api/checkin/camper/${encodeURIComponent(camperNumber)}`, {
                            headers: apiHeaders(this.token),
                        });
                        const data = await res.json();
                        if (res.ok && data.success) {
                            this.result       = data.camper;
                            this.resultStatus = data.camper.is_checked_in ? 'already_in' : 'found';
                            // Update IndexedDB cache with latest check-in status
                            const db = await this.getDB();
                            db.transaction('campers', 'readwrite').objectStore('campers').put(data.camper, data.camper.camper_number);
                            return;
                        }
                        // 404 from API — camper genuinely doesn't exist
                        if (res.status === 404) {
                            this.result       = null;
                            this.resultStatus = 'not_found';
                            return;
                        }
                    } catch(e) {
                        console.warn('Online lookup failed, falling back to cache', e);
                    }
                }

                // Offline or API error — use IndexedDB cache
                const record = await readFromCache(camperNumber);
                if (record) {
                    this.result       = record;
                    // Use cached is_checked_in if available, else check pending events
                    const db     = await this.getDB();
                    const events = await new Promise((resolve) => {
                        const req = db.transaction('events', 'readonly').objectStore('events').getAll();
                        req.onsuccess = () => resolve(req.result);
                        req.onerror   = () => resolve([]);
                    });
                    const myEvents = events
                        .filter(e => e.camper_number === camperNumber)
                        .sort((a,b) => new Date(b.occurred_at) - new Date(a.occurred_at));

                    const lastEvent = myEvents[0];
                    const isIn = lastEvent
                        ? lastEvent.event_type === 'check_in'
                        : (record.is_checked_in || false);

                    this.result.is_checked_in = isIn;
                    this.resultStatus = isIn ? 'already_in' : 'found';
                } else {
                    this.result       = null;
                    this.resultStatus = 'not_found';
                }
            },

            async checkIn(collectConsent) {
                if (!this.result) return;

                // Prevent double check-in
                if (this.result.is_checked_in) {
                    alert(`⚠️ ${this.result.full_name} is already checked in!`);
                    return;
                }

                await this.saveEvent({
                    uuid:              crypto.randomUUID(),
                    camper_number:     this.result.camper_number,
                    event_type:        'check_in',
                    occurred_at:       new Date().toISOString(),
                    device_id:         DEVICE_ID,
                    consent_collected: collectConsent,
                });

                // Update local record so UI reflects check-in immediately
                if (this.result) this.result.is_checked_in = true;

                alert(`✓ ${this.result?.full_name} checked in successfully!`);
                this.reset();
                this.startCamera();
            },

            reset() { this.result = null; this.resultStatus = 'idle'; this.scannedCode = ''; this.manualCode = ''; },

            // ── Mode & attendance ──────────────────────────────────────────────

            setMode(m) {
                this.mode = m;
                localStorage.setItem('checkin_mode', m);
                this.reset();
                if (m === 'attendance') this.loadSessions();
            },

            async loadSessions() {
                if (!this.token) return;
                try {
                    const res = await fetch('/api/checkin/sessions', {
                        headers: { Authorization: `Bearer ${this.token}` }
                    });
                    if (res.ok) this.sessions = await res.json();
                } catch(e) { console.error('loadSessions failed', e); }
            },

            async saveEvent(event) {
                const db = await this.getDB();
                await new Promise((resolve, reject) => {
                    const req = db.transaction('events', 'readwrite').objectStore('events').add(event);
                    req.onsuccess = resolve;
                    req.onerror   = reject;
                });
                this.updateStats();
                if (this.online) await this.syncEvents();
            },

            async markAttendance() {
                if (!this.result || !this.selectedSessionId) return;
                await this.saveEvent({
                    uuid:                 crypto.randomUUID(),
                    camper_number:        this.result.camper_number,
                    event_type:           'programme_attendance',
                    programme_session_id: parseInt(this.selectedSessionId),
                    occurred_at:          new Date().toISOString(),
                    device_id:            DEVICE_ID,
                });
                alert(`📋 ${this.result.full_name} marked present!`);
                this.reset();
                this.startCamera();
            },

            async doCheckOut() {
                if (!this.result) return;
                await this.saveEvent({
                    uuid:          crypto.randomUUID(),
                    camper_number: this.result.camper_number,
                    event_type:    'check_out',
                    occurred_at:   new Date().toISOString(),
                    device_id:     DEVICE_ID,
                });
                alert(`🚪 ${this.result.full_name} checked out.`);
                this.reset();
                this.startCamera();
            },

            // ── Sync ──────────────────────────────────────────────────────────

            async syncAll() {
                this.dbg('Manual sync triggered');
                try {
                    await this.syncData();
                    await this.syncEvents();
                    this.dbg('Manual sync complete');
                } catch(e) {
                    this.dbg('Manual sync threw: ' + e.message);
                }
            },

            async syncData() {
                this.dbg('syncData() called. online=' + this.online + ' hasToken=' + !!this.token);
                if (!this.online || !this.token) {
                    this.dbg('syncData() aborted — online or token missing');
                    return;
                }
                this.syncing = true;
                try {
                    let page = 1, hasMore = true, totalSaved = 0;
                    const db = await this.getDB();

                    while (hasMore) {
                        this.dbg(`GET /api/checkin/sync?page=${page}...`);
                        const res  = await fetch(`/api/checkin/sync?page=${page}&per_page=200`, {
                            headers: apiHeaders(this.token),
                        });
                        this.dbg(`Sync response: HTTP ${res.status}`);
                        if (!res.ok) {
                            const t = await res.text();
                            this.dbg(`Sync error body: ${t.substring(0, 100)}`);
                            throw new Error('Sync HTTP ' + res.status);
                        }
                        const data = await res.json();
                        const campers = data.data || [];

                        // Each camper stored in its own transaction to avoid timeout
                        for (const c of campers) {
                            const tx    = db.transaction('campers', 'readwrite');
                            const store = tx.objectStore('campers');
                            store.put(c, c.camper_number);
                            totalSaved++;
                        }

                        hasMore = data.current_page < data.last_page;
                        page++;
                    }

                    console.log(`Sync complete: ${totalSaved} campers cached`);
                    this.updateStats();
                } catch (e) {
                    this.dbg('SYNC ERROR: ' + e.message);
                    console.error('Sync failed', e);
                }
                this.syncing = false;
            },

            dbg(msg) {
                const el = document.getElementById('debug-log');
                if (el) {
                    const line = document.createElement('div');
                    line.textContent = new Date().toLocaleTimeString() + ' ' + msg;
                    el.insertBefore(line, el.firstChild);
                    while (el.children.length > 8) el.removeChild(el.lastChild);
                }
                console.log('[PWA]', msg);
            },

            async syncEvents() {
                if (!this.online || !this.token) return;
                const db     = await this.getDB();
                const events = await new Promise((resolve, reject) => {
                    const req = db.transaction('events', 'readonly').objectStore('events').getAll();
                    req.onsuccess = () => resolve(req.result);
                    req.onerror   = reject;
                });
                if (!events.length) return;

                try {
                    const batch = events.slice(0, 50);
                    this.dbg(`POST /api/checkin/events (${batch.length} events)...`);
                    const res = await fetch('/api/checkin/events', {
                        method:  'POST',
                        headers: apiHeaders(this.token),
                        body:    JSON.stringify({ events: batch }),
                    });
                    const text = await res.text();
                    this.dbg(`Events response: HTTP ${res.status} — ${text.substring(0, 80)}`);

                    if (res.ok) {
                        let data = {};
                        try { data = JSON.parse(text); } catch(e) {}
                        const syncedUuids = batch.map(e => e.uuid);
                        for (const uuid of syncedUuids) {
                            db.transaction('events', 'readwrite').objectStore('events').delete(uuid);
                        }
                        this.dbg(`Cleared ${syncedUuids.length} events from queue`);
                        this.updateStats();
                    } else {
                        this.dbg(`Events FAILED: ${res.status} ${text.substring(0, 100)}`);
                    }
                } catch (e) {
                    this.dbg(`Events exception: ${e.message}`);
                    console.error('Event sync failed', e);
                }
            },

            // ── IndexedDB ─────────────────────────────────────────────────────

            dbInstance: null,

            async getDB() {
                if (this.dbInstance) return this.dbInstance;
                return new Promise((resolve, reject) => {
                    const req = indexedDB.open('camp-checkin', 1);
                    req.onupgradeneeded = e => {
                        const db = e.target.result;
                        if (!db.objectStoreNames.contains('campers')) db.createObjectStore('campers');
                        if (!db.objectStoreNames.contains('events'))  db.createObjectStore('events', { keyPath: 'uuid' });
                    };
                    req.onsuccess = e => { this.dbInstance = e.target.result; resolve(this.dbInstance); };
                    req.onerror   = reject;
                });
            },

            async updateStats() {
                try {
                    const db = await this.getDB();

                    this.cachedCount = await new Promise((resolve, reject) => {
                        const req = db.transaction('campers', 'readonly').objectStore('campers').count();
                        req.onsuccess = () => resolve(req.result);
                        req.onerror   = reject;
                    });

                    this.pendingEvents = await new Promise((resolve, reject) => {
                        const req = db.transaction('events', 'readonly').objectStore('events').count();
                        req.onsuccess = () => resolve(req.result);
                        req.onerror   = reject;
                    });
                } catch(e) { console.error('updateStats failed', e); }
            },
        }
    }
</script>
</body>
</html>
