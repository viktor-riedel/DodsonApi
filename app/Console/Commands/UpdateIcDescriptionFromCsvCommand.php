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

        $log = fopen(sprintf(storage_path('files') . '/%s_%s_%s.log', $make, $model, $generation), 'ab');
        $count = 1;
        try {
            $baseItem = NomenclatureBaseItem::with('nomenclaturePositions')
                ->where([
                    'make' => strtoupper($make),
                    'model' => strtoupper($model),
                    'generation' => (int) $generation
                ])->first();

            if (!$baseItem) {
                $this->error('Base car not found');
                return;
            }

            $records = file(storage_path('files/' . $fileName));
            foreach($records as $record) {
                $data = str_getcsv($record);
                $ic_number = $data[1] ?? '';
                $description = $data[2] ?? '';

                $positions = $baseItem->nomenclaturePositions()->where('ic_number', $ic_number)->get();
                if ($positions->count() > 1) {
                    fwrite($log, $ic_number . ' ' . $description . ' has more than 1 position' . PHP_EOL);
                } else if ($positions->count() === 1) {
                    $positions->first()->nomenclatureBaseItemPdrCard->update([
                        'description' => $description,
                    ]);
                    $positions->first()->update([
                       'ic_description' => $description,
                    ]);
                    $count++;
                }
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage() . ' ' . $e->getLine());
        } finally {
            fclose($log);
        }

        $this->info('updated ' . $count . ' cards and positions');
    }
}
