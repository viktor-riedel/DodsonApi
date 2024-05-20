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
@foreach($order->items as $item)
    <ul style="list-style: none">
    @if($item->car_id)
        @php
            $car = \App\Models\Car::with('carFinance', 'images', 'carAttributes', 'modifications')
                ->find($item->car_id);
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
            @if($user->country_code === 'RU')
                @if($item->with_engine)
                    Car with engine price: {{$car->carFinance->price_with_engine_ru}} ₽
                @else
                    Car without engine price: {{$car->carFinance->price_without_engine_ru}} ₽
                @endif
            @endif
            @if($user->country_code === 'NZ')
                @if($item->with_engine)
                   Car with engine price: {{$car->carFinance->price_with_engine_nz}} $
                @else
                   Car without engine price: {{$car->carFinance->price_without_engine_ru}} $
                @endif
            @endif
            @if($user->country_code === 'MN')
                @if($item->with_engine)
                   Car with engine price: {{$car->carFinance->price_with_engine_mn}}
                @else
                   Car without engine price: {{$car->carFinance->price_without_engine_ru}}
                @endif
            @endif
            @if(!$user->country_code || !in_array($user->country_code, ['NZ', 'RU', 'MN']))
                @if($item->with_engine)
                   Car with engine price: {{$car->carFinance->price_with_engine_jp}} ¥
                @else
                   Car without engine price: {{$car->carFinance->price_without_engine_jp}} ¥
                @endif
            @endif
        </li>
        <li>
           MVR: {{$car->car_mvr}}
        </li>
        @endif
    @endif
    </ul>
    @if($item->part_id)
    @endif
@endforeach
<p>--------------------------------------</p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
