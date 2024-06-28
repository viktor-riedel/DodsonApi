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
