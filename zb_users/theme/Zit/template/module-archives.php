{if $style==1}
<select id="slArchives" onchange="window.location=this.options[this.selectedIndex].value">
{foreach $urls as $url}
<option value="{$url.Url}">{$url.Name} ({$url.Count})</option>
{/foreach}
</select>
{else}
{foreach $urls as $url}
<li class="stock"><a href="{$url.Url}" class="kico-calendar kico-gap">{$url.Name} <mark>{$url.Count}</mark></a></li>
{/foreach}
{/if}