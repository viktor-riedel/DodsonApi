<?php

namespace App\Jobs\Sync;

use App\Actions\Api\GetDoneCarDataAction;
use App\Events\Sync\Export\CarDoneEvent;
use App\Http\ExternalApiHelpers\SendDoneCar;
use App\Http\Traits\SyncPartWithOrderTrait;
use App\Models\Car;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDoneCarJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, SyncPartWithOrderTrait;

    public Car $car;
    public User $user;
    public SendDoneCar $httpHelper;

    public function __construct(Car $car, User $user)
    {
        $this->car = $car;
        $this->user = $user;
    }

    public function handle(): void
    {
        try {
            event(new CarDoneEvent($this->user, $this->car, true, false));
            $data = app()->make(GetDoneCarDataAction::class)->handle($this->car);
            $this->httpHelper = new SendDoneCar();
            $response = $this->httpHelper->sendData($data);
            if ($response) {
                $this->car->syncedPartsData()->create([
                    'document_number' => $response['Number'] ?? null,
                    'document_date' => $response['Date'] ?
                        Carbon::createFromFormat('d-m-Y', $response['Date'])->format('d/m/Y') :
                        null,
                    'created_by' => $this->user->id,
                ]);
                //set status
                $items = OrderItem::with('order')->where('car_id', $this->car->id)
                    ->get()
                    ->pluck('order');
                foreach($items as $order) {
                    $order->update(['order_status' => Order::ORDER_STATUS_INT['COMPLETE']]);
                    $order->refresh();
                    $order->setItemsStatus();
                }

                $this->syncOrdersWithDoneResponse($this->car, $response['Invoices'] ?? []);
                event(new CarDoneEvent($this->user, $this->car, false, true, $response['Date'], $response['Number']));
            }
            // update db with server response
        } catch (\Exception $e) {
            \Log::error('SYNC DONE CAR: ' . $e->getMessage() . ' ' . $e->getFile() . ' ' . $e->getLine());
        }
    }
}
