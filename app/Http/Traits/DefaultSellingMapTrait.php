<?php

namespace App\Http\Traits;

use App\Models\Car;
use App\Models\OrderItem;
use App\Models\PartList;
use App\Models\SellingMapItem;
use Illuminate\Support\Collection;

trait DefaultSellingMapTrait
{

    use CarPdrTrait;

    private function createMainFolders(array $folders = []): void
    {
        if (count($folders)) {
            $directories = SellingMapItem::where('parent_id', 0)->get();
            foreach ($folders as $folder) {
                $exist = $directories->where('item_name_eng', $folder)->first();
                if (!$exist) {
                    SellingMapItem::create([
                        'item_name_eng' => $folder,
                    ]);
                }
            }
        }
    }

    private function getPartsNamesByIds(array $ids): array
    {
        return SellingMapItem::whereIn('id', $ids)
            ->where('parent_id', '>', 0)
            ->get()
            ->pluck('item_name_eng')
            ->toArray();
    }

    private function getDefaultSellingMap(): Collection
    {
        $directories = SellingMapItem::where('parent_id', 0)->get();
        foreach ($directories as $directory) {
            $directory->items = SellingMapItem::where('parent_id', $directory->id)
                ->get();
        }
        return $directories;
    }

    private function getDefaultSellingMapWithOrdered(Car $car): Collection
    {
        $directories = SellingMapItem::where('parent_id', 0)->get();
        $orderItems = OrderItem::where('car_id', $car->id)->get();
        $partsList = $this->getPricingPartsList($car);
        $parts = $partsList->pluck('name_eng')->toArray();
        $car->load('pdrs', 'pdrs.positions', 'pdrs.positions.card.comments');

        foreach ($directories as $directory) {
            $directory->items = SellingMapItem::where('parent_id', $directory->id)
                ->whereIn('item_name_eng', $parts)
                ->get()->each(function($item) use ($partsList, $car) {
                    $userAssigned = false;
                    foreach($car->pdrs as $pdr) {
                        $position = $pdr->positions()->where('item_name_eng', $item->item_name_eng)->first();
                        if ($position) {
                            $userAssigned = $position->user_id && $position->user_id !== 135;
                            if ($position->card->comments->count() > 0) {
                                foreach($position->card->comments as $comment) {
                                    if ($comment->user_id === auth()->user()->id) {
                                        $userAssigned = true;
                                    }
                                }
                            }
                        }
                    }
                    $item->available = !$userAssigned;
                        //$orderItems->where('item_name_eng', $item->item_name_eng)->count() === 0;
                    $item->price_jp = $partsList->where('name_eng', $item->item_name_eng)
                        ->first()->card->priceCard->pricing_jp_wholesale ?? 0;
                    $item->price_ru = $partsList->where('name_eng', $item->item_name_eng)
                        ->first()->card->priceCard->pricing_ru_wholesale ?? 0;
                    $item->price_nz = $partsList->where('name_eng', $item->item_name_eng)
                        ->first()->card->priceCard->pricing_nz_wholesale ?? 0;
                    $item->price_mng = $partsList->where('name_eng', $item->item_name_eng)
                        ->first()->card->priceCard->pricing_mng_wholesale ?? 0;
                });
        }
        return $directories;
    }

    private function getDefaultPartsListWithoutUsed(): Collection
    {
        $usedPartsInMap = SellingMapItem::where('parent_id', '>', 0)->get()
            ->pluck('item_name_eng')
            ->toArray();
        return PartList::where('is_folder', 0)
            ->whereNotIn('item_name_eng', $usedPartsInMap)
            ->orderBy('item_name_eng')
            ->get();
    }

    private function findPartParentName(string $itemNameEng): string
    {
        $item = SellingMapItem::where('item_name_eng', strtoupper($itemNameEng))
            ->first();
        if ($item) {
            $name = SellingMapItem::where('id', $item->parent_id)->first()->name;
        }

        return $name ?? 'Other Parts';
    }

    private function getDefaultMapItemsCount(): int
    {
        return SellingMapItem::all()->count();
    }

}
