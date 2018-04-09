{* Template Name:列表页普通文章 *}
<div class="post multi">
	<h4 class="post-date">{$article.Time()}</h4>
	<h2 class="post-title"><a href="{$article.Url}">{$article.Title}</a></h2>
	<div class="post-body">{$article.Intro}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		{$lang['msg']['author']}:{$article.Author.StaticName} | {$lang['msg']['category']}:{$article.Category.Name} | {$lang['default']['view']}:{$article.ViewNums} | {$lang['msg']['comment']}:{$article.CommNums}
	</h6>
</div>