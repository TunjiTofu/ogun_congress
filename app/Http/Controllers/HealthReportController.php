<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\District;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HealthReportController extends Controller
{
    public function export(Request $request)
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            abort(403);
        }

        try {
            // Only campers with at least one health concern recorded
            $campers = Camper::with([
                'church.district',
                'health',
                'contacts' => fn ($q) => $q->where('type', 'parent_guardian'),
            ])
                ->whereHas('health', fn ($q) =>
                $q->where(fn ($q2) =>
                $q2->whereNotNull('medical_conditions')->where('medical_conditions', '!=', '')
                    ->orWhere(fn ($q3) => $q3->whereNotNull('medications')->where('medications', '!=', ''))
                    ->orWhere(fn ($q4) => $q4->whereNotNull('allergies')->where('allergies', '!=', ''))
                )
                )
                ->get()
                // Sort in PHP to avoid ambiguous column joins
                ->sortBy([
                    fn ($a, $b) => strcmp(
                        $a->church?->district?->name ?? '',
                        $b->church?->district?->name ?? ''
                    ),
                    fn ($a, $b) => strcmp($a->full_name ?? '', $b->full_name ?? ''),
                ]);

            // Group by district for the PDF layout
            $byDistrict = $campers
                ->groupBy(fn ($c) => $c->church?->district?->name ?? 'Unknown District')
                ->sortKeys();

            $totalCount  = $campers->count();
            $generatedAt = now('Africa/Lagos')->format('d M Y, H:i') . ' WAT';

            $html = view('pdf.health-report', compact(
                'byDistrict', 'totalCount', 'generatedAt'
            ))->render();

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => false,
                    'isRemoteEnabled'      => false,
                    'defaultFont'          => 'DejaVu Sans',
                    'dpi'                  => 150,
                ]);

            return $pdf->download('health-report-' . now()->format('Y-m-d') . '.pdf');

        } catch (\Throwable $e) {
            Log::error('health_report_export_failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}
