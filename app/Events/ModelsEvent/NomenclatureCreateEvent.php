<?php

namespace App\Events\ModelsEvent;

use App\Models\NomenclatureModification;
use Illuminate\Foundation\Events\Dispatchable;

class NomenclatureCreateEvent
{
    use Dispatchable;

    public NomenclatureModification $modification;

    public function __construct(NomenclatureModification $modification)
    {
        $this->modification = $modification;
    }
}
