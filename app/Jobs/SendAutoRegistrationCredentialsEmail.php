<?php

namespace App\Jobs;

use App\Mail\AutoRegistrationCredentialsMailable;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAutoRegistrationCredentialsEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $plainPassword,
    ) {}

    public function handle(): void
    {
        $email = new AutoRegistrationCredentialsMailable($this->user, $this->plainPassword);
        Mail::to($this->user->email)->send($email);
    }
}
