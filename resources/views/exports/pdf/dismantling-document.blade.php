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

        .s5 {
            color: black;
            font-family: DejaVu Sans, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9px;
        }

        table, tbody {
            vertical-align: top;
            overflow: visible;
        }

        @page {
            size: landscape;
        }

        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    @include('exports.pdf.components.dismantling-document-header', ['car' => $car])
    @foreach($parts->chunk(30) as $page => $chunk)
    @if($page > 0)
        @include('exports.pdf.components.dismantling-document-header', ['car' => $car])
    @endif
    <table style="border-collapse: collapse;width: 97.5%; margin-left: 10pt"
           class="{{ !$loop->last ? 'page-break' : ''}}">
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
        @foreach($chunk as $index => $part)
            @include('exports.pdf.components.dismantling-document-table', [
                'part' => $part,
                'index' => $index,
            ])
        @endforeach
        <tr>
            <td colspan="7" style="text-align: left">
                <span style="margin-top: 10px; font-size: 9px; font-family: DejaVu Sans, sans-serif;">
                    стр {{++$page}} из {{(ceil($parts->count() / 30))}}
                </span>
            </td>
        </tr>
    </table>
    @endforeach
</body>
</html>
