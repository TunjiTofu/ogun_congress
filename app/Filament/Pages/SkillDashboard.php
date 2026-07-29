<?php

namespace App\Filament\Pages;

use App\Models\CampSetting;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SkillDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $navigationGroup = 'Skill Acquisition';
    protected static ?int    $navigationSort  = 0;
    protected static string  $view            = 'filament.pages.skill-dashboard';
    protected static ?string $title           = 'Skill Acquisition Dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'skill_manager']);
    }

    protected function getHeaderActions(): array
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            return [];
        }

        $isOpen = setting('skill_registration_open', '0') === '1';

        return [
            Action::make('toggle_registration')
                ->label($isOpen ? 'Close Registration' : 'Open Registration')
                ->icon($isOpen ? 'heroicon-o-lock-closed' : 'heroicon-o-lock-open')
                ->color($isOpen ? 'danger' : 'success')
                ->requiresConfirmation()
                ->modalHeading($isOpen ? 'Close Skill Registration?' : 'Open Skill Registration?')
                ->modalDescription($isOpen
                    ? 'Campers will no longer be able to register or change their skill selection.'
                    : 'Campers will be able to register and change their skill selection.')
                ->action(function () {
                    // Read fresh from DB inside the action to avoid stale captured value
                    $currentlyOpen = setting('skill_registration_open', '0') === '1';
                    $newValue      = $currentlyOpen ? '0' : '1';

                    CampSetting::updateOrCreate(
                        ['key'   => 'skill_registration_open'],
                        ['label' => 'Skill Registration Open', 'value' => $newValue]
                    );

                    Notification::make()
                        ->title('Skill registration is now ' . ($newValue === '1' ? 'OPEN' : 'CLOSED'))
                        ->color($newValue === '1' ? 'success' : 'warning')
                        ->send();

                    $this->redirect(static::getUrl());
                }),
        ];
    }
}
