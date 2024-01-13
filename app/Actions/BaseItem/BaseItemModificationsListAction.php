<?php

namespace App\Actions\BaseItem;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Models\NomenclatureBaseItemPdrPosition;
use Cache;
use DB;

class BaseItemModificationsListAction
{
    public function handle(NomenclatureBaseItemPdrPosition $nomenclatureBaseItem, CatalogApiHelper $apiHelper): array
    {
        $key = 'modifications-' . $nomenclatureBaseItem->id;
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $make = $nomenclatureBaseItem->nomenclatureBaseItemPdr->nomenclatureBaseItem->make;
            $model = $nomenclatureBaseItem->nomenclatureBaseItemPdr->nomenclatureBaseItem->model;
            $generation = $nomenclatureBaseItem->nomenclatureBaseItemPdr->nomenclatureBaseItem->generation;
            $data = $apiHelper->findMvrHeadersByMakeModelGeneration(
                $make,
                $model,
                $generation,
                false
            );
            Cache::put($key, $data, now()->addHours(3));
        }

        $modifications = $nomenclatureBaseItem->nomenclatureBaseItemModifications;

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
                'years_string' => $modification['years_string'],
                'checked' => false,
            ];
        }

        foreach($availableModifications as $el => $modification) {
            foreach($modifications as $setModification) {
                if (
                    $modification['header'] === $setModification->header &&
                    $modification['generation'] === $setModification->generation &&
                    $modification['modification'] === $setModification->modification &&
                    $modification['engine_name'] === $setModification->engine_name &&
                    $modification['engine_type'] === $setModification->engine_type &&
                    $modification['engine_size'] === $setModification->engine_size &&
                    (int) $modification['engine_power'] === (int) $setModification->engine_power &&
                    (int) $modification['doors'] === $setModification->doors &&
                    $modification['transmission'] === $setModification->transmission &&
                    $modification['drive_train'] === $setModification->drive_train &&
                    $modification['chassis'] === $setModification->chassis &&
                    $modification['body_type'] === $setModification->body_type &&
                    $modification['month_from'] === $setModification->month_from &&
                    $modification['month_to'] === $setModification->month_to &&
                    $modification['year_from'] === $setModification->year_from &&
                    $modification['year_to'] === $setModification->year_to)
                {
                    $availableModifications[$el]['checked'] = true;
                }
            }
        }

        return [
            'available_modifications' => $availableModifications,
            'current_modifications' => $modifications,
        ];
    }
}
