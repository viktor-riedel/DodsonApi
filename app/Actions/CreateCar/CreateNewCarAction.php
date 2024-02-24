<?php

namespace App\Actions\CreateCar;

use App\Http\Traits\BaseItemPdrTreeTrait;
use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class CreateNewCarAction
{
    use BaseItemPdrTreeTrait;

    public function handle(Request $request): int
    {
        $baseCar = NomenclatureBaseItem::where('make', strtoupper(trim($request->input('make'))))
            ->where('model', strtoupper(trim($request->input('model'))))
            ->where('generation', trim($request->input('generation')))
            ->first();

//        $modifications = $this->getModifications($baseCar->id, $request);
//        ray($modifications->pluck('nomenclature_base_item_pdr_id')->toArray());
//        return -1;

        $car = Car::create([
            'parent_inner_id' => $baseCar->inner_id,
            'make' => strtoupper(trim($request->input('make'))),
            'model' => strtoupper(trim($request->input('model'))),
            'generation' => trim($request->input('generation')),
            'created_by' => $request->user()->id,
        ]);

        $car->carAttributes()->create([]);
        $car->modification()->create([
            'body_type' => $request->input('modification.body_type'),
            'chassis' => $request->input('modification.chassis'),
            'generation' => $request->input('modification.generation'),
            'engine_size' => $request->input('modification.engine_size'),
            'drive_train' => $request->input('modification.drive_train'),
            'header' => $request->input('modification.header'),
            'month_from' => (int) $request->input('modification.month_from'),
            'month_to' => (int) $request->input('modification.month_to'),
            'restyle' => (bool) $request->input('modification.restyle'),
            'doors' => (int) $request->input('modification.doors'),
            'transmission' => $request->input('modification.transmission'),
            'year_from' => (int) $request->input('modification.year_from'),
            'year_to' => (int) $request->input('modification.year_to'),
            'years_string' => $request->input('modification.years_string'),
        ]);

        if (is_array($request->photos)) {
            foreach($request->photos as $photo) {
                $car->images()->create([
                    'url' => $photo['uploaded_url'] ?? '',
                    'mime' => $photo['mime'] ?? '',
                    'original_file_name' => $photo['original_file_name'] ?? '',
                    'folder_name' => $photo['folder_name'] ?? '',
                    'extension' => $photo['ext'] ?? '',
                    'file_size' => $photo['size'] ?? '',
                    'special_flag' => null,
                    'created_by' => $request->user()->id
                ]);
            }
        }

        return $car->id;
    }

    private function getModifications(int $baseItemId, Request $request): Collection
    {
        return \DB::table('nomenclature_base_item_modifications')
            ->select('*')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.id', '=', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.id', '=', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id')
            ->join('nomenclature_base_items', 'nomenclature_base_items.id', '=', 'nomenclature_base_item_pdrs.nomenclature_base_item_id')
            ->where('nomenclature_base_items.id', $baseItemId)
            ->whereNull('nomenclature_base_item_modifications.deleted_at')
            ->where('nomenclature_base_item_modifications.body_type', $request->input('modification.body_type'))
            ->where('nomenclature_base_item_modifications.chassis', $request->input('modification.chassis'))
            ->where('nomenclature_base_item_modifications.generation', $request->input('modification.generation'))
            ->where('nomenclature_base_item_modifications.engine_size', $request->input('modification.engine_size'))
            ->where('nomenclature_base_item_modifications.drive_train', $request->input('modification.drive_train'))
            ->where('nomenclature_base_item_modifications.header', $request->input('modification.header'))
            ->where('nomenclature_base_item_modifications.month_from', $request->input('modification.month_from'))
            ->where('nomenclature_base_item_modifications.month_to', $request->input('modification.month_to'))
            ->where('nomenclature_base_item_modifications.restyle', $request->input('modification.restyle'))
            ->where('nomenclature_base_item_modifications.doors', $request->input('modification.doors'))
            ->where('nomenclature_base_item_modifications.transmission', $request->input('modification.transmission'))
            ->where('nomenclature_base_item_modifications.year_from', $request->input('modification.year_from'))
            ->where('nomenclature_base_item_modifications.year_to', $request->input('modification.year_to'))
            ->when($request->input('modification.restyle')  && in_array((int) $request->input('modification.year_from'), [0, 1], true), function($q) use($request) {
                $q->where('nomenclature_base_item_modifications.restyle', $request->input('modification.restyle'));
            })
            ->when( $request->input('modification.year_from', 'no') === 'no', function($q) {
                $q->whereNull('nomenclature_base_item_modifications.restyle');
            })
            ->get();
    }
}
