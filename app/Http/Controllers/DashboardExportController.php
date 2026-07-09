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

        ini_set('memory_limit', '256M');

        try {
            return $this->generatePdf();
        } catch (\Throwable $e) {
            Log::error('management_report_export_failed', [
                'message'   => $e->getMessage(),
                'exception' => get_class($e),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'trace'     => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function generatePdf()
    {
        $stats                   = $this->gatherStats();
        $byChurch                = $this->gatherByChurch();
        $tshirtSizes             = $this->gatherTshirtSizes();
        $tshirtByDistrictChurch  = $this->gatherTshirtByDistrictChurch();
        $tshirtByDeptDistrict    = $this->gatherTshirtByDeptDistrict();
        $unclaimedByChurch       = $this->gatherUnclaimedByChurch();
        $tshirtByDept            = $this->gatherTshirtByDept();
        $logoBase64              = $this->logoBase64();
        $campVenue               = setting('camp_venue', 'Abeokuta');
        $campDates               = setting('camp_dates', 'Aug 16–22, 2026');

        $html = view('pdf.management-report', compact(
            'stats', 'byChurch', 'tshirtSizes',
            'tshirtByDistrictChurch', 'tshirtByDeptDistrict',
            'unclaimedByChurch', 'tshirtByDept',
            'logoBase64', 'campVenue', 'campDates'
        ))->render();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => false,
                'isRemoteEnabled'      => false,
                'defaultFont'          => 'DejaVu Sans',
                'dpi'                  => 150,
            ]);

        return $pdf->download(
            'ogun-congress-2026-management-report-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    // ── Stats ─────────────────────────────────────────────────────────────────

    private function gatherStats(): array
    {
        $totalCampers = Camper::count();

        $adventurers = Camper::where('category', 'adventurer')->count();
        $pathfinders = Camper::where('category', 'pathfinder')->count();
        $seniorYouth = Camper::where('category', 'senior_youth')->count();
        $officials   = Camper::where('is_official', true)->count();

        $male   = Camper::where('gender', 'male')->count();
        $female = Camper::where('gender', 'female')->count();

        $codesPending     = RegistrationCode::where('status', 'PENDING')->count();
        $codesActive      = RegistrationCode::where('status', 'ACTIVE')->count();
        $codesClaimed     = RegistrationCode::where('status', 'CLAIMED')->count();
        $codesVoidExpired = RegistrationCode::whereIn('status', ['VOID', 'EXPIRED'])->count();
        $totalCodes       = $codesPending + $codesActive + $codesClaimed + $codesVoidExpired;

        $onlinePayments           = RegistrationCode::where('payment_type', 'online')->where('status', 'CLAIMED')->count();
        $offlinePaymentsConfirmed = OfflinePayment::where('status', 'confirmed')->count();
        $offlinePaymentsPending   = OfflinePayment::where('status', 'pending')->count();
        $offlinePaymentsRejected  = OfflinePayment::where('status', 'rejected')->count();
        $confirmedPayments        = $onlinePayments + $offlinePaymentsConfirmed;

        // Revenue from completed registrations (CLAIMED codes)
        $claimedRevenue = (int) RegistrationCode::where('status', 'CLAIMED')->sum('amount_paid');

        // Revenue from confirmed payments where registration is not yet complete (ACTIVE codes)
        $activeRevenue = (int) RegistrationCode::where('status', 'ACTIVE')->sum('amount_paid');

        // Total confirmed revenue = claimed + active (both are paid and confirmed)
        $totalRevenue = $claimedRevenue + $activeRevenue;

        // Pending revenue: offline payments awaiting finance approval
        $pendingRevenue = (int) OfflinePayment::where('status', 'pending')->sum('amount');

        $consentOutstanding = Camper::whereIn('category', ['adventurer', 'pathfinder'])->where('consent_collected', false)->count();
        $consentCollected   = Camper::whereIn('category', ['adventurer', 'pathfinder'])->where('consent_collected', true)->count();
        $photosPending      = Camper::where('photo_status', 'pending')->count();
        $photosApproved     = Camper::where('photo_status', 'approved')->count();

        return [
            'total_campers'              => $totalCampers,
            'adventurers'                => $adventurers,
            'pathfinders'                => $pathfinders,
            'senior_youth'               => $seniorYouth,
            'officials'                  => $officials,
            'male'                       => $male,
            'female'                     => $female,
            'active_codes'               => $codesActive,
            'codes_pending'              => $codesPending,
            'codes_claimed'              => $codesClaimed,
            'codes_void_expired'         => $codesVoidExpired,
            'total_codes'                => $totalCodes,
            'confirmed_payments'         => $confirmedPayments,
            'claimed_revenue'            => $claimedRevenue,
            'active_revenue'             => $activeRevenue,
            'total_revenue'              => $totalRevenue,
            'pending_revenue'            => $pendingRevenue,
            'online_payments'            => $onlinePayments,
            'offline_payments_confirmed' => $offlinePaymentsConfirmed,
            'offline_payments_pending'   => $offlinePaymentsPending,
            'offline_payments_rejected'  => $offlinePaymentsRejected,
            'consent_outstanding'        => $consentOutstanding,
            'consent_collected'          => $consentCollected,
            'photos_pending'             => $photosPending,
            'photos_approved'            => $photosApproved,
        ];
    }

    // ── By Church ─────────────────────────────────────────────────────────────

    private function gatherByChurch(): array
    {
        $rows = Camper::with('church.district')
            ->selectRaw('church_id, category, COUNT(*) as cnt')
            ->groupBy('church_id', 'category')
            ->get();

        $byChurch = [];
        foreach ($rows as $r) {
            $district = $r->church?->district?->name ?? 'Unknown';
            $church   = $r->church?->name ?? 'Unknown';
            $key      = $district . '||' . $church;

            $byChurch[$key] ??= ['district' => $district, 'church' => $church, 'adv' => 0, 'pf' => 0, 'syl' => 0, 'total' => 0];

            $catKey = $r->category instanceof \App\Enums\CamperCategory ? $r->category->value : (string) $r->category;
            $map    = ['adventurer' => 'adv', 'pathfinder' => 'pf', 'senior_youth' => 'syl'];

            if ($field = ($map[$catKey] ?? null)) {
                $byChurch[$key][$field] += $r->cnt;
            }
            $byChurch[$key]['total'] += $r->cnt;
        }

        ksort($byChurch);
        return array_values($byChurch);
    }

    // ── T-Shirt overall ───────────────────────────────────────────────────────

    private function gatherTshirtSizes(): array
    {
        return Camper::selectRaw('tshirt_size, COUNT(*) as count')
            ->whereNotNull('tshirt_size')
            ->groupBy('tshirt_size')
            ->orderByRaw("FIELD(tshirt_size,'XS','S','M','L','XL','XXL','XXXL')")
            ->get()
            ->map(fn ($r) => ['size' => $r->tshirt_size, 'count' => (int) $r->count])
            ->toArray();
    }

    // ── T-Shirt by district + church (cross-tab) ──────────────────────────────

    private function gatherTshirtByDistrictChurch(): array
    {
        $rows = Camper::with('church.district')
            ->selectRaw('church_id, tshirt_size, COUNT(*) as cnt')
            ->whereNotNull('tshirt_size')
            ->groupBy('church_id', 'tshirt_size')
            ->get();

        $result = [];
        foreach ($rows as $r) {
            $district = $r->church?->district?->name ?? 'Unknown';
            $church   = $r->church?->name ?? 'Unknown';
            $result[$district][$church][$r->tshirt_size] =
                ($result[$district][$church][$r->tshirt_size] ?? 0) + $r->cnt;
        }

        ksort($result);
        foreach ($result as &$churches) ksort($churches);
        return $result;
    }

    // ── T-Shirt by department, district, church ───────────────────────────────

    private function gatherTshirtByDeptDistrict(): array
    {
        $rows = Camper::with('church.district')
            ->selectRaw('church_id, category, tshirt_size, COUNT(*) as cnt')
            ->whereNotNull('tshirt_size')
            ->groupBy('church_id', 'category', 'tshirt_size')
            ->get();

        // Structure: [ district => [ church => [ category => [ size => count ] ] ] ]
        $result = [];
        foreach ($rows as $r) {
            $district = $r->church?->district?->name ?? 'Unknown';
            $church   = $r->church?->name ?? 'Unknown';
            $catKey   = $r->category instanceof \App\Enums\CamperCategory
                ? $r->category->value
                : (string) $r->category;

            $result[$district][$church][$catKey][$r->tshirt_size] =
                ($result[$district][$church][$catKey][$r->tshirt_size] ?? 0) + $r->cnt;
        }

        ksort($result);
        foreach ($result as &$churches) ksort($churches);
        return $result;
    }

    // ── T-Shirt by department ────────────────────────────────────────────────

    private function gatherTshirtByDept(): array
    {
        $rows = Camper::selectRaw('category, tshirt_size, COUNT(*) as cnt')
            ->whereNotNull('tshirt_size')
            ->groupBy('category', 'tshirt_size')
            ->get();

        // Structure: [ category_value => [ size => count ] ]
        $result = [];
        foreach ($rows as $r) {
            $catKey = $r->category instanceof \App\Enums\CamperCategory
                ? $r->category->value
                : (string) $r->category;
            $result[$catKey][$r->tshirt_size] = ($result[$catKey][$r->tshirt_size] ?? 0) + $r->cnt;
        }

        return $result;
    }

    // ── Unclaimed codes by district/church ───────────────────────────────────

    private function gatherUnclaimedByChurch(): array
    {
        $rows = RegistrationCode::with('church.district')
            ->where('status', 'ACTIVE')
            ->selectRaw('prefill_church_id, COUNT(*) as cnt')
            ->groupBy('prefill_church_id')
            ->get();

        $result = [];
        foreach ($rows as $r) {
            $district = $r->church?->district?->name ?? 'Unknown';
            $church   = $r->church?->name ?? 'Unknown';
            $key      = $district . '||' . $church;

            $result[$key] = [
                'district' => $district,
                'church'   => $church,
                'count'    => (int) $r->cnt,
            ];
        }

        ksort($result);
        return array_values($result);
    }

    // ── Logo ──────────────────────────────────────────────────────────────────

    private function logoBase64(): ?string
    {
        foreach ([
                     public_path('images/congress_logo.png'),
                     public_path('images/congress_logo.jpg'),
                     storage_path('app/public/images/congress_logo.png'),
                 ] as $path) {
            if (file_exists($path)) {
                return base64_encode(file_get_contents($path));
            }
        }
        return null;
    }
}
