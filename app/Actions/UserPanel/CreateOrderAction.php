<?php

namespace App\Actions\UserPanel;

use App\Events\Order\OrderCreatedEvent;
use App\Http\Traits\SyncWishedCarsTrait;
use App\Mail\UserOrderCreatedMail;
use App\Models\Car;
use App\Models\Order;
use Illuminate\Http\Request;

class CreateOrderAction
{
    use SyncWishedCarsTrait;

    public function handle(Request $request): int
    {
        $cars = $request->input('cars');
        $parts = $request->input('parts');
        $user = $request->user();
        $orderNumber = Order::getNextOrderNumber();

        $order = Order::create([
            'user_id' => $user->id,
            'order_number' => $orderNumber,
            'order_status' =>  Order::ORDER_STATUS_INT[Order::ORDER_STATUS_STRING[0]],
            'invoice_url' => null,
            'order_total' => 0,
            'country_code' => $user->country_code,
        ]);

        $orderSum = 0;
        $carIds = [];
        if (is_array($cars) && count($cars) > 0) {
            foreach ($cars as $car) {
                $currentCar = Car::with('carFinance')->find($car['id']);
                $withEngine = $car['buy_with_engine'] === Car::WITH_ENGINE;
                $withoutEngine = $car['buy_with_engine'] === Car::WITHOUT_ENGINE;
                $item = $order->items()->create([
                    'car_id' => $car['id'],
                    'part_id' => null,
                    'with_engine' => $withEngine,
                    'without_engine' => $withoutEngine,
                    'price_with_engine' => 0,
                    'price_without_engine' => 0,
                ]);
                $carIds[] = $car['id'];
                switch ($user->country_code) {
                    case 'RU':
                        $item->update(['price_with_engine' => $currentCar->carFinance->price_with_engine_ru ?? 0]);
                        $item->update(['price_without_engine' => $currentCar->carFinance->price_without_engine_ru ?? 0]);
                        if ($withEngine) {
                            $orderSum += $currentCar->carFinance->price_with_engine_ru ?? 0;
                        } else if ($withoutEngine) {
                            $orderSum += $currentCar->carFinance->price_without_engine_ru ?? 0;
                        }
                        break;
                    case 'NZ':
                        $item->update(['price_with_engine' => $currentCar->carFinance->price_with_engine_nz ?? 0]);
                        $item->update(['price_without_engine' => $currentCar->carFinance->price_without_engine_nz ?? 0]);
                        if ($withEngine) {
                            $orderSum += $currentCar->carFinance->price_with_engine_nz ?? 0;
                        } else if ($withoutEngine) {
                            $orderSum += $currentCar->carFinance->price_without_engine_nz ?? 0;
                        }
                        break;
                    case 'MN':
                        $item->update(['price_with_engine' => $currentCar->carFinance->price_with_engine_mn ?? 0]);
                        $item->update(['price_without_engine' => $currentCar->carFinance->price_without_engine_mn ?? 0]);
                        if ($withEngine) {
                            $orderSum += $currentCar->carFinance->price_with_engine_mn ?? 0;
                        } else if ($withoutEngine) {
                            $orderSum += $currentCar->carFinance->price_without_engine_mn ?? 0;
                        }
                        break;
                    default:
                        $item->update(['price_with_engine' => $currentCar->carFinance->price_with_engine_jp ?? 0]);
                        $item->update(['price_without_engine' => $currentCar->carFinance->price_without_engine_jp ?? 0]);
                        if ($withEngine) {
                            $orderSum += $currentCar->carFinance->price_with_engine_jp ?? 0;
                        } else {
                            $orderSum += $currentCar->carFinance->price_without_engine_jp ?? 0;
                        }
                }
            }

            //remove from cart
            $user->cart->cartItems()->whereIn('car_id', $carIds)->delete();
            foreach ($carIds as $carId) {
                $this->SyncWishedCars($carId);
            }
        }

        if (is_array($parts) && count($parts) > 0) {
            // TO BE DONE
        }

        $order->update(['order_total' => $orderSum]);

        event(new OrderCreatedEvent($user, $order));
        \Mail::to(config('mail.info_email'))
            ->send(new UserOrderCreatedMail($user, $order));

        return $order->id;
    }
}
