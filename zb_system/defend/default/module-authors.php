{foreach $authors as $author}
<li><a title="{$author->Name}" href="{$author->Url}">{$author->Name}<span class="article-nums"> ({$author->Articles})</span></a></li>
{/foreach}
