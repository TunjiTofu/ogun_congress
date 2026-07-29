<?php

namespace App\Http\Controllers;

use App\Models\CamperSkillRegistration;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SkillRegistrationExportController extends Controller
{
    public function export(Request $request)
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin', 'skill_manager'])) {
            abort(403);
        }

        try {
            return $this->generatePdf($request);
        } catch (\Throwable $e) {
            Log::error('skill_registration_export_failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            throw $e;
        }
    }

    private function generatePdf(Request $request)
    {
        $query = CamperSkillRegistration::with([
            'camper.church.district',
            'skill',
        ]);

        // Optional filters passed as query params from the export button
        if ($skillId = $request->integer('skill_id')) {
            $query->where('skill_id', $skillId);
        }
        if ($churchId = $request->integer('church_id')) {
            $query->whereHas('camper', fn ($q) => $q->where('church_id', $churchId));
        }
        if ($districtId = $request->integer('district_id')) {
            $query->whereHas('camper.church', fn ($q) => $q->where('district_id', $districtId));
        }
        if ($category = $request->string('category')->toString()) {
            $query->whereHas('camper', fn ($q) => $q->where('category', $category));
        }

        $registrations = $query
            ->join('skills',  'camper_skill_registrations.skill_id',  '=', 'skills.id')
            ->join('campers', 'camper_skill_registrations.camper_id', '=', 'campers.id')
            ->orderBy('skills.name')
            ->orderBy('campers.full_name')
            ->select('camper_skill_registrations.*')
            ->get();

        // Group by skill for the PDF layout
        $bySkill = $registrations->groupBy('skill_id')->map(fn ($rows) => [
            'skill' => $rows->first()->skill,
            'rows'  => $rows,
        ])->sortBy('skill.name')->values();

        // Overall skill capacity summary
        $skillSummary = Skill::withCount('registrations')
            ->orderBy('name')
            ->get()
            ->map(fn ($s) => [
                'name'       => $s->name,
                'category'   => $s->categoryLabel(),
                'registered' => $s->registrations_count,
                'capacity'   => $s->maximum_attendees,
                'remaining'  => max(0, $s->maximum_attendees - $s->registrations_count),
            ]);

        $totalRegistered = $registrations->count();
        $generatedAt     = now('Africa/Lagos')->format('d M Y, H:i') . ' WAT';

        $filterLabel = collect([
            $request->integer('skill_id')    ? 'Skill: ' . optional(Skill::find($request->integer('skill_id')))->name : null,
            $request->integer('district_id') ? 'District filter applied' : null,
            $request->integer('church_id')   ? 'Church filter applied' : null,
            $request->string('category')->toString() ? 'Category: ' . $request->string('category') : null,
        ])->filter()->implode(' · ') ?: 'All registrations';

        $html = view('pdf.skill-registrations', compact(
            'bySkill', 'skillSummary', 'totalRegistered', 'generatedAt', 'filterLabel'
        ))->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        return $pdf->download('skill-registrations-' . now()->format('Y-m-d') . '.pdf');
    }
}
