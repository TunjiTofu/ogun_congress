<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no"/>
    <meta name="theme-color" content="#0B2455"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="Camp Check-In"/>
    <title>Camp Check-In — Ogun Congress 2026</title>
    <link rel="manifest" href="/checkin/manifest.json"/>
    <link rel="apple-touch-icon" href="/images/congress_logo.png"/>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=DM+Mono:wght@400;600&display=swap" rel="stylesheet"/>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #0B2455; --navy2: #071640; --blue: #1B3A8F;
            --gold: #C9A94D; --gold2: #E8C255;
            --green: #065F46; --green-bg: #D1FAE5; --green-border: #6EE7B7;
            --amber: #92400E; --amber-bg: #FEF3C7; --amber-border: #FCD34D;
            --red: #991B1B; --red-bg: #FEE2E2; --red-border: #FCA5A5;
            --surface: #F4F6FB;
        }
        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--navy2);
            color: #fff;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        /* ── Top bar ──────────────────────────────────────────── */
        .topbar {
            background: rgba(7,22,64,0.95);
            backdrop-filter: blur(8px);
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid rgba(201,169,77,0.15);
            position: sticky; top: 0; z-index: 100;
        }
        .topbar-left { display: flex; align-items: center; gap: 0.6rem; }
        .topbar-logo { width: 32px; height: 32px; border-radius: 50%; border: 1.5px solid rgba(201,169,77,0.4); }
        .topbar-title { font-size: 0.8rem; font-weight: 700; color: var(--gold2); }
        .topbar-sub   { font-size: 0.6rem; color: rgba(255,255,255,0.45); margin-top: 1px; }
        .offline-pill {
            background: var(--amber-bg); color: var(--amber);
            font-size: 0.62rem; font-weight: 700; padding: 0.2rem 0.6rem;
            border-radius: 100px; border: 1px solid var(--amber-border);
            display: none;
        }
        .offline-pill.show { display: block; }

        /* ── Main area ────────────────────────────────────────── */
        .main { flex: 1; padding: 1rem; display: flex; flex-direction: column; gap: 1rem; max-width: 480px; margin: 0 auto; width: 100%; }

        /* ── Scanner area ─────────────────────────────────────── */
        .scanner-wrap {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            position: relative;
        }
        .scanner-head {
            padding: 1rem 1.25rem 0.75rem;
            display: flex; align-items: center; justify-content: space-between;
        }
        .scanner-title { font-size: 0.82rem; font-weight: 700; color: rgba(255,255,255,0.8); }
        .scanner-toggle {
            font-size: 0.7rem; font-weight: 600; padding: 0.3rem 0.75rem;
            border-radius: 100px; border: 1px solid rgba(255,255,255,0.15);
            background: rgba(255,255,255,0.06); color: rgba(255,255,255,0.7);
            cursor: pointer; transition: all 0.15s;
        }
        .scanner-toggle:hover { background: rgba(255,255,255,0.12); }

        #video-container {
            width: 100%; aspect-ratio: 1; background: #000; position: relative; overflow: hidden;
        }
        #qr-video { width: 100%; height: 100%; object-fit: cover; display: block; }
        .scan-overlay {
            position: absolute; inset: 0;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
        }
        .scan-frame {
            width: 60%; aspect-ratio: 1;
            border: 2px solid var(--gold2);
            border-radius: 12px;
            box-shadow: 0 0 0 9999px rgba(0,0,0,0.45);
            position: relative;
        }
        .scan-frame::before, .scan-frame::after {
            content: ''; position: absolute;
            width: 20px; height: 20px;
            border-color: var(--gold2); border-style: solid;
        }
        .scan-frame::before { top: -2px; left: -2px; border-width: 3px 0 0 3px; border-radius: 8px 0 0 0; }
        .scan-frame::after  { bottom: -2px; right: -2px; border-width: 0 3px 3px 0; border-radius: 0 0 8px 0; }
        .scan-line {
            position: absolute; left: 10%; right: 10%; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold2), transparent);
            animation: scan 2s ease-in-out infinite;
        }
        @keyframes scan {
            0%, 100% { top: 10%; } 50% { top: 85%; }
        }

        /* ── Manual search ────────────────────────────────────── */
        .manual-wrap { padding: 0.75rem 1rem 1rem; border-top: 1px solid rgba(255,255,255,0.06); }
        .manual-label { font-size: 0.65rem; font-weight: 700; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 0.4rem; }
        .manual-row { display: flex; gap: 0.5rem; }
        .manual-input {
            flex: 1; padding: 0.65rem 0.85rem;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px; color: #fff;
            font-family: 'DM Mono', monospace; font-size: 0.85rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: 0.05em;
            outline: none; transition: border 0.15s;
        }
        .manual-input::placeholder { color: rgba(255,255,255,0.25); font-family: 'DM Sans', sans-serif; font-size: 0.8rem; }
        .manual-input:focus { border-color: var(--gold2); }
        .btn-search {
            padding: 0.65rem 1rem; background: var(--gold);
            color: var(--navy2); font-weight: 700; font-size: 0.82rem;
            border-radius: 10px; border: none; cursor: pointer; transition: background 0.15s;
            white-space: nowrap;
        }
        .btn-search:hover { background: var(--gold2); }

        /* ── Result card ──────────────────────────────────────── */
        .result-card {
            border-radius: 20px; overflow: hidden;
            animation: fadeSlide 0.3s ease;
            display: none;
        }
        .result-card.show { display: block; }
        @keyframes fadeSlide {
            from { opacity: 0; transform: translateY(12px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .result-header {
            padding: 1.25rem 1.25rem 1rem;
            display: flex; align-items: flex-start; gap: 1rem;
        }
        .result-photo {
            width: 72px; height: 72px; border-radius: 12px;
            object-fit: cover; object-position: top center;
            flex-shrink: 0; border: 2px solid rgba(255,255,255,0.2);
        }
        .result-photo-ph {
            width: 72px; height: 72px; border-radius: 12px; flex-shrink: 0;
            background: rgba(255,255,255,0.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: rgba(255,255,255,0.4);
        }
        .result-name { font-size: 1.2rem; font-weight: 800; color: #fff; line-height: 1.2; margin-bottom: 0.2rem; }
        .result-code { font-family: 'DM Mono', monospace; font-size: 0.72rem; color: rgba(255,255,255,0.55); letter-spacing: 0.08em; margin-bottom: 0.4rem; }
        .result-dept { display: inline-flex; align-items: center; gap: 0.35rem; background: rgba(255,255,255,0.12); border-radius: 100px; padding: 0.2rem 0.65rem; font-size: 0.7rem; font-weight: 700; color: rgba(255,255,255,0.85); }

        .result-body { padding: 0 1.25rem 1.25rem; }

        .result-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 0.75rem; }
        .meta-item { background: rgba(255,255,255,0.07); border-radius: 8px; padding: 0.55rem 0.75rem; }
        .meta-lbl { font-size: 0.55rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.4); margin-bottom: 0.1rem; }
        .meta-val { font-size: 0.78rem; font-weight: 600; color: #fff; }

        .consent-banner { border-radius: 10px; padding: 0.65rem 0.85rem; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem; }

        .btn-row { display: grid; grid-template-columns: 1fr 1fr; gap: 0.6rem; }
        .btn-checkin {
            padding: 0.85rem; border-radius: 12px; border: none;
            font-size: 0.88rem; font-weight: 700; cursor: pointer; transition: all 0.15s;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
        }
        .btn-checkin.green { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .btn-checkin.green:hover { background: #A7F3D0; }
        .btn-checkin.red { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .btn-checkin.red:hover { background: #FECACA; }
        .btn-checkin.gray { background: rgba(255,255,255,0.08); color: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.1); }
        .btn-checkin:disabled { opacity: 0.45; cursor: not-allowed; }

        /* Status banners */
        .status-ok    { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .status-warn  { background: var(--amber-bg); color: var(--amber); border: 1px solid var(--amber-border); }
        .status-error { background: var(--red-bg);   color: var(--red);   border: 1px solid var(--red-border); }
        .status-banner {
            border-radius: 14px; padding: 1rem 1.25rem;
            display: flex; align-items: center; gap: 0.75rem;
            font-size: 0.82rem; font-weight: 700;
            animation: fadeSlide 0.3s ease;
            display: none;
        }
        .status-banner.show { display: flex; }
        .status-icon { font-size: 1.5rem; flex-shrink: 0; }

        /* ── Sync bar ─────────────────────────────────────────── */
        .sync-bar {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 0.85rem 1rem;
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
        }
        .sync-info { font-size: 0.72rem; color: rgba(255,255,255,0.5); }
        .sync-info strong { color: rgba(255,255,255,0.8); display: block; font-size: 0.78rem; margin-bottom: 1px; }
        .btn-sync {
            font-size: 0.72rem; font-weight: 700; padding: 0.4rem 0.9rem;
            border-radius: 100px; border: none;
            background: var(--navy); color: var(--gold2); cursor: pointer;
            border: 1px solid rgba(201,169,77,0.3); transition: all 0.15s; white-space: nowrap;
        }
        .btn-sync:hover { background: var(--blue); }
        .btn-sync:disabled { opacity: 0.5; cursor: wait; }

        /* ── Loading spinner ──────────────────────────────────── */
        .spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,0.2); border-top-color: var(--gold2); border-radius: 50%; animation: spin 0.6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-left">
        <img src="/images/congress_logo.png" class="topbar-logo" alt="Logo"/>
        <div>
            <div class="topbar-title">Camp Check-In</div>
            <div class="topbar-sub">Ogun Congress 2026</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:0.5rem">
        <span class="offline-pill" id="offline-pill">⚡ OFFLINE</span>
        <div class="spinner" id="loading-spinner" style="display:none"></div>
    </div>
</div>

<div class="main">

    <!-- Status flash -->
    <div class="status-banner" id="status-banner">
        <span class="status-icon" id="status-icon"></span>
        <div>
            <div id="status-title" style="font-size:0.9rem;font-weight:800"></div>
            <div id="status-body" style="font-size:0.75rem;font-weight:500;margin-top:2px;opacity:0.8"></div>
        </div>
    </div>

    <!-- Scanner -->
    <div class="scanner-wrap">
        <div class="scanner-head">
            <span class="scanner-title">📷 QR Scanner</span>
            <button class="scanner-toggle" id="toggle-camera" onclick="toggleCamera()">Start Camera</button>
        </div>
        <div id="video-container" style="display:none">
            <video id="qr-video" playsinline muted></video>
            <div class="scan-overlay">
                <div class="scan-frame">
                    <div class="scan-line"></div>
                </div>
            </div>
        </div>
        <div class="manual-wrap">
            <div class="manual-label">Manual — enter code or name</div>
            <div class="manual-row">
                <input type="text" id="manual-input" class="manual-input"
                       placeholder="OGN-2026-XXXXXX or name"
                       oninput="this.value = this.value.toUpperCase()"
                       onkeydown="if(event.key==='Enter') doSearch()"/>
                <button class="btn-search" onclick="doSearch()">Search</button>
            </div>
        </div>
    </div>

    <!-- Result card -->
    <div class="result-card" id="result-card">
        <div id="result-header-color" class="result-header" style="background:#1B3A8F">
            <div id="result-photo-el">
                <div class="result-photo-ph">👤</div>
            </div>
            <div>
                <div class="result-name" id="result-name">—</div>
                <div class="result-code" id="result-code">—</div>
                <span class="result-dept" id="result-dept">—</span>
            </div>
        </div>
        <div class="result-body" style="background:rgba(255,255,255,0.04)">
            <div class="result-meta">
                <div class="meta-item">
                    <div class="meta-lbl">Church</div>
                    <div class="meta-val" id="result-church">—</div>
                </div>
                <div class="meta-item">
                    <div class="meta-lbl">District</div>
                    <div class="meta-val" id="result-district">—</div>
                </div>
                <div class="meta-item">
                    <div class="meta-lbl">Gender</div>
                    <div class="meta-val" id="result-gender">—</div>
                </div>
                <div class="meta-item">
                    <div class="meta-lbl">Status</div>
                    <div class="meta-val" id="result-status">—</div>
                </div>
            </div>

            <div class="consent-banner" id="consent-banner" style="display:none"></div>

            <div class="btn-row">
                <button class="btn-checkin green" id="btn-checkin" onclick="doCheckIn('check_in')">
                    ✅ Check In
                </button>
                <button class="btn-checkin red" id="btn-checkout" onclick="doCheckIn('check_out')">
                    🚪 Check Out
                </button>
            </div>
        </div>
    </div>

    <!-- Sync bar -->
    <div class="sync-bar">
        <div class="sync-info">
            <strong id="sync-count">0 campers cached</strong>
            <span id="sync-time">Not synced yet</span>
        </div>
        <button class="btn-sync" id="btn-sync" onclick="syncNow()">⟳ Sync Now</button>
    </div>

</div>

<!-- jsQR -->
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    // ── State ─────────────────────────────────────────────────────────────────
    const DB_NAME    = 'ogun-checkin-v1';
    const DB_VERSION = 1;
    const META_KEY   = 'checkin_meta';

    let db            = null;
    let cameraActive  = false;
    let scanLoop      = null;
    let currentCamper = null;
    let pendingQueue  = [];
    let isOnline      = navigator.onLine;

    // ── IndexedDB setup ────────────────────────────────────────────────────────
    function openDB() {
        return new Promise((resolve, reject) => {
            const req = indexedDB.open(DB_NAME, DB_VERSION);
            req.onupgradeneeded = e => {
                const d = e.target.result;
                if (!d.objectStoreNames.contains('campers')) {
                    const s = d.createObjectStore('campers', { keyPath: 'camper_number' });
                    s.createIndex('full_name', 'full_name', { unique: false });
                }
                if (!d.objectStoreNames.contains('pending_events')) {
                    d.createObjectStore('pending_events', { keyPath: 'uuid' });
                }
                if (!d.objectStoreNames.contains('meta')) {
                    d.createObjectStore('meta', { keyPath: 'key' });
                }
            };
            req.onsuccess = e => resolve(e.target.result);
            req.onerror   = e => reject(e.target.error);
        });
    }

    function dbGet(store, key) {
        return new Promise((resolve, reject) => {
            const tx  = db.transaction(store, 'readonly');
            const req = tx.objectStore(store).get(key);
            req.onsuccess = () => resolve(req.result);
            req.onerror   = () => reject(req.error);
        });
    }

    function dbPut(store, value) {
        return new Promise((resolve, reject) => {
            const tx  = db.transaction(store, 'readwrite');
            const req = tx.objectStore(store).put(value);
            req.onsuccess = () => resolve(req.result);
            req.onerror   = () => reject(req.error);
        });
    }

    function dbGetAll(store) {
        return new Promise((resolve, reject) => {
            const tx  = db.transaction(store, 'readonly');
            const req = tx.objectStore(store).getAll();
            req.onsuccess = () => resolve(req.result);
            req.onerror   = () => reject(req.error);
        });
    }

    function dbClear(store) {
        return new Promise((resolve, reject) => {
            const tx  = db.transaction(store, 'readwrite');
            const req = tx.objectStore(store).clear();
            req.onsuccess = () => resolve();
            req.onerror   = () => reject(req.error);
        });
    }

    function generateUUID() {
        return ([1e7]+-1e3+-4e3+-8e3+-1e11).replace(/[018]/g, c =>
            (c ^ crypto.getRandomValues(new Uint8Array(1))[0] & 15 >> c / 4).toString(16));
    }

    // ── Online/Offline ─────────────────────────────────────────────────────────
    window.addEventListener('online',  () => { isOnline = true;  updateOnlinePill(); flushPending(); });
    window.addEventListener('offline', () => { isOnline = false; updateOnlinePill(); });

    function updateOnlinePill() {
        document.getElementById('offline-pill').classList.toggle('show', !isOnline);
    }

    // ── Camera ─────────────────────────────────────────────────────────────────
    async function toggleCamera() {
        if (cameraActive) {
            stopCamera();
        } else {
            await startCamera();
        }
    }

    async function startCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 640 }, height: { ideal: 640 } }
            });
            const video = document.getElementById('qr-video');
            video.srcObject = stream;
            await video.play();
            document.getElementById('video-container').style.display = 'block';
            document.getElementById('toggle-camera').textContent = 'Stop Camera';
            cameraActive = true;
            scanQR();
        } catch(e) {
            showStatus('error', '📷', 'Camera unavailable', 'Use manual search instead.');
        }
    }

    function stopCamera() {
        clearTimeout(scanLoop);
        const video = document.getElementById('qr-video');
        if (video.srcObject) {
            video.srcObject.getTracks().forEach(t => t.stop());
            video.srcObject = null;
        }
        document.getElementById('video-container').style.display = 'none';
        document.getElementById('toggle-camera').textContent = 'Start Camera';
        cameraActive = false;
    }

    function scanQR() {
        const video = document.getElementById('qr-video');
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
            const canvas = document.createElement('canvas');
            canvas.width  = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const imageData = canvas.getContext('2d').getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, { inversionAttempts: 'dontInvert' });
            if (code && code.data) {
                // QR encodes OGN:XXXX or /verify/XXXX or just the code
                let raw = code.data.trim();
                if (raw.startsWith('OGN:')) raw = raw.slice(4);
                if (raw.includes('/verify/')) raw = raw.split('/verify/').pop();
                handleCode(raw.trim().toUpperCase());
                return; // pause scanning until result dismissed
            }
        }
        scanLoop = setTimeout(scanQR, 250);
    }

    // ── Search & Lookup ────────────────────────────────────────────────────────
    async function doSearch() {
        const val = document.getElementById('manual-input').value.trim().toUpperCase();
        if (!val) return;
        await handleCode(val);
    }

    async function handleCode(query) {
        setLoading(true);
        clearResult();
        currentCamper = null;

        // Try offline cache first
        let camper = await dbGet('campers', query);

        // If not found by code, try name search in IndexedDB
        if (!camper && !query.startsWith('OGN-')) {
            const all = await dbGetAll('campers');
            const lq  = query.toLowerCase();
            camper = all.find(c => c.full_name?.toLowerCase().includes(lq));
        }

        // If online and not found locally, hit the API
        if (!camper && isOnline) {
            try {
                const resp = await fetch('/api/checkin/camper/' + encodeURIComponent(query), {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                });
                if (resp.ok) {
                    camper = await resp.json();
                    await dbPut('campers', camper); // cache it
                }
            } catch(e) {}
        }

        setLoading(false);

        if (!camper) {
            showStatus('error', '❓', 'Camper not found', isOnline
                ? 'This code/name was not found. Check the entry.'
                : 'Not in local cache. Reconnect to look up recently registered campers.');
            return;
        }

        currentCamper = camper;
        renderResult(camper);
        // Resume scanning after 3s
        if (cameraActive) { scanLoop = setTimeout(scanQR, 3000); }
    }

    function renderResult(c) {
        // Header colour by category
        const colours = { adventurer: '#1B6B3A', pathfinder: '#1B3A8F', senior_youth: '#6B1B1B' };
        document.getElementById('result-header-color').style.background = colours[c.category] || '#1B3A8F';

        // Photo
        const photoEl = document.getElementById('result-photo-el');
        if (c.photo_url) {
            photoEl.innerHTML = `<img src="${c.photo_url}" class="result-photo" alt="Photo" onerror="this.outerHTML='<div class=result-photo-ph>👤</div>'"/>`;
        } else {
            photoEl.innerHTML = '<div class="result-photo-ph">👤</div>';
        }

        document.getElementById('result-name').textContent     = c.full_name || '—';
        document.getElementById('result-code').textContent     = c.camper_number || '—';
        document.getElementById('result-dept').textContent     = (c.category_label || c.category || '—')
            + (c.club_rank ? ' · ' + c.club_rank : '');
        document.getElementById('result-church').textContent   = c.church_name || '—';
        document.getElementById('result-district').textContent = c.district_name || '—';
        document.getElementById('result-gender').textContent   = c.gender
            ? c.gender.charAt(0).toUpperCase() + c.gender.slice(1) : '—';

        // Check-in status
        const isIn = c.is_checked_in;
        document.getElementById('result-status').textContent   = isIn ? '✅ Checked In' : '⏳ Not checked in';

        // Consent
        const consentEl = document.getElementById('consent-banner');
        if (c.requires_consent) {
            consentEl.style.display = 'flex';
            if (c.consent_collected) {
                consentEl.className = 'consent-banner';
                consentEl.style.background = '#D1FAE5'; consentEl.style.color = '#065F46'; consentEl.style.border = '1px solid #6EE7B7';
                consentEl.textContent = '✅ Consent form collected';
            } else {
                consentEl.className = 'consent-banner';
                consentEl.style.background = '#FEF3C7'; consentEl.style.color = '#92400E'; consentEl.style.border = '1px solid #FCD34D';
                consentEl.textContent = '⚠️ Consent form NOT yet collected — collect at check-in';
            }
        } else {
            consentEl.style.display = 'none';
        }

        // Buttons
        document.getElementById('btn-checkin').disabled  = isIn;
        document.getElementById('btn-checkout').disabled = !isIn;

        document.getElementById('result-card').classList.add('show');
    }

    // ── Check-in / Check-out ───────────────────────────────────────────────────
    async function doCheckIn(eventType) {
        if (!currentCamper) return;

        const event = {
            uuid:          generateUUID(),
            camper_number: currentCamper.camper_number,
            event_type:    eventType,
            occurred_at:   new Date().toISOString(),
            device_id:     getDeviceId(),
        };

        // Update local cache immediately
        currentCamper.is_checked_in = (eventType === 'check_in');
        await dbPut('campers', currentCamper);
        renderResult(currentCamper);

        // Try to post immediately if online
        if (isOnline) {
            try {
                const resp = await fetch('/api/checkin/events', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ events: [event] }),
                });
                if (resp.ok) {
                    showStatus('ok', eventType === 'check_in' ? '✅' : '🚪',
                        eventType === 'check_in' ? 'Checked In!' : 'Checked Out',
                        currentCamper.full_name + ' — synced to server');
                    return;
                }
            } catch(e) {}
        }

        // Queue for later sync
        await dbPut('pending_events', event);
        showStatus('warn', '⚡',
            eventType === 'check_in' ? 'Checked In (offline)' : 'Checked Out (offline)',
            'Saved locally. Will sync when connection returns.');
    }

    // ── Sync ───────────────────────────────────────────────────────────────────
    async function syncNow() {
        const btn = document.getElementById('btn-sync');
        btn.disabled = true;
        btn.textContent = '⟳ Syncing…';

        try {
            // Download fresh camper list
            const resp = await fetch('/api/checkin/sync?page=1&per_page=500', {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            });

            if (resp.ok) {
                const data = await resp.json();
                const campers = data.data || data.campers || data;

                await dbClear('campers');
                for (const c of campers) {
                    await dbPut('campers', c);
                }

                await dbPut('meta', { key: META_KEY, synced_at: new Date().toISOString(), count: campers.length });
                updateSyncBar();
            }

            // Flush pending events
            await flushPending();

            showStatus('ok', '✅', 'Sync complete', 'Camper data updated.');
        } catch(e) {
            showStatus('error', '❌', 'Sync failed', 'Check your connection and try again.');
        }

        btn.disabled = false;
        btn.textContent = '⟳ Sync Now';
    }

    async function flushPending() {
        const pending = await dbGetAll('pending_events');
        if (!pending.length || !isOnline) return;

        try {
            const resp = await fetch('/api/checkin/events', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ events: pending }),
            });

            if (resp.ok) {
                await dbClear('pending_events');
            }
        } catch(e) {}
    }

    async function updateSyncBar() {
        const meta = await dbGet('meta', META_KEY);
        const all  = await dbGetAll('campers');
        document.getElementById('sync-count').textContent = all.length + ' campers cached';
        document.getElementById('sync-time').textContent  = meta?.synced_at
            ? 'Last synced: ' + new Date(meta.synced_at).toLocaleTimeString()
            : 'Not synced yet';
    }

    // ── UI helpers ─────────────────────────────────────────────────────────────
    function showStatus(type, icon, title, body) {
        const el = document.getElementById('status-banner');
        el.className = 'status-banner show ' + (type === 'ok' ? 'status-ok' : type === 'warn' ? 'status-warn' : 'status-error');
        document.getElementById('status-icon').textContent  = icon;
        document.getElementById('status-title').textContent = title;
        document.getElementById('status-body').textContent  = body;
        setTimeout(() => el.classList.remove('show'), 6000);
    }

    function clearResult() {
        document.getElementById('result-card').classList.remove('show');
    }

    function setLoading(v) {
        document.getElementById('loading-spinner').style.display = v ? 'inline-block' : 'none';
    }

    function getDeviceId() {
        let id = localStorage.getItem('checkin_device_id');
        if (!id) {
            id = 'device-' + generateUUID();
            localStorage.setItem('checkin_device_id', id);
        }
        return id;
    }

    // ── Init ───────────────────────────────────────────────────────────────────
    (async function init() {
        db = await openDB();
        updateOnlinePill();
        await updateSyncBar();
        // Auto-sync if online
        if (isOnline) syncNow();
    })();
</script>
</body>
</html>
