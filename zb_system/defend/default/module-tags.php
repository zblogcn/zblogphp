{foreach $urls as $url}
<li><a href="{$url[0]}">{$url[1]}<span class="tag-count"> ({$url[2]})</span></a></li>'
{/foreach}