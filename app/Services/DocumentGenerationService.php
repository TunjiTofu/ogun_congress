<?php

namespace App\Services;

use App\Models\Camper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentGenerationService
{
    public function generateIdCard(Camper $camper): string
    {
        // Fresh-load media so getFirstMedia() never returns stale null
        $camper->load(['media', 'church.district']);

        $qrCode      = $this->generateQrCode($camper->camper_number, $camper->id);
        $photoBase64 = $this->encodePhotoBase64($camper);
        $badgeColor  = $camper->badge_color
            ?? config("camp.badge_colors.{$camper->category->value}", '#1B3A6B');

        $pdf = Pdf::loadView('pdf.id-card', [
            'camper'      => $camper,
            'qrCode'      => $qrCode,
            'photoBase64' => $photoBase64,
            'badgeColor'  => $badgeColor,
            'campName'    => setting('camp_name', 'Ogun Youth Camp'),
            'campYear'    => now()->year,
        ])->setPaper([0, 0, 153.01, 243.78], 'portrait');

        $path = 'id-cards/' . $camper->camper_number . '.pdf';
        Storage::disk('private')->put($path, $pdf->output());
        $camper->update(['id_card_path' => $path]);

        Log::info('docs.id_card_generated', ['camper_number' => $camper->camper_number]);
        return $path;
    }

    public function generateConsentForm(Camper $camper): string
    {
        $pdf = Pdf::loadView('pdf.consent-form', [
            'camper'    => $camper->load(['church.district', 'contacts']),
            'campName'  => setting('camp_name', 'Ogun Youth Camp'),
            'campDates' => setting('camp_dates', 'TBA'),
            'campVenue' => setting('camp_venue', 'TBA'),
        ])->setPaper('a4', 'portrait');

        $path = 'consent-forms/' . $camper->camper_number . '.pdf';
        Storage::disk('private')->put($path, $pdf->output());
        $camper->update(['consent_form_path' => $path]);

        Log::info('docs.consent_form_generated', ['camper_number' => $camper->camper_number]);
        return $path;
    }

    public function getDownloadUrl(string $path, int $hours = 24): string
    {
        if (config('filesystems.disks.private.driver') === 's3') {
            return Storage::disk('private')->temporaryUrl($path, now()->addHours($hours));
        }
        return route('documents.download', ['path' => base64_encode($path)]);
    }

    private function generateQrCode(string $camperNumber, ?int $camperId = null): string
    {
        $verifyUrl = url('/verify/' . $camperNumber);

        // PNG via GD — no imagick needed
        if (extension_loaded('gd')) {
            try {
                $png = (string) QrCode::format('png')
                    ->size(200)->margin(1)->errorCorrection('M')
                    ->generate($verifyUrl);

                Storage::disk('public')->put('qr-codes/' . $camperNumber . '.png', $png);
                if ($camperId) {
                    \App\Models\Camper::where('id', $camperId)
                        ->update(['qr_code_path' => 'qr-codes/' . $camperNumber . '.png']);
                }
                return 'data:image/png;base64,' . base64_encode($png);
            } catch (\Throwable $e) {
                Log::debug('QR PNG failed, trying SVG', ['error' => $e->getMessage()]);
            }
        }

        // SVG fallback
        try {
            $svg = (string) QrCode::format('svg')
                ->size(200)->margin(1)->errorCorrection('M')
                ->generate($verifyUrl);

            Storage::disk('public')->put('qr-codes/' . $camperNumber . '.svg', $svg);
            if ($camperId) {
                \App\Models\Camper::where('id', $camperId)
                    ->update(['qr_code_path' => 'qr-codes/' . $camperNumber . '.svg']);
            }
            return 'data:image/svg+xml;base64,' . base64_encode($svg);
        } catch (\Throwable $e) {
            Log::warning('QR generation failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    /**
     * Encode the camper's photo as a base64 data URL for embedding in DomPDF.
     *
     * Since RegistrationController now converts all uploads to JPEG before storage,
     * both the original and thumb are always JPEG — no format detection needed.
     */
    private function encodePhotoBase64(Camper $camper): string
    {
        $media = $camper->getFirstMedia('photo');

        if (! $media) {
            Log::debug('encodePhotoBase64: no media', ['camper_id' => $camper->id]);
            return '';
        }

        // Prefer thumb (always JPEG, smaller file = faster PDF generation)
        $path = $media->hasGeneratedConversion('thumb')
            ? $media->getPath('thumb')
            : $media->getPath();

        if (! file_exists($path)) {
            // Thumb may not exist yet — try original directly
            $path = $media->getPath();
        }

        if (! file_exists($path)) {
            Log::warning('encodePhotoBase64: no file on disk', [
                'camper_id' => $camper->id,
                'media_id'  => $media->id,
                'path'      => $path,
            ]);
            return '';
        }

        // All photos stored by this system are JPEG (converted at upload time)
        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
    }
}
