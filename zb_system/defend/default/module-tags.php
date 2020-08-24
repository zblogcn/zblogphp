{foreach $tags as $tag}
<li><a title="{$tag.Name}" href="{$tag.Url}">{$tag.Name}<span class="tag-count"> ({$tag.Count})</span></a></li>
{/foreach}