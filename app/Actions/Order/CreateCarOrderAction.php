<?php

namespace App\Actions\Order;

use App\Http\Controllers\SellingPartsMap\SellingPartsMapController;
use App\Http\Traits\InnerIdTrait;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\CarPdrPositionCard;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CreateCarOrderAction
{

    use InnerIdTrait;

    private int $orderTotal = 0;
    private array $engine = [];
    private array $front = [];
    private array $exterior = [];
    private array $interior = [];
    private array $frontSuspension = [];
    private array $rearSuspension = [];
    private array $other = [];
    private User $user;


    public function handle(Request $request, Car $car): int
    {
        $this->user = $request->user();
        $orderNumber = Order::getNextOrderNumber();
        $this->engine = $request->input('engine');
        $this->front = $request->input('front');
        $this->exterior = $request->input('exterior');
        $this->interior = $request->input('interior');
        $this->frontSuspension = $request->input('frontSuspension');
        $this->rearSuspension = $request->input('rearSuspension');
        $this->other = $request->input('other');

        // create order
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => $orderNumber,
            'order_status' =>  Order::ORDER_STATUS_INT[Order::ORDER_STATUS_STRING[0]],
            'invoice_url' => null,
            'order_total' => 0,
            'country_code' => $this->user->country_code,
            'comment' => $request->input('comment'),
        ]);

        //create items
        if (count($this->engine)) {
            $this->createOrderItems($order, $car, $this->engine);
        }
        if (count($this->front)) {
            $this->createOrderItems($order, $car, $this->front);
        }
        if (count($this->exterior)) {
            $this->createOrderItems($order, $car, $this->exterior);
        }
        if (count($this->interior)) {
            $this->createOrderItems($order, $car, $this->interior);
        }
        if (count($this->frontSuspension)) {
            $this->createOrderItems($order, $car, $this->frontSuspension);
        }
        if (count($this->rearSuspension)) {
            $this->createOrderItems($order, $car, $this->rearSuspension);
        }
        if (count($this->other)) {
            $this->createOrderItems($order, $car, $this->other);
        }

        $order->update(['order_total' => $this->orderTotal]);

        // add parts to parts list of the car + user
        $this->createPartsEntries($car, $request);

        return $order->id;
    }

    private function createOrderItems(Order $order, Car $car, array $parts = []): void
    {
        foreach ($parts as $part) {
            $order->items()->create([
                'car_id' => $car->id,
                'part_id' => null,
                'with_engine' => false,
                'item_name_eng' => $part['item_name_eng'] ?? '',
                'item_name_ru' => $part['item_name_ru'] ?? null,
                'price_jpy' => $part['price_jpy'] ?? 0,
                'engine_price' => 0,
                'catalyst_price' => 0,
                'user_id' => $this->user->id,
                'currency' => 'JPY',
            ]);
            $this->orderTotal += (int) $part['price_jpy'];
        }
    }

    private function createPartsEntries(Car $car): void
    {
        if (count($this->engine)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[0],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->engine);
        }
        if (count($this->front)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[1],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->front);
        }
        if (count($this->exterior)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[2],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->exterior);
        }
        if (count($this->interior)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[3],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->interior);
        }
        if (count($this->frontSuspension)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[4],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->frontSuspension);
        }
        if (count($this->rearSuspension)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[5],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->rearSuspension);
        }
        if (count($this->other)) {
            $folder = $car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => SellingPartsMapController::MAIN_DIRECTORIES[6],
                'item_name_ru' => '',
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
            $this->createCards($folder, $this->other);
        }
    }

    private function createCards(CarPdr $folder, array $parts): void
    {
        foreach ($parts as $part) {
            $position = $folder->positions()->create([
                'item_name_ru' => $part['item_name_ru'] ?? '',
                'item_name_eng' => $part['item_name_eng'] ?? '',
                'ic_number' => null,
                'oem_number' => null,
                'ic_description' => null,
                'is_virtual' => false,
                'created_by' => $this->user->id,
                'user_id' => $this->user->id,
            ]);
            $card = $position->card()->create([
                'parent_inner_id' => $this->generateInnerId(\Str::random(10) . now()),
                'name_eng' => $part['item_name_eng'] ?? '',
                'name_ru' => $part['item_name_ru'] ?? '',
                'comment' => null,
                'description' => null,
                'ic_number' => '',
                'oem_number' => null,
                'created_by' => $this->user->id,
                'barcode' => $this->generateBarCode(),
            ]);
            $card->priceCard()->create([
                'price_currency' => 'JPY',
                'price_nz_wholesale' => null,
                'price_nz_retail' => null,
                'price_ru_wholesale' => null,
                'price_ru_retail' => null,
                'price_jp_minimum_buy' => null,
                'price_jp_maximum_buy' => null,
                'minimum_threshold_nz_retail' => null,
                'minimum_threshold_nz_wholesale' => null,
                'minimum_threshold_ru_retail' => null,
                'minimum_threshold_ru_wholesale' => null,
                'delivery_price_nz' => null,
                'delivery_price_ru' => null,
                'pinnacle_price' => null,
                'minimum_threshold_jp_retail' => null,
                'minimum_threshold_jp_wholesale' => null,
                'minimum_threshold_mng_retail' => null,
                'minimum_threshold_mng_wholesale' => null,
                'selling_price' => null,
                'buying_price' => (int) $part['price_jpy'],
            ]);
            $card->partAttributesCard()->create([
                'color' => null,
                'weight' => null,
                'volume' => null,
                'amount' => isset($part['amount']) ? (int) $part['amount'] : 1,
                'ordered_for_user_id' => null,
            ]);
        }
    }

    private function generateBarCode(): int
    {
        $exist = true;
        $barcode = 0;
        while($exist) {
            $barcode = random_int(1000000, 6999999);
            $exist = CarPdrPositionCard::where('barcode', $barcode)->exists();
        }
        return $barcode;
    }
}
