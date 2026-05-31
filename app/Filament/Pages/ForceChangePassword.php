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

class ForceChangePassword extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view            = 'filament.pages.force-change-password';
    protected static ?string $slug           = 'change-password-required';
    protected static bool $shouldRegisterNavigation = false;

    // Use $data array — required for ->same() validation to work correctly
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->must_change_password;
    }

    public function getTitle(): string
    {
        return 'Password Change Required';
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Current (Temporary) Password')
                    ->password()
                    ->revealable()
                    ->required()
                    ->helperText('Enter the temporary password sent to you.'),

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
            ->statePath('data');  // bind to $this->data array — makes ->same() work
    }

    public function save(): void
    {
        // Validate form first — this catches mismatched passwords
        $validated = $this->form->getState();

        $user = auth()->user();

        // Verify current password
        if (! Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'data.current_password' => 'The current password is incorrect.',
            ]);
        }

        // Disallow reuse of the temporary password
        if ($validated['password'] === $user->temp_password) {
            throw ValidationException::withMessages([
                'data.password' => 'You cannot reuse your temporary password. Please choose a new one.',
            ]);
        }

        // Update password and clear the forced-change flag
        $user->update([
            'password'             => Hash::make($validated['password']),
            'must_change_password' => false,
            'temp_password'        => null,
        ]);

        // Log the user out — force a clean login with new password
        auth()->logout();
        session()->flash('password_changed', true);
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        // Redirect to login with success message
        $this->redirect(url('/admin/login'));
    }
}
