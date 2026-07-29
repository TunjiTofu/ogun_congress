<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillRegistrationResource\Pages;
use App\Models\CamperSkillRegistration;
use App\Models\Skill;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SkillRegistrationResource extends Resource
{
    protected static ?string $model = CamperSkillRegistration::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Skill Acquisition';
    protected static ?string $navigationLabel = 'Registrations';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'skill_manager']);
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                CamperSkillRegistration::with([
                    'camper.church.district',
                    'skill',
                    'updatedBy',
                ])
            )
            ->columns([
                Tables\Columns\TextColumn::make('camper.full_name')
                    ->label('Camper')->searchable()->weight('bold')->sortable(),

                Tables\Columns\TextColumn::make('camper.camper_number')
                    ->label('Reg. No')->fontFamily('mono')->searchable()->copyable(),

                Tables\Columns\TextColumn::make('camper.church.name')
                    ->label('Church')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('camper.church.district.name')
                    ->label('District')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('camper.category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label()),

                Tables\Columns\TextColumn::make('camper.club_rank')
                    ->label('Rank')->placeholder('—'),

                Tables\Columns\TextColumn::make('skill.name')
                    ->label('Skill')->searchable()->sortable()->weight('bold'),

                Tables\Columns\TextColumn::make('skill.facilitator')
                    ->label('Facilitator')->placeholder('—')->toggleable(),

                Tables\Columns\TextColumn::make('selected_at')
                    ->label('Registered At')->dateTime('d M Y, H:i')->sortable(),

                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->label('Changed By')->placeholder('Self')->toggleable(),
            ])
            ->defaultSort('selected_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('skill_id')
                    ->label('Skill')
                    ->options(Skill::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),

                Tables\Filters\SelectFilter::make('church')
                    ->label('Church')
                    ->relationship('camper.church', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('district')
                    ->label('District')
                    ->relationship('camper.church.district', 'name')
                    ->searchable(),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Category')
                    ->options([
                        'adventurer'   => 'Adventurers',
                        'pathfinder'   => 'Pathfinders',
                        'senior_youth' => 'Senior Youth',
                    ])
                    ->query(fn ($query, $data) => $data['value']
                        ? $query->whereHas('camper', fn ($q) => $q->where('category', $data['value']))
                        : $query),
            ])
            ->actions([
                // Super admin can change a camper's skill
                Tables\Actions\Action::make('change_skill')
                    ->label('Change Skill')
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin']))
                    ->form([
                        Select::make('skill_id')
                            ->label('New Skill')
                            ->options(Skill::active()->orderBy('name')->pluck('name', 'id'))
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (CamperSkillRegistration $record, array $data) {
                        $newSkill = Skill::findOrFail($data['skill_id']);

                        if ($newSkill->isFull() && $record->skill_id !== $newSkill->id) {
                            Notification::make()
                                ->title('This skill is at maximum capacity.')
                                ->danger()->send();
                            return;
                        }

                        $record->update([
                            'skill_id'    => $newSkill->id,
                            'selected_at' => now(),
                            'updated_by'  => auth()->id(),
                        ]);

                        Notification::make()->title('Skill updated.')->success()->send();
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('Remove Selection')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_pdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->url(route('exports.skill-registrations'))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSkillRegistrations::route('/'),
        ];
    }
}
