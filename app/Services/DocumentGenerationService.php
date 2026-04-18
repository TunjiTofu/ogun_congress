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
        $qrCode      = $this->generateQrCodeSvg($camper->camper_number);
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
        ])->setPaper([0, 0, 242.65, 153.01]);

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

    private function generateQrCodeSvg(string $camperNumber): string
    {
        return (string) QrCode::format('svg')->size(150)->errorCorrection('M')->generate("OGN:{$camperNumber}");
    }

    private function encodePhotoBase64(Camper $camper): string
    {
        $media = $camper->getFirstMedia('photo');
        if (! $media) return '';
        $path = $media->hasGeneratedConversion('thumb') ? $media->getPath('thumb') : $media->getPath();
        if (! file_exists($path)) return '';
        return 'data:' . $media->mime_type . ';base64,' . base64_encode(file_get_contents($path));
    }
}
