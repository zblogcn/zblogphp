{foreach $authors as $author}
<li><a href="{$author->Url}">{$author->Name}<span class="article-nums"> ({$author->Articles})</span></a></li>
{/foreach}
