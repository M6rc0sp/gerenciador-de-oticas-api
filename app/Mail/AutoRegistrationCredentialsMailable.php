<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AutoRegistrationCredentialsMailable extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Bem-vindo! Suas credenciais de acesso',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.auto-registration-credentials',
            with: [
                'user' => $this->user,
                'email' => $this->user->email,
                'password' => $this->plainPassword,
            ],
        );
    }
}
