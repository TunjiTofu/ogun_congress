<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends Resource
{
    protected static ?string $model           = ContactMessage::class;
    protected static ?string $navigationIcon  = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Messages';
    protected static ?int    $navigationSort  = 15;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'accountant']);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = ContactMessage::where('is_read', false)->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('sender_name')->label('Name')->disabled(),
            Forms\Components\TextInput::make('sender_phone')->label('Phone')->disabled(),
            Forms\Components\TextInput::make('sender_email')->label('Email')->disabled(),
            Forms\Components\Select::make('category')->options([
                'general'   => 'General Enquiry',
                'complaint' => 'Complaint',
                'inquiry'   => 'Inquiry',
                'payment'   => 'Payment Enquiry',
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
                Tables\Columns\IconColumn::make('is_read')
                    ->label('')
                    ->boolean()
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope')
                    ->trueColor('gray')
                    ->falseColor('warning')
                    ->width('30px'),

                Tables\Columns\TextColumn::make('sender_name')
                    ->label('From')
                    ->searchable()
                    ->weight(fn ($record) => $record->is_read ? 'normal' : 'bold'),

                Tables\Columns\TextColumn::make('sender_phone')->label('Phone'),

                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'gray'    => 'general',
                        'warning' => 'complaint',
                        'info'    => 'inquiry',
                        'success' => 'payment',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'general'   => 'General',
                        'complaint' => 'Complaint',
                        'inquiry'   => 'Inquiry',
                        'payment'   => 'Payment',
                        default     => $state,
                    }),

                Tables\Columns\TextColumn::make('message')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')->label('Read Status')
                    ->trueLabel('Read')->falseLabel('Unread'),
                Tables\Filters\SelectFilter::make('category')->options([
                    'general'   => 'General',
                    'complaint' => 'Complaint',
                    'inquiry'   => 'Inquiry',
                    'payment'   => 'Payment',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('mark_read')
                    ->label('Mark Read')
                    ->icon('heroicon-o-envelope-open')
                    ->visible(fn (ContactMessage $r) => ! $r->is_read)
                    ->action(fn (ContactMessage $r) => $r->markAsRead()),

                Tables\Actions\ViewAction::make(),
            ])
            ->recordClasses(fn (ContactMessage $r) => $r->is_read ? '' : 'bg-amber-50');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'view'  => Pages\ViewContactMessage::route('/{record}'),
        ];
    }
}
