{* Template Name:文章页文章内容 *}
<div class="post single">
	<h4 class="post-date">{$article.Time()}</h4>
	<h2 class="post-title">{$article.Title}</h2>
	<div class="post-body">{$article.Content}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		{$lang['msg']['author']}:{$article.Author.StaticName} | {$lang['msg']['category']}:{$article.Category.Name} | {$lang['default']['view']}:{$article.ViewNums} | {$lang['msg']['comment']}:{$article.CommNums}
	</h6>
</div>

{if !$article.IsLock}
{template:comments}
{/if}