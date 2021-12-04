<div class="post multi">
	<p class="post-date">{$article.Time('Y年m月d日')}</p>
	<h2 class="post-title"><a href="{$article.Url}">{$article.Title}</a></h2>
	<div class="post-body">{$article.Intro}</div>
	<p class="post-footer">
		作者:{$article.Author.StaticName}&nbsp;,&nbsp;分类:{$article.Category.Name}&nbsp;,&nbsp;浏览:{$article.ViewNums}&nbsp;,&nbsp;评论:{$article.CommNums}
	</p>
</div>