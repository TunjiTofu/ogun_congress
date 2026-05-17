<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BulkIdCardController extends Controller
{
    // Department colors — must match DocumentGenerationService
    private array $departmentColors = [
        'pathfinder'   => '#2D7A3A',  // Green
        'adventurer'   => '#1B3A8F',  // Navy Blue
        'senior_youth' => '#C9A94D',  // Gold
    ];

    public function export(Request $request)
    {
        if (! auth()->user()->hasRole('super_admin')) {
            abort(403);
        }

        $query = Camper::with(['media', 'church.district'])
            ->orderBy('church_id')
            ->orderBy('full_name');

        if ($request->filled('church_id'))   $query->where('church_id', $request->church_id);
        if ($request->filled('district_id')) {
            $ids = Church::where('district_id', $request->district_id)->pluck('id');
            $query->whereIn('church_id', $ids);
        }
        if ($request->filled('category'))    $query->where('category', $request->category);
        if ($request->filled('club_rank'))   $query->where('club_rank', $request->club_rank);

        $campers = $query->get();

        if ($campers->isEmpty()) {
            abort(404, 'No campers match the selected filters.');
        }

        $logoBase64 = $this->encodeLogo();

        // Pre-encode per-camper data
        $campers->each(function (Camper $c) {
            $c->photo_base64 = $this->encodePhoto($c);
            $c->qr_base64    = $this->encodeQr($c);

            // Compute badge color per department
            $categoryValue   = $c->category?->value ?? 'senior_youth';
            $c->badge_color_computed = $c->badge_color
                ?? $this->departmentColors[$categoryValue]
                ?? '#1B3A6B';
        });

        // 2 columns × 3 rows = 6 cards per A4 page
        $pages = $campers->chunk(6);

        $pdf = Pdf::loadView('pdf.bulk-id-cards', [
            'pages'      => $pages,
            'campers'    => $campers,
            'logoBase64' => $logoBase64,
            'campName'   => setting('camp_name', 'Ogun Youth Camp'),
            'campYear'   => now()->year,
        ])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                  => 150,
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
            ]);

        $label = collect([
            $request->filled('district_id') ? District::find($request->district_id)?->name : null,
            $request->filled('church_id')   ? Church::find($request->church_id)?->name     : null,
            $request->filled('category')    ? $request->category : null,
        ])->filter()->join('-');

        $filename = 'id-cards' . ($label ? '-' . str($label)->slug() : '') . '-' . now()->format('Y-m-d') . '.pdf';

        return $pdf->download($filename);
    }

    private function encodePhoto(Camper $c): ?string
    {
        $media = $c->getFirstMedia('photo');
        if (! $media) return null;

        $path = ($media->hasGeneratedConversion('thumb') && file_exists($media->getPath('thumb')))
            ? $media->getPath('thumb')
            : $media->getPath();

        if (! file_exists($path)) return null;

        $mime = mime_content_type($path) ?: 'image/jpeg';
        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }

    private function encodeQr(Camper $c): ?string
    {
        if (! $c->qr_code_path) return null;

        foreach ([
                     storage_path('app/public/' . $c->qr_code_path),
                     storage_path('app/' . $c->qr_code_path),
                 ] as $path) {
            if (file_exists($path)) {
                return 'data:image/png;base64,' . base64_encode(file_get_contents($path));
            }
        }
        return null;
    }

    private function encodeLogo(): ?string
    {
        foreach ([
                     public_path('images/congress_logo.png'),
                     public_path('images/favicon.png'),
                     public_path('images/logo.png'),
                 ] as $path) {
            if (file_exists($path)) {
                return 'data:' . (mime_content_type($path) ?: 'image/png') . ';base64,' . base64_encode(file_get_contents($path));
            }
        }
        return null;
    }
}
