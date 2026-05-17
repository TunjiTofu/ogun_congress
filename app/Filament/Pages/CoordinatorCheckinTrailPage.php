<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\Church;
use App\Models\ProgrammeSession;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class CoordinatorCheckinTrailPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Check-In Trail';
    protected static ?string $navigationGroup = 'Camp Operations';
    protected static ?int    $navigationSort  = 5;
    protected static string  $view            = 'filament.pages.coordinator-checkin-trail';

    public string $activeTab = 'checkin';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            'church_coordinator', 'district_coordinator',
            'secretariat', 'camp_director', 'super_admin',
        ]);
    }

    protected function scopedCamperIds(): ?array
    {
        $user = auth()->user();

        if ($user->hasRole('church_coordinator') && $user->church_id) {
            return Camper::where('church_id', $user->church_id)->pluck('id')->toArray();
        }

        if ($user->hasRole('district_coordinator') && $user->district_id) {
            $churchIds = Church::where('district_id', $user->district_id)->pluck('id');
            return Camper::whereIn('church_id', $churchIds)->pluck('id')->toArray();
        }

        return null;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable(); // Force Filament to rebuild the table query
    }

    public function table(Table $table): Table
    {
        $camperIds = $this->scopedCamperIds();

        if ($this->activeTab === 'attendance') {
            return $this->attendanceTable($table, $camperIds);
        }

        return $this->checkinTable($table, $camperIds);
    }

    // ── Tab 1: Check-In / Check-Out ───────────────────────────────────────────
    // One row per unique camper. "View Trail" modal shows full in/out history.
    private function checkinTable(Table $table, ?array $camperIds): Table
    {
        $currentlyInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        $query = Camper::query()
            ->whereHas('checkinEvents', fn ($q) => $q->where('event_type', 'check_in'))
            ->with(['church.district', 'media']);

        if ($camperIds !== null) {
            $query->whereIn('id', $camperIds);
        }

        return $table
            ->query($query)
            ->heading('Check-In / Check-Out — One row per camper')
            ->defaultSort('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')->fontFamily('mono')->copyable()->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('church_coordinator')),

                Tables\Columns\TextColumn::make('current_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($currentlyInIds): HtmlString {
                        $isIn = $currentlyInIds->contains($record->id);
                        $last = CheckinEvent::where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->latest('occurred_at')->first();
                        $time = $last ? Carbon::parse($last->occurred_at)->format('g:i A') : '';
                        return $isIn
                            ? new HtmlString('<div><span style="background:#D1FAE5;color:#065F46;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:100px">✅ In Camp</span><div style="font-size:0.65rem;color:#64748B;margin-top:2px">' . $time . '</div></div>')
                            : new HtmlString('<div><span style="background:#FEE2E2;color:#991B1B;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:100px">🚪 Checked Out</span><div style="font-size:0.65rem;color:#64748B;margin-top:2px">' . $time . '</div></div>');
                    })
                    ->html(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Current Status')
                    ->options(['in' => '✅ In Camp', 'out' => '🚪 Checked Out'])
                    ->query(function ($query, array $data) use ($currentlyInIds) {
                        if (($data['value'] ?? null) === 'in') return $query->whereIn('id', $currentlyInIds);
                        if (($data['value'] ?? null) === 'out') return $query->whereNotIn('id', $currentlyInIds);
                    }),
                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),
            ])
            ->actions([
                Tables\Actions\Action::make('trail')
                    ->label('View Trail')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Check-In Trail — ' . $record->full_name)
                    ->modalContent(function (Camper $record): HtmlString {
                        $events = CheckinEvent::with('recordedBy')
                            ->where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->orderBy('occurred_at', 'desc')
                            ->get();

                        if ($events->isEmpty()) {
                            return new HtmlString('<p style="color:#94A3B8;font-style:italic;padding:1rem 0">No events recorded.</p>');
                        }

                        $html = '<div style="max-height:60vh;overflow-y:auto">';
                        foreach ($events as $e) {
                            $type  = is_string($e->event_type) ? $e->event_type : $e->event_type?->value;
                            $isIn  = $type === 'check_in';
                            $icon  = $isIn ? '✅' : '🚪';
                            $label = $isIn ? 'Check In' : 'Check Out';
                            $bg    = $isIn ? '#D1FAE5' : '#FEE2E2';
                            $tc    = $isIn ? '#065F46' : '#991B1B';
                            $bc    = $isIn ? '#6EE7B7' : '#FCA5A5';
                            $time  = Carbon::parse($e->occurred_at)->format('g:i A, d M Y');
                            $by    = $e->recordedBy?->name ?? 'Device';

                            $html .= '<div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #F1F5F9">';
                            $html .= '<span style="font-size:1.2rem;flex-shrink:0">' . $icon . '</span>';
                            $html .= '<div style="flex:1">';
                            $html .= '<span style="background:' . $bg . ';color:' . $tc . ';border:1px solid ' . $bc . ';font-size:0.72rem;font-weight:700;padding:2px 10px;border-radius:100px">' . $label . '</span>';
                            $html .= '<div style="font-size:0.72rem;color:#64748B;margin-top:3px">🕐 ' . e($time) . ' · 👤 ' . e($by) . '</div>';
                            $html .= '</div></div>';
                        }
                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->paginated([25, 50])
            ->poll('30s');
    }

    // ── Tab 2: Programme Attendance ───────────────────────────────────────────
    // One row per CAMPER. "View Sessions" modal shows every session they attended.
    private function attendanceTable(Table $table, ?array $camperIds): Table
    {
        // Build a list of camper IDs that have attended at least one programme
        $attendedQuery = Camper::query()
            ->whereHas('checkinEvents', fn ($q) => $q->where('event_type', 'programme_attendance'))
            ->with(['church.district', 'media']);

        if ($camperIds !== null) {
            $attendedQuery->whereIn('id', $camperIds);
        }

        return $table
            ->query($attendedQuery)
            ->heading('Programme Attendance — One row per camper')
            ->defaultSort('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')->fontFamily('mono')->copyable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('church_coordinator')),

                // Count of sessions attended
                Tables\Columns\TextColumn::make('sessions_attended')
                    ->label('Sessions Attended')
                    ->getStateUsing(fn ($record) =>
                    CheckinEvent::where('camper_id', $record->id)
                        ->where('event_type', 'programme_attendance')
                        ->distinct('programme_session_id')
                        ->count()
                    )
                    ->alignCenter()
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),
                Tables\Filters\SelectFilter::make('church_id')
                    ->label('Church')
                    ->visible(fn () => ! auth()->user()->hasRole('church_coordinator'))
                    ->options(fn () => $camperIds !== null
                        ? Church::whereHas('campers', fn ($q) => $q->whereIn('id', $camperIds ?? []))->pluck('name', 'id')
                        : Church::orderBy('name')->pluck('name', 'id'))
                    ->query(fn ($query, array $data) => ($data['value'] ?? null)
                        ? $query->where('church_id', $data['value']) : $query),
            ])
            ->actions([
                // View all sessions this camper attended
                Tables\Actions\Action::make('view_sessions')
                    ->label('View Sessions')
                    ->icon('heroicon-o-calendar-days')
                    ->color('info')
                    ->modalHeading(fn ($record) => 'Programme Attendance — ' . $record->full_name)
                    ->modalContent(function (Camper $record): HtmlString {
                        $events = CheckinEvent::with(['programmeSession', 'recordedBy'])
                            ->where('camper_id', $record->id)
                            ->where('event_type', 'programme_attendance')
                            ->orderBy('occurred_at', 'desc')
                            ->get();

                        if ($events->isEmpty()) {
                            return new HtmlString('<p style="color:#94A3B8;font-style:italic;padding:1rem 0">No programme attendance recorded.</p>');
                        }

                        $html = '<div style="max-height:60vh;overflow-y:auto">';

                        // Group by session
                        $bySession = $events->groupBy('programme_session_id');

                        foreach ($bySession as $sessionId => $sessionEvents) {
                            $session = $sessionEvents->first()->programmeSession;
                            $title   = $session?->title ?? 'Unknown Session';
                            $date    = $session?->date ? Carbon::parse($session->date)->format('d M Y') : '—';
                            $start   = $session?->start_time ? Carbon::parse($session->start_time)->format('g:i A') : '';
                            $end     = $session?->end_time   ? ' – ' . Carbon::parse($session->end_time)->format('g:i A') : '';
                            $venue   = $session?->venue ?? '';

                            $html .= '<div style="border:1px solid #E2E8F0;border-radius:10px;padding:0.85rem 1rem;margin-bottom:0.75rem">';
                            $html .= '<div style="display:flex;align-items:flex-start;justify-content:space-between;gap:0.5rem;margin-bottom:0.4rem">';
                            $html .= '<strong style="font-size:0.85rem;color:#F9FFFE">' . e($title) . '</strong>';
                            $html .= '<span style="background:#EEF2FF;color:#3730A3;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:100px;flex-shrink:0">📋 Attended</span>';
                            $html .= '</div>';
                            $html .= '<div style="font-size:0.75rem;color:#64748B;display:grid;gap:0.25rem">';
                            $html .= '<div>📅 ' . e($date) . ($start ? ' · 🕐 ' . e($start . $end) : '') . '</div>';
                            if ($venue) $html .= '<div>📍 ' . e($venue) . '</div>';

                            // Show all scan times for this session (could be multiple)
                            foreach ($sessionEvents as $ev) {
                                $scannedAt = Carbon::parse($ev->occurred_at)->format('g:i A, d M Y');
                                $by        = $ev->recordedBy?->name ?? 'Device';
                                $html     .= '<div style="margin-top:0.25rem;padding-top:0.25rem;border-top:1px solid #F1F5F9;font-size:0.7rem">Marked at: ' . e($scannedAt) . ' · By: ' . e($by) . '</div>';
                            }

                            $html .= '</div></div>';
                        }

                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->paginated([25, 50]);
    }
}
