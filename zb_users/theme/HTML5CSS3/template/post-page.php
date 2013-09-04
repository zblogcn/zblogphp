<article id="log{$article.ID}">
  <header>
    <h2>{$article.Title}</h2>
  </header>
  <section>{$article.Content}</section>
  <footer></footer>
  <nav>
{if $article.Prev}
<a class="l" href="{$article.Prev.Url}" title="{$article.Prev.Title}">« 上一篇</a>
{/if}
{if $article.Next}
<a class="r" href="{$article.Next.Url}" title="{$article.Next.Title}"> 下一篇 »</a>
{/if}
  </nav>
</article>

{if !$article.IsLock}
{template:comments}
{/if}