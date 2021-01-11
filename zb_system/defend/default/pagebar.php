{* Template Name:分页 *}
{if $pagebar}
{foreach $pagebar.buttons as $k=>$v}
  {if $pagebar.isFullLink==false && $pagebar.PageNow==$k}
	<span class="page now-page">{$k}</span>
  {else}
	<a title="{$k}" href="{$v}"><span class="page">{$k}</span></a>
  {/if}
{/foreach}
{/if}