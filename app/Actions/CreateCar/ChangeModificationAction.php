<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\User;
use Illuminate\Http\Request;

class ChangeModificationAction
{
    use InnerIdTrait;

    private Request $request;
    private Car $car;
    private User $user;

    public function handle(Request $request, Car $car, User $user): bool
    {
        $this->request = $request;
        $this->car = $car;
        $this->user = $user;

        $baseCar = NomenclatureBaseItem::where('make', strtoupper(trim($request->input('make'))))
            ->where('model', strtoupper(trim($request->input('model'))))
            ->where('generation', $request->input('catalog_generation.generation_number'))
            ->first();

        if (!$baseCar) {
            abort(422, 'Base car not found');
        }

        $innerId = $this->generateInnerId(
            $request['mvr_header'] .
            $request['generation'] .
            implode('#', $request['catalog_modification.chassis']));

        $modification = $baseCar->modifications()->where('inner_id', $innerId)->first();

        if (!$modification) {
            abort(422, 'Modification not found');
        }

        $car->update([
            'parent_inner_id' => $baseCar->inner_id,
            'generation' => $request->input('catalog_generation.generation_number'),
        ]);

        $car->carAttributes()->create([
            'engine' => $modification->engine_name,
        ]);

        $car->modification()?->delete();

        $car->modification()->create([
            'body_type' => $modification->body_type,
            'chassis' => $modification->chassis,
            'generation' => $modification->generation,
            'engine_size' => $modification->engine_size,
            'drive_train' => $modification->drive_train,
            'header' => $modification->header,
            'month_from' => $modification->month_from,
            'month_to' => $modification->month_to,
            'restyle' => $modification->restyle,
            'doors' => $modification->doors,
            'transmission' => $modification->transmission,
            'year_from' => $modification->year_from,
            'year_to' => $modification->year_to,
            'years_string' => $modification->years_string,
        ]);

        $car->modifications()->delete();
        $car->modifications()->create($modification->toArray());

        foreach ($car->pdrs as $pdr) {
            foreach ($pdr->positions as $position) {
                $position->update(['ic_number' => null, 'ic_description' => null]);
                $position->card()->update(['ic_number' => null, 'description' => null]);
                $position->card->modification()->delete();
                $position->modification()->delete();
            }
        }

        return true;
    }
}
