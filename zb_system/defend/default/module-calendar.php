<table id="tbCalendar">
    <caption><a title="{$lang['msg']['prev_month']}" href="{$prevMonthUrl}">«</a>&nbsp;&nbsp;&nbsp;<a href="{$nowMonthUrl}">
{if $option['ZC_BLOG_LANGUAGEPACK'] == 'zh-cn' || $option['ZC_BLOG_LANGUAGEPACK'] == 'zh-tw'}
    {php}echo str_replace(array('%y%', '%m%'), array($nowYear, $nowMonth), $lang['msg']['year_month']);{/php}
{else}
    {date("F , Y", mktime(0, 0, 0, $nowMonth, 1, $nowYear))}
{/if}
    </a>&nbsp;&nbsp;&nbsp;<a title="{$lang['msg']['next_month']}" href="{$nextMonthUrl}">»</a></caption>
    <thead><tr>{for $i = 1; $i <= 7; $i++}<th title="{$lang['week'][$i]}" scope="col"><small>{$lang['week_abbr'][$i]}</small></th>{/for}</tr></thead>
    <tbody>
    {php}
    $days = date('t', strtotime($date)); //一个月多少天
    $dayOfWeek = date('N', strtotime($date . '-1')); //1号是星期几
    $numberOfDays = range(1,$days);
    $weeks = array_chunk(array_pad($numberOfDays, -1 * $days - --$dayOfWeek, 0), 7);
    {/php}
    {foreach $weeks as $key => $week}
<tr>{foreach $week as $k => $day}<td>{if isset($arraydate[$day])}<a href="{$arraydate[$day]['Url']}" title="{$arraydate[$day]['Date']} ({$arraydate[$day]['Count']})" target="_blank">{$day}</a>{elseif $day}{$day}{/if}</td>{/foreach}
{if $key == max(array_keys($weeks)) && $k < 6}{str_pad('',(7 - count($week)) * 9,"<td></td>")}{/if}</tr>
    {/foreach}
	</tbody>
</table>