<?php

namespace App\Console\Commands;

use App\Models\CurrencyRate;
use Http;
use Illuminate\Console\Command;

class UpdateCurrenciesRatesCommand extends Command
{
    protected $signature = 'currency:update-currencies-rates';

    protected $description = 'This command updates currencies rates';

    private const CURRENCY_API = 'https://data.fixer.io/api';

    public function handle(): void
    {
        $fromYenToRub = $this->getConvertedApiUrl('JPY', 'RUB');
        $fromYenToNzd = $this->getConvertedApiUrl('JPY', 'NZD');
        $fromYenToEur = $this->getConvertedApiUrl('JPY', 'EUR');
        $fromYenToUsd = $this->getConvertedApiUrl('JPY', 'USD');
        $fromYenToMnt = $this->getConvertedApiUrl('JPY', 'MNT');

        $fromRubToYen = $this->getConvertedApiUrl('RUB', 'JPY');
        $fromNzdToYen = $this->getConvertedApiUrl('NZD', 'JPY');
        $fromUerToYen = $this->getConvertedApiUrl('EUR', 'JPY');
        $fromUsdToYen = $this->getConvertedApiUrl('USD', 'JPY');
        $fromMntToYen = $this->getConvertedApiUrl('MNT', 'JPY');

        if ($fromYenToRub && $fromYenToNzd) {
            CurrencyRate::all()->each(fn($rate) => $rate->delete());

            CurrencyRate::create([
                'currency_code' => CurrencyRate::CURRENCIES['RUB'],
                'rate_from_jpy' => $this->getRate($fromYenToRub),
                'rate_to_jpy' => $this->getRate($fromRubToYen),
            ]);
            CurrencyRate::create([
                'currency_code' => CurrencyRate::CURRENCIES['NZD'],
                'rate_from_jpy' => $this->getRate($fromYenToNzd),
                'rate_to_jpy' => $this->getRate($fromNzdToYen),
            ]);
            CurrencyRate::create([
                'currency_code' => CurrencyRate::CURRENCIES['EUR'],
                'rate_from_jpy' => $this->getRate($fromYenToEur),
                'rate_to_jpy' => $this->getRate($fromUerToYen),
            ]);
            CurrencyRate::create([
                'currency_code' => CurrencyRate::CURRENCIES['USD'],
                'rate_from_jpy' => $this->getRate($fromYenToUsd),
                'rate_to_jpy' => $this->getRate($fromUsdToYen),
            ]);
            CurrencyRate::create([
                'currency_code' => CurrencyRate::CURRENCIES['MNT'],
                'rate_from_jpy' => $this->getRate($fromYenToMnt),
                'rate_to_jpy' => $this->getRate($fromMntToYen),
            ]);

            $this->info('rates updated');
        } else {
            \Log::error('CANNOT UOPDATE CURRENCIES RATE');
        }
    }

    private function getConvertedApiUrl(string $from, string $to): string
    {
        return self::CURRENCY_API . '/convert?access_key=' . config('currency.api_key')
            . '&from=' . $from
            . '&to=' . $to . '&amount=1';
    }

    private function getRate(string $url): float
    {
        $response = Http::get($url)->json();
        if ($response) {
            return $response['result'];
        }
        return 0;
    }
}
