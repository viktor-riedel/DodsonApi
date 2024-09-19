<table>
    <tr>
        <td>
            <strong>{{$car->make}} {{$car->model}}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>Chassis: {{$car->carAttributes->chassis}}</strong>
        </td>
    </tr>
    <tr>
        <td>
            <strong>MVR: {{$car->car_mvr ?? '-'}}</strong>
        </td>
    </tr>
</table>
<table>
    <thead>
        <tr>
            <th style="background-color: #7ECFE7; border: 2px solid black;">#</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Client Name</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Name ENG</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Name RU</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Selling Price</th>
        </tr>
    </thead>
    <tbody>
    @foreach($parts as $count => $part)
        <tr>
            <td style="border: 2px solid black;">{{$count + 1}}</td>
            <td style="border: 2px solid black;">{{$part->client_name}}</td>
            <td style="border: 2px solid black;">{{$part->name_eng}}</td>
            <td style="border: 2px solid black;">{{$part->name_ru}}</td>
            <td style="border: 2px solid black;">{{$part->selling_price}}</td>
        </tr>
    @endforeach
    </tbody>

</table>
