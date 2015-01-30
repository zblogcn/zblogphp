{if $pagebar}
{foreach $pagebar.buttons as $k=>$v}
  {if $pagebar.PageNow==$k}
	<span class="page now-page">{$k}</span>
  {else}
	<a href="{$v}"><span class="page">{$k}</span></a>
  {/if}
{/foreach}
{/if}