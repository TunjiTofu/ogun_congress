<?php

namespace App\Services;

use App\Models\Camper;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DocumentGenerationService
{
    public function generateIdCard(Camper $camper): string
    {
        $camper->load(['media', 'church.district']);

        // Department colors
        $departmentColors = [
            'pathfinder'   => '#2D7A3A',
            'adventurer'   => '#1B3A8F',
            'senior_youth' => '#C9A94D',
        ];
        $categoryValue = $camper->category?->value ?? 'senior_youth';
        $badgeColor    = $camper->badge_color
            ?? $departmentColors[$categoryValue]
            ?? '#1B3A6B';

        $qrCode      = $this->generateQrCode($camper->camper_number, $camper->id);
        $photoBase64 = $this->encodePhotoBase64($camper);
        $logoBase64  = $this->encodeLogoBase64();

        // Official role name — null for regular campers, role name for officials
        $camper->loadMissing('campRole');
        $officialRole = $camper->is_official && $camper->campRole
            ? $camper->campRole->name
            : null;

        // Override badge color for officials (use role color)
        if ($camper->is_official && $camper->campRole?->color) {
            $badgeColor = $camper->campRole->color;
        }

        // CR80 standard card: 54mm wide x 85.6mm tall (portrait)
        // In points (1mm = 2.8346pt): 153.07 x 242.57
        $pdf = Pdf::loadView('pdf.id-card', [
            'camper'       => $camper,
            'qrCode'       => $qrCode,
            'photoBase64'  => $photoBase64,
            'logoBase64'   => $logoBase64,
            'badgeColor'   => $badgeColor,
            'officialRole' => $officialRole,
            'campName'     => setting('camp_name', 'Ogun Youth Camp'),
            'campYear'     => now()->year,
        ])->setPaper([0, 0, 153.07, 242.57], 'portrait')
            ->setOptions([
                'dpi'                  => 150,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

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

    private function generateQrBase64(Camper $camper): string
    {
        return $this->generateQrCode($camper->camper_number, $camper->id);
    }

    private function encodeLogoBase64(): ?string
    {
        $paths = [
            public_path('images/congress_logo.png'),
            public_path('images/favicon.png'),
            public_path('images/logo.png'),
        ];
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $mime = mime_content_type($path) ?: 'image/png';
                return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
            }
        }
        return null;
    }

    /**
     * Generate a QR code as a base64-encoded PNG.
     *
     * simplesoftwareio/simple-qrcode requires Imagick for PNG on PHP 8.4+.
     * On servers without Imagick we bypass the wrapper entirely:
     * 1. Use BaconQrCode (already installed as a transitive dependency) to
     *    encode the data into a raw pixel matrix — pure PHP, no extensions.
     * 2. Render that matrix as a PNG with PHP's GD extension.
     *
     * DomPDF cannot render SVG data URLs, so we never fall back to SVG.
     */
    private function generateQrCode(string $camperNumber, ?int $camperId = null): string
    {
        // Encode a full URL so phone cameras open /verify directly when scanned.
        // The checkin PWA handles OGN: prefix; phone cameras need a real URL.
        $content = url('/verify/' . $camperNumber);

        try {
            $png = $this->generateQrPngWithGd($content, 200);

            Storage::disk('public')->put('qr-codes/' . $camperNumber . '.png', $png);

            if ($camperId) {
                \App\Models\Camper::where('id', $camperId)
                    ->update(['qr_code_path' => 'qr-codes/' . $camperNumber . '.png']);
            }

            return 'data:image/png;base64,' . base64_encode($png);

        } catch (\Throwable $e) {
            Log::error('QR code generation failed', [
                'camper_number'  => $camperNumber,
                'error'          => $e->getMessage(),
                'gd_loaded'      => extension_loaded('gd'),
                'imagick_loaded' => extension_loaded('imagick'),
            ]);
            // Transparent 1×1 placeholder — keeps the PDF layout intact
            return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';
        }
    }

    /**
     * Render a QR code as a raw PNG string using only GD (no Imagick).
     *
     * Uses BaconQrCode to produce the bit-matrix, then draws each dark
     * module as a filled rectangle with imagefilledrectangle().
     */
    private function generateQrPngWithGd(string $content, int $targetSize = 200): string
    {
        if (! extension_loaded('gd')) {
            throw new \RuntimeException('GD extension is not loaded');
        }

        // Encode data into a QR matrix via BaconQrCode low-level API.
        // BaconQrCode v2.x (dasprid/enum): use __callStatic — e.g. M().
        // Do NOT access ::M as a constant — it is protected and PHP 8.4 throws.
        // Suppress deprecation notices from BaconQrCode v2.x on PHP 8.4
        // (these are harmless "nullable parameter" deprecations, not errors).
        $prevErrorLevel = error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        $ecLevel = \BaconQrCode\Common\ErrorCorrectionLevel::M();
        error_reporting($prevErrorLevel);

        $qrCode  = \BaconQrCode\Encoder\Encoder::encode(
            $content,
            $ecLevel,
            'ISO-8859-1'
        );
        $matrix  = $qrCode->getMatrix();
        $modules = $matrix->getWidth(); // matrix is square

        // Add 4-module quiet zone on all sides (required by QR spec)
        $quietZone    = 4;
        $totalModules = $modules + $quietZone * 2;

        // Pixel size per module (at least 1px)
        $cellSize  = max(1, (int) floor($targetSize / $totalModules));
        $imageSize = $totalModules * $cellSize;

        $img   = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);

        imagefill($img, 0, 0, $white);

        for ($row = 0; $row < $modules; $row++) {
            for ($col = 0; $col < $modules; $col++) {
                // get() returns 1 (int) or true (bool) for dark modules
                if ($matrix->get($col, $row) !== 0) {
                    $x1 = ($col + $quietZone) * $cellSize;
                    $y1 = ($row + $quietZone) * $cellSize;
                    imagefilledrectangle($img, $x1, $y1, $x1 + $cellSize - 1, $y1 + $cellSize - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        if (empty($png)) {
            throw new \RuntimeException('GD imagepng() returned empty output');
        }

        return $png;
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

        return 'data:image/jpeg;base64,' . base64_encode(file_get_contents($path));
    }
}
