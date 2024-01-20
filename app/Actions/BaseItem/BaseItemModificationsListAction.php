<?php

namespace App\Actions\BaseItem;

use App\Http\ExternalApiHelpers\CatalogApiHelper;
use App\Http\Traits\BaseItemModificationsTrait;
use App\Models\NomenclatureBaseItemPdrPosition;
use Cache;

class BaseItemModificationsListAction
{
    use BaseItemModificationsTrait;

    public function handle(NomenclatureBaseItemPdrPosition $nomenclatureBaseItem, CatalogApiHelper $apiHelper): array
    {
        $modifications = $nomenclatureBaseItem->nomenclatureBaseItemModifications;

        $availableModifications = $this->loadAvailableModificationByBaseItemPosition($nomenclatureBaseItem, $apiHelper);

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
                    (string) $modification['chassis'] === (string) $setModification->chassis &&
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
