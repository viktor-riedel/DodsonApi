<!doctype html>
<html lang="eng">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dismantling document</title>
    <style>
        body {
            font-family: "Roboto Light", serif;
        }
    </style>
</head>
<body>
<p>Dismantling document for:</p>
<table style="border-collapse: collapse; width: 100%; height: 75px;" border="1">
    <tbody>
    <tr style="height: 18px;">
        <td style="width: 11%; height: 18px;">Make</td>
        <td style="width: 89%; height: 18px;">&nbsp;<strong>{{$car->make}}</strong></td>
    </tr>
    <tr style="height: 18px;">
        <td style="width: 11%; height: 18px;">Model</td>
        <td style="width: 89%; height: 18px;">&nbsp;<strong>{{$car->model}}</strong></td>
    </tr>
    <tr style="height: 18px;">
        <td style="width: 11%; height: 18px;">Year</td>
        <td style="width: 89%; height: 18px;">&nbsp;<strong>{{$car->carAttributes->year}}</strong></td>
    </tr>
    <tr>
        <td style="width: 11%;">Chassis</td>
        <td style="width: 89%;">&nbsp;<strong>{{$car->carAttributes->chassis}}</strong></td>
    </tr>
    <tr>
        <td style="width: 11%;">Generation</td>
        <td style="width: 89%;">&nbsp;<strong>{{$car->generation}}</strong></td>
    </tr>
    <tr>
        <td style="width: 11%;">Modification</td>
        <td style="width: 89%;">&nbsp;<strong>{{$car->modifications->header}}</strong></td>
    </tr>
    <tr style="height: 18px;">
        <td style="width: 11%; height: 18px;">MVR</td>
        <td style="width: 89%; height: 18px;">&nbsp;<strong>{{$car->car_mvr}}</strong></td>
    </tr>
    </tbody>
</table>
<p>Parts:</p>
<table style="border-collapse: collapse; width: 100%; height: 18px;" border="1">
    <tbody>
    @foreach($parts as $part)
    <tr style="height: 18px;">
        <td style="width: 20%; height: 18px;">
            <span style="font-size: 14px">
                {{$part->name_eng}}
            </span>
        </td>
        <td style="width: 20%; height: 18px;">
            <span style="font-size: 14px">
                {{$part->name_ru}}
            </span>
        </td>
        <td style="width: 20%; height: 18px; text-align: center">
            {{$part->ic_number}}
        </td>
        <td style="width: 20%; height: 18px;">
            <span style="font-size: 12px">
                {{$part->ic_description}}
            </span>
        </td>
        <td style="width: 20%; height: 18px;">
            <img src="data:image/png;base64,'{{DNS1D::getBarcodePNG((string) $part->id, \App\Models\CarPdrPositionCard::BARCODE_ALGO)}}" alt="">
        </td>
    </tr>
    <tr><td colspan="5"></td></tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
