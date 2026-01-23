{foreach $authors as $author}
<li><a title="{$author->StaticName}" href="{$author->Url}">{$author->StaticName}<span class="article-nums"> ({$author->Articles})</span></a></li>
{/foreach}
