<?php

namespace App\Actions\Import;

use App\Jobs\Import\ImportCsvFromPinnacleJob;
use App\Models\ImportLog;
use Bus;
use Illuminate\Http\Request;

class ImportFromPinnacleCsvAction
{
    public function handle(Request $request): void
    {
        $file = $request->file('uploadPartsPinnacle');
        ImportLog::create([
            'system' => 'Pinnacle CSV',
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'file_mime' => 'CSV',
            'extension' => '.' . $file->getClientOriginalExtension(),
            'user_id' => $request->user()->id,
        ]);

        $content = file($file->getPathname());
        $jobs = [];
        $jobs_count = 1;
        foreach($content as $i => $line) {
            $data = str_getcsv($line);
            if ($i > 1) {
                $jobs[] = new ImportCsvFromPinnacleJob($data);
                $jobs_count++;
            }
            if ($jobs_count === 50) {
                $jobs_count = 1;
                Bus::chain($jobs)->dispatch();
                $jobs = [];
            }
        }
    }
}
