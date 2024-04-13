<?php

namespace App\Console\Commands;

use App\Models\NomenclatureBaseItem;
use Illuminate\Console\Command;

class UpdateIcDescriptionFromCsvCommand extends Command
{
    protected $signature = 'update:ic-description-from-csv {make} {model} {generation} {filename}';

    protected $description = 'This command updates ic descriptions from CSV file provided';

    public function handle(): void
    {
        $make = $this->argument('make');
        $model = $this->argument('model');
        $generation = $this->argument('generation');
        $fileName = $this->argument('filename');

        $baseItem = NomenclatureBaseItem::with('nomenclaturePositions')
        ->where([
            'make' => strtoupper($make),
            'model' => strtoupper($model),
            'generation' => (int) $generation
        ])->first();

        $records = file(storage_path('files/' . $fileName));
        foreach($records as $record) {
            $data = str_getcsv($record);
            $ic_number = $data[1] ?? '';
            $description = $data[2] ?? '';

            $positions = $baseItem->nomenclaturePositions()->where('ic_number', $ic_number)->get();
            if ($positions->count() > 1) {
                dump($positions);
            }
        }

        $this->info('done');
    }
}
