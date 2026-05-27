<?php

namespace App\Jobs;

use App\Models\Camper;
use App\Services\DocumentGenerationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateCamperDocumentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 120;

    public function __construct(public readonly int $camperId) {}

    public function handle(DocumentGenerationService $documentService): void
    {
        // Always fresh-load with media relation so getFirstMedia() is never stale.
        // This is critical — if the job runs immediately after registration,
        // a cached $camper without media loaded would return null from getFirstMedia().
        $camper = Camper::with(['media', 'church.district', 'contacts'])
            ->findOrFail($this->camperId);

        Log::info('GenerateCamperDocumentsJob: starting', [
            'camper_number' => $camper->camper_number,
            'has_photo'     => $camper->getFirstMedia('photo') !== null,
        ]);

        // Generate ID card
        $documentService->generateIdCard($camper);

        // Generate consent form only for campers requiring parental consent
        if ($camper->requiresConsentForm()) {
            $documentService->generateConsentForm($camper);
        }

        Log::info('GenerateCamperDocumentsJob: complete', [
            'camper_number' => $camper->camper_number,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateCamperDocumentsJob: failed', [
            'camper_id' => $this->camperId,
            'error'     => $exception->getMessage(),
        ]);
    }
}
