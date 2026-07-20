<?php

namespace App\Filament\Pages;

use App\Models\BulkRegistrationBatch;
use App\Models\Camper;
use App\Models\RegistrationCode;
use App\Services\DocumentGenerationService;
use Filament\Pages\Page;

class CoordinatorDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'My Dashboard';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.coordinator-dashboard';
    protected static ?string $title           = 'Youth Leader Dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('church_coordinator');
    }

    public function getViewData(): array
    {
        $user   = auth()->user();
        $church = $user->church()->with('district')->first();

        if (! $church) {
            return [
                'church'           => null,
                'batches'          => collect(),
                'confirmedCampers' => collect(),
                'totalRegistered'  => 0,
                'totalPaid'        => 0,
                'activeCodesCount' => 0,
                'documentService'  => null,
            ];
        }

        $batches = BulkRegistrationBatch::where('created_by', $user->id)
            ->with('entries.registrationCode')->latest()->get();

        $confirmedCampers = Camper::whereHas('church', fn ($q) => $q->where('id', $church->id))
            ->with(['church'])
            ->get();

        $totalRegistered = $confirmedCampers->count();
        $totalPaid       = $batches->where('status', 'confirmed')->sum('amount_paid');
        $documentService = app(DocumentGenerationService::class);

        // Codes that are ACTIVE for this church: payment confirmed but
        // the camper has not yet completed their registration form.
        $activeCodesCount = RegistrationCode::where('status', 'ACTIVE')
            ->where('prefill_church_id', $church->id)
            ->count();

        return compact(
            'church', 'batches', 'confirmedCampers',
            'totalRegistered', 'totalPaid', 'activeCodesCount', 'documentService'
        );
    }
}
