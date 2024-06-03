<?php

namespace App\Events\StockCars;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RemovedFromWishListEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $message;
    public string $title;
    private string $user_id;
    public string $message_type;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->message = 'Car removed from wish list';
        $this->title = 'Wish List';
        $this->message_type = 'warn';
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel.' . $this->user->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user-removed-from-wish-list';
    }

}
