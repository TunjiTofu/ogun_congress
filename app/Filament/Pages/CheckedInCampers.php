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
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat']);
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
        // Get IDs of campers whose LAST check-in/out event was a check_in
        $checkedInIds = CheckinEvent::selectRaw('camper_id')
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
            ->heading('Currently Checked-In — ' . today()->format('d M Y'))
            ->defaultSort('full_name')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->copyable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label())
                    ->sortable(),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')
                    ->sortable(),

                Tables\Columns\TextColumn::make('checkin_status')
                    ->label('Status')
                    ->getStateUsing(function ($record): HtmlString {
                        $last = CheckinEvent::where('camper_id', $record->id)
                            ->whereIn('event_type', ['check_in', 'check_out'])
                            ->latest('occurred_at')
                            ->first();

                        if (! $last) {
                            return new HtmlString(
                                '<span style="color:#94A3B8;font-size:0.78rem">—</span>'
                            );
                        }

                        $type  = is_string($last->event_type) ? $last->event_type : $last->event_type?->value;
                        $isIn  = $type === 'check_in';
                        $time  = Carbon::parse($last->occurred_at)->format('g:i A');
                        $date  = Carbon::parse($last->occurred_at)->format('d M');
                        $icon  = $isIn ? '✅' : '🚪';
                        $label = $isIn ? 'Checked In' : 'Checked Out';
                        $color = $isIn ? '#065F46' : '#991B1B';
                        $bg    = $isIn ? '#D1FAE5' : '#FEE2E2';
                        $bdr   = $isIn ? '#6EE7B7' : '#FCA5A5';

                        return new HtmlString(
                            '<div style="display:flex;flex-direction:column;gap:2px">'
                            . '<span style="display:inline-flex;align-items:center;gap:4px;'
                            . 'background:' . $bg . ';color:' . $color . ';border:1px solid ' . $bdr . ';'
                            . 'font-size:0.7rem;font-weight:700;padding:2px 8px;border-radius:100px;width:fit-content">'
                            . $icon . ' ' . $label . '</span>'
                            . '<span style="font-size:0.72rem;color:#64748B">' . $time . ', ' . $date . '</span>'
                            . '</div>'
                        );
                    })
                    ->html(),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning'),
            ])
            ->actions([
                Tables\Actions\Action::make('trail')
                    ->label('Trail')
                    ->icon('heroicon-o-clock')
                    ->color('gray')
                    ->modalHeading(fn ($record) => 'Check-In Trail — ' . $record->full_name)
                    ->modalContent(function ($record): HtmlString {
                        $events = CheckinEvent::where('camper_id', $record->id)
                            ->orderBy('occurred_at', 'desc')
                            ->get();

                        if ($events->isEmpty()) {
                            return new HtmlString(
                                '<p style="color:#94A3B8;font-style:italic;padding:1rem 0">No events recorded.</p>'
                            );
                        }

                        $rows = '';
                        foreach ($events as $e) {
                            $type  = is_string($e->event_type) ? $e->event_type : $e->event_type?->value;
                            $isIn  = $type === 'check_in';
                            $isOut = $type === 'check_out';

                            $icon  = $isIn ? '✅' : ($isOut ? '🚪' : '📋');
                            $label = $isIn ? 'Check In' : ($isOut ? 'Check Out' : 'Programme Attendance');
                            $color = $isIn ? '#065F46' : ($isOut ? '#991B1B' : '#1E40AF');
                            $bg    = $isIn ? '#D1FAE5' : ($isOut ? '#FEE2E2' : '#DBEAFE');
                            $time  = Carbon::parse($e->occurred_at)->format('g:i A, d M Y');
                            $dev   = $e->device_id
                                ? '<span style="font-size:0.68rem;color:#94A3B8;margin-left:6px">' . e($e->device_id) . '</span>'
                                : '';

                            $rows .= '<div style="display:flex;align-items:center;gap:0.75rem;'
                                . 'padding:0.65rem 0;border-bottom:1px solid #F1F5F9">'
                                . '<span style="font-size:1.2rem;flex-shrink:0">' . $icon . '</span>'
                                . '<div style="flex:1">'
                                . '<span style="display:inline-block;background:' . $bg . ';color:' . $color . ';'
                                . 'font-size:0.72rem;font-weight:700;padding:0.15rem 0.6rem;border-radius:100px">'
                                . $label . '</span>' . $dev . '</div>'
                                . '<span style="font-size:0.78rem;color:#475569;white-space:nowrap">' . $time . '</span>'
                                . '</div>';
                        }

                        return new HtmlString('<div style="padding:0 0.25rem">' . $rows . '</div>');
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),

                Tables\Filters\SelectFilter::make('church')
                    ->relationship('church', 'name')
                    ->searchable(),
            ])
            ->paginated([25, 50, 100])
            ->poll('30s');
    }
}
