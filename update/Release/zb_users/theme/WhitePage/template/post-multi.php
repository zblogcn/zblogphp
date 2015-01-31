<div class="post multi">
	<h4 class="post-date">{$article.Time('Y年m月d日')}</h4>
	<h2 class="post-title"><a href="{$article.Url}">{$article.Title}</a></h2>
	<div class="post-body">{$article.Intro}</div>
	<h6 class="post-footer">
		作者:{$article.Author.Name}&nbsp;,&nbsp;分类:{$article.Category.Name}&nbsp;,&nbsp;浏览:{$article.ViewNums}&nbsp;,&nbsp;评论:{$article.CommNums}
	</h6>
</div>