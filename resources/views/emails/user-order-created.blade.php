<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DODSON PARTS</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    <style>
    </style>
</head>
<body class="antialiased">
<p>-------------USER DETAILS------------</p>
<p>A new order has been created</p>
<p>From User: {{$user->name}}</p>
<p>User Email: {{$user->email}}</p>
<p>Order Number: {{$order->order_number}}</p>
<p>-------------ORDER DETAILS------------</p>
<ul style="list-style: none">
@if($order)
    @php
        $car = \App\Models\Car::with('carFinance', 'images', 'carAttributes', 'modifications')
            ->find($order->items->first()->car_id);
    @endphp
    <li>
        Car: {{$car->make}} {{$car->model}} {{$car->year}} {{$car->chassis}}
    </li>
    @if($car->images->count())
    <li>
        <img src="{{$car->images[0]->url}}" alt="" style="max-width: 350px">
    </li>
    <li>
        Modification: {{$car->modifications->header}}
    </li>
    <li>
        <p>-------------ORDERED PARTS------------</p>
    </li>
    <li>
        <ol>
        @foreach($order->items as $orderItem)
            <li>
                {{ $orderItem->item_name_eng }} / {{$orderItem->item_name_ru}}, price: {{number_format($orderItem->price_jpy)}}
            </li>
        @endforeach
        </ol>
    </li>
    <li>
        <p>-------------END ORDERED PARTS------------</p>
    </li>
    <li>
       MVR: {{$car->car_mvr}}
    </li>
    <li>
        Comment: {{$order->comment}}
    </li>
    @endif
@endif
</ul>
<p>--------------------------------------</p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
