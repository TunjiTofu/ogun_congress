<?php

namespace App\Jobs;

use App\Models\Camper;
use App\Models\Church;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateBulkIdCardsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout       = 300; // 5 minutes
    public int $tries         = 2;
    public int $maxExceptions = 1;

    private array $departmentColors = [
        'pathfinder'   => '#2D7A3A',
        'adventurer'   => '#1B3A8F',
        'senior_youth' => '#875216',
    ];

    public function __construct(
        private Collection $camperIds,
        private string     $exportKey,
        private string     $filename,
    ) {}

    public function handle(): void
    {
        ini_set('memory_limit', '512M');

        Cache::put("id_card_export:{$this->exportKey}", [
            'status'     => 'processing',
            'total'      => $this->camperIds->count(),
            'filename'   => $this->filename,
            'started_at' => now()->toDateTimeString(),
        ], now()->addHours(2));

        $logoBase64  = $this->encodeLogo();
        $allCampers  = collect();
        $chunkSize   = 50; // photo-encode in chunks to control peak memory

        try {
            // ── Phase 1: encode photos chunk-by-chunk to stay within memory ──
            foreach ($this->camperIds->chunk($chunkSize) as $ids) {
                $campers = Camper::with(['media', 'church.district', 'campRole'])
                    ->whereIn('id', $ids)
                    ->orderBy('church_id')
                    ->orderBy('full_name')
                    ->get();

                $campers->each(function (Camper $c) {
                    $c->photo_base64 = $this->encodePhoto($c);
                    $c->qr_base64    = $this->encodeQr($c);

                    if ($c->is_official && $c->campRole) {
                        $c->badge_color_computed = $c->campRole->color ?? '#722F37';
                        $c->official_role_label  = strtoupper($c->campRole->name);
                    } else {
                        $val                     = $c->category?->value ?? 'senior_youth';
                        $c->badge_color_computed = $c->badge_color ?? $this->departmentColors[$val] ?? '#1B3A6B';
                        $c->official_role_label  = null;
                    }
                });

                $allCampers = $allCampers->concat($campers);
                unset($campers);
                gc_collect_cycles();
            }

            // ── Phase 2: render ONE PDF with all campers — no merging needed ──
            $pages = $allCampers->chunk(6);

            $pdf = Pdf::loadView('pdf.bulk-id-cards', [
                'pages'      => $pages,
                'totalCards' => $allCampers->count(),
                'logoBase64' => $logoBase64,
                'campName'   => setting('camp_name', 'Ogun Youth Camp'),
                'campYear'   => now()->year,
            ])
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'dpi'                     => 150,
                    'isHtml5ParserEnabled'    => false,
                    'isRemoteEnabled'         => false,
                    'isFontSubsettingEnabled' => true,
                ]);

            $finalPath = 'exports/' . $this->exportKey . '-' . $this->filename;
            Storage::disk('local')->put($finalPath, $pdf->output());

            unset($pdf, $pages, $allCampers);
            gc_collect_cycles();

            Cache::put("id_card_export:{$this->exportKey}", [
                'status'       => 'done',
                'total'        => $this->camperIds->count(),
                'path'         => $finalPath,
                'filename'     => $this->filename,
                'completed_at' => now()->toDateTimeString(),
            ], now()->addHours(2));

        } catch (\Throwable $e) {
            Log::error('bulk_id_cards.job_failed', [
                'key'   => $this->exportKey,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Cache::put("id_card_export:{$this->exportKey}", [
                'status'   => 'failed',
                'error'    => 'Export failed. Please try again with a smaller batch.',
                'filename' => $this->filename,
            ], now()->addHours(1));
        }
    }

    private function mergeChunks(array $chunkPaths): string
    {
        $finalPath = 'exports/' . $this->exportKey . '-' . $this->filename;

        if (count($chunkPaths) === 1) {
            Storage::disk('local')->move($chunkPaths[0], $finalPath);
            return $finalPath;
        }

        $fullPaths = array_map(fn ($p) => Storage::disk('local')->path($p), $chunkPaths);
        $output    = Storage::disk('local')->path($finalPath);
        $inputArgs = implode(' ', array_map('escapeshellarg', $fullPaths));

        exec("which gs 2>/dev/null", $gsOut);
        if (! empty($gsOut)) {
            $cmd = "gs -dBATCH -dNOPAUSE -sDEVICE=pdfwrite -sOutputFile=" . escapeshellarg($output) . " {$inputArgs} 2>/dev/null";
            exec($cmd, $out, $code);

            if ($code === 0 && file_exists($output)) {
                return $finalPath;
            }
        }

        // Ghostscript unavailable — fall back to first chunk (largest, not last)
        // Install setasign/fpdi for proper PHP-native PDF merging without Ghostscript
        Log::warning('bulk_id_cards.gs_unavailable_using_first_chunk', ['chunks' => count($chunkPaths)]);
        Storage::disk('local')->move(array_shift($chunkPaths), $finalPath);
        return $finalPath;
    }

    private function encodeLogo(): string
    {
        $path = public_path('images/congress_logo.png');
        return file_exists($path)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($path))
            : '';
    }

    private function encodePhoto(Camper $c): ?string
    {
        try {
            $media = $c->getFirstMedia('photo');
            if (! $media) return null;

            // Always use the full-resolution original — thumb is too small for print
            $path = $media->getPath();
            if (! file_exists($path)) return null;

            if (extension_loaded('gd')) {
                $src = @imagecreatefromstring(file_get_contents($path));
                if ($src) {
                    $srcW = imagesx($src);
                    $srcH = imagesy($src);

                    // Pre-crop to 19:24 (card photo box ratio) — top-centre crop
                    // DomPDF ignores object-fit, so we crop in PHP to avoid distortion
                    $targetRatio = 19 / 24;
                    if ($srcW / $srcH > $targetRatio) {
                        $cropH = $srcH;
                        $cropW = (int) round($srcH * $targetRatio);
                    } else {
                        $cropW = $srcW;
                        $cropH = (int) round($srcW / $targetRatio);
                    }
                    $cropX = (int) round(($srcW - $cropW) / 2);
                    $cropY = (int) round(($srcH - $cropH) * 0.15); // bias toward top for faces

                    $dst   = imagecreatetruecolor(426, 544);
                    $white = imagecolorallocate($dst, 255, 255, 255);
                    imagefill($dst, 0, 0, $white);
                    imagecopyresampled($dst, $src, 0, 0, $cropX, $cropY, 426, 544, $cropW, $cropH);
                    imagedestroy($src);

                    ob_start();
                    imagejpeg($dst, null, 92);
                    $jpeg = ob_get_clean();
                    imagedestroy($dst);
                    return 'data:image/jpeg;base64,' . base64_encode($jpeg);
                }
            }

            $mime = mime_content_type($path) ?: 'image/jpeg';
            return "data:{$mime};base64," . base64_encode(file_get_contents($path));
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function encodeQr(Camper $c): ?string
    {
        try {
            return $this->generateQrPngWithGd(url('/verify/' . $c->camper_number), 120);
        } catch (\Throwable $e) {
            Log::warning('bulk_id_card.qr_encode_failed', ['camper' => $c->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function generateQrPngWithGd(string $content, int $targetSize = 120): string
    {
        $prev    = error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        $ecLevel = \BaconQrCode\Common\ErrorCorrectionLevel::M();
        error_reporting($prev);

        $qrCode  = \BaconQrCode\Encoder\Encoder::encode($content, $ecLevel, 'ISO-8859-1');
        $matrix  = $qrCode->getMatrix();
        $modules = $matrix->getWidth();

        $quiet    = 4;
        $total    = $modules + $quiet * 2;
        $cell     = max(1, (int) floor($targetSize / $total));
        $size     = $total * $cell;

        $img   = imagecreatetruecolor($size, $size);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        for ($row = 0; $row < $modules; $row++) {
            for ($col = 0; $col < $modules; $col++) {
                if ($matrix->get($col, $row) !== 0) {
                    $x1 = ($col + $quiet) * $cell;
                    $y1 = ($row + $quiet) * $cell;
                    imagefilledrectangle($img, $x1, $y1, $x1 + $cell - 1, $y1 + $cell - 1, $black);
                }
            }
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        if (empty($png)) {
            throw new \RuntimeException('GD imagepng() returned empty');
        }

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
