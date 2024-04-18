<table>
    <tr>
        <td>
            <strong>{{$car->make}} {{$car->model}}</strong>
        </td>
    </tr>
    <tr>
        <strong>Chassis: {{$car->carAttributes->chassis}}</strong>
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
            <th style="background-color: #7ECFE7; border: 2px solid black;">IC number</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">IC description</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Name ENG</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Name RU</th>
            <th style="background-color: #7ECFE7; border: 2px solid black;">Comment</th>
        </tr>
    </thead>
    <tbody>
    @foreach($parts as $count => $part)
        <tr>
            <td style="border: 2px solid black;">{{$count + 1}}</td>
            <td style="border: 2px solid black;">{{$part->ic_number}}</td>
            <td style="border: 2px solid black;">{{$part->ic_description}}</td>
            <td style="border: 2px solid black;">{{$part->name_eng}}</td>
            <td style="border: 2px solid black;">{{$part->name_ru}}</td>
            <td style="border: 2px solid black;">{{$part->comment}}</td>
        </tr>
    @endforeach
    </tbody>

</table>
