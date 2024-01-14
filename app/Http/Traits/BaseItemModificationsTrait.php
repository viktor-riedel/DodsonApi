<?php

namespace App\Http\Traits;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Models\NomenclatureBaseItem;
use App\Models\NomenclatureBaseItemPdrPosition;
use Cache;

trait BaseItemModificationsTrait
{
    private function loadAvailableModificationByBaseItem(NomenclatureBaseItem $nomenclatureBaseItem, CatalogApiHelper $apiHelper): array
    {
        $key = 'modifications-' . $nomenclatureBaseItem->id;
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $make = $nomenclatureBaseItem->make;
            $model = $nomenclatureBaseItem->model;
            $generation = $nomenclatureBaseItem->generation;
            $data = $apiHelper->findMvrHeadersByMakeModelGeneration(
                $make,
                $model,
                $generation,
                false
            );
            Cache::put($key, $data, now()->addHours(3));
        }

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

        return $availableModifications;
    }

    private function loadAvailableModificationByBaseItemPosition(NomenclatureBaseItemPdrPosition $baseItemPosition, CatalogApiHelper $apiHelper): array
    {
        $key = 'modifications-' . $baseItemPosition->id;
        if (Cache::has($key)) {
            $data = Cache::get($key);
        } else {
            $make = $baseItemPosition->nomenclatureBaseItemPdr->nomenclatureBaseItem->make;
            $model = $baseItemPosition->nomenclatureBaseItemPdr->nomenclatureBaseItem->model;
            $generation = $baseItemPosition->nomenclatureBaseItemPdr->nomenclatureBaseItem->generation;
            $data = $apiHelper->findMvrHeadersByMakeModelGeneration(
                $make,
                $model,
                $generation,
                false
            );
            Cache::put($key, $data, now()->addHours(3));
        }

        $modifications = $baseItemPosition->nomenclatureBaseItemModifications;

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

        return $availableModifications;
    }
}
