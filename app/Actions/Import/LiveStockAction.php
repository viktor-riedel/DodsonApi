<?php

namespace App\Actions\Import;

use App\Http\ExternalApiHelpers\GetRetailStockHelper;
use App\Jobs\Import\ImportRetailStockPartsJob;

class LiveStockAction
{
    public function handle(): void
    {
        $helper = new GetRetailStockHelper();
        $data = $helper->getStock();
        if (isset($data['ResultData']) && is_array($data['ResultData']) && count($data['ResultData']) > 0) {
            $parts = [];
            foreach ($data['ResultData'] as $stockPart) {
                if (count($parts) < 100) {
                    $parts[] = $stockPart;
                } else {
                    ImportRetailStockPartsJob::dispatch($parts);
                    $parts = [];
                }
            }
        }
    }
}
