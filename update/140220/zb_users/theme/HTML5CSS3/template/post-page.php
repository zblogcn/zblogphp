<article id="log{$article.ID}">
  <header>
    <h2>{$article.Title}</h2>
  </header>
  <section>{$article.Content}</section>
  <footer></footer>
</article>

{if !$article.IsLock}
{template:comments}
{/if}