<?php

namespace App\Events\Cart;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ItemRemovedFromCartEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $userId;
    public string $message;
    public string $title;
    private string $user_id;
    public string $message_type;


    public function __construct(User $user, CartItem $item)
    {
        $this->userId = $user->id;
        $this->title = 'Cart';
        if ($item->car_id) {
            $this->message = $item->car->make . ' ' . $item->car->model . ' removed from cart';
        } else {
            $this->message = 'Part removed To Cart';
        }
        $this->message_type = 'warn';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel.' . $this->userId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'item-removed-from-cart';
    }
}
