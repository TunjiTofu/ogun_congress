<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactMessageResource extends Resource
{
    protected static ?string $model           = ContactMessage::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Messages';
    protected static ?int    $navigationSort  = 15;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole([
            'super_admin', 'camp_director', 'secretariat', 'accountant',
        ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $userId = auth()->id();
        // Count messages the current user has NOT yet read
        $count = static::getEloquentQuery()
            ->whereDoesntHave('readers', fn ($q) => $q->where('user_id', $userId))
            ->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    /**
     * Scope messages by role — each role only sees what's routed to them.
     * super_admin sees everything.
     */
    public static function getEloquentQuery(): Builder
    {
        $user  = auth()->user();
        $query = parent::getEloquentQuery()->latest();

        if ($user->hasRole('super_admin')) {
            return $query;
        }

        $roleCategories = [
            'camp_director' => ['general', 'complaint', 'inquiry', 'question'],
            'secretariat'   => ['general', 'inquiry', 'question'],
            'accountant'    => ['payment', 'inquiry'],
        ];

        $visible = collect($roleCategories)
            ->filter(fn ($cats, $role) => $user->hasRole($role))
            ->flatten()->unique()->values()->toArray();

        return $query->whereIn('category', $visible);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sender_name')->label('Name')->disabled(),
            Forms\Components\TextInput::make('sender_phone')->label('Phone')->disabled(),
            Forms\Components\TextInput::make('sender_email')->label('Email')->disabled(),
            Forms\Components\Select::make('category')
                ->options([
                    'general'   => 'General Enquiry',
                    'complaint' => 'Complaint',
                    'inquiry'   => 'Inquiry',
                    'payment'   => 'Payment Question',
                    'question'  => 'Question',
                ])->disabled(),
            Forms\Components\Textarea::make('message')->rows(5)->disabled()->columnSpanFull(),
            Forms\Components\Toggle::make('is_read')->label('Marked as Read'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('read_status')
                    ->label('')
                    ->width('30px')
                    ->getStateUsing(fn (ContactMessage $r) => $r->isReadBy(auth()->id()) ? '✓' : '●')
                    ->color(fn (ContactMessage $r) => $r->isReadBy(auth()->id()) ? 'gray' : 'warning')
                    ->weight(fn (ContactMessage $r) => $r->isReadBy(auth()->id()) ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('sender_name')
                    ->label('From')
                    ->searchable()
                    ->weight(fn ($record) => $record->isReadBy(auth()->id()) ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('sender_phone')->label('Phone')->copyable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'gray'    => 'general',
                        'danger'  => 'complaint',
                        'info'    => 'inquiry',
                        'success' => 'payment',
                        'warning' => 'question',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'general'   => 'General Enquiry',
                        'complaint' => 'Complaint',
                        'inquiry'   => 'Inquiry',
                        'payment'   => 'Payment Question',
                        'question'  => 'Question',
                        default     => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('message')->limit(60)->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, g:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Read Status')
                    ->trueLabel('Read')->falseLabel('Unread'),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'general'   => 'General Enquiry',
                        'complaint' => 'Complaint',
                        'inquiry'   => 'Inquiry',
                        'payment'   => 'Payment Question',
                        'question'  => 'Question',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-envelope-open')
                    ->color('gray')
                    ->visible(fn (ContactMessage $r) => ! $r->isReadBy(auth()->id()))
                    ->action(fn (ContactMessage $r) => $r->markReadFor(auth()->id())),

                Tables\Actions\ViewAction::make()
                    ->after(fn (ContactMessage $r) => $r->markReadFor(auth()->id())),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_all_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn ($records) => $records->each->markReadFor(auth()->id())),
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('super_admin')),
                ]),
            ])
            ->recordClasses(fn (ContactMessage $r) => $r->isReadBy(auth()->id()) ? '' : 'bg-amber-50');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view'  => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
