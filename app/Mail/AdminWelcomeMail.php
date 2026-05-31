<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User   $user,
        public string $plainPassword,
        public string $roleName,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Ogun Conference Youth Congress — Your Admin Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin-welcome',
            with: [
                'user'          => $this->user,
                'plainPassword' => $this->plainPassword,
                'roleName'      => $this->roleName,
                'adminUrl'      => url('/admin'),
                'landingUrl'    => url('/'),
            ],
        );
    }
}
