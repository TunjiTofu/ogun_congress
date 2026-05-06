<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\ProgrammeSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    /**
     * Export attendance for a single session as PDF or CSV.
     */
    public function exportSession(Request $request, ProgrammeSession $session)
    {
        $format = $request->query('format', 'pdf');

        $campers = Camper::whereHas('checkinEvents', fn ($q) => $q
            ->where('programme_session_id', $session->id)
            ->where('event_type', 'programme_attendance')
        )
            ->with(['church.district'])
            ->orderBy('full_name')
            ->get();

        // Attach time recorded
        $campers->each(function ($camper) use ($session) {
            $camper->attended_at = CheckinEvent::where('camper_id', $camper->id)
                ->where('programme_session_id', $session->id)
                ->where('event_type', 'programme_attendance')
                ->latest('occurred_at')
                ->value('occurred_at');
        });

        if ($format === 'csv') {
            return $this->exportSessionCsv($session, $campers);
        }

        return $this->exportSessionPdf($session, $campers);
    }

    /**
     * Export all sessions for a given date (or all dates) as PDF.
     */
    public function exportAll(Request $request)
    {
        $date     = $request->query('date');
        $format   = $request->query('format', 'pdf');

        $sessions = ProgrammeSession::with(['attendees.camper.church.district'])
            ->when($date, fn ($q) => $q->whereDate('date', $date))
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();

        if ($format === 'csv') {
            return $this->exportAllCsv($sessions);
        }

        $pdf = Pdf::loadView('pdf.attendance-all', compact('sessions', 'date'))
            ->setPaper('a4', 'portrait');

        $filename = $date
            ? 'attendance-' . $date . '.pdf'
            : 'attendance-all-sessions.pdf';

        return $pdf->download($filename);
    }

    /**
     * Day-view: all checked-in campers for a given date.
     */
    public function dailyCheckins(Request $request)
    {
        $date = $request->query('date', today()->toDateString());

        $events = CheckinEvent::with(['camper.church.district'])
            ->where('event_type', 'check_in')
            ->whereDate('occurred_at', $date)
            ->orderBy('occurred_at')
            ->get();

        if ($request->query('format') === 'pdf') {
            $pdf = Pdf::loadView('pdf.daily-checkins', compact('events', 'date'))
                ->setPaper('a4', 'portrait');
            return $pdf->download('checkins-' . $date . '.pdf');
        }

        if ($request->query('format') === 'csv') {
            return $this->dailyCsv($events, $date);
        }

        // Return JSON for admin dashboard
        return response()->json([
            'date'    => $date,
            'total'   => $events->count(),
            'campers' => $events->map(fn ($e) => [
                'camper_number' => $e->camper->camper_number,
                'full_name'     => $e->camper->full_name,
                'category'      => $e->camper->category?->label(),
                'church'        => $e->camper->church?->name,
                'checked_in_at' => $e->occurred_at->format('H:i'),
            ]),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function exportSessionPdf(ProgrammeSession $session, $campers)
    {
        $pdf = Pdf::loadView('pdf.attendance-session', compact('session', 'campers'))
            ->setPaper('a4', 'portrait');

        $filename = 'attendance-' . str($session->title)->slug() . '-' . $session->date->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    private function exportSessionCsv(ProgrammeSession $session, $campers)
    {
        $rows   = [];
        $rows[] = ['#', 'Camper Number', 'Full Name', 'Category', 'Church', 'District', 'Time Recorded'];

        foreach ($campers as $i => $c) {
            $rows[] = [
                $i + 1,
                $c->camper_number,
                $c->full_name,
                $c->category?->label(),
                $c->church?->name,
                $c->church?->district?->name,
                $c->attended_at ? Carbon::parse($c->attended_at)->format('H:i, d M Y') : '—',
            ];
        }

        $csv      = collect($rows)->map(fn ($r) => implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v ?? '') . '"', $r)))->implode("\n");
        $filename = 'attendance-' . str($session->title)->slug() . '-' . $session->date->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    private function exportAllCsv($sessions)
    {
        $rows   = [];
        $rows[] = ['Session', 'Date', 'Time', 'Camper Number', 'Full Name', 'Category', 'Church', 'District'];

        foreach ($sessions as $session) {
            foreach ($session->attendees as $event) {
                $c      = $event->camper;
                $rows[] = [
                    $session->title,
                    $session->date->format('d M Y'),
                    $session->start_time,
                    $c?->camper_number,
                    $c?->full_name,
                    $c?->category?->label(),
                    $c?->church?->name,
                    $c?->church?->district?->name,
                ];
            }
        }

        $csv = collect($rows)->map(fn ($r) => implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v ?? '') . '"', $r)))->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-all.csv"',
        ]);
    }

    private function dailyCsv($events, string $date)
    {
        $rows   = [];
        $rows[] = ['#', 'Camper Number', 'Full Name', 'Category', 'Church', 'District', 'Checked In At'];

        foreach ($events as $i => $e) {
            $c      = $e->camper;
            $rows[] = [
                $i + 1,
                $c->camper_number,
                $c->full_name,
                $c->category?->label(),
                $c->church?->name,
                $c->church?->district?->name,
                $e->occurred_at->format('H:i'),
            ];
        }

        $csv = collect($rows)->map(fn ($r) => implode(',', array_map(fn ($v) => '"' . str_replace('"', '""', $v ?? '') . '"', $r)))->implode("\n");

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"checkins-{$date}.csv\"",
        ]);
    }
}
