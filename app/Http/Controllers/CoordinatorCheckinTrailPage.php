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

    /** Active tab: 'checkin' or 'attendance' */
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

        return null; // null means no restriction (all roles)
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
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
    // Shows ONE row per unique camper (their current status + last event time).
    // Clicking "Trail" opens a modal with their full in/out history.
    private function checkinTable(Table $table, ?array $camperIds): Table
    {
        $user = auth()->user();

        // Campers who have ever had a check_in event
        $query = Camper::query()
            ->whereHas('checkinEvents', fn ($q) => $q->where('event_type', 'check_in'))
            ->with(['church.district', 'media']);

        if ($camperIds !== null) {
            $query->whereIn('id', $camperIds);
        }

        // Currently-in IDs
        $currentlyInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        return $table
            ->query($query)
            ->heading('Check-In / Check-Out — Unique Campers')
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

                // Current status
                Tables\Columns\TextColumn::make('current_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($currentlyInIds): HtmlString {
                        $isIn = $currentlyInIds->contains($record->id);
                        $last = CheckinEvent::where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->latest('occurred_at')->first();
                        $time = $last ? Carbon::parse($last->occurred_at)->format('g:i A') : '';
                        if ($isIn) {
                            return new HtmlString(
                                '<div><span style="background:#D1FAE5;color:#065F46;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:100px">✅ In Camp</span>'
                                .'<div style="font-size:0.65rem;color:#64748B;margin-top:2px">'.$time.'</div></div>'
                            );
                        }
                        return new HtmlString(
                            '<div><span style="background:#FEE2E2;color:#991B1B;font-size:0.7rem;font-weight:700;padding:2px 10px;border-radius:100px">🚪 Checked Out</span>'
                            .'<div style="font-size:0.65rem;color:#64748B;margin-top:2px">'.$time.'</div></div>'
                        );
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('checkin_count')
                    ->label('Events')
                    ->getStateUsing(fn ($record) => CheckinEvent::where('camper_id', $record->id)
                        ->whereIn('event_type', ['check_in', 'check_out'])->count())
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Current Status')
                    ->options(['in' => '✅ In Camp', 'out' => '🚪 Checked Out'])
                    ->query(function ($query, array $data) use ($currentlyInIds) {
                        if ($data['value'] === 'in') return $query->whereIn('id', $currentlyInIds);
                        if ($data['value'] === 'out') return $query->whereNotIn('id', $currentlyInIds);
                    }),
                Tables\Filters\SelectFilter::make('category')->options(\App\Enums\CamperCategory::class),
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

                        $html = '<div style="max-height:60vh;overflow-y:auto;padding:0 0.25rem">';
                        foreach ($events as $e) {
                            $type  = is_string($e->event_type) ? $e->event_type : $e->event_type?->value;
                            $isIn  = $type === 'check_in';
                            $icon  = $isIn ? '✅' : '🚪';
                            $label = $isIn ? 'Check In' : 'Check Out';
                            $bg    = $isIn ? '#D1FAE5' : '#FEE2E2';
                            $tc    = $isIn ? '#065F46' : '#991B1B';
                            $bc    = $isIn ? '#6EE7B7' : '#FCA5A5';
                            $time  = Carbon::parse($e->occurred_at)->format('g:i A, d M Y');
                            $by    = $e->recordedBy?->name ?? 'Unknown';

                            $html .= '<div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #F1F5F9">';
                            $html .= '<span style="font-size:1.2rem;flex-shrink:0">'.$icon.'</span>';
                            $html .= '<div style="flex:1">';
                            $html .= '<span style="background:'.$bg.';color:'.$tc.';border:1px solid '.$bc.';font-size:0.72rem;font-weight:700;padding:2px 10px;border-radius:100px">'.$label.'</span>';
                            $html .= '<div style="font-size:0.72rem;color:#64748B;margin-top:3px">🕐 '.e($time).' &nbsp;·&nbsp; 👤 '.e($by).'</div>';
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
    // Shows programme_attendance events. One row per event.
    private function attendanceTable(Table $table, ?array $camperIds): Table
    {
        $query = CheckinEvent::with(['camper.church.district', 'recordedBy', 'programmeSession'])
            ->where('event_type', 'programme_attendance')
            ->orderBy('occurred_at', 'desc');

        if ($camperIds !== null) {
            $query->whereIn('camper_id', $camperIds);
        }

        return $table
            ->query($query)
            ->heading('Programme Attendance')
            ->defaultSort('occurred_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('occurred_at')
                    ->label('Time')->dateTime('g:i A, d M Y')->sortable(),

                Tables\Columns\TextColumn::make('programmeSession.title')
                    ->label('Session')->placeholder('—')->searchable(),

                Tables\Columns\ImageColumn::make('camper.photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->camper?->getFirstMedia('photo')
                        ? route('camper.photo', $record->camper_id) : null),

                Tables\Columns\TextColumn::make('camper.full_name')
                    ->label('Camper')->searchable()->weight('bold'),

                Tables\Columns\TextColumn::make('camper.camper_number')
                    ->label('Code')->fontFamily('mono')->copyable(),

                Tables\Columns\TextColumn::make('camper.category')
                    ->label('Category')->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('camper.church.name')
                    ->label('Church')->sortable()
                    ->visible(fn () => ! auth()->user()->hasRole('church_coordinator')),

                Tables\Columns\TextColumn::make('recordedBy.name')
                    ->label('Recorded By')->placeholder('—'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('programme_session_id')
                    ->label('Session')
                    ->options(ProgrammeSession::orderBy('date', 'desc')->orderBy('start_time')
                        ->get()->mapWithKeys(fn ($s) => [$s->id => $s->title . ' (' . $s->date->format('d M') . ')'])),

                Tables\Filters\Filter::make('date')
                    ->form([\Filament\Forms\Components\DatePicker::make('date')->label('Date')->native(false)])
                    ->query(fn ($query, array $data) => ($data['date'] ?? null)
                        ? $query->whereDate('occurred_at', $data['date']) : $query),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')->options(\App\Enums\CamperCategory::class)
                    ->query(fn ($query, array $data) => $data['value']
                        ? $query->whereHas('camper', fn ($q) => $q->where('category', $data['value'])) : $query),
            ])
            ->paginated([25, 50]);
    }
}
