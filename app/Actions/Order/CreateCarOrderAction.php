<?php

namespace App\Actions\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Http\Controllers\SellingPartsMap\SellingPartsMapController;
use App\Http\Traits\BadgeGeneratorTrait;
use App\Http\Traits\InnerIdTrait;
use App\Mail\UserOrderCreatedMail;
use App\Models\Car;
use App\Models\CarPdr;
use App\Models\CarPdrPositionCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;

class CreateCarOrderAction
{

    use InnerIdTrait, BadgeGeneratorTrait;

    private int $orderTotal = 0;
    private array $engine = [];
    private array $front = [];
    private array $exterior = [];
    private array $interior = [];
    private array $frontSuspension = [];
    private array $rearSuspension = [];
    private array $other = [];
    private User $user;
    private Car $car;


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
        $this->car = $car;

        //check if user already has an active order for that car
        $order = OrderItem::with('order')->where([
            'car_id' => $car->id,
            'user_id' => $this->user->id
        ])->first()?->order;

        if (!$order) {
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
        }

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

        // add parts to parts list of the car
        $this->createPartsEntries();

        //fire email event
        event(new OrderCreatedEvent($this->user, $order));

        $emails = explode(',', config('mail.info_email'));
        if (count($emails)) {
            foreach ($emails as $email) {
                \Mail::to($email)->send(new UserOrderCreatedMail($this->user, $order));
            }
        }

        return $order->id;
    }

    private function createOrderItems(Order $order, Car $car, array $parts = []): void
    {
        foreach ($parts as $part) {

            //check part already chosen by another user
            $item = OrderItem::where([
                'car_id' => $car->id,
                'item_name_eng' =>  $part['item_name_eng']
            ])->first();

            if (!$item) {
                $orderItem = $order->items()->create([
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
                switch ($this->user->country_code) {
                    case 'JP':
                        $orderItem->update(['price_jpy' => $part['price_jp']]);
                        break;
                    case 'NZ':
                        $orderItem->update(['price_jpy' => $part['price_nz']]);
                        break;
                    case 'RU':
                        $orderItem->update(['price_jpy' => $part['price_ru']]);
                        break;
                    case 'MNG':
                        $orderItem->update(['price_jpy' => $part['price_mng']]);
                        break;
                    default:
                        break;
                }
                $orderItem->refresh();
                $this->orderTotal += (int) $orderItem->price_jpy;
            }
        }
    }

    private function createPartsEntries(): void
    {
        if (count($this->engine)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[0]);
            $this->updateCards($folder, $this->engine);
        }
        if (count($this->front)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[1]);
            $this->updateCards($folder, $this->front);
        }
        if (count($this->exterior)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[2]);
            $this->updateCards($folder, $this->exterior);
        }
        if (count($this->interior)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[3]);
            $this->updateCards($folder, $this->interior);
        }
        if (count($this->frontSuspension)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[4]);
            $this->updateCards($folder, $this->frontSuspension);
        }
        if (count($this->rearSuspension)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[5]);
            $this->updateCards($folder, $this->rearSuspension);
        }
        if (count($this->other)) {
            $folder = $this->resolveFolder(SellingPartsMapController::MAIN_DIRECTORIES[6]);
            $this->updateCards($folder, $this->other);
        }
    }

    private function updateCards(CarPdr $folder, array $parts): void
    {
        foreach ($parts as $part) {
            $position = $folder->positions()->where('item_name_eng', $part['item_name_eng'])->first();
            if ($position) {
                $position->update([
                    'barcode' => $this->generateNextBarcode(),
                    'user_id' => $this->user->id
                ]);
                switch ($this->user->country_code) {
                    case 'JP':
                        $position->card->priceCard()->update(['buying_price' => $part['price_jp']]);
                        break;
                    case 'NZ':
                        $position->card->priceCard()->update(['buying_price' => $part['price_nz']]);
                        break;
                    case 'RU':
                        $position->card->priceCard()->update(['buying_price' => $part['price_ru']]);
                        break;
                    case 'MNG':
                        $position->card->priceCard()->update(['buying_price' => $part['price_mng']]);
                        break;
                    default:
                        break;
                }
            }
        }
    }

    private function resolveFolder(string $folderName): CarPdr
    {
        $folder = CarPdr::where(
            [
                'car_id' => $this->car->id,
                'item_name_eng' => $folderName,
                'is_folder' => true,
            ]
        )->first();

        if (!$folder) {
            $folder = $this->car->pdrs()->create([
                'parent_id' => 0,
                'item_name_eng' => $folderName,
                'item_name_ru' => $folderName,
                'is_folder' => true,
                'is_deleted' => false,
                'parts_list_id' => null,
                'created_by' => $this->user->id,
            ]);
        }
        return $folder;
    }
}
