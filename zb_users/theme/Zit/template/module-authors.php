{foreach $authors as $author}
<li class="stock"><a href="{$author.Url}" title="{$author.Name}{$msg->logs}" class="kico-portrait">{$author.Name}  <mark>{$author.Articles}</mark></a></li>
{/foreach}
