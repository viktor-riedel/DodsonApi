<?php

namespace App\Console\Commands;

use App\Models\CarPdrPositionCard;
use Illuminate\Console\Command;

class UpdateCarCardsCommentCommand extends Command
{
    protected $signature = 'update:car-cards-comment';

    protected $description = 'This command creates entry for all comments';

    public function handle(): void
    {
        $cards = CarPdrPositionCard::all();
        foreach($cards as $card) {
            $card->comments()->create([
               'comment' => $card->comment,
               'user_id' => $card->created_by,
               'created_at' => $card->updated_at,
            ]);
        }
        $this->info('done');
    }
}
