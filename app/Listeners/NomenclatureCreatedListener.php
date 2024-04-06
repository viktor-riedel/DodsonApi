<?php

namespace App\Listeners;

use App\Events\ModelsEvent\NomenclatureCreateEvent;

class NomenclatureCreatedListener
{
    public function handle(NomenclatureCreateEvent $event): void
    {
        $modification = $event->modification;
        $year_from_str = str_pad($modification->month_from,2,0,STR_PAD_LEFT) . '.'.
            $modification->year_from;
        if ($modification->month_to && $modification->year_to) {
        $year_end_str = str_pad($modification->month_to,2,0,STR_PAD_LEFT) . '.'.
            $modification->year_to;
        } else {
            $year_end_str = 'now';
        }
        $modification->update([
            'years_string' => $year_from_str . '-' . $year_end_str,
        ]);
    }
}
