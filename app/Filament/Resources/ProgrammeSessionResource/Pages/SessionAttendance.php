<?php

namespace App\Filament\Resources\ProgrammeSessionResource\Pages;

use App\Filament\Resources\ProgrammeSessionResource;
use Filament\Actions;
use App\Models\CheckinEvent;
use App\Models\ProgrammeSession;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;


/**
 * Custom page: Attendance sheet for a single programme session.
 * Secretariat and admin can view + export.
 */
class SessionAttendance extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProgrammeSessionResource::class;
    protected static string $view     = 'filament.pages.session-attendance';

    public ProgrammeSession $record;

    public function mount(ProgrammeSession $record): void
    {
        $this->record = $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-text')
                ->color('danger')
                ->url(route('attendance.export.session', ['session' => $this->record->id, 'format' => 'pdf']))
                ->openUrlInNewTab(),

            Actions\Action::make('export_csv')
                ->label('Export CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('attendance.export.session', ['session' => $this->record->id, 'format' => 'csv']))
                ->openUrlInNewTab(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Camper::query()
                    ->whereHas('checkinEvents', fn ($q) => $q
                        ->where('programme_session_id', $this->record->id)
                        ->where('event_type', 'programme_attendance')
                    )
                    ->with(['church.district'])
            )
            ->heading('Attendance — ' . $this->record->title . ' (' . $this->record->date->format('d M Y') . ')')
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null),

                Tables\Columns\TextColumn::make('camper_number')
                    ->label('Code')
                    ->fontFamily('mono')
                    ->copyable(),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable(),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District'),

                Tables\Columns\TextColumn::make('attended_at')
                    ->label('Time Recorded')
                    ->getStateUsing(fn ($record) => CheckinEvent::where('camper_id', $record->id)
                        ->where('programme_session_id', $this->record->id)
                        ->where('event_type', 'programme_attendance')
                        ->latest('occurred_at')
                        ->value('occurred_at'))
                    ->dateTime('H:i, d M'),
            ])
            ->defaultSort('full_name')
            ->paginated([25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(\App\Enums\CamperCategory::class),
            ]);
    }
}
