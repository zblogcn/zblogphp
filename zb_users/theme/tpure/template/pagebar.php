{if $pagebar && $pagebar.PageAll > 1}
    {foreach $pagebar.buttons as $k => $v}
        {if $pagebar.PageNow==$k}
        <span class="now-page">{$k}</span>
        {elseif $pagebar.PageNow+1==$k}
        <span class="next-page"><a href="{$v}">{$k}</a></span>
        {elseif $k == $zbp.lang['tpure']['index'] || $k == $zbp.lang['tpure']['prevpage'] || $k == $zbp.lang['tpure']['nextpage'] || $k == $zbp.lang['tpure']['endpage']}
        <a href="{$v}" class="m">{$k}</a>
        {else}
		<a href="{$v}">{$k}</a>
        {/if}
    {/foreach}
{/if}