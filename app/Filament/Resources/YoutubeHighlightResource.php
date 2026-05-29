<?php

namespace App\Filament\Resources;

use App\Models\YoutubeHighlight;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class YoutubeHighlightResource extends Resource
{
    protected static ?string $model           = YoutubeHighlight::class;
    protected static ?string $navigationIcon  = 'heroicon-o-play-circle';
    protected static ?string $navigationLabel = 'YouTube Highlights';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 20;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'camp_director']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Video Details')->schema([
                Forms\Components\TextInput::make('youtube_id')
                    ->label('YouTube Video ID')
                    ->helperText('e.g. dQw4w9WgXcQ — the part after ?v= in the YouTube URL')
                    ->required()
                    ->maxLength(20)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if ($state) {
                            $set('thumbnail_url', "https://i.ytimg.com/vi/{$state}/hqdefault.jpg");
                        }
                    }),

                Forms\Components\TextInput::make('title')
                    ->label('Title')
                    ->required()
                    ->maxLength(200),

                Forms\Components\TextInput::make('eyebrow')
                    ->label('Eyebrow / Label')
                    ->helperText('e.g. "Official trailer · 2026" or "Mid-week diary"')
                    ->maxLength(100),

                Forms\Components\Textarea::make('description')
                    ->label('Short Description')
                    ->rows(2)
                    ->maxLength(300),

                Forms\Components\TextInput::make('duration_label')
                    ->label('Duration')
                    ->helperText('e.g. 2:14')
                    ->maxLength(10),

                Forms\Components\TextInput::make('thumbnail_url')
                    ->label('Thumbnail URL')
                    ->helperText('Auto-filled from YouTube. Override if needed.')
                    ->url()
                    ->maxLength(500),
            ])->columns(2),

            Forms\Components\Section::make('Display Settings')->schema([
                Forms\Components\Select::make('phase')
                    ->label('Phase Tag')
                    ->options(['before' => '⏮ Before Camp', 'during' => '▶ During Camp', 'after' => '⏭ After Camp'])
                    ->nullable(),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower = shown first'),

                Forms\Components\Toggle::make('is_featured')
                    ->label('Featured (large card)')
                    ->helperText('Only one video should be featured at a time')
                    ->default(false),

                Forms\Components\Toggle::make('is_active')
                    ->label('Visible on website')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_url')
                    ->label('Thumb')
                    ->width(80)->height(50)
                    ->defaultImageUrl(fn ($record) => "https://i.ytimg.com/vi/{$record->youtube_id}/default.jpg"),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()->limit(40),

                Tables\Columns\BadgeColumn::make('phase')
                    ->colors(['primary' => 'before', 'warning' => 'during', 'success' => 'after']),

                Tables\Columns\TextColumn::make('duration_label')->label('Duration'),

                Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Visible')->boolean(),

                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-o-play')
                    ->url(fn ($record) => "https://www.youtube.com/watch?v={$record->youtube_id}", true),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Make Visible')
                        ->icon('heroicon-o-eye')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Hide')
                        ->icon('heroicon-o-eye-slash')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => \App\Filament\Resources\YoutubeHighlightResource\Pages\ListYoutubeHighlights::route('/'),
            'create' => \App\Filament\Resources\YoutubeHighlightResource\Pages\CreateYoutubeHighlight::route('/create'),
            'edit'   => \App\Filament\Resources\YoutubeHighlightResource\Pages\EditYoutubeHighlight::route('/{record}/edit'),
        ];
    }
}
