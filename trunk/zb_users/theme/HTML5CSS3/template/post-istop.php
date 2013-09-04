<article class="top cate{$article.Category.ID} auth{$article.Author.ID}">
  <header>
    <time>{$article.Time('Y年m月d日')}</time>
    <h2><a href="{$article.Url}">[置顶] {$article.Title}</a></h2>
  </header>
</article>