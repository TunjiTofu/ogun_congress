<?php

namespace App\Services;

use App\Models\Camper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DocumentGenerationService
{
    /**
     * Generate the camper ID card PDF and store it on the private disk.
     * Updates camper.id_card_path on completion.
     */
    public function generateIdCard(Camper $camper): string
    {
        $qrCode = $this->generateQrCodeBase64($camper->camper_number);

        $photoBase64 = $this->encodePhotoBase64($camper);

        $badgeColor = $camper->badge_color
            ?? config("camp.badge_colors.{$camper->category->value}", '#1B3A6B');

        $pdf = Pdf::loadView('pdf.id-card', [
            'camper'      => $camper,
            'qrCode'      => $qrCode,
            'photoBase64' => $photoBase64,
            'badgeColor'  => $badgeColor,
            'campName'    => setting('camp_name', 'Ogun Youth Camp'),
            'campYear'    => now()->year,
        ])->setPaper([0, 0, 242.65, 153.01]); // CR80 in points (85.6mm × 53.98mm)

        $path     = config('camp.documents.id_card_path') . '/' . $camper->camper_number . '.pdf';
        $disk     = config('camp.documents.id_card_disk', 'private');
        $contents = $pdf->output();

        Storage::disk($disk)->put($path, $contents);

        $camper->update(['id_card_path' => $path]);

        return $path;
    }

    /**
     * Generate the parental consent form PDF (under-18 campers only).
     * Updates camper.consent_form_path on completion.
     */
    public function generateConsentForm(Camper $camper): string
    {
        $pdf = Pdf::loadView('pdf.consent-form', [
            'camper'    => $camper,
            'campName'  => setting('camp_name', 'Ogun Youth Camp'),
            'campDates' => setting('camp_dates', 'TBA'),
            'campVenue' => setting('camp_venue', 'TBA'),
        ])->setPaper('a4', 'portrait');

        $path     = config('camp.documents.consent_form_path') . '/' . $camper->camper_number . '.pdf';
        $disk     = config('camp.documents.consent_form_disk', 'private');
        $contents = $pdf->output();

        Storage::disk($disk)->put($path, $contents);

        $camper->update(['consent_form_path' => $path]);

        return $path;
    }

    /**
     * Return a signed temporary URL for a stored PDF.
     */
    public function temporaryUrl(string $path, int $hours = 24): string
    {
        return Storage::disk(config('camp.documents.id_card_disk', 'private'))
            ->temporaryUrl($path, now()->addHours($hours));
    }

    // ── Private helpers ───────────────────────────────────────────────────────

    /**
     * Generate QR code as a base64-encoded PNG for embedding in the PDF.
     * QR encodes: OGN:{camper_number}
     */
    private function generateQrCodeBase64(string $camperNumber): string
    {
        $svg = QrCode::format('svg')
            ->size(150)
            ->errorCorrection('M')
            ->generate("OGN:{$camperNumber}");

        // Return as inline SVG string (DomPDF supports inline SVG)
        return $svg;
    }

    /**
     * Read the camper photo and return it as a base64 data URI.
     * Falls back to a placeholder if no photo is uploaded.
     */
    private function encodePhotoBase64(Camper $camper): string
    {
        $media = $camper->getFirstMedia('photo');

        if (! $media) {
            return '';
        }

        $path     = $media->getPath('thumb');
        $mimeType = $media->mime_type;

        if (! file_exists($path)) {
            return '';
        }

        $encoded = base64_encode(file_get_contents($path));

        return "data:{$mimeType};base64,{$encoded}";
    }
}
