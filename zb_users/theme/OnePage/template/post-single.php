<div class="post single">
	<p class="post-date">{$article.Time('Y年m月d日')}</p>
	<h2 class="post-title">{$article.Title}</h2>
	<div class="post-body">{$article.Content}</div>
	{if Count($article.Tags)>0}<p class="post-tags">标签：{foreach $article.Tags as $tag}<a href="{$tag.Url}" target="_blank">{$tag.Name}</a>&nbsp;{/foreach}</p>{/if}
	<p class="post-footer">
		作者:{$article.Author.StaticName}&nbsp;,&nbsp;分类:{$article.Category.Name}&nbsp;,&nbsp;浏览:{$article.ViewNums}&nbsp;,&nbsp;评论:{$article.CommNums}
	</p>
</div>

{if !$article.IsLock}
{template:comments}
{/if}