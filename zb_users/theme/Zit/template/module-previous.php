{foreach $articles as $article}
<li class="illus"><a href="{$article.Url}" title="{$article.Title}">{if $article->Cover}<img src="{$article->Cover}" alt="{$article.Title}" class="cover">{/if}<small>{$article.Time('Y-m-d')}</small>{$article.Title}</a></li>
{/foreach}