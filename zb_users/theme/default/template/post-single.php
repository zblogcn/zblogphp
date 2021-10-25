{* Template Name:文章页文章内容 *}
<div class="post single">
	<h2 class="post-title">{$article.Title}<span class="post-date">{$article.Time()}</span></h2>
	<div class="post-body">{$article.Content}</div>
	<p class="post-tags">
                {if count($article.Tags)>0}{$lang['msg']['tags']}:{foreach $article.Tags as $i => $tag}&nbsp;<a href='{$tag.Url}' title='{$tag.Name}'>{$tag.Name}</a>&nbsp;{if count($article.Tags) > $i}<small>,</small>{/if}{/foreach}{/if}
	</p>
	<p class="post-footer">
		{$lang['msg']['author']}:{$article.Author.StaticName} <small>|</small> {$lang['msg']['category']}:{$article.Category.Name} <small>|</small> {$lang['default']['view']}:{$article.ViewNums} <small>|</small> {$lang['msg']['comment']}:{$article.CommNums}
	</p>
</div>

{if !$article.IsLock}
{template:comments}
{/if}