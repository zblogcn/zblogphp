{* Template Name:文章页文章内容 *}
<div class="post single">
	<h2 class="post-title">{$article.Title}<span class="post-date">{$article.Time()}</span></h2>
	<div class="post-body">{$article.Content}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		{$lang['msg']['author']}:{$article.Author.StaticName} <small>|</small> {$lang['msg']['category']}:{$article.Category.Name} <small>|</small> {$lang['default']['view']}:{$article.ViewNums} <small>|</small> {$lang['msg']['comment']}:{$article.CommNums}
	</h6>
</div>

{if !$article.IsLock}
{template:comments}
{/if}