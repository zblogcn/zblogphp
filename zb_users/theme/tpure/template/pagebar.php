{if $pagebar && $pagebar.PageAll > 1}
    {foreach $pagebar.buttons as $key => $v}
        {$page_item = tpure_GetPagabarAlias($key)}
        {if $key == $pagebar.PageNow}
        <span class="now-page">{$page_item[1]}</span>
        {elseif $pagebar.PageNow+1==$key}
        <span class="next-page"><a href="{$v}">{$page_item[1]}</a></span>
        {else}
        <a href="{$v}">{$page_item[1]}</a>
        {/if}
    {/foreach}
{/if}

