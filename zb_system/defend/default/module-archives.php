{if $style==1}
<select id="slArchives" onchange="window.location=this.options[this.selectedIndex].value">
{foreach $urls as $url}
<option value="{$url.Url}">{$url.Name} ({$url.Count})</option>
{/foreach}
</select>
{else}
{foreach $urls as $url}
<li><a title="{$url.Name}" href="{$url.Url}">{$url.Name} ({$url.Count})</a></li>
{/foreach}
{/if}