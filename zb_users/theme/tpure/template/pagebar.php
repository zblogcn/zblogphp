{if $pagebar && $pagebar.PageAll > 1}
    {foreach $pagebar.buttons as $k => $v}
        {if $pagebar.PageNow==$k}
        <span class="now-page">{$k}</span>
        {elseif $pagebar.PageNow+1==$k}
        <span class="next-page"><a href="{$v}">{$k}</a></span>
        {else}
        <a href="{$v}">{$k}</a>
        {/if}
    {/foreach}
{/if}