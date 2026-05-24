<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessagesResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MessagesResource extends Resource
{
    protected static ?string $model           = ContactMessage::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationLabel = 'Messages';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 5;

    // Badge showing unread count
    public static function getNavigationBadge(): ?string
    {
        return static::scopedQuery()->where('is_read', false)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin', 'camp_director', 'secretariat', 'accountant',
        ]);
    }

    /**
     * Scope messages so non-super-admins only see messages routed to their role.
     */
    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery()->latest();

        if ($user->hasRole('super_admin')) {
            return $query; // sees all
        }

        $roleCategories = [
            'camp_director' => ['general', 'complaint', 'inquiry', 'question'],
            'secretariat'   => ['general', 'inquiry', 'question'],
            'accountant'    => ['payment', 'inquiry'],
        ];

        $visibleCategories = collect($roleCategories)
            ->filter(fn ($cats, $role) => $user->hasRole($role))
            ->flatten()
            ->unique()
            ->toArray();

        return $query->whereIn('category', $visibleCategories);
    }

    protected static function scopedQuery(): Builder
    {
        return static::getEloquentQuery();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-ellipsis-horizontal-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->width(40),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, g:i A')
                    ->sortable(),

                Tables\Columns\TextColumn::make('sender_name')
                    ->label('Name')
                    ->searchable()
                    ->weight(fn ($record) => $record->is_read ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('sender_phone')
                    ->label('Phone')
                    ->copyable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'info'    => 'general',
                        'danger'  => 'complaint',
                        'primary' => 'inquiry',
                        'warning' => 'payment',
                        'gray'    => 'question',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'general'   => 'General Enquiry',
                        'complaint' => 'Complaint',
                        'inquiry'   => 'Inquiry',
                        'payment'   => 'Payment Question',
                        'question'  => 'Question',
                        default     => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('message')
                    ->limit(80)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general'   => 'General Enquiry',
                        'complaint' => 'Complaint',
                        'inquiry'   => 'Inquiry',
                        'payment'   => 'Payment Question',
                        'question'  => 'Question',
                    ]),
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Status')
                    ->trueLabel('Read')
                    ->falseLabel('Unread')
                    ->nullable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->after(function (ContactMessage $record) {
                        if (! $record->is_read) {
                            $record->update(['is_read' => true, 'read_at' => now()]);
                        }
                    }),

                Tables\Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-check')
                    ->color('gray')
                    ->visible(fn ($record) => ! $record->is_read)
                    ->action(fn ($record) => $record->update(['is_read' => true, 'read_at' => now()])),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->update(['is_read' => true, 'read_at' => now()])),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Message Details')
                ->schema([
                    Infolists\Components\TextEntry::make('sender_name')->label('From')->weight('bold'),
                    Infolists\Components\TextEntry::make('sender_phone')->label('Phone')->copyable(),
                    Infolists\Components\TextEntry::make('sender_email')->label('Email')->placeholder('Not provided'),
                    Infolists\Components\TextEntry::make('category')->label('Category')->badge(),
                    Infolists\Components\TextEntry::make('created_at')->label('Received')->dateTime('d M Y, g:i A'),
                    Infolists\Components\TextEntry::make('message')
                        ->label('Message')
                        ->columnSpanFull()
                        ->prose(),
                ])->columns(2),
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // read-only resource
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'view'  => Pages\ViewMessage::route('/{record}'),
        ];
    }
}
