{foreach $urls as $url}
<li><a href="{$url.Url}">{$url.Name} ({$url.Count})</a></li>
{/foreach}