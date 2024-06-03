<?php

namespace App\Events\Order;

use App\Models\Order;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Order $order;
    public string $message;
    public string $title;
    public string $message_type;


    public function __construct(User $user, Order $order)
    {
        $this->user = $user;
        $this->order = $order;
        $this->message = 'Order has been placed';
        $this->title = 'Order';
        $this->message_type = 'success';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel.' . $this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user-order-created';
    }
}
