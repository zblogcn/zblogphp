<article class="top cate{$article.Category.ID} auth{$article.Author.ID}">
  <header>
    <time>{$article.Time('Y年m月d日')}</time>
    <h3><a href="{$article.Url}">[置顶] {$article.Title}</a></h3>
  </header>
</article>