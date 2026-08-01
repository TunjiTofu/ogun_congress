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
                'dpi'                  => 300,
                'isHtml5ParserEnabled' => false,
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
     * Uses the full-resolution original (never the thumb) and pre-crops it to
     * the exact 18:23 ratio of the card's photo box before encoding. This avoids
     * relying on CSS object-fit (unsupported by DomPDF) for cropping — DomPDF
     * only needs to scale the image proportionally, which it does cleanly.
     */
    private function encodePhotoBase64(Camper $camper): string
    {
        $media = $camper->getFirstMedia('photo');

        if (! $media) {
            Log::debug('encodePhotoBase64: no media', ['camper_id' => $camper->id]);
            return '';
        }

        $path = $media->getPath(); // always use full-resolution original

        if (! file_exists($path)) {
            Log::warning('encodePhotoBase64: no file on disk', [
                'camper_id' => $camper->id,
                'media_id'  => $media->id,
                'path'      => $path,
            ]);
            return '';
        }

        return $this->cropAndResizePhotoForIdCard($path);
    }

    /**
     * Crop and resize a photo to fit the ID card photo box (18mm × 23mm).
     *
     * DomPDF does not support object-fit: cover. Without pre-cropping, a portrait
     * photo in a portrait box looks fine, but a landscape or square photo gets
     * stretched/squashed — causing visible distortion and blur.
     *
     * Strategy:
     * 1. Crop the source image to exactly the card ratio (18:23) from the centre,
     *    favouring the top for face photos (top-centre crop, not true centre).
     * 2. Resize the cropped area to 426 × 544 px (2× the 213 × 272 px display
     *    size at 300 DPI). 2× gives DomPDF room to downsample sharply.
     * 3. Output as JPEG quality 92.
     */
    private function cropAndResizePhotoForIdCard(string $sourcePath): string
    {
        $fallback = fn () => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($sourcePath));

        if (! extension_loaded('gd')) {
            return $fallback();
        }

        $src = @imagecreatefromstring(file_get_contents($sourcePath));
        if (! $src) {
            return $fallback();
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        // Target ratio: 18mm wide × 23mm tall = 18/23
        $targetRatio = 18 / 23; // ≈ 0.7826

        // Determine crop dimensions that fit the target ratio within the source
        if ($srcW / $srcH > $targetRatio) {
            // Source is wider than target ratio — crop width, keep full height
            $cropH = $srcH;
            $cropW = (int) round($srcH * $targetRatio);
        } else {
            // Source is taller than target ratio — crop height, keep full width
            $cropW = $srcW;
            $cropH = (int) round($srcW / $targetRatio);
        }

        // Top-centre crop — centred horizontally, biased to top for face photos
        $cropX = (int) round(($srcW - $cropW) / 2);
        $cropY = (int) round(($srcH - $cropH) * 0.15); // 15% from top, not dead-centre

        // Output at 2× the 300-DPI display size for crispness
        $dstW = 426;
        $dstH = 544;

        $dst   = imagecreatetruecolor($dstW, $dstH);
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        // imagecopyresampled = bicubic — smooth, sharp edges
        imagecopyresampled($dst, $src, 0, 0, $cropX, $cropY, $dstW, $dstH, $cropW, $cropH);
        imagedestroy($src);

        ob_start();
        imagejpeg($dst, null, 92);
        $jpeg = ob_get_clean();
        imagedestroy($dst);

        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }
}
