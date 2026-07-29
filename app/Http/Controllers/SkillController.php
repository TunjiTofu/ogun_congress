<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\CamperSkillRegistration;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SkillController extends Controller
{
    private const SESSION_KEY = 'skill_portal_camper_id';

    // ── Public portal ─────────────────────────────────────────────────────────

    /** GET /skills — Show code entry or dashboard if already logged in. */
    public function index()
    {
        if ($camperId = session(self::SESSION_KEY)) {
            $camper = Camper::with(['church.district', 'skillRegistration.skill'])
                ->find($camperId);
            if ($camper) {
                return redirect()->route('skills.dashboard');
            }
            session()->forget(self::SESSION_KEY);
        }

        return view('skills.index');
    }

    /** POST /skills/login — Validate registration code. */
    public function login(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $code = strtoupper(trim($request->code));

        $camper = Camper::whereHas('registrationCode', fn ($q) => $q->where('code', $code)
            ->where('status', 'CLAIMED'))
            ->with(['church.district', 'skillRegistration.skill'])
            ->first();

        if (! $camper) {
            return back()
                ->withInput()
                ->with('error', 'Registration code not found or registration not yet complete. Please ensure you have finished your camp registration first.');
        }

        session([self::SESSION_KEY => $camper->id]);

        return redirect()->route('skills.dashboard');
    }

    /** GET /skills/dashboard — Show camper profile + eligible skills. */
    public function dashboard()
    {
        $camper = $this->authCamper();
        if (! $camper) return redirect()->route('skills.index');

        $registrationOpen = $this->isRegistrationOpen();
        $existing         = $camper->skillRegistration;

        $skills = $registrationOpen
            ? Skill::eligibleFor($camper)->orderByRaw("category IS NULL DESC")->orderBy('name')->get()
            : collect();

        return view('skills.dashboard', compact('camper', 'skills', 'existing', 'registrationOpen'));
    }

    /** POST /skills/register — Select or change a skill. */
    public function register(Request $request)
    {
        $camper = $this->authCamper();
        if (! $camper) return redirect()->route('skills.index');

        if (! $this->isRegistrationOpen()) {
            return back()->with('error', 'Skill registration is currently closed.');
        }

        $request->validate(['skill_id' => ['required', 'integer', 'exists:skills,id']]);

        $newSkill = Skill::findOrFail($request->skill_id);

        // Eligibility check
        $eligible = Skill::where('id', $newSkill->id)
            ->eligibleFor($camper)
            ->exists();

        if (! $eligible) {
            return back()->with('error', 'You are not eligible for this skill.');
        }

        try {
            DB::transaction(function () use ($camper, $newSkill) {
                $existing = $camper->skillRegistration;

                // Capacity check inside the transaction with a lock
                $currentCount = CamperSkillRegistration::where('skill_id', $newSkill->id)->lockForUpdate()->count();
                if ($currentCount >= $newSkill->maximum_attendees) {
                    throw new \RuntimeException('This skill is now fully booked. Please choose another.');
                }

                if ($existing) {
                    // Change: update in place (releases old slot, takes new one)
                    $existing->update([
                        'skill_id'    => $newSkill->id,
                        'selected_at' => now(),
                        'updated_by'  => null,
                    ]);
                } else {
                    // New registration
                    CamperSkillRegistration::create([
                        'camper_id'   => $camper->id,
                        'skill_id'    => $newSkill->id,
                        'selected_at' => now(),
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('skill_registration_failed', [
                'camper_id' => $camper->id,
                'skill_id'  => $newSkill->id,
                'error'     => $e->getMessage(),
            ]);
            return back()->with('error', 'An error occurred. Please try again.');
        }

        return redirect()->route('skills.dashboard')
            ->with('success', 'You have successfully registered for: ' . $newSkill->name);
    }

    /** POST /skills/logout */
    public function logout()
    {
        session()->forget(self::SESSION_KEY);
        return redirect()->route('skills.index')->with('info', 'You have been logged out.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authCamper(): ?Camper
    {
        $id = session(self::SESSION_KEY);
        if (! $id) return null;

        return Camper::with(['church.district', 'skillRegistration.skill'])->find($id);
    }

    private function isRegistrationOpen(): bool
    {
        return setting('skill_registration_open', '0') === '1';
    }
}
