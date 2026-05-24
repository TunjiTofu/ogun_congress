<?php

namespace App\Filament\Pages;

use App\Models\CampSetting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class RegistrationControlPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-lock-closed';
    protected static ?string $navigationLabel = 'Registration Control';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 15;
    protected static string  $view            = 'filament.pages.registration-control';

    public bool   $registration_open      = true;
    public string $registration_closes_at = '';
    public bool   $camp_over              = false;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public function mount(): void
    {
        $this->registration_open      = setting('registration_open', '1') === '1';
        $this->registration_closes_at = setting('registration_closes_at', '');
        $this->camp_over              = setting('camp_over', '0') === '1';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Registration Gate')
                ->description('Control whether campers can submit forms and make payments.')
                ->schema([
                    Forms\Components\Toggle::make('registration_open')
                        ->label('Registration is Open')
                        ->helperText('Turn OFF to stop all new registrations and payments immediately.')
                        ->onIcon('heroicon-o-lock-open')
                        ->offIcon('heroicon-o-lock-closed')
                        ->onColor('success')
                        ->offColor('danger'),

                    Forms\Components\DateTimePicker::make('registration_closes_at')
                        ->label('Auto-Close Registration On')
                        ->helperText('Registration closes automatically at this date/time. Leave blank for no auto-close.')
                        ->native(false)
                        ->seconds(false)
                        ->displayFormat('d M Y, g:i A')
                        ->nullable()
                        ->minDate(now()),
                ]),

            Forms\Components\Section::make('⛺ Camp Over Mode')
                ->description('When enabled, ALL admin logins except super_admin are disabled. All currently-logged-in admins are logged out on their next request.')
                ->schema([
                    Forms\Components\Toggle::make('camp_over')
                        ->label('Camp Is Over — Disable All Admin Access')
                        ->helperText('⚠️ This will immediately lock out all staff. Only super_admin can toggle this back.')
                        ->onIcon('heroicon-o-x-circle')
                        ->offIcon('heroicon-o-check-circle')
                        ->onColor('danger')
                        ->offColor('success'),
                ]),
        ])->statePath(null);
    }

    public function save(): void
    {
        $this->validate(['registration_closes_at' => 'nullable|date']);

        $settings = [
            'registration_open'      => $this->registration_open ? '1' : '0',
            'registration_closes_at' => $this->registration_closes_at ?? '',
            'camp_over'              => $this->camp_over ? '1' : '0',
        ];

        foreach ($settings as $key => $value) {
            CampSetting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => 'registration']);
        }

        \Illuminate\Support\Facades\Cache::forget('camp_settings');

        $msg = $this->camp_over
            ? '⛺ Camp Over mode activated. All non-super_admin logins are now disabled.'
            : ($this->registration_open ? 'Registration is now OPEN.' : 'Registration is now CLOSED.');

        Notification::make()->title($msg)->success()->send();
    }
}
