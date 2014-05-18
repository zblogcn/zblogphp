<article id="log{$article.ID}" class="top cate{$article.Category.ID} auth{$article.Author.ID}">
  <header>
    <time>{$article.Time('Y年m月d日')}</time>
    <h2><a href="{$article.Url}">{$article.Title}</a></h2>
  </header>
  <section>{$article.Intro}</section>
  <footer>
{if $article.Tags}
    <h4>标签: {foreach $article.Tags as $tag}<a href="{$tag.Url}">{$tag.Name}</a>{/foreach}</h4>
{/if}
    <h5><em>作者:{$article.Author.StaticName}</em> <em>分类:{$article.Category.Name}</em> <em>浏览:{$article.ViewNums}</em> <em>评论:{$article.CommNums}</em></h5>
  </footer>
</article>

