<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\RegistrationCode;
use App\Services\DocumentGenerationService;
use Illuminate\Http\Request;

class CamperPortalController extends Controller
{
    public function __construct(
        private readonly DocumentGenerationService $documentService,
    ) {}

    /**
     * GET /portal
     * The camper portal login page — enter registration code to access.
     */
    public function index()
    {
        return view('portal.index');
    }

    /**
     * POST /portal/login
     * Validate code and show the camper's personal dashboard.
     */
    public function login(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $code = strtoupper(trim($request->input('code')));

        $registrationCode = RegistrationCode::where('code', $code)
            ->where('status', 'CLAIMED')
            ->with(['camper.church.district'])
            ->first();

        if (! $registrationCode || ! $registrationCode->camper) {
            return back()->withInput()->with('error',
                'Code not found or registration not yet complete. '
                . 'If you just registered, please wait a moment and try again.'
            );
        }

        // Store code in session as the "camper session"
        session(['camper_code' => $code]);

        return redirect()->route('portal.dashboard');
    }

    /**
     * GET /portal/dashboard
     * The camper's personal portal — view documents and announcements.
     */
    public function dashboard()
    {
        $code = session('camper_code');

        if (! $code) {
            return redirect()->route('portal.index')->with('error', 'Please enter your registration code.');
        }

        $registrationCode = RegistrationCode::where('code', $code)
            ->where('status', 'CLAIMED')
            ->with(['camper.church.district'])
            ->firstOrFail();

        $camper = $registrationCode->camper;

        $idCardUrl = $camper->id_card_path
            ? $this->documentService->getDownloadUrl($camper->id_card_path)
            : null;

        $consentFormUrl = $camper->consent_form_path
            ? $this->documentService->getDownloadUrl($camper->consent_form_path)
            : null;

        // Announcements — fetch from camp_settings
        $announcements = \App\Models\CampSetting::where('group', 'announcements')
            ->orderByDesc('updated_at')
            ->get();

        return view('portal.dashboard', compact(
            'camper',
            'registrationCode',
            'idCardUrl',
            'consentFormUrl',
            'announcements',
        ));
    }

    /**
     * POST /portal/logout
     */
    public function logout()
    {
        session()->forget('camper_code');
        return redirect()->route('portal.index')->with('success', 'You have been logged out.');
    }
}
