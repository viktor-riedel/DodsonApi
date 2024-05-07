<?php

namespace App\Events\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LoginSuccessEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $title;
    private $user_id;
    public $message_type;

    public function __construct(int $user_id, string $title = '', string $message = '', string $type = 'success')
    {
        $this->message = $message;
        $this->title = $title;
        $this->user_id = $user_id;
        $this->message_type = $type;

    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel.' . $this->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'user-login-event';
    }

}
