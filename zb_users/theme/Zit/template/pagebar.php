{if $pagebar}
<nav id="pagi">
{foreach $pagebar.buttons as $k=>$v}
  {if $pagebar.PageNow==$k}
  <b class="zit">{$k}</b>
  {else}
  <a href="{$v}">{$k}</a>
  {/if}
{/foreach}
</nav>
{/if}