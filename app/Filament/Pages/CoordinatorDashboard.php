<?php

namespace App\Filament\Pages;

use App\Models\BulkRegistrationBatch;
use App\Models\Camper;
use App\Services\DocumentGenerationService;
use Filament\Pages\Page;

class CoordinatorDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'My Dashboard';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.coordinator-dashboard';

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

        return compact(
            'church', 'batches', 'confirmedCampers',
            'totalRegistered', 'totalPaid', 'documentService'
        );
    }
}
