<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use Illuminate\Support\Facades\Log;

class DashboardExportController extends Controller
{
    public function export()
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            abort(403);
        }

        try {
            return $this->buildCsv();
        } catch (\Throwable $e) {
            Log::error('management_report_export_failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            abort(500, 'Export failed. Please try again.');
        }
    }

    private function buildCsv()
    {
        $filename = 'ogun-congress-2026-report-' . now()->format('Y-m-d-His') . '.csv';
        $tmpPath  = tempnam(sys_get_temp_dir(), 'ogun_') . '.csv';

        $fh = fopen($tmpPath, 'w');

        // UTF-8 BOM — makes Excel open without encoding prompts
        fwrite($fh, "\xEF\xBB\xBF");

        // ── Summary ───────────────────────────────────────────────────────────
        fputcsv($fh, ['OGUN CONFERENCE YOUTH CONGRESS 2026 — MANAGEMENT REPORT']);
        fputcsv($fh, ['Generated', now()->format('d M Y H:i')]);
        fputcsv($fh, []);
        fputcsv($fh, ['REGISTRATION SUMMARY', '']);
        fputcsv($fh, ['Metric', 'Count']);
        fputcsv($fh, ['Total Registered Campers', Camper::count()]);
        fputcsv($fh, ['Adventurers', Camper::where('category', 'adventurer')->count()]);
        fputcsv($fh, ['Pathfinders', Camper::where('category', 'pathfinder')->count()]);
        fputcsv($fh, ['Senior Youth', Camper::where('category', 'senior_youth')->count()]);
        fputcsv($fh, ['Officials', Camper::where('is_official', true)->count()]);
        fputcsv($fh, []);
        fputcsv($fh, ['CODES', '']);
        fputcsv($fh, ['Active (Unclaimed)', RegistrationCode::where('status', 'ACTIVE')->count()]);
        fputcsv($fh, ['Claimed', RegistrationCode::where('status', 'CLAIMED')->count()]);
        fputcsv($fh, ['Pending Payment', RegistrationCode::where('status', 'PENDING')->count()]);
        fputcsv($fh, ['Void / Expired', RegistrationCode::whereIn('status', ['VOID', 'EXPIRED'])->count()]);
        fputcsv($fh, []);
        fputcsv($fh, ['PAYMENTS', '']);
        fputcsv($fh, ['Pending Offline Payments', OfflinePayment::where('status', 'pending')->count()]);
        fputcsv($fh, ['Confirmed Offline Payments', OfflinePayment::where('status', 'confirmed')->count()]);
        fputcsv($fh, []);
        fputcsv($fh, ['CAMP READINESS', '']);
        fputcsv($fh, ['Consent Forms Outstanding', Camper::whereIn('category', ['adventurer', 'pathfinder'])->where('consent_collected', false)->count()]);
        fputcsv($fh, ['Consent Forms Collected', Camper::whereIn('category', ['adventurer', 'pathfinder'])->where('consent_collected', true)->count()]);
        fputcsv($fh, ['Photos Pending Approval', Camper::where('photo_status', 'pending')->count()]);
        fputcsv($fh, ['Photos Approved', Camper::where('photo_status', 'approved')->count()]);
        fputcsv($fh, []);

        // ── T-shirt sizes ─────────────────────────────────────────────────────
        fputcsv($fh, ['T-SHIRT SIZES', '']);
        fputcsv($fh, ['Size', 'Count']);
        $sizes = Camper::selectRaw('tshirt_size, COUNT(*) as cnt')
            ->whereNotNull('tshirt_size')
            ->groupBy('tshirt_size')
            ->orderByRaw("FIELD(tshirt_size,'XS','S','M','L','XL','XXL','XXXL')")
            ->get();
        foreach ($sizes as $s) {
            fputcsv($fh, [$s->tshirt_size, $s->cnt]);
        }
        fputcsv($fh, []);

        // ── By district / church ──────────────────────────────────────────────
        fputcsv($fh, ['BREAKDOWN BY DISTRICT & CHURCH', '', '', '', '', '']);
        fputcsv($fh, ['District', 'Church', 'Adventurers', 'Pathfinders', 'Senior Youth', 'Total']);

        $rows = Camper::with('church.district')
            ->selectRaw('church_id, category, COUNT(*) as cnt')
            ->groupBy('church_id', 'category')
            ->get();

        $byChurch = [];
        foreach ($rows as $r) {
            $district = $r->church?->district?->name ?? 'Unknown';
            $church   = $r->church?->name ?? 'Unknown';
            $key      = $district . '||' . $church;
            $byChurch[$key] ??= ['district' => $district, 'church' => $church, 'adventurer' => 0, 'pathfinder' => 0, 'senior_youth' => 0];
            $byChurch[$key][$r->category] = $r->cnt;
        }
        ksort($byChurch);

        foreach ($byChurch as $data) {
            $total = $data['adventurer'] + $data['pathfinder'] + $data['senior_youth'];
            fputcsv($fh, [$data['district'], $data['church'], $data['adventurer'], $data['pathfinder'], $data['senior_youth'], $total]);
        }
        fputcsv($fh, []);

        // ── Full camper list ──────────────────────────────────────────────────
        fputcsv($fh, ['FULL CAMPER LIST', '', '', '', '', '', '', '', '', '', '']);
        fputcsv($fh, ['#', 'Camper No.', 'Full Name', 'Category', 'Gender', 'T-Shirt', 'Church', 'District', 'Club Rank', 'Consent', 'Registered']);

        // Chunk to avoid memory exhaustion on 1,000+ campers
        $i = 1;
        Camper::with('church.district')
            ->orderBy('category')->orderBy('church_id')->orderBy('full_name')
            ->chunk(200, function ($campers) use ($fh, &$i) {
                foreach ($campers as $c) {
                    fputcsv($fh, [
                        $i++,
                        $c->camper_number,
                        $c->full_name,
                        ucfirst(str_replace('_', ' ', $c->category?->value ?? '')),
                        ucfirst($c->gender?->value ?? ''),
                        $c->tshirt_size ?? '',
                        $c->church?->name ?? '',
                        $c->church?->district?->name ?? '',
                        $c->club_rank ?? '',
                        $c->consent_collected ? 'Yes' : 'No',
                        $c->created_at?->format('d M Y'),
                    ]);
                }
            });

        fclose($fh);

        return response()
            ->download($tmpPath, $filename, ['Content-Type' => 'text/csv; charset=UTF-8'])
            ->deleteFileAfterSend(true);
    }
}
