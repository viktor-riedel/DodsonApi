<?php

namespace App\Actions\Api;

use App\Models\Car;
use App\Models\NomenclatureBaseItem;
use App\Models\StatusUpdateLog;

class GetDoneCarDataAction
{
    public function handle(Car $car): array
    {
        $car->load('images',
            'carAttributes',
            'carFinance',
            'modifications',
            'positions',
            'positions.card',
            'positions.card.comments',
            'positions.card.comments.createdBy',
            'latestSyncData',
            'latestSyncData.createdBy',
            'positions.card.priceCard');

        $baseCar = NomenclatureBaseItem::
            with('modifications')
            ->where('inner_id', $car->parent_inner_id)
            ->first();

        $dismantledDate = StatusUpdateLog::with('user')
            ->where('car_id', $car->id)
            ->where('new_status', 4)
            ->latest()
            ->first();

        $usedModification = $baseCar->modifications->where('inner_id', $car->modifications->inner_id)->first();

        $result = [
            'base_car' => [
                'inner_id' => $baseCar->inner_id,
                'make' => $baseCar->make,
                'model' => $baseCar->model,
                'base_modification' => [
                    'inner_id' => $usedModification?->inner_id,
                    'header' => $usedModification?->header,
                    'generation' => $usedModification?->generation,
                    'engine_name' => $usedModification?->engine_name,
                    'engine_type' => $usedModification?->engine_type,
                    'engine_size' => $usedModification?->engine_size,
                    'engine_power' => $usedModification?->engine_power,
                    'doors' => $usedModification?->doors,
                    'transmission' => $usedModification?->transmission,
                    'drive_train' => $usedModification?->drive_train,
                    'chassis' => $usedModification?->chassis,
                    'body_type' => $usedModification?->body_type,
                    'restyle' => $usedModification?->restyle,
                    'month_from' => $usedModification?->month_from,
                    'month_to' => $usedModification?->month_to,
                    'year_from' => $usedModification?->year_from,
                    'year_to' => $usedModification?->year_to,
                ]
            ],
            'car' => [
                'make' => $car->make,
                'model' => $car->model,
                'color' => $car->carAttributes->color,
                'chassis' => $car->carAttributes->chassis,
                'engine' => $car->carAttributes->engine,
                'mileage' => $car->carAttributes->mileage,
                'year' => $car->carAttributes->year,
                'generation' => $car->generation,
                'mvr' => [
                    'mvr' => $car->car_mvr,
                ],
                'synced_data' => [
                  'number' => $car->latestSyncData?->document_number,
                  'date' => $car->latestSyncData?->document_date,
                  'synced_by' => [
                      'name' => $car->latestSyncData?->createdBy?->name,
                      'email' => $car->latestSyncData?->createdBy?->email,
                      'date' => $car->latestSyncData?->created_at?->format('d/m/Y H:i'),
                  ],
                ],
                'finance' => [
                    'contr_agent' => $car->contr_agent_name,
                    'purchase_price' => $car->carFinance->purchase_price,
                    'selling_price' => $car->positions->sum('card.priceCard.selling_price'),
                    'parts_price' => $car->positions->sum('card.priceCard.buying_price'),
                    'price_without_engine_nz' => $car->carFinance->price_without_engine_nz,
                    'price_with_engine_nz' => $car->carFinance->price_with_engine_nz,
                    'price_without_engine_ru' => $car->carFinance->price_without_engine_ru,
                    'price_with_engine_ru' => $car->carFinance->price_with_engine_ru,
                    'price_with_engine_mn' => $car->carFinance->price_with_engine_mn,
                    'price_without_engine_mn' => $car->carFinance->price_without_engine_mn,
                    'price_with_engine_jp' => $car->carFinance->price_with_engine_jp,
                    'price_without_engine_jp' => $car->carFinance->price_without_engine_jp,
                ],
                'images' => $car->images->pluck('url')->toArray(),
                'dismantled_date' => $dismantledDate?->created_at->format('d/m/Y'),
                'dismantled_set_by' => [
                    'name' => $dismantledDate?->user?->name,
                    'email' => $dismantledDate?->user?->email,
                ],
                'items' => [],
            ],
        ];

        foreach($car->positions as $position) {
            $result['car']['items'][] = [
                'inner_id' => $position->card?->parent_inner_id,
                'item_name_ru' => $position->card?->name_ru,
                'item_name_eng' => $position->card?->name_eng,
                'ic_number' => $position->ic_number,
                'ic_description' => $position->ic_description,
                'barcode' => [
                    'code' => (string) $position->card?->barcode,
                ],
                'client' => [
                   'name' => $position->client?->name,
                   'registered_name' => $position->client?->userCard?->trading_name,
                   'email' => $position->client?->email,
                ],
                'finance' => [
                    'selling_price' => $position->card->priceCard->buying_price,
                    'buying_price' => $position->card->priceCard->selling_price,
                    'price_currency' => $position->card->priceCard->price_currency,
                    'price_nz_wholesale' => $position->card->priceCard->price_nz_wholesale,
                    'price_nz_retail' => $position->card->priceCard->price_nz_retail,
                    'price_ru_wholesale' => $position->card->priceCard->price_ru_wholesale,
                    'price_ru_retail' => $position->card->priceCard->price_ru_retail,
                    'needs' => $position->card->priceCard->needs,
                    'mng_needs' => $position->card->priceCard->mng_needs,
                    'jp_needs' => $position->card->priceCard->jp_needs,
                    'ru_needs' => $position->card->priceCard->ru_needs,
                    'nz_needs' => $position->card->priceCard->nz_needs,
                    'nz_team_needs' => $position->card->priceCard->nz_team_needs,
                    'nz_team_price' => $position->card->priceCard->nz_team_price,
                    'price_jp_wholesale' => $position->card->priceCard->price_jp_wholesale,
                    'price_jp_retail' => $position->card->priceCard->price_jp_retail,
                    'price_mng_retail' => $position->card->priceCard->price_mng_retail,
                    'price_mng_wholesale' => $position->card->priceCard->price_mng_wholesale,
                    'price_jp_minimum_buy' => $position->card->priceCard->price_jp_minimum_buy,
                    'price_jp_maximum_buy' => $position->card->priceCard->price_jp_maximum_buy,
                    'minimum_threshold_nz_retail' => $position->card->priceCard->minimum_threshold_nz_retail,
                    'minimum_threshold_nz_wholesale' => $position->card->priceCard->minimum_threshold_nz_wholesale,
                    'minimum_threshold_ru_retail' => $position->card->priceCard->minimum_threshold_ru_retail,
                    'minimum_threshold_ru_wholesale' => $position->card->priceCard->minimum_threshold_ru_wholesale,
                    'delivery_price_nz' => $position->card->priceCard->delivery_price_nz,
                    'delivery_price_ru' => $position->card->priceCard->delivery_price_ru,
                    'pinnacle_price' => $position->card->priceCard->pinnacle_price,
                    'minimum_threshold_jp_retail' => $position->card->priceCard->minimum_threshold_jp_retail,
                    'minimum_threshold_jp_wholesale' => $position->card->priceCard->minimum_threshold_jp_wholesale,
                    'minimum_threshold_mng_retail' => $position->card->priceCard->minimum_threshold_mng_retail,
                    'minimum_threshold_mng_wholesale' => $position->card->priceCard->minimum_threshold_mng_wholesale,
                ],
                'comments' => $position->card->comments->transform(function ($comment) {
                    return [
                      'email' => $comment->createdBy->email,
                      'user' => $comment->createdBy->name,
                      'comment' => $comment->comment,
                      'created_at' => $comment->created_at->format('d/m/Y'),
                    ];
                })->toArray(),
            ];
        }

        return $result;
    }
}
