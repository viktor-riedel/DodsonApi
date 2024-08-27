<table>
    <thead>
    <tr>
        <th style="background-color: #7ECFE7; border: 2px solid black;">#</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Created</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Status</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Client name</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">MVR</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Make</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Model</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Year</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Chassis</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Parts count</th>
        <th style="background-color: #7ECFE7; border: 2px solid black;">Order Sum</th>
    </tr>
    </thead>
    <tbody>
    @foreach($orders as $count => $order)
        <tr>
            <td style="border: 2px solid black;">{{$count + 1}}</td>
            <td style="border: 2px solid black;">{{$order->created_at->format('d/m/Y')}}</td>
            <td style="border: 2px solid black;">{{$order->status_ru ?? \App\Models\Order::ORDER_STATUS_STRING[$order->order_status]}}</td>
            <td style="border: 2px solid black;">{{$order->createdBy->name}}</td>
            <td style="border: 2px solid black;">{{$order->items->first()?->car?->car_mvr ?? '-'}}</td>
            <td style="border: 2px solid black;">{{$order->items->first()?->car?->make ?? 'Parts Order'}}</td>
            <td style="border: 2px solid black;">{{$order->items->first()?->car?->model ?? '-'}}</td>
            <td style="border: 2px solid black;">{{$order->items->first()?->car?->carAttributes?->year ?? '-'}}</td>
            <td style="border: 2px solid black;">{{$order->items->first()?->car?->chassis ?? '-'}}</td>
            <td style="border: 2px solid black;">{{$order->items->count()}}</td>
            <td style="border: 2px solid black;">{{$order->order_total}}</td>
        </tr>
    @endforeach
    </tbody>

</table>
