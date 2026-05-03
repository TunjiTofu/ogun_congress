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

    /**
     * Generate QR code using SVG format — requires NO imagick or gd extension.
     * Works on all shared cPanel/WHM hosting out of the box.
     *
     * The QR encodes the public /verify/{camper_number} URL so any phone
     * camera scan opens the camper's verification page.
     */
    private function generateQrCode(string $camperNumber, ?int $camperId = null): string
    {
        $verifyUrl = url('/verify/' . $camperNumber);

        // SVG renderer has zero PHP extension dependencies (no imagick, no gd)
        $svg = (string) QrCode::format('svg')
            ->size(200)
            ->margin(1)
            ->errorCorrection('M')
            ->generate($verifyUrl);

        // Store SVG on public disk
        $qrPath = 'qr-codes/' . $camperNumber . '.svg';
        Storage::disk('public')->put($qrPath, $svg);

        // Update camper record
        if ($camperId) {
            \App\Models\Camper::where('id', $camperId)
                ->update(['qr_code_path' => $qrPath]);
        }

        // Strip the XML declaration before inline embedding.
        // DomPDF cannot parse processing instructions inside HTML.
        $svgClean = preg_replace('/<' . '?xml[^?]*?' . '>/i', '', $svg);
        return trim($svgClean);
    }

    private function encodePhotoBase64(Camper $camper): string
    {
        $media = $camper->getFirstMedia('photo');
        if (! $media) return '';

        // Try thumb first, fall back to original
        $path = null;
        if ($media->hasGeneratedConversion('thumb')) {
            $thumbPath = $media->getPath('thumb');
            if (file_exists($thumbPath)) {
                $path = $thumbPath;
            }
        }
        if (! $path) {
            $originalPath = $media->getPath();
            if (file_exists($originalPath)) {
                $path = $originalPath;
            }
        }
        if (! $path) return '';

        $mimeType = $media->mime_type ?: mime_content_type($path) ?: 'image/jpeg';
        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($path));
    }
}
