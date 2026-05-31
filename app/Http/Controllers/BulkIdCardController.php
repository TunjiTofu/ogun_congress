<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BulkIdCardController extends Controller
{
    private const CARDS_PER_PAGE  = 6;    // 2 col × 3 row on A4
    private const CHUNK_SIZE      = 30;   // campers per PDF chunk (5 pages)
    private const MEMORY_LIMIT    = '512M';

    private array $departmentColors = [
        'pathfinder'   => '#2D7A3A',
        'adventurer'   => '#1B3A8F',
        'senior_youth' => '#875216',
    ];

    // ── Main export entry point ──────────────────────────────────────────────
    public function export(Request $request)
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'secretariat'])) {
            abort(403);
        }

        $query = $this->buildQuery($request);
        $total = $query->count();

        if ($total === 0) {
            abort(404, 'No campers match the selected filters.');
        }

        // For small batches (≤30) stream directly
        if ($total <= self::CHUNK_SIZE) {
            return $this->streamDirect($query->get(), $request);
        }

        // For large batches — dispatch a queue job and redirect to status page
        $jobKey = $this->dispatchBulkJob($query->get(), $request, $total);

        return redirect()->route('exports.id-cards.status', ['key' => $jobKey]);
    }

    // ── Status / download page for queued exports ────────────────────────────
    public function status(Request $request)
    {
        $key  = $request->query('key');
        $data = Cache::get("id_card_export:{$key}");

        if (! $data) {
            abort(404, 'Export not found or has expired.');
        }

        return view('exports.id-cards-status', compact('data', 'key'));
    }

    public function download(Request $request, string $key)
    {
        $data = Cache::get("id_card_export:{$key}");

        if (! $data || $data['status'] !== 'done') {
            abort(404, 'Export not ready or expired.');
        }

        $path = $data['path'];
        if (! Storage::disk('local')->exists($path)) {
            abort(404, 'File not found.');
        }

        return Storage::disk('local')->download($path, $data['filename']);
    }

    // ── Private helpers ──────────────────────────────────────────────────────

    private function buildQuery(Request $request)
    {
        $query = Camper::with(['media', 'church.district', 'campRole'])
            ->orderBy('church_id')
            ->orderBy('full_name');

        if ($request->filled('church_id'))   $query->where('church_id', $request->church_id);
        if ($request->filled('district_id')) {
            $ids = Church::where('district_id', $request->district_id)->pluck('id');
            $query->whereIn('church_id', $ids);
        }
        if ($request->filled('category'))    $query->where('category', $request->category);
        if ($request->filled('club_rank'))   $query->where('club_rank', $request->club_rank);

        return $query;
    }

    /**
     * Stream a PDF directly for small batches.
     * Raises memory limit for the duration of the request only.
     */
    private function streamDirect($campers, Request $request): \Symfony\Component\HttpFoundation\Response
    {
        ini_set('memory_limit', self::MEMORY_LIMIT);

        $logoBase64 = $this->encodeLogo();
        $this->prepareCampers($campers, $logoBase64);

        $pages = $campers->chunk(self::CARDS_PER_PAGE);

        $pdf = Pdf::loadView('pdf.bulk-id-cards', [
            'pages'      => $pages,
            'totalCards' => $campers->count(),
            'logoBase64' => $logoBase64,
            'campName'   => setting('camp_name', 'Ogun Youth Camp'),
            'campYear'   => now()->year,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                  => 96,   // lowered from 150 — saves ~40% memory
                'isHtml5ParserEnabled' => false, // disable HTML5 parser — was causing the OOM
                'isRemoteEnabled'      => false,
                'isFontSubsettingEnabled' => true,
            ]);

        $filename = $this->makeFilename($request);

        return $pdf->download($filename);
    }

    /**
     * For large exports: split into chunks, generate separate PDFs,
     * merge with Ghostscript if available, otherwise zip them.
     * Dispatches to queue.
     */
    private function dispatchBulkJob($campers, Request $request, int $total): string
    {
        $key = \Illuminate\Support\Str::uuid()->toString();

        Cache::put("id_card_export:{$key}", [
            'status'   => 'queued',
            'total'    => $total,
            'filename' => $this->makeFilename($request),
            'queued_at'=> now()->toDateTimeString(),
        ], now()->addHours(2));

        \App\Jobs\GenerateBulkIdCardsJob::dispatch($campers->pluck('id'), $key, $this->makeFilename($request));

        return $key;
    }

    private function prepareCampers($campers, string $logoBase64): void
    {
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
    }

    private function makeFilename(Request $request): string
    {
        $label = collect([
            $request->filled('district_id') ? District::find($request->district_id)?->name : null,
            $request->filled('church_id')   ? Church::find($request->church_id)?->name     : null,
            $request->filled('category')    ? $request->category : null,
        ])->filter()->join('-');

        return 'id-cards' . ($label ? '-' . str($label)->slug() : '') . '-' . now()->format('Y-m-d') . '.pdf';
    }

    private function encodeLogo(): string
    {
        $path = public_path('images/congress_logo.png');
        if (! file_exists($path)) {
            return '';
        }
        return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
    }

    private function encodePhoto(Camper $c): ?string
    {
        try {
            $media = $c->getFirstMedia('photo');
            if (! $media) {
                return null;
            }

            $path = ($media->hasGeneratedConversion('thumb') && file_exists($media->getPath('thumb')))
                ? $media->getPath('thumb')
                : $media->getPath();

            if (! file_exists($path)) {
                return null;
            }

            // Resize to thumbnail to save memory — 120×120 is plenty for a card
            if (extension_loaded('gd')) {
                $src  = imagecreatefromstring(file_get_contents($path));
                if ($src) {
                    $thumb = imagecreatetruecolor(120, 120);
                    imagecopyresampled($thumb, $src, 0, 0, 0, 0, 120, 120, imagesx($src), imagesy($src));
                    ob_start();
                    imagejpeg($thumb, null, 80);
                    $jpeg = ob_get_clean();
                    imagedestroy($src);
                    imagedestroy($thumb);
                    return 'data:image/jpeg;base64,' . base64_encode($jpeg);
                }
            }

            $mime = mime_content_type($path) ?: 'image/jpeg';
            return "data:{$mime};base64," . base64_encode(file_get_contents($path));

        } catch (\Throwable $e) {
            Log::warning('id_card.photo_encode_failed', ['camper' => $c->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    private function encodeQr(Camper $c): ?string
    {
        try {
            return $this->generateQrPngWithGd('OGN:' . $c->camper_number, 120);
        } catch (\Throwable $e) {
            Log::warning('id_card.qr_encode_failed', ['camper' => $c->id, 'error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Generate a QR code PNG using pure GD — no Imagick required.
     * BaconQrCode (already installed) provides the bit matrix;
     * GD renders it to PNG.
     */
    private function generateQrPngWithGd(string $content, int $targetSize = 120): string
    {
        $prevErrorLevel = error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
        $ecLevel = \BaconQrCode\Common\ErrorCorrectionLevel::M();
        error_reporting($prevErrorLevel);

        $qrCode  = \BaconQrCode\Encoder\Encoder::encode($content, $ecLevel, 'ISO-8859-1');
        $matrix  = $qrCode->getMatrix();
        $modules = $matrix->getWidth();

        $quietZone    = 4;
        $totalModules = $modules + $quietZone * 2;
        $cellSize     = max(1, (int) floor($targetSize / $totalModules));
        $imageSize    = $totalModules * $cellSize;

        $img   = imagecreatetruecolor($imageSize, $imageSize);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        for ($row = 0; $row < $modules; $row++) {
            for ($col = 0; $col < $modules; $col++) {
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

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
