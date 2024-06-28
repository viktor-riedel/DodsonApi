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
        <p class="s5" style="text-indent: 0pt;line-height: 10pt;text-align: right;">
            {{$part->ic_number}}
        </p>
    </td>
    <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s5" style="text-indent: 0pt;line-height: 10pt;text-align: center;">
            {{$part->ic_description}}
        </p>
    </td>
    <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s5" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
            {{$part->name_eng}}
        </p>
    </td>
    <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s5" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
            {{$part->name_ru}}
        </p>
    </td>
    <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s5" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: left;">
            {{$part->client_name}}
        </p>
    </td>
    <td style="border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
        <p class="s5" style="padding-left: 1pt;text-indent: 0pt;line-height: 10pt;text-align: center;">
            @if($part->card->comments->count())
                @foreach($part->card->comments as $comment)
                    {{$comment->comment}}<br>
                @endforeach
            @endif
        </p>
    </td>
</tr>
