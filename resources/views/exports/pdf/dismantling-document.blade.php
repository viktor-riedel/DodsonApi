<!DOCTYPE  html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title></title>
    <meta name="author" content=""/>
    <style type="text/css"> * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        .s1 {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8.5pt;
        }

        p {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8.5pt;
            margin: 0pt;
        }

        .s2 {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: underline;
            font-size: 8.5pt;
        }

        .s3 {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8.5pt;
        }

        .s4 {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8.5pt;
        }

        table, tbody {
            vertical-align: top;
            overflow: visible;
        }

        @page {
            size: landscape;
        }
        .page_break { page-break-before: always; }
    </style>
</head>
<body>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse:collapse;width: 100%; margin: 10pt;">
    <tr style="height:10pt">
        <td style="width:77pt">
            <p class="s1" style="padding-left: 2pt;text-indent: 0pt;line-height: 9pt;text-align: left;">
                {{$car->make}} {{$car->model}}
            </p>
        </td>
    </tr>
    <tr style="height:12pt">
        <td style="width:100pt">
            <p class="s1" style="padding-left: 2pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Chassis: {{$car->chassis}}
                &nbsp;&nbsp;&nbsp;Год выпуска: {{$car->carAttributes->year ?: ''}}
            </p>
        </td>
    </tr>
    <tr style="height:10pt">
        <td style="width:77pt">
            <p class="s1" style="padding-left: 2pt;text-indent: 0pt;line-height: 9pt;text-align: left;">
                MVR: {{$car->car_mvr}}
            </p>
        </td>
    </tr>
</table>
<p style="text-indent: 0pt;text-align: left;"/>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<p style="padding-left: 10pt;text-indent: 0pt;text-align: left;">
    Разборщик:
    <span class="s3">{{auth()->user()?->name}}</span>
    Дата:
    <span class="s3">{{now()->format('d/m/Y')}}</span>
    Приемщик :<span class="s2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </span>
    <span class="s3"> </span>
    Дата<span class="s2"> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span></p>
<p style="text-indent: 0pt;text-align: left;"><br/></p>
<table style="border-collapse: collapse;width: 97%; margin-left: 10pt;">
    <tr style="height:11pt">
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                #
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                IC number
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                IC description
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Name ENG
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Name RU
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Client
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
            bgcolor="#7DCFE7">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                Comment
            </p>
        </td>
    </tr>
    @foreach($parts as $index => $part)
        @if($index === 0)
            <tr style="height:11pt">
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="4" bgcolor="#7DCFE7">
                    <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                        {{$part->folder}}
                    </p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
            </tr>
        @elseif ($part->folder !== $parts[$index - 1]->folder)
            <tr style="height:11pt">
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    colspan="4" bgcolor="#7DCFE7">
                    <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                        {{$part->folder}}
                    </p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
                <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt"
                    bgcolor="#7DCFE7">
                    <p style="text-indent: 0pt;text-align: left;"><br/></p>
                </td>
            </tr>
        @endif
    <tr style="height:11pt">
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;line-height: 10pt;text-align: right;">
                {{$index + 1}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;line-height: 10pt;text-align: right;">
                {{$part->ic_number}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="text-indent: 0pt;line-height: 10pt;text-align: center;">
                {{$part->ic_description}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                {{$part->name_eng}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                {{$part->name_ru}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
                {{$part->client_name}}
            </p>
        </td>
        <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
            <p class="s4" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
                @if($part->card->comments->count())
                    @foreach($part->card->comments as $comment)
                       {{$comment->comment}}<br>
                    @endforeach
                @endif
            </p>
        </td>
    </tr>
    @endforeach
</table>
</body>
</html>
