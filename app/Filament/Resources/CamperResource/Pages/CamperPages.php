<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Filament\Resources\CamperResource;
use App\Enums\CamperCategory;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Builder;

class ListCampers extends ListRecords
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Campers are created via registration only
    }

    public function getTabs(): array
    {
        return [
            'all'         => Tab::make('All'),
            'adventurers' => Tab::make('Adventurers')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::ADVENTURER))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::ADVENTURER)->count()),
            'pathfinders' => Tab::make('Pathfinders')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::PATHFINDER))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::PATHFINDER)->count()),
            'senior_youth'=> Tab::make('Senior Youth')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::SENIOR_YOUTH))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::SENIOR_YOUTH)->count()),
        ];
    }
}

class ViewCamper extends ViewRecord
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Section::make('Identity')
                ->schema([
                    Grid::make(4)->schema([
                        ImageEntry::make('photo')
                            ->label('Photo')
                            ->getStateUsing(fn ($record) => $record->getFirstMediaUrl('photo', 'thumb'))
                            ->circular()
                            ->columnSpan(1),

                        Group::make([
                            TextEntry::make('full_name')->label('Full Name')->weight('bold'),
                            TextEntry::make('camper_number')->label('Camper Number')->copyable(),
                            TextEntry::make('phone')->label('Phone'),
                        ])->columnSpan(3),
                    ]),
                ]),

            Section::make('Personal Details')
                ->columns(3)
                ->schema([
                    TextEntry::make('date_of_birth')->label('Date of Birth')->date(),
                    TextEntry::make('age')->label('Age')->getStateUsing(fn ($record) => $record->age . ' years'),
                    TextEntry::make('gender')->label('Gender')->formatStateUsing(fn ($state) => $state?->label()),
                    TextEntry::make('category')->label('Category')->formatStateUsing(fn ($state) => $state?->label())->badge(),
                    TextEntry::make('home_address')->label('Address')->columnSpanFull()->placeholder('Not provided'),
                ]),

            Section::make('Church & Ministry')
                ->columns(3)
                ->schema([
                    TextEntry::make('church.name')->label('Church'),
                    TextEntry::make('church.district.name')->label('District'),
                    TextEntry::make('ministry')->label('Ministry')->placeholder('—'),
                    TextEntry::make('club_rank')->label('Club Rank')->placeholder('—'),
                    TextEntry::make('volunteer_role')->label('Volunteer Role')->placeholder('—'),
                ]),

            Section::make('Payment')
                ->columns(3)
                ->schema([
                    TextEntry::make('registrationCode.payment_type')
                        ->label('Payment Method')
                        ->formatStateUsing(fn ($state) => $state?->label())
                        ->badge(),
                    TextEntry::make('registrationCode.amount_paid')
                        ->label('Amount Paid')
                        ->money('NGN'),
                    TextEntry::make('registrationCode.activated_at')
                        ->label('Payment Confirmed')
                        ->dateTime('d M Y, H:i'),
                ]),

            Section::make('Check-In Status')
                ->columns(3)
                ->schema([
                    TextEntry::make('consent_collected')
                        ->label('Consent Form Collected')
                        ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                        ->badge()
                        ->color(fn ($state) => $state ? 'success' : 'danger'),
                    TextEntry::make('id_card_path')
                        ->label('ID Card')
                        ->formatStateUsing(fn ($state) => $state ? 'Generated' : 'Pending')
                        ->badge()
                        ->color(fn ($state) => $state ? 'success' : 'warning'),
                    TextEntry::make('consent_form_path')
                        ->label('Consent Form PDF')
                        ->formatStateUsing(fn ($state, $record) => $record->requiresConsentForm()
                            ? ($state ? 'Generated' : 'Pending')
                            : 'N/A')
                        ->badge()
                        ->color(fn ($state, $record) => ! $record->requiresConsentForm()
                            ? 'gray'
                            : ($state ? 'success' : 'warning')),
                ]),
        ]);
    }
}

class EditCamper extends EditRecord
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}

class CreateCamper extends CreateRecord
{
    protected static string $resource = CamperResource::class;
}
