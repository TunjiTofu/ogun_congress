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
    public int $timeout = 60; // seconds

    public function __construct(private readonly int $camperId)
    {
        $this->onQueue('documents');
    }

    public function handle(DocumentGenerationService $documentService): void
    {
        $camper = Camper::with(['church.district', 'registrationCode'])
            ->findOrFail($this->camperId);

        $documentService->generateIdCard($camper);

        if ($camper->requiresConsentForm()) {
            $documentService->generateConsentForm($camper);
        }

        Log::info('docs.generated', [
            'camper_number' => $camper->camper_number,
            'consent_form'  => $camper->requiresConsentForm(),
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('docs.generation_failed', [
            'camper_id' => $this->camperId,
            'error'     => $exception->getMessage(),
        ]);
    }
}
