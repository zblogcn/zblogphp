{* Template Name:列表页普通文章 *}
<div class="post multi">
	<h2 class="post-title"><a href="{$article.Url}">{$article.Title}</a><span class="post-date">{$article.Time()}</span></h2>
	<div class="post-body">{$article.Intro}</div>
	<p class="post-footer">
		{$lang['msg']['author']}:{$article.Author.StaticName} <small>|</small> {$lang['msg']['category']}:{$article.Category.Name} <small>|</small> {$lang['default']['view']}:{$article.ViewNums} <small>|</small> {$lang['msg']['comment']}:{$article.CommNums}
	</p>
</div>