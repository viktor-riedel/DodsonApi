<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <style type="text/css"> * {margin:0; padding:0; text-indent:0; }
        .s1 { color: black; font-family:DejaVu Sans, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 16pt; }
        .s2 { color: black; font-family:DejaVu Sans, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 12pt; }
        .s3 { color: black; font-family:DejaVu Sans, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 12pt; }
        .s4 { color: black; font-family:DejaVu Sans, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 10pt; }
        .s5 { color: black; font-family:DejaVu Sans, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 8pt; }
        .s6 { color: black; font-family:DejaVu Sans, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 6pt; }
        .s7 { color: black; font-family:DejaVu Sans, sans-serif; font-style: normal; font-weight: normal; text-decoration: none; font-size: 6pt; }
        .s8 { color: black; font-family:DejaVu Sans, sans-serif; font-style: italic; font-weight: bold; text-decoration: none; font-size: 10pt; }
        table, tbody {vertical-align: top; overflow: visible; }
        .page_break { page-break-before: always; }
        @page { size: 10cm 7cm landscape;}
    </style>
</head>
<body>
@foreach($parts as $part)
    <table style="border-collapse:collapse;width: 100%;margin:auto;" cellspacing="0">
        <tr style="height:31pt">
            <td style="width:157pt;border-top-style:dashed;border-top-width:1pt;border-top-color:#333333;border-left-style:dashed;border-left-width:1pt;border-left-color:#333333;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#333333" rowspan="2">
                <p class="s1" style="margin-left: 10px; padding-left: 34pt;text-indent: 0pt;line-height: 16pt;text-align: left;">
                    {{$part->barcode}}
                </p>
                <p style="text-indent: 0pt;text-align: left;">
                    <span>
                        <table border="0" cellspacing="0" cellpadding="0" style="margin-left: 10px">
                            <tr>
                                <td>
                                    <img width="209" height="43" src="data:image/png;base64,'{{DNS1D::getBarcodePNG((string) $part->barcode, \App\Models\CarPdrPositionCard::BARCODE_ALGO)}}"/>
                                </td>
                            </tr>
                        </table>
                    </span></p>
                <p style="padding-top: 3pt;text-indent: 0pt;text-align: left;"><br/></p>
            </td>
            <td style="width:88pt;border-top-style:dashed;border-top-width:1pt;border-top-color:#333333;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#333333;border-right-style:dashed;border-right-width:1pt;border-right-color:#333333">
                <p class="s2" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Stock #: {{$car->car_mvr}}</p>
            </td>
        </tr>
        <tr style="height:32pt">
            <td style="width:88pt;border-top-style:solid;border-top-width:1pt;border-top-color:#333333;border-bottom-style:solid;border-bottom-width:1pt;border-bottom-color:#333333;border-right-style:dashed;border-right-width:1pt;border-right-color:#333333">
                <p class="s2" style="padding-left: 3pt;text-indent: 0pt;text-align: left;">Yr: <span class="s3">{{$car->carAttributes->year > 0 ? $car->carAttributes->year : ''}}</span></p>
                <p class="s2" style="padding-top: 2pt;padding-left: 3pt;text-indent: 0pt;text-align: left;">IC: {{$part->ic_number}}</p>
            </td>
        </tr>
        <tr>
            <td style="width:100%;height: 118pt;border-top-style:solid;border-top-width:1pt;border-top-color:#333333;border-left-style:dashed;border-left-width:1pt;border-left-color:#333333;border-bottom-style:dashed;border-bottom-width:1pt;border-bottom-color:#333333;border-right-style:dashed;border-right-width:1pt;border-right-color:#333333" colspan="2">
                <p style="padding-top: 10pt;text-indent: 0pt;text-align: left;"><br/></p>
                @if(count(explode(' ', $part->name_ru)) < 3)
                    <p class="s2" style="margin-left: 5px;padding-left: 2pt;padding-right: 27pt;text-indent: 0pt;text-align: left;">
                        {{$part->name_eng}} {{$part->name_ru}}
                    </p>
                @else
                    <p class="s8" style="margin-left: 5px;padding-left: 2pt;padding-right: 27pt;text-indent: 0pt;text-align: left;">
                        {{$part->name_eng}} {{$part->name_ru}}
                    </p>
                @endif
                <p class="s2" style="margin-left: 5px;padding-left: 2pt;text-indent: 0pt;text-align: left;">{{$car->make}}, {{$car->model}}</p>
                <p class="s7" style="margin-left: 5px;padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                    IC description:
                    @if($part->ic_description)
                        {{$part->ic_description}}
                    @else
                        <br>
                    @endif
                </p>
                <p class="s5" style="margin-left: 5px;padding-top: 1pt;padding-left: 1pt;text-indent: 0pt;text-align: left;">
                    Comment:
                </p>
                <p class="s6" style="margin-left: 5px;padding-top: 4pt;padding-left: 0;text-indent: 0pt;line-height: 9pt;text-align: left;">
                    {{now()->format('d.m.Y H:i:s')}} / {{auth()->user()?->name ?? ''}}
                </p>
            </td>
        </tr>
    </table>
    <div class="page_break"></div>
@endforeach
</body>
</html>
