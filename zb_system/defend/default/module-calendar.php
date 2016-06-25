<table id="tbCalendar">
    <caption><a href="{$prevMonthUrl}">«</a>&nbsp;&nbsp;&nbsp;<a href="{$nowMonthUrl}">{$nowYear}年{$nowMonth}月</a>&nbsp;&nbsp;&nbsp;<a href="{$nextMonthUrl}">»</a></caption>
    <thead><tr>{for $i = 1; $i <= 7; $i++}<th title="{$lang['week'][$i]}" scope="col"><small>{$lang['week_abbr'][$i]}</small></th>{/for}</tr></thead>
    <tbody>
    <tr>
{php}
$numberOfDays = date('t', strtotime($date));
$dayOfWeek = date('N', strtotime($date . '-1'));
$lastDayOfWeek = 7 - date('N', strtotime($date . '-' . $numberOfDays));
$dayOfWeekColspan = $dayOfWeek - 1;
$lastDayOfWeekColspan = $lastDayOfWeek - 1;
{/php}
{if $dayOfWeek > 1}<td class="pad" colspan="{$dayOfWeekColspan}"></td>{/if}
{php}
$weekCounter = $dayOfWeek - 1;
for ($i = 1; $i <= $numberOfDays; $i++) {
	{/php}<td>{if isset($arraydate[$i])}<a href="{$arraydate[$i]['Url']}" title="{$arraydate[$i]['Date']} ({$arraydate[$i]['Count']})" target="_blank">{$i}</a>{else}{$i}{/if}</td>{php}
	$weekCounter++;
	if ($weekCounter % 7 == 0) {
    {/php}</tr><tr>{php}
    }
}
{/php}
{if $lastDayOfWeek > 1}<td class="pad" colspan="{$lastDayOfWeekColspan}"> </td>{/if}
	</tr></tbody>
</table>