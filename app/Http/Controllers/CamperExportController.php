<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CamperExportController extends Controller
{
    public function export(Request $request)
    {
        $user = auth()->user();

        if (! $user->hasAnyRole([
            'super_admin', 'camp_director', 'district_coordinator',
            'secretariat', 'church_coordinator',
        ])) {
            abort(403);
        }

        $query = Camper::with(['church.district'])->orderBy('church_id')->orderBy('full_name');

        // Scope by role — always enforce, regardless of request params
        if ($user->hasRole('church_coordinator') && $user->church_id) {
            $query->where('church_id', $user->church_id);
        } elseif ($user->isDistrictCoordinator() && $user->district_id) {
            $churchIds = Church::where('district_id', $user->district_id)->pluck('id');
            $query->whereIn('church_id', $churchIds);
        } else {
            // Admin-level roles — apply optional request filters
            if ($request->filled('church_id')) {
                $query->where('church_id', $request->church_id);
            }
            if ($request->filled('district_id')) {
                $churchIds = Church::where('district_id', $request->district_id)->pluck('id');
                $query->whereIn('church_id', $churchIds);
            }
        }

        // Category filter applies to all roles
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('club_rank')) {
            $query->where('club_rank', $request->club_rank);
        }

        $campers  = $query->get();
        $byChurch = $campers->groupBy('church_id');

        $district = $request->filled('district_id') ? District::find($request->district_id) : null;
        $church   = $request->filled('church_id')   ? Church::find($request->church_id)     : null;
        $filters  = compact('district', 'church');

        $pdf = Pdf::loadView('pdf.camper-list', compact('campers', 'byChurch', 'filters'))
            ->setPaper('a4', 'portrait')
            ->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => false]);

        $filename = 'camper-list-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }
}
