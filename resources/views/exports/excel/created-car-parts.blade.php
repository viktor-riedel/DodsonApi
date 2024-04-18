<table>
    <thead>
        <tr>
            <th>IC number</th>
            <th>IC description</th>
            <th>Name ENG</th>
            <th>Name RU</th>
            <th>Comment</th>
            <th>QTY</th>
        </tr>
    </thead>
    <tbody>
        @foreach($parts as $part)
            <tr>
                <td>{{$part->ic_number}}</td>
                <td>{{$part->ic_description}}</td>
                <td>{{$part->name_eng}}</td>
                <td>{{$part->name_ru}}</td>
                <td>{{$part->comment}}</td>
                <td>{{$part->card->part_attributes_card?->amount}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
