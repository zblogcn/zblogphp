<dl id="{$module.HtmlID}" class="sidebox">
    {if (!$module.IsHideTitle)&&($module.Name)}<dt class="sidetitle">{$module.Name}</dt>{else}<dt></dt>{/if}
    <dd>
        {if $module.Type=='div'}
		<div>{$module.Content}</div>
		{/if}
		{if $module.Type=='ul'}
		<ul>{$module.Content}</ul>
		{/if}
    </dd>
</dl>