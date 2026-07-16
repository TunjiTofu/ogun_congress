<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use Illuminate\Support\Facades\Log;

class RejectedPhotosExportController extends Controller
{
    public function export()
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            abort(403);
        }

        try {
            return $this->generatePdf();
        } catch (\Throwable $e) {
            Log::error('rejected_photos_export_failed', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            throw $e;
        }
    }

    private function generatePdf()
    {
        // Fetch all rejected campers, grouped by district then church
        $campers = Camper::with(['church.district'])
            ->where('photo_status', 'rejected')
            ->orderBy('church_id')
            ->orderBy('full_name')
            ->get();

        // Group: [ district_name => [ church_name => [ camper, ... ] ] ]
        $grouped = [];
        foreach ($campers as $c) {
            $district = $c->church?->district?->name ?? 'Unknown District';
            $church   = $c->church?->name ?? 'Unknown Church';
            $grouped[$district][$church][] = $c;
        }
        ksort($grouped);
        foreach ($grouped as &$churches) ksort($churches);

        $totalCount       = $campers->count();
        $churchCount      = $campers->unique('church_id')->count();
        $districtCount    = count($grouped);
        $generatedAt      = now()->format('d M Y, H:i') . ' WAT';

        $html = view('pdf.rejected-photos-report', compact(
            'grouped', 'totalCount', 'churchCount', 'districtCount', 'generatedAt'
        ))->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        return $pdf->download('rejected-photos-report-' . now()->format('Y-m-d') . '.pdf');
    }
}
