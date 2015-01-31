<div class="post multi">
	<h4 class="post-date">{$article.Time('Y年m月d日')}</h4>
	<h2 class="post-title"><a href="{$article.Url}">{$article.Title}</a></h2>
	<div class="post-body">{$article.Intro}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		作者:{$article.Author.StaticName} | 分类:{$article.Category.Name} | 浏览:{$article.ViewNums} | 评论:{$article.CommNums}
	</h6>
</div>