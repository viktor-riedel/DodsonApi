<?php

namespace App\Events\User;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $title;
    public $message_type;

    public function __construct(string $title = '', string $message = '', string $type = 'success')
    {
        $this->message = $message;
        $this->title = $title;
        $this->message_type = $type;
    }

    public function broadcastOn(): array
    {
        return ['notification-channel'];
    }

    public function broadcastAs()
    {
        return 'notification-event';
    }
}
