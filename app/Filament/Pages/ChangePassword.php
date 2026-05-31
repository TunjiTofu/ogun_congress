<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class ChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string  $view             = 'filament.pages.change-password';
    protected static ?string $slug             = 'change-password';
    protected static ?string $navigationLabel  = 'Change Password';
    protected static ?string $navigationIcon   = 'heroicon-o-key';
    protected static bool    $shouldRegisterNavigation = false;

    // Use $data array — required for ->same() validation to work correctly
    public ?array $data = [];

    public function getTitle(): string { return 'Change My Password'; }

    public static function canAccess(): bool { return auth()->check(); }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current Password')
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('password')
                    ->label('New Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->rule(PasswordRule::min(8)->mixedCase()->numbers()->symbols())
                    ->helperText('Min 8 chars · uppercase · lowercase · number · special character.'),

                TextInput::make('password_confirmation')
                    ->label('Confirm New Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->same('password')
                    ->helperText('Must match the new password exactly.'),
            ])
            ->statePath('data');  // bind to $this->data — makes ->same() work
    }

    public function save(): void
    {
        // Validate form — catches mismatched passwords before anything else
        $validated = $this->form->getState();

        $user = auth()->user();

        // Verify current password
        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => 'Your current password is incorrect.',
            ]);
        }

        // Disallow using same password
        if (Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.password' => 'New password must be different from your current password.',
            ]);
        }

        $user->update([
            'password'             => Hash::make($validated['password']),
            'must_change_password' => false,
            'temp_password'        => null,
        ]);

        // Log the user out and redirect to login — fresh start with new password
        auth()->logout();
        session()->flash('password_changed', true);
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect(url('/admin/login'));
    }
}
