<div class="widget widget_{$module.HtmlID}">
  {if (!$module.IsHideTitle)&&($module.Name)}<h3 class="widget_tit">{$module.Name}</h3>{/if}
  {if $module.Type=='div'}
	<div>{$module.Content}</div>
	{/if}

	{if $module.Type=='ul'}
	<ul>{$module.Content}</ul>
	{/if}
</div>