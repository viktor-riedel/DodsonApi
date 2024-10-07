<?php

namespace App\Http\Controllers\Parts;

use App\Actions\Import\ImportFromPinnacleCsvAction;
use App\Actions\TradeMe\TradeMeListingAction;
use App\Events\TradeMe\CreateListingEvent;
use App\Events\TradeMe\UpdateTradeMeListingEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Part\EditPartResource;
use App\Http\Resources\Part\MakeResource;
use App\Http\Resources\Part\ModelResource;
use App\Http\Resources\Part\PartGroupResource;
use App\Http\Resources\Part\PartNameResource;
use App\Http\Resources\Part\PartResource;
use App\Http\Resources\Part\YearResource;
use App\Models\CarPdrPosition;
use DB;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Storage;

class PartsController extends Controller
{
    public function list(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make', '');
        $model = $request->get('model', '');
        $years = $request->get('years', '');
        $text = $request->get('search', '');
        $itemNames = $request->get('part_names', '');
        $groupNames = $request->get('part_groups', '');

        $names = [];
        $groups = [];
        $carYears = [];

        if ($years) {
            $carYears = explode(',', $years);
        }

        if ($itemNames) {
            $names = explode(',', $itemNames);
        }

        if ($groupNames) {
            $groups = explode(',', $groupNames);
        }

        $parts = DB::table('car_pdr_positions')
            ->select(
                'car_pdr_positions.id',
                'car_pdr_position_cards.barcode',
                'car_pdr_position_cards.oem_number',
                'car_pdr_position_cards.ic_number',
                'car_pdr_position_cards.description',
                'cars.car_mvr',
                'cars.make',
                'cars.model',
                'car_attributes.year',
                'car_attributes.mileage',
                'car_pdr_position_cards.name_eng',
                'car_pdr_position_cards.name_ru',
                'car_pdrs.item_name_eng',
                'car_pdr_position_card_prices.selling_price',
                'cars.generation',
            )
            ->selectRaw('null as images, null as tradeMeListing')
            ->join('car_pdr_position_cards', function(JoinClause $join)  {
                $join->on('car_pdr_position_cards.car_pdr_position_id', '=', 'car_pdr_positions.id');
            })
            ->join('car_pdr_position_card_prices', function(JoinClause $join) {
                $join->on('car_pdr_position_card_prices.car_pdr_position_card_id', '=', 'car_pdr_position_cards.id');
            })
            ->join('car_pdrs', function (JoinClause $join) use ($groups) {
                $join->on('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id')
                ->when(count($groups), function ($query) use ($groups) {
                    return $query->whereIn('car_pdrs.item_name_eng', $groups);
                });
            })
            ->join('cars', function (JoinClause $join) use ($make, $model, $text) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                ->where('cars.virtual_retail', true)
                ->when($make, function ($query, $make) {
                    return $query->where('cars.make', $make);
                })
                ->when($model, function ($query, $model) {
                    return $query->where('cars.model', $model);
                });
            })
            ->join('car_attributes', function (JoinClause $join) use ($carYears) {
                $join->on('car_attributes.car_id', '=', 'cars.id')
                ->when(count($carYears), function ($query) use ($carYears) {
                    return $query->whereIn('car_attributes.year', $carYears);
                });
            })
            ->when($text, function ($query) use ($text) {
                return $query->where('car_pdr_positions.item_name_eng', 'like', "%$text%")
                    ->orWhere('car_pdr_positions.ic_number', 'like', "%$text%")
                    ->orWhere('car_pdr_positions.ic_description', 'like', "%$text%")
                    ->orWhere('car_pdr_position_cards.barcode', 'like', "%$text%")
                    ->orWhere('cars.make', 'like', "%$text%")
                    ->orWhere('cars.model', 'like', "%$text%")
                    ->orWhere('cars.car_mvr', 'like', "%$text%");
            })
            ->when(count($names), function ($query) use ($names) {
                return $query->whereIn('car_pdr_positions.item_name_eng', $names);
            })
            ->orderBy('cars.car_mvr')
            ->orderBy('car_pdr_positions.item_name_eng')
            ->paginate(50);

        $parts->getCollection()->each(function($part) {
           $position = CarPdrPosition::with('images' ,'tradeMeListing')
               ->find($part->id);
           $part->images = $position?->images;
           $part->tradeMeListing = $position->tradeMeListing;
        });

        return PartResource::collection($parts);
    }

    public function delete(CarPdrPosition $part): JsonResponse
    {
        $part->tradeMeListing()?->delete();
        $part->delete();
        return response()->json(null, 204);
    }

    public function get(CarPdrPosition $part): EditPartResource
    {
        $part->load('card', 'card.priceCard', 'carPdr', 'carPdr.car', 'images', 'tradeMeListing');
        return new EditPartResource($part);
    }

    public function update(Request $request, CarPdrPosition $part): EditPartResource
    {
        $fireUpdateTradeMeEvent = $request->integer('price_nzd') !== $part->card->priceCard->selling_price ||
            $request->integer('standard_price_nzd') !==  $part->card->priceCard->selling_price;

        $part->update([
            'ic_number' => $request->input('ic_number'),
            'oem_number' => $request->input('oem_number'),
            'item_name_eng' => $request->input('item_name_eng'),
            'item_name_ru' => $request->input('item_name_ru'),
            'ic_description' => $request->input('ic_description'),
        ]);
        $part->card()->update([
            'barcode' => $request->input('stock_number'),
            'ic_number' => $request->input('ic_number'),
            'oem_number' => $request->input('oem_number'),
            'description' => $request->input('ic_description'),
            'name_eng' => $request->input('item_name_eng'),
            'name_ru' => $request->input('item_name_ru'),
        ]);
        $part->card->priceCard()->update([
            'selling_price' => $request->input('price_nzd'),
            'standard_price' => $request->input('standard_price_nzd'),
        ]);
        $part->carPdr->car()->update([
            'make' => $request->input('make'),
            'model' => $request->input('model'),
            'generation' => $request->input('generation'),
        ]);
        $part->carPdr->car->carAttributes()->update([
            'year' => $request->input('year'),
            'mileage' => $request->integer('mileage'),
        ]);
        $part->refresh();
        if ($fireUpdateTradeMeEvent && $part->tradeMeListing) {
            event(new UpdateTradeMeListingEvent($part->tradeMeListing));
        }
        return new EditPartResource($part);
    }

    public function uploadPhoto(Request $request, CarPdrPosition $part): JsonResponse
    {
        if ($request->file('uploadPartPhotos')) {
            $storage = Storage::disk('s3');
            foreach ($request->file('uploadPartPhotos') as $file) {
                $fileName = \Str::random();
                $originFileName = $file->getFilename();
                $folderName = 'parts/' . $part->id;
                $mime = $file?->getMimeType();
                $fileExtension = '.' . $file?->clientExtension();
                $savePath = $folderName . '/' . $fileName.$fileExtension;
                $size = $file->getSize();
                $storage->put($savePath, $file->getContent(), 'public');
                $part->images()->create([
                    'url' => $storage->url($savePath),
                    'mime' => $mime,
                    'original_file_name' => $originFileName,
                    'folder_name' => $folderName,
                    'extension' => $fileExtension,
                    'file_size' => $size,
                    'special_flag' => null,
                    'created_by' => $request->user()->id,
                ]);
            }
        }
        return response()->json($part->images);
    }

    public function deletePhoto(Request $request, CarPdrPosition $part, int $photo): JsonResponse
    {
        $photo = $part->images()->where('id', $photo)->first();
        if ($photo) {
            $photo->update(['deleted_by' => $request->user()->id]);
            $photo->delete();
        }
        return response()->json($part->images);

    }

    public function partNames(): AnonymousResourceCollection
    {
        $partNames = DB::table('car_pdr_positions')
            ->selectRaw('distinct car_pdr_positions.item_name_eng')
            ->join('car_pdrs', function(JoinClause $join) {
                $join->on('car_pdrs.id', '=', 'car_pdr_positions.car_pdr_id');
            })
            ->join('cars', function(JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->where('cars.virtual_retail', true);
            })
            ->whereRaw('car_pdr_positions.item_name_eng != ""')
            ->orderBy('car_pdr_positions.item_name_eng')
            ->get();
        return PartNameResource::collection($partNames);
    }

    public function partGroups(): AnonymousResourceCollection
    {
        $partGroups = DB::table('car_pdrs')
            ->selectRaw('distinct item_name_eng')
            ->join('cars', function(JoinClause $join) {
                $join->on('cars.id', '=', 'car_pdrs.car_id')
                    ->where('cars.virtual_retail', true);
            })
            ->where('car_pdrs.is_folder', 1)
            ->orderBy('item_name_eng')
            ->get();
        return PartGroupResource::collection($partGroups);
    }

    public function makes(): AnonymousResourceCollection
    {
        $makes = DB::table('cars')
            ->selectRaw('distinct(make)')
            ->where('make', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('make')
            ->where('virtual_retail', true)
            ->orderBy('make')
            ->get();

        return MakeResource::collection($makes);
    }

    public function models(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make');
        $models = DB::table('cars')
            ->selectRaw('distinct(model)')
            ->where('make', '=', $make)
            ->where('model', '!=', '')
            ->whereNull('deleted_at')
            ->whereNotNull('model')
            ->where('virtual_retail', true)
            ->orderBy('model')
            ->get();

        return ModelResource::collection($models);
    }


    public function years(Request $request): AnonymousResourceCollection
    {
        $make = $request->get('make');
        $model = $request->get('model');
        $years = DB::table('cars')
            ->selectRaw('distinct(car_attributes.year)')
            ->join('car_attributes', 'cars.id', '=', 'car_attributes.car_id')
            ->where('cars.make', '=', $make)
            ->where('cars.model', $model)
            ->whereNull('cars.deleted_at')
            ->whereNotNull('cars.model')
            ->where('cars.virtual_retail', true)
            ->orderBy('car_attributes.year')
            ->get();

        return YearResource::collection($years);
    }

    public function importFromPinnacle(Request $request): JsonResponse
    {
        if ($request->file('uploadPartsPinnacle')) {
            app()->make(ImportFromPinnacleCsvAction::class)->handle($request);
        }
        return response()->json(['loaded' => 100]);
    }

    public function importFromOneC(Request $request): JsonResponse
    {
        return response()->json([]);
    }

    public function tradeMeListing(CarPdrPosition $part): JsonResponse
    {
        $listing = app()->make(TradeMeListingAction::class)->handle($part);
        return response()->json($listing);
    }

    public function createTradeMeListing(Request $request, CarPdrPosition $part): JsonResponse
    {
        $listing = $part->tradeMeListing()->create([
            'listed_by' => $request->user()->id,
            'listing_id' => 0,
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'category_name' => $request->input('category_name'),
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'delivery_options' => implode(',', $request->input('delivery_options')),
            'default_duration' => $request->input('default_duration'),
            'payments_options' => implode(',', $request->input('payments_options')),
            'update_prices' => (bool) $request->input('update_prices'),
            'relist' => (bool) $request->input('relist'),
        ]);
        if ($request->input('listing_photos') && is_array($request->input('listing_photos'))) {
            foreach ($request->input('listing_photos') as $photo) {
                $listing->tradeMePhotos()->create([
                   'image_url' => $photo['url'],
                ]);
            }
        }

        event(new CreateListingEvent($listing));

        return response()->json(['success' => true]);
    }

    public function updateTradeMeListing(Request $request, CarPdrPosition $part): JsonResponse
    {
        $part->tradeMeListing()->update([
            'title' => $request->input('title'),
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'delivery_options' => implode(',', $request->input('delivery_options')),
            'default_duration' => $request->input('default_duration'),
            'payments_options' => implode(',', $request->input('payments_options')),
            'update_prices' => (bool) $request->input('update_prices'),
            'relist' => (bool) $request->input('relist'),
        ]);
        if ($request->input('listing_photos') && is_array($request->input('listing_photos'))) {
            $part->tradeMeListing->tradeMePhotos()->each(function ($photo) {
                $photo->delete();
            });
            foreach ($request->input('listing_photos') as $photo) {
                $part->tradeMeListing->tradeMePhotos()->create([
                    'image_url' => $photo['url'],
                ]);
            }
        }

        event (new UpdateTradeMeListingEvent($part->tradeMeListing));

        return response()->json(['success' => true]);
    }

    public function deleteTradeMeListing(CarPdrPosition $part): JsonResponse
    {
        $part->tradeMeListing()->delete();
        return response()->json(['success' => true]);
    }
}
