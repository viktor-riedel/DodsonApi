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
<p>A new parts order has been created</p>
<p>From User: {{$user->name}}</p>
<p>User Email: {{$user->email}}</p>
<p>Order Number: {{$order->order_number}}</p>
<p>-------------ORDER DETAILS------------</p>
<ul style="list-style: none">
    @if($order)
        <li>
            <p>-------------ORDERED PARTS------------</p>
        </li>
        <li>
            <ol>
                @foreach($order->items as $orderItem)
                    <li @if($orderItem->price_jpy === 0) style="color: red" @endif>
                        Item: {{$orderItem->item_name_eng}} / {{$orderItem->item_name_ru}} Price: {{$orderItem->price_jpy}} ¥
                    </li>
                @endforeach
            </ol>
        <li>
            Total for order: {{$order->order_total}} ¥
        </li>
        <li>
            <p>-------------END ORDERED PARTS------------</p>
        </li>
        <li>
            Comment: {{$order->comment}}
        </li>
    @endif
</ul>
<p>--------------------------------------</p>
<p>Kind Regards, Dodson Team</p>
</body>
</html>
