<?php

namespace App\Actions\BaseItem;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Models\NomenclatureBaseItem;
use DB;

class BaseItemModificationsListAction
{
    public function handle(NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): array
    {
        $data = $apiHelper->findMvrHeadersByMakeModelGeneration(
            $nomenclatureBaseItem->make,
            $nomenclatureBaseItem->model,
            $nomenclatureBaseItem->generation
        );

        $availableModifications = [];
        foreach ($data as $modification) {
            $availableModifications[] = [
                'id' => $modification['catalog_mvr_id'],
                'header' => $modification['mvr_header'],
                'generation' => $modification['catalog_generation']['generation'],
                'modification' => $modification['mvr_header'],
                'engine_name' => $modification['catalog_modification']['engine_name'],
                'engine_type' => $modification['catalog_modification']['engine_type'],
                'engine_size' => $modification['catalog_modification']['engine_size'],
                'engine_power' => $modification['catalog_modification']['engine_power'],
                'doors' => $modification['catalog_header']['doors'],
                'transmission' => $modification['catalog_modification']['transmission_type'],
                'drive_train' => $modification['catalog_modification']['drive_train'],
                'chassis' => implode('#', $modification['catalog_modification']['chassis']),
                'body_type' => $modification['catalog_header']['body_type'],
                'image_url' => $modification['model_image_url'],
                'restyle' => $modification['catalog_header']['restyle'],
                'not_restyle' => $modification['catalog_header']['not_restyle'],
                'month_from' => $modification['month_start'],
                'month_to' => $modification['month_stop'],
                'year_from' => $modification['year_start'],
                'year_to' => $modification['year_stop'],
            ];
        }

        $modifications = DB::table('nomenclature_base_items')
            ->selectRaw('distinct nomenclature_base_item_modifications.header,
                                    nomenclature_base_item_modifications.generation, 
                                    nomenclature_base_item_modifications.modification, 
                                    nomenclature_base_item_modifications.engine_name, 
                                    nomenclature_base_item_modifications.engine_type,
                                    nomenclature_base_item_modifications.engine_size, 
                                    nomenclature_base_item_modifications.engine_power, 
                                    nomenclature_base_item_modifications.doors, 
                                    nomenclature_base_item_modifications.transmission, 
                                    nomenclature_base_item_modifications.drive_train, 
                                    nomenclature_base_item_modifications.chassis,
                                    nomenclature_base_item_modifications.body_type, 
                                    nomenclature_base_item_modifications.image_url, 
                                    nomenclature_base_item_modifications.restyle, 
                                    nomenclature_base_item_modifications.not_restyle, 
                                    nomenclature_base_item_modifications.month_from, 
                                    nomenclature_base_item_modifications.month_to,
                                    nomenclature_base_item_modifications.year_from, 
                                    nomenclature_base_item_modifications.year_to')
            ->join('nomenclature_base_item_pdrs', 'nomenclature_base_item_pdrs.nomenclature_base_item_id', '=', 'nomenclature_base_items.id')
            ->join('nomenclature_base_item_pdr_positions', 'nomenclature_base_item_pdr_positions.nomenclature_base_item_pdr_id', '=', 'nomenclature_base_item_pdrs.id')
            ->join('nomenclature_base_item_modifications', 'nomenclature_base_item_modifications.nomenclature_base_item_pdr_position_id', '=', 'nomenclature_base_item_pdr_positions.id')
            ->where('nomenclature_base_items.id', $nomenclatureBaseItem->id)
            ->whereNull('nomenclature_base_item_modifications.deleted_at')
            ->get();

        $currentModifications = [];
        $removedIds = [];
        foreach($modifications as $modification) {
            for($i = 0; $i <= count($availableModifications) - 1; $i++) {
                $mod = $availableModifications[$i];
                if ($mod['header'] === $modification->header &&
                    $mod['generation'] === $modification->generation &&
                    $mod['modification'] === $modification->modification &&
                    $mod['engine_name'] === $modification->engine_name &&
                    $mod['engine_type'] === $modification->engine_type &&
                    $mod['engine_size'] === $modification->engine_size &&
                    $mod['engine_power'] === (int) $modification->engine_power &&
                    $mod['doors'] === $modification->doors &&
                    $mod['transmission'] === $modification->transmission &&
                    $mod['drive_train'] === $modification->drive_train &&
                    $mod['body_type'] === $modification->body_type &&
                    $mod['restyle'] === $modification->restyle &&
                    $mod['not_restyle'] === $modification->not_restyle &&
                    $mod['month_from'] === $modification->month_from &&
                    $mod['month_to'] === $modification->month_to &&
                    $mod['year_from'] === $modification->year_from &&
                    $mod['year_to'] === $modification->year_to) {
                    $modification->id = \Str::random(10);
                    $currentModifications[] = $modification;
                    $removedIds[] = $mod['id'];
                }
            }
        }

        $filtered = collect($availableModifications)->filter(function($item) use ($removedIds) {
           return !in_array($item['id'], $removedIds, true);
        });

        return [
            'available_modifications' => $filtered->values(),
            'current_modifications' => $currentModifications,
            'other_modifications' => [],
        ];
    }
}
