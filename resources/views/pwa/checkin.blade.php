<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta name="theme-color" content="#1B3A6B" />
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
                    ⚠ Already checked in. Confirm only if re-entry is authorised.
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
                </div>

                <div class="flex gap-2 pt-2">
                    <button @click="checkIn(false)"
                            x-show="!result?.consent_required || result?.consent_collected"
                            class="flex-1 bg-green-600 font-bold py-3 rounded-xl text-sm hover:bg-green-500 transition">
                        ✓ Check In
                    </button>
                    <button @click="checkIn(true)"
                            x-show="result?.consent_required && !result?.consent_collected"
                            class="flex-1 bg-green-600 font-bold py-3 rounded-xl text-sm hover:bg-green-500 transition">
                        ✓ Check In + Collect Consent
                    </button>
                    <button @click="reset()"
                            class="bg-gray-700 font-bold px-4 py-3 rounded-xl text-sm hover:bg-gray-600 transition">
                        Reset
                    </button>
                </div>
            </div>
        </template>
    </div>

</div>

<script>
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
            online:        navigator.onLine,
            syncing:       false,
            cameraOn:      false,
            scanInterval:  null,
            videoStream:   null,

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
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ device_id: DEVICE_ID, email: this.loginEmail, pin: this.loginPin }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.token         = data.token;
                        this.authenticated = true;
                        localStorage.setItem('checkin_token', data.token);
                        this.syncData();
                        this.startCamera();
                    } else {
                        this.loginError = 'Invalid credentials. Please try again.';
                    }
                } catch { this.loginError = 'Could not connect. Check your internet.'; }
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
                const code = raw.startsWith('OGN:') ? raw.slice(4) : raw;
                this.stopCamera();
                this.lookup(code);
            },

            lookupManual() {
                if (!this.manualCode.trim()) return;
                this.lookup(this.manualCode.trim().toUpperCase());
            },

            async lookup(camperNumber) {
                this.scannedCode = camperNumber;
                this.result      = null;

                // Try online first
                if (this.online) {
                    try {
                        const res  = await fetch(`/api/checkin/camper/${camperNumber}`, {
                            headers: { Authorization: `Bearer ${this.token}` }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.result       = data.camper;
                            this.resultStatus = data.camper.latest_event?.type === 'Check In' ? 'already_in' : 'found';
                            return;
                        }
                    } catch {}
                }

                // Fall back to IndexedDB cache
                const db     = await this.getDB();
                const tx     = db.transaction('campers', 'readonly');
                const record = await tx.objectStore('campers').get(camperNumber);
                if (record) {
                    this.result       = record;
                    this.resultStatus = 'found';
                } else {
                    this.result       = null;
                    this.resultStatus = 'not_found';
                }
            },

            async checkIn(collectConsent) {
                if (!this.result) return;

                const event = {
                    uuid:              crypto.randomUUID(),
                    camper_number:     this.result.camper_number,
                    event_type:        'check_in',
                    scanned_at:        new Date().toISOString(),
                    device_id:         DEVICE_ID,
                    consent_collected: collectConsent,
                };

                // Save to IndexedDB queue
                const db = await this.getDB();
                await db.transaction('events', 'readwrite').objectStore('events').add(event);
                this.updateStats();

                // Try to sync immediately if online
                if (this.online) this.syncEvents();

                alert(`✓ ${this.result.full_name} checked in successfully!`);
                this.reset();
                this.startCamera();
            },

            reset() { this.result = null; this.resultStatus = 'idle'; this.scannedCode = ''; this.manualCode = ''; },

            // ── Sync ──────────────────────────────────────────────────────────

            async syncAll() { await Promise.all([this.syncData(), this.syncEvents()]); },

            async syncData() {
                if (!this.online || !this.token) return;
                this.syncing = true;
                try {
                    let page = 1, hasMore = true;
                    const db = await this.getDB();
                    const tx = db.transaction('campers', 'readwrite');
                    const store = tx.objectStore('campers');

                    while (hasMore) {
                        const res  = await fetch(`/api/checkin/sync?page=${page}`, { headers: { Authorization: `Bearer ${this.token}` } });
                        const data = await res.json();
                        for (const c of data.data) store.put(c, c.camper_number);
                        hasMore = page < data.last_page;
                        page++;
                    }
                    await tx.done;
                    this.updateStats();
                } catch (e) { console.error('Sync failed', e); }
                this.syncing = false;
            },

            async syncEvents() {
                if (!this.online || !this.token) return;
                const db     = await this.getDB();
                const events = await db.transaction('events', 'readonly').objectStore('events').getAll();
                if (!events.length) return;

                try {
                    const res = await fetch('/api/checkin/events', {
                        method:  'POST',
                        headers: { 'Content-Type': 'application/json', Authorization: `Bearer ${this.token}` },
                        body:    JSON.stringify({ events: events.slice(0, 50) }),
                    });
                    const data = await res.json();
                    if (data.success) {
                        const tx = db.transaction('events', 'readwrite');
                        const synced = events.slice(0, 50).map(e => e.uuid);
                        for (const uuid of synced) tx.objectStore('events').delete(uuid);
                        await tx.done;
                        this.updateStats();
                    }
                } catch {}
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
                    const db      = await this.getDB();
                    const count   = await db.transaction('campers', 'readonly').objectStore('campers').count();
                    const pending = await db.transaction('events',  'readonly').objectStore('events').count();
                    this.cachedCount   = count;
                    this.pendingEvents = pending;
                } catch {}
            },
        }
    }
</script>
</body>
</html>
