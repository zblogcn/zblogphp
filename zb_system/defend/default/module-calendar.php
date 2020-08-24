<table id="tbCalendar">
    <caption><a title="{$lang['msg']['prev_month']}" href="{$prevMonthUrl}">«</a>&nbsp;&nbsp;&nbsp;<a title="{$nowMonth}" href="{$nowMonthUrl}">
{if $option['ZC_BLOG_LANGUAGEPACK'] == 'zh-cn' || $option['ZC_BLOG_LANGUAGEPACK'] == 'zh-tw'}
    {php}echo str_replace(array('%y%', '%m%'), array($nowYear, $nowMonth), $lang['msg']['year_month']);{/php}
{else}
    {date("F , Y", mktime(0, 0, 0, $nowMonth, 1, $nowYear))}
{/if}
    </a>&nbsp;&nbsp;&nbsp;<a title="{$lang['msg']['next_month']}" href="{$nextMonthUrl}">»</a></caption>
    <thead><tr>{for $i = 1; $i <= 7; $i++}<th title="{$lang['week'][$i]}" scope="col"><small>{$lang['week_abbr'][$i]}</small></th>{/for}</tr></thead>
    <tbody>
    <tr>
{php}
$strCalendar = '';
$numberOfDays = date('t', strtotime($date));
$dayOfWeek = date('N', strtotime($date . '-1'));
$lastDayOfWeek = 7 - date('N', strtotime($date . '-' . $numberOfDays));
$dayOfWeekColspan = $dayOfWeek - 1;
$lastDayOfWeekColspan = $lastDayOfWeek - 1;

if ($dayOfWeek > 1) {
    $strCalendar .= '<td class="pad" colspan="' . $dayOfWeekColspan . '"></td>';
}

$weekCounter = $dayOfWeek - 1;
for ($i = 1; $i <= $numberOfDays; $i++) {
    $strCalendar .= '<td>';
    if (isset($arraydate[$i])) {
        $strCalendar .= '<a title="' . $i . '" href="' . $arraydate[$i]['Url'] . '" title="' . $arraydate[$i]['Date'] . ' (' . $arraydate[$i]['Count'] . ')" target="_blank">' . $i . '</a>';
    } else {
        $strCalendar .= $i;
    }
    
    $strCalendar .= '</td>';

    $weekCounter++;
    if ($weekCounter % 7 == 0) {
        $strCalendar .= '</tr><tr>';
    }
}
if ($lastDayOfWeek > 1) {
    $strCalendar .= '<td class="pad" colspan="' . $lastDayOfWeekColspan . '"> </td>';
}
$strCalendar .= '</tbody>';
$strCalendar = str_replace('<tr></tbody>', '</tbody>', $strCalendar);
echo $strCalendar;
{/php}
</table>