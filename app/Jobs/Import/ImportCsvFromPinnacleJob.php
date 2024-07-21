<?php

namespace App\Jobs\Import;

use App\Actions\CsvParsers\ParsePinnacleCsvLineAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportCsvFromPinnacleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $csv_line;

    public function __construct(array $csv_line)
    {
        $this->csv_line = $csv_line;
    }

    public function handle(): void
    {
        app()->make(ParsePinnacleCsvLineAction::class)->handle($this->csv_line);
    }
}
