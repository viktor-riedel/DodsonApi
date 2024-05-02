<?php

namespace App\Console\Commands;

use App\Models\Car;
use App\Models\CarPdrPositionCard;
use Illuminate\Console\Command;

class UpdateCarCardsCommentCommand extends Command
{
    protected $signature = 'update:car-cards-comment';

    protected $description = 'This command creates entry for all comments';

    public function handle(): void
    {
        $cards = CarPdrPositionCard::with('comments')->get();
        foreach($cards as $card) {
            if (!$card->comments) {
                $card->comments()->create([
                    'comment' => $card->comment,
                    'user_id' => $card->created_by,
                    'created_at' => $card->updated_at,
                ]);
            }
        }
        $cars = Car::with('carFinance')->get();
        foreach($cars as $car) {
            if (!$car->carFinance) {
                $car->carFinance()->create([]);
            }
        }
        $this->info('done');
    }
}
