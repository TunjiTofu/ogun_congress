<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class PhotoReviewPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Photo Review';
    protected static ?string $navigationGroup = 'Campers';
    protected static ?int    $navigationSort  = 2;
    protected static string  $view            = 'filament.pages.photo-review';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole(['super_admin', 'admin']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Camper::query()
                    ->whereHas('media', fn ($q) => $q->where('collection_name', 'photo'))
                    ->with(['media', 'church.district'])
                    // Pending first (includes re-submitted), then rejected, then approved
                    ->orderByRaw("FIELD(photo_status, 'pending', 'rejected', 'approved')")
                    ->orderBy('full_name')
            )
            ->heading('Camper Photo Review')
            ->columns([
                Tables\Columns\TextColumn::make('photo_preview')
                    ->label('Photo')
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')
                        ? route('camper.photo', $record->id) : null)
                    ->formatStateUsing(fn ($state): HtmlString => $state
                        ? new HtmlString('<img src="' . e($state) . '?v=' . time() . '" style="width:56px;height:72px;object-fit:cover;object-position:top center;border-radius:6px;border:1px solid #E2E8F0">')
                        : new HtmlString('<div style="width:56px;height:72px;background:#F1F5F9;border-radius:6px;display:flex;align-items:center;justify-content:center;color:#94A3B8;font-size:1.2rem">&#128100;</div>'))
                    ->html(),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable()
                    ->weight('bold')
                    ->sortable(),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\BadgeColumn::make('photo_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'  => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        default    => $state,
                    }),

                Tables\Columns\TextColumn::make('media_updated')
                    ->label('Photo Uploaded')
                    ->getStateUsing(fn ($record) => $record->getFirstMedia('photo')?->created_at)
                    ->dateTime('d M Y, H:i')
                    ->sortable(false)
                    ->color('gray'),

                Tables\Columns\TextColumn::make('photo_rejection_reason')
                    ->label('Rejection Reason')
                    ->placeholder('—')
                    ->wrap()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('photo_status')
                    ->label('Status')
                    ->options([
                        'pending'  => 'Pending Review',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending'),

                Tables\Filters\SelectFilter::make('church_id')
                    ->label('Church')
                    ->relationship('church', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->photo_status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update([
                            'photo_status'           => 'approved',
                            'photo_rejection_reason' => null,
                        ]);
                        \App\Jobs\GenerateCamperDocumentsJob::dispatch($record->id);
                        Notification::make()->title('Photo approved. ID card queued.')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->visible(fn ($record) => $record->photo_status !== 'rejected')
                    ->form([
                        Textarea::make('reason')
                            ->label('Reason')
                            ->required()
                            ->rows(2)
                            ->placeholder('e.g. Not a clear passport photo, background not plain...'),
                    ])
                    ->action(function ($record, array $data) {
                        $record->clearMediaCollection('photo');
                        if ($record->id_card_path) {
                            \Illuminate\Support\Facades\Storage::disk('private')->delete($record->id_card_path);
                        }
                        $record->update([
                            'photo_status'           => 'rejected',
                            'photo_rejection_reason' => $data['reason'],
                            'id_card_path'           => null,
                        ]);
                        // Notify the church coordinator
                        \App\Models\User::where('church_id', $record->church_id)
                            ->whereHas('roles', fn ($q) => $q->where('name', 'church_coordinator'))
                            ->each(fn ($u) => Notification::make()
                                ->title('Photo Rejected — ' . $record->full_name)
                                ->body($data['reason'])
                                ->danger()
                                ->sendToDatabase($u));
                        Notification::make()->title('Photo rejected. Coordinator notified.')->warning()->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('bulk_approve')
                    ->label('Approve Selected')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->deselectRecordsAfterCompletion()
                    ->action(function ($records) {
                        $records->each(function ($r) {
                            $r->update([
                                'photo_status'           => 'approved',
                                'photo_rejection_reason' => null,
                            ]);
                            \App\Jobs\GenerateCamperDocumentsJob::dispatch($r->id);
                        });
                        Notification::make()->title('Photos approved. ID cards queued.')->success()->send();
                    }),

                Tables\Actions\BulkAction::make('bulk_reject')
                    ->label('Reject Selected')
                    ->color('danger')
                    ->icon('heroicon-o-x-circle')
                    ->deselectRecordsAfterCompletion()
                    ->form([
                        Textarea::make('reason')
                            ->label('Rejection Reason')
                            ->helperText('This reason will be applied to every selected photo and sent to each camper\'s church coordinator.')
                            ->required()
                            ->rows(3)
                            ->placeholder('e.g. Not a clear passport photo, background not plain...'),
                    ])
                    ->action(function ($records, array $data) {
                        $records->each(function ($r) use ($data) {
                            $r->clearMediaCollection('photo');
                            if ($r->id_card_path) {
                                \Illuminate\Support\Facades\Storage::disk('private')->delete($r->id_card_path);
                            }
                            $r->update([
                                'photo_status'           => 'rejected',
                                'photo_rejection_reason' => $data['reason'],
                                'id_card_path'           => null,
                            ]);
                            \App\Models\User::where('church_id', $r->church_id)
                                ->whereHas('roles', fn ($q) => $q->where('name', 'church_coordinator'))
                                ->each(fn ($u) => Notification::make()
                                    ->title('Photo Rejected — ' . $r->full_name)
                                    ->body($data['reason'])
                                    ->danger()
                                    ->sendToDatabase($u));
                        });
                        Notification::make()->title('Photos rejected. Coordinators notified.')->warning()->send();
                    }),
            ])
            ->poll('3660s');
    }
}
