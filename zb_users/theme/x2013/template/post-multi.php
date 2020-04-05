<article class="excerpt  cate{$article.Category.ID} auth{$article.Author.ID}">
	<h2>
	  <a href="{$article.Url}" title="{$article.Title} - {$name}">{$article.Title}</a>
	</h2>
	<div class="info">
		<time class="time">{$article.Time('m月d日')}</time> 
		<a class="comm" href="{$article.Url}#comments" title="查看 {$article.Title} 的评论">
		  {$article.CommNums}条评论
		</a> 
		<span class="view">{$article.ViewNums} ℃</span>
	</div>
	<div class="note">{$article.Intro}...</div>
</article>