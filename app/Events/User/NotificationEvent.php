<?php

namespace App\Events\User;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public string $title;
    public string  $message_type;

    public function __construct(string $title = '', string $message = '', string $type = 'success')
    {
        $this->message = $message;
        $this->title = $title;
        $this->message_type = $type;
    }

    public function broadcastOn(): array
    {
        return [new Channel('notifications-users-channel')];
    }

    public function broadcastAs(): string
    {
        return 'notification-event';
    }
}
