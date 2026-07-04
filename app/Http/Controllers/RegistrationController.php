<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRegistrationRequest;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService,
    ) {}

    // ── JSON API ──────────────────────────────────────────────────────────────

    public function validateCode(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);
        try {
            $prefill = $this->registrationService->validateCode($request->input('code'));
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
        return response()->json($prefill);
    }

    public function status(string $code): JsonResponse
    {
        try {
            $registrationCode = \App\Models\RegistrationCode::where('code', $code)->firstOrFail();
            return response()->json(['status' => $registrationCode->status->value]);
        } catch (\Throwable) {
            return response()->json(['status' => 'not_found'], 404);
        }
    }

    public function downloads(string $identifier)
    {
        // Accept either a registration code OR a camper_number
        $camper = \App\Models\Camper::where('camper_number', $identifier)->first();

        if (! $camper) {
            // Fallback: look up via registration code
            $registrationCode = \App\Models\RegistrationCode::with('camper')
                ->where('code', $identifier)->first();
            $camper = $registrationCode?->camper;
        }

        if (! $camper) {
            return response()->json(['status' => 'not_found']);
        }

        $needsConsent = $camper->requiresConsentForm();
        $idReady      = (bool) $camper->id_card_path;
        $consentReady = ! $needsConsent || (bool) $camper->consent_form_path;
        $allReady     = $idReady && $consentReady;

        return response()->json([
            'status'        => $allReady ? 'ready' : 'pending',
            'needs_consent' => $needsConsent,
            'urls'          => [
                'id_card'      => $camper->id_card_path
                    ? route('documents.download', ['path' => base64_encode($camper->id_card_path)])
                    : null,
                'consent_form' => $camper->consent_form_path
                    ? route('documents.download', ['path' => base64_encode($camper->consent_form_path)])
                    : null,
            ],
        ]);
    }

    // ── Web routes ────────────────────────────────────────────────────────────

    /**
     * Whether the overall registration programme is open.
     * Controls coordinator batch creation and the welcome page banner.
     * Does NOT gate the camper web form — use websiteFormLocked() for that.
     */
    private function registrationIsOpen(): bool
    {
        if (setting('registration_open', '1') !== '1') {
            return false;
        }
        $closesAt = setting('registration_closes_at');
        if ($closesAt && now()->gt(\Illuminate\Support\Carbon::parse($closesAt))) {
            return false;
        }
        return true;
    }

    /**
     * Whether the public website registration form is explicitly locked.
     *
     * This is a SEPARATE toggle from registration_open. It specifically prevents
     * campers from completing registration on the website — for example, after the
     * congress has started and you want to stop walk-in online registrations without
     * closing the admin panel for coordinators.
     *
     * Set camp_setting key "website_form_locked" to "1" to lock, "0" to unlock.
     */
    private function websiteFormLocked(): bool
    {
        return setting('website_form_locked', '0') === '1';
    }

    /**
     * POST /registration/validate
     * Validates the code and redirects to the form — or blocks if the website form is locked.
     * Note: we do NOT block on registrationIsOpen() here. A camper who already has a code
     * should be able to proceed even when registration is "closed" to new batches.
     */
    public function validateCodeWeb(Request $request)
    {
        if ($this->websiteFormLocked()) {
            return back()->with('error', 'Online registration is currently not available. Please contact your church coordinator for more information.');
        }

        $request->validate(['code' => ['required', 'string']]);
        try {
            $this->registrationService->validateCode($request->input('code'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('registration.form', ['code' => $request->input('code')]);
    }

    /**
     * GET /registration/form/{code}
     * Shows the registration form — blocked only by websiteFormLocked(), not registrationIsOpen().
     */
    public function form(string $code)
    {
        // Block the form only if it is explicitly locked — not just because
        // registration is "closed" (coordinators may still be processing batches).
        if ($this->websiteFormLocked()) {
            return redirect()->route('registration.index')
                ->with('error', 'Online registration is currently not available. Please contact your church coordinator for more information.');
        }

        try {
            $prefill = $this->registrationService->validateCode($code);
        } catch (\Throwable $e) {
            return redirect()->route('registration.index')->with('error', $e->getMessage());
        }

        $districts = \App\Models\District::orderBy('name')->get();

        $clubRanks = \App\Models\ClubRank::orderBy('sort_order')
            ->get(['ministry', 'rank_name'])
            ->groupBy('ministry')
            ->map(fn ($ranks) => $ranks->pluck('rank_name')->values())
            ->toArray();

        return view('registration.form', compact('code', 'prefill', 'districts', 'clubRanks'));
    }

    /**
     * POST /registration/submit (web form)
     *
     * CRITICAL — Photo handling for shared hosting:
     *
     * 1. Read file into memory IMMEDIATELY (before any DB work) to avoid /tmp cleanup.
     * 2. Convert to JPEG using GD right here in the controller.
     *    This guarantees DomPDF always gets a JPEG regardless of what the user uploaded
     *    (WebP, PNG, JPEG, HEIC-converted-to-JPEG by browser, etc.).
     * 3. Pass raw JPEG bytes to the service via photo_contents.
     */
    public function submitWeb(SubmitRegistrationRequest $request)
    {
        // Double-check the lock at submission time (in case the form was already open
        // in the browser when the setting was toggled).
        if ($this->websiteFormLocked()) {
            return redirect()->route('registration.index')
                ->with('error', 'Online registration is currently not available. Please contact your church coordinator.');
        }

        $data = $request->validated();
        unset($data['photo']);

        // ── Photo from file upload ───────────────────────────────────────────
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file     = $request->file('photo');
            $realPath = $file->getRealPath();

            if ($realPath && file_exists($realPath)) {
                $rawBytes  = file_get_contents($realPath);
                $jpegBytes = $this->toJpeg($rawBytes);

                $data['photo_contents']  = $jpegBytes;
                $data['photo_mime_type'] = 'image/jpeg';
                $data['photo_filename']  = 'photo.jpg';
            }
        }

        // ── Photo from live camera capture (base64 data URL) ─────────────────
        // Submitted via <input type="hidden" name="photo_data_url"> when camper uses camera.
        // Takes precedence only when no file upload is present.
        if (empty($data['photo_contents'])) {
            $dataUrl = $request->input('photo_data_url', '');
            if (str_starts_with($dataUrl, 'data:image/')) {
                // Strip the data URL prefix: data:image/jpeg;base64,{base64}
                $base64   = preg_replace('/^data:image\/[a-z]+;base64,/', '', $dataUrl);
                $rawBytes = base64_decode($base64);
                if ($rawBytes !== false && strlen($rawBytes) > 0) {
                    $jpegBytes = $this->toJpeg($rawBytes);
                    $data['photo_contents']  = $jpegBytes;
                    $data['photo_mime_type'] = 'image/jpeg';
                    $data['photo_filename']  = 'photo.jpg';
                }
            }
        }
        unset($data['photo_data_url']); // never passed to service

        $camper = $this->registrationService->submit($data);

        return redirect()->route('registration.success', ['code' => $camper->camper_number]);
    }

    public function success(string $code)
    {
        $camper = \App\Models\Camper::where('camper_number', $code)->first();

        return view('registration.success', compact('code', 'camper'));
    }

    /**
     * Convert raw image bytes to JPEG using GD.
     * Falls back to original bytes if GD is unavailable or conversion fails.
     */
    private function toJpeg(string $rawBytes): string
    {
        if (! extension_loaded('gd')) {
            return $rawBytes;
        }

        try {
            $img = @imagecreatefromstring($rawBytes);

            if ($img === false) {
                return $rawBytes;
            }

            // Preserve transparency for PNG sources
            $width  = imagesx($img);
            $height = imagesy($img);
            $canvas = imagecreatetruecolor($width, $height);
            $white  = imagecolorallocate($canvas, 255, 255, 255);
            imagefill($canvas, 0, 0, $white);
            imagecopy($canvas, $img, 0, 0, 0, 0, $width, $height);
            imagedestroy($img);

            ob_start();
            imagejpeg($canvas, null, 90);
            $jpeg = ob_get_clean();
            imagedestroy($canvas);

            return $jpeg ?: $rawBytes;
        } catch (\Throwable) {
            return $rawBytes;
        }
    }
}
