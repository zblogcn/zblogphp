{if $module.IsHidden==false}

<dl class="function" id="{$module.HtmlID}">
{if $module.IsHideTitle==false}<dt class="function_t">{$module.Name}</dt>{else} {/if}
<dd class="function_c">

{if $module.Type=='div'}
<div>{$module.Content}</div>
{/if}

{if $module.Type=='ul'}
<ul>{$module.Content}</ul>
{/if}

</dd>
</dl>

{/if}