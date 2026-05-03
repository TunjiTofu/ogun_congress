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

    public function downloads(string $code)
    {
        $registrationCode = \App\Models\RegistrationCode::with('camper')
            ->where('code', $code)->first();

        if (! $registrationCode?->camper) {
            return response()->json(['id_card_url' => null, 'consent_form_url' => null]);
        }

        $camper = $registrationCode->camper;

        return response()->json([
            'id_card_url'      => $camper->id_card_path
                ? route('documents.download', ['path' => base64_encode($camper->id_card_path)])
                : null,
            'consent_form_url' => $camper->consent_form_path
                ? route('documents.download', ['path' => base64_encode($camper->consent_form_path)])
                : null,
        ]);
    }

    // ── Web routes ────────────────────────────────────────────────────────────

    public function validateCodeWeb(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);
        try {
            $this->registrationService->validateCode($request->input('code'));
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
        return redirect()->route('registration.form', ['code' => $request->input('code')]);
    }

    public function form(string $code)
    {
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
        $data = $request->validated();
        unset($data['photo']);

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $file     = $request->file('photo');
            $realPath = $file->getRealPath();

            if ($realPath && file_exists($realPath)) {
                $rawBytes = file_get_contents($realPath);

                // Convert to JPEG via GD — handles WebP, PNG, GIF, JPEG.
                // GD's imagecreatefromstring() auto-detects the format.
                // Storing as JPEG ensures DomPDF compatibility permanently.
                $jpegBytes = $this->toJpeg($rawBytes);

                $data['photo_contents']  = $jpegBytes;
                $data['photo_mime_type'] = 'image/jpeg';
                $data['photo_filename']  = 'photo.jpg';
            }
        }

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
