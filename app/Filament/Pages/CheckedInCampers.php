<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use App\Models\CheckinEvent;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class CheckedInCampers extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Checked-In Campers';
    protected static ?string $navigationGroup = 'Camp Operations';
    protected static ?int    $navigationSort  = 1;
    protected static string  $view            = 'filament.pages.checked-in-campers';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat', 'camp_director']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(route('attendance.daily.checkins', ['format' => 'pdf', 'date' => today()->toDateString()]))
                ->openUrlInNewTab(),

            Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('attendance.daily.checkins', ['format' => 'csv', 'date' => today()->toDateString()]))
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        // Get IDs of campers who have ever checked in
        $checkedInIds = CheckinEvent::where('event_type', 'check_in')
            ->distinct('camper_id')
            ->pluck('camper_id');

        // Get currently-in IDs (last event = check_in)
        $currentlyInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        return $table
            ->query(
                Camper::query()
                    ->whereIn('id', $checkedInIds)
                    ->with(['church.district', 'media'])
            )
            ->heading('Camp Check-In Register — ' . today()->format('d M Y'))
            ->defaultSort('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')->fontFamily('mono')->copyable()->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')->sortable(),

                // Status — In / Out based on last event
                Tables\Columns\TextColumn::make('checkin_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) use ($currentlyInIds): HtmlString {
                        $isIn = $currentlyInIds->contains($record->id);
                        $last = CheckinEvent::where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->latest('occurred_at')
                            ->first();
                        $time = $last ? Carbon::parse($last->occurred_at)->format('g:i A') : '';
                        if ($isIn) {
                            return new HtmlString(
                                '<div><span style="background:#D1FAE5;color:#065F46;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:100px">✅ In Camp</span>'
                                . '<div style="font-size:0.68rem;color:#64748B;margin-top:2px">' . $time . '</div></div>'
                            );
                        }
                        return new HtmlString(
                            '<div><span style="background:#FEE2E2;color:#991B1B;font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:100px">🚪 Checked Out</span>'
                            . '<div style="font-size:0.68rem;color:#64748B;margin-top:2px">' . $time . '</div></div>'
                        );
                    })
                    ->html(),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')->boolean()->trueColor('success')->falseColor('warning'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Current Status')
                    ->options(['in' => '✅ In Camp', 'out' => '🚪 Checked Out'])
                    ->query(function ($query, array $data) use ($currentlyInIds) {
                        if ($data['value'] === 'in') {
                            return $query->whereIn('id', $currentlyInIds);
                        }
                        if ($data['value'] === 'out') {
                            return $query->whereNotIn('id', $currentlyInIds);
                        }
                    }),

                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),

                Tables\Filters\SelectFilter::make('church')
                    ->relationship('church', 'name')->searchable(),
            ])
            ->actions([
                // Trail modal — shows full check-in/out history for this camper
                Tables\Actions\Action::make('trail')
                    ->label('Trail')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Check-In Trail — ' . $record->full_name)
                    ->modalContent(function ($record): HtmlString {
                        $events = CheckinEvent::with('recordedBy')
                            ->where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->orderBy('occurred_at', 'desc')
                            ->get();

                        if ($events->isEmpty()) {
                            return new HtmlString('<p style="color:#94A3B8;font-style:italic;padding:1rem 0">No events recorded.</p>');
                        }

                        $rows = '';
                        foreach ($events as $e) {
                            $type  = is_string($e->event_type) ? $e->event_type : $e->event_type?->value;
                            $isIn  = $type === 'check_in';
                            $icon  = $isIn ? '✅' : '🚪';
                            $label = $isIn ? 'Check In' : 'Check Out';
                            $color = $isIn ? '#065F46' : '#991B1B';
                            $bg    = $isIn ? '#D1FAE5' : '#FEE2E2';
                            $bdr   = $isIn ? '#6EE7B7' : '#FCA5A5';
                            $time  = Carbon::parse($e->occurred_at)->format('g:i A, d M Y');
                            $by    = $e->recordedBy?->name ?? 'Unknown';

                            $rows .= '<div style="display:flex;align-items:flex-start;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid #F1F5F9">'
                                . '<span style="font-size:1.2rem;flex-shrink:0">' . $icon . '</span>'
                                . '<div style="flex:1">'
                                . '<span style="background:' . $bg . ';color:' . $color . ';border:1px solid ' . $bdr . ';font-size:0.72rem;font-weight:700;padding:2px 10px;border-radius:100px">' . $label . '</span>'
                                . '<div style="font-size:0.72rem;color:#64748B;margin-top:3px">🕐 ' . $time . ' &nbsp;·&nbsp; 👤 ' . e($by) . '</div>'
                                . '</div></div>';
                        }
                        return new HtmlString('<div style="max-height:60vh;overflow-y:auto;padding:0 0.25rem">' . $rows . '</div>');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->paginated([25, 50, 100])
            ->poll('30s');
    }
}
