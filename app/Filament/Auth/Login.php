<?php

// Place this at: app/Filament/Auth/Login.php
// NOT in app/Filament/Pages/ — that path is auto-discovered

namespace App\Filament\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public function authenticate(): ?LoginResponse
    {
        if (setting('camp_over', '0') === '1') {
            // Get the submitted email before credentials are checked
            $data  = $this->form->getState();
            $email = $data['email'] ?? '';
            $user  = \App\Models\User::where('email', $email)->first();

            if (! $user || ! $user->hasRole('super_admin')) {
                throw ValidationException::withMessages([
                    'data.email' => 'Camp has ended. Administrative access is currently disabled.',
                ]);
            }
        }

        return parent::authenticate();
    }
}
