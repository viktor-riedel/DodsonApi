<?php

namespace App\Jobs\Notifications;

use App\Mail\UserRegisteredNotificationMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UserRegisteredJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $emails = explode(',', config('mail.info_email'));
        if (count($emails)) {
            foreach ($emails as $email) {
                \Mail::to($email)->send(new UserRegisteredNotificationMail($this->user));
            }
        }

    }
}
