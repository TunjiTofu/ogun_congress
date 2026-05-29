<?php

namespace App\Filament\Resources;

use App\Models\CampMedia;
use App\Models\District;
use App\Services\CloudinaryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class CampMediaResource extends Resource
{
    protected static ?string $model           = CampMedia::class;
    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Camp Media';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 21;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Upload Media')
                ->description('Files are uploaded directly to Cloudinary. Images up to 10 MB, videos up to 100 MB.')
                ->schema([
                    Forms\Components\FileUpload::make('upload_file')
                        ->label('Select File')
                        // No custom disk/directory — use Livewire's default temp storage
                        ->acceptedFileTypes(array_merge(
                            config('cloudinary.allowed_image_types', ['image/jpeg','image/png','image/webp']),
                            config('cloudinary.allowed_video_types', ['video/mp4','video/quicktime','video/webm'])
                        ))
                        ->maxSize(config('cloudinary.max_video_size_mb', 100) * 1024) // KB
                        ->imagePreviewHeight('200')
                        ->previewable(true)
                        ->helperText('Accepted: JPG, PNG, WEBP, HEIC, MP4, MOV, WEBM'),

                    Forms\Components\Placeholder::make('current_media')
                        ->label('Current File')
                        ->content(fn ($record) => $record?->cloudinary_url
                            ? new \Illuminate\Support\HtmlString(
                                '<a href="' . $record->cloudinary_url . '" target="_blank" style="color:#B8924A;font-size:13px">View uploaded file ↗</a>'
                            )
                            : '—'
                        )
                        ->visibleOn('edit'),
                ]),

            Forms\Components\Section::make('Details & Assignment')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->maxLength(200),

                Forms\Components\Textarea::make('caption')
                    ->label('Caption')
                    ->rows(2)
                    ->maxLength(500),

                Forms\Components\Select::make('district_id')
                    ->label('District')
                    ->options(District::orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->nullable(),

                Forms\Components\Select::make('category')
                    ->label('Source')
                    ->options(['official' => '🏛 Official', 'camper' => '👤 Camper upload'])
                    ->default('official')
                    ->required(),

                Forms\Components\TextInput::make('congress_year')
                    ->label('Year')
                    ->default(now()->year)
                    ->maxLength(4),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending'  => '⏳ Pending Review',
                        'approved' => '✅ Approved',
                        'rejected' => '❌ Rejected',
                    ])
                    ->default('pending')
                    ->required(),

                Forms\Components\TextInput::make('rejection_reason')
                    ->label('Rejection Reason')
                    ->maxLength(300)
                    ->visible(fn (Forms\Get $get) => $get('status') === 'rejected'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cloudinary_url')
                    ->label('Preview')
                    ->width(90)->height(60)
                    ->square()
                    ->placeholder('No image'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->limit(35)
                    ->description(fn ($record) => $record->caption),

                Tables\Columns\TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state === 'video' ? '🎬 Video' : '🖼 Image'),

                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')
                    ->badge()
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'pending', 'success' => 'approved', 'danger' => 'rejected']),

                Tables\Columns\TextColumn::make('created_at')->label('Uploaded')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']),
                Tables\Filters\SelectFilter::make('district_id')
                    ->label('District')
                    ->relationship('district', 'name'),
                Tables\Filters\SelectFilter::make('media_type')
                    ->options(['image' => 'Images', 'video' => 'Videos']),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) => $record->status !== 'approved')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->update(['status' => 'approved']);
                        Notification::make()->title('Approved — now visible on the album.')->success()->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn ($record) => $record->status !== 'rejected')
                    ->form([
                        Forms\Components\TextInput::make('rejection_reason')->label('Reason')->maxLength(300),
                    ])
                    ->action(function ($record, array $data) {
                        $record->update(['status' => 'rejected', 'rejection_reason' => $data['rejection_reason'] ?? null]);
                        Notification::make()->title('Rejected.')->warning()->send();
                    }),

                Tables\Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $record->cloudinary_url, true)
                    ->visible(fn ($record) => (bool) $record->cloudinary_url),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->after(function ($record) {
                        // Delete from Cloudinary when record is deleted
                        if ($record->cloudinary_public_id) {
                            try {
                                app(CloudinaryService::class)->delete(
                                    $record->cloudinary_public_id,
                                    $record->media_type ?? 'image'
                                );
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning('cloudinary.delete_failed', ['error' => $e->getMessage()]);
                            }
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check-circle')->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'approved'])),
                    Tables\Actions\BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-o-x-circle')->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['status' => 'rejected'])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\CampMediaResource\Pages\ListCampMedia::route('/'),
            'create' => \App\Filament\Resources\CampMediaResource\Pages\CreateCampMedia::route('/create'),
            'edit'   => \App\Filament\Resources\CampMediaResource\Pages\EditCampMedia::route('/{record}/edit'),
        ];
    }
}
