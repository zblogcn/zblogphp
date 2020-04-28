{if $cfg.MobileSide}
{foreach $sidebar6 as $module}
<div class="pane onlym" id="{$module.HtmlID}">
  {if (!$module.IsHideTitle)&&($module.Name)}<h4 class="zit">{$module.Name}</h4>{/if}
  <{$module.Type}>{$module.Content}</{$module.Type}>
</div>
{/foreach}
{/if}