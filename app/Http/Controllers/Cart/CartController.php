<?php

namespace App\Http\Controllers\Cart;

use App\Events\Cart\ItemAddedToCartEvent;
use App\Events\Cart\ItemRemovedFromCartEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Cart\CartResource;
use App\Http\Traits\CartTrait;
use App\Models\Car;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use CartTrait;

    public function add(Request $request, Car $car): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->checkCartExist($request->user());
        $item = $request->user()->cart->cartItems()->create([
            'cart_id'=> $request->user()->cart->id,
            'user_id' => $request->user()->id,
            'car_id' => $car->id,
        ]);
        event(new ItemAddedToCartEvent($request->user(), $item));
        return CartResource::collection($request->user()->cart->cartItems);
    }

    public function items(Request $request): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $this->checkCartExist($request->user());
        return CartResource::collection($request->user()->cart->cartItems);
    }

    public function delete(Request $request, Car $car): \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $item = $request->user()->cart->cartItems()->where('car_id', $car->id)->first();
        event(new ItemRemovedFromCartEvent($request->user(), $item));

        $request->user()->cart->cartItems()->where('car_id', $car->id)->delete();
        return CartResource::collection($request->user()->cart->cartItems);
    }
}
