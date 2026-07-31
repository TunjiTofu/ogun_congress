<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillResource\Pages;
use App\Models\Skill;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SkillResource extends Resource
{
    protected static ?string $model           = Skill::class;
    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Skill Acquisition';
    protected static ?string $navigationLabel = 'Skills';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'skill_manager']);
    }

    public static function canCreate(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canEdit($record): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function canDelete($record): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()->maxLength(255)->columnSpanFull(),

            Select::make('category')
                ->label('Category Restriction')
                ->options([
                    ''             => 'General (available to all)',
                    'adventurer'   => 'Adventurers only',
                    'pathfinder'   => 'Pathfinders only',
                    'senior_youth' => 'Senior Youth only',
                ])
                ->default('')
                ->helperText('Leave as General to make this skill available to every camper.'),

            Select::make('club_rank')
                ->label('Club Rank Restriction')
                ->options(fn () => \Illuminate\Support\Facades\DB::table('club_ranks')
                    ->orderBy('rank_name')
                    ->pluck('rank_name', 'rank_name')
                    ->prepend('All ranks (no restriction)', '')
                    ->toArray()
                )
                ->default('')
                ->helperText('Only applies when a category is set. Leave as "All ranks" to allow any rank within the selected category.'),

            TextInput::make('facilitator')->maxLength(255),

            TextInput::make('maximum_attendees')
                ->label('Maximum Attendees')
                ->numeric()->required()->default(30)->minValue(1),

            Select::make('status')
                ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                ->default('active')->required(),

            Textarea::make('description')
                ->rows(2)
                ->columnSpanFull(),

            RichEditor::make('requirement')
                ->label('Requirements')
                ->toolbarButtons([
                    'bold', 'italic', 'underline',
                    'bulletList', 'orderedList',
                    'h3',
                    'blockquote',
                ])
                ->columnSpanFull(),

            RichEditor::make('curriculum')
                ->label('Curriculum / What They Will Learn')
                ->toolbarButtons([
                    'bold', 'italic', 'underline',
                    'bulletList', 'orderedList',
                    'h2', 'h3',
                    'blockquote',
                ])
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('For')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'adventurer'   => 'Adventurers',
                        'pathfinder'   => 'Pathfinders',
                        'senior_youth' => 'Senior Youth',
                        null, ''       => 'General',
                        default        => $state,
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'adventurer'   => 'info',
                        'pathfinder'   => 'success',
                        'senior_youth' => 'warning',
                        default        => 'gray',
                    }),

                Tables\Columns\TextColumn::make('club_rank')
                    ->label('Rank')
                    ->placeholder('All ranks')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('facilitator')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('maximum_attendees')
                    ->label('Capacity')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('registrations_count')
                    ->label('Registered')
                    ->counts('registrations')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('remaining')
                    ->label('Remaining')
                    ->getStateUsing(fn (Skill $r) => $r->maximum_attendees - $r->registrations_count)
                    ->alignCenter()
                    ->color(fn ($state) => $state <= 5 ? 'danger' : ($state <= 15 ? 'warning' : 'success')),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['success' => 'active', 'danger' => 'inactive']),
            ])
            ->defaultSort('name')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        ''             => 'General',
                        'adventurer'   => 'Adventurers',
                        'pathfinder'   => 'Pathfinders',
                        'senior_youth' => 'Senior Youth',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive']),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                Tables\Actions\Action::make('toggle_status')
                    ->label(fn (Skill $r) => $r->status === 'active' ? 'Deactivate' : 'Activate')
                    ->icon(fn (Skill $r) => $r->status === 'active' ? 'heroicon-o-pause-circle' : 'heroicon-o-play-circle')
                    ->color(fn (Skill $r) => $r->status === 'active' ? 'danger' : 'success')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->requiresConfirmation()
                    ->action(function (Skill $record) {
                        $record->update([
                            'status' => $record->status === 'active' ? 'inactive' : 'active',
                        ]);
                        Notification::make()
                            ->title('Skill ' . ($record->status === 'active' ? 'activated' : 'deactivated'))
                            ->success()->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSkills::route('/'),
            'create' => Pages\CreateSkill::route('/create'),
            'edit'   => Pages\EditSkill::route('/{record}/edit'),
        ];
    }
}
