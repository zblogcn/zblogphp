<article id="log{$article.ID}" class="top cate{$article.Category.ID} auth{$article.Author.ID}">
  <header>
    <time>{$article.Time('Y年m月d日 H:i:s')}</time>
    <h2>{$article.Title}</h2>
  </header>
  <section>{$article.Content}</section>
  <footer>
{if $article.Tags}
    <h4>标签: {foreach $article.Tags as $tag}<a href="{$tag.Url}">{$tag.Name}</a>{/foreach}</h4>
{/if}
    <h5><em>作者:{$article.Author.StaticName}</em> <em>分类:{$article.Category.Name}</em> <em>浏览:{$article.ViewNums}</em> <em>评论:{$article.CommNums}</em></h5>
  </footer>
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