<div class="post single">
	<h4 class="post-date">{$article.Time('Y年m月d日')}</h4>
	<h2 class="post-title">{$article.Title}</h2>
	<div class="post-body">{$article.Content}</div>
	{if Count($article.Tags)>0}<h5 class="post-tags">标签：{foreach $article.Tags as $tag}<a href="{$tag.Url}" target="_blank">{$tag.Name}</a>&nbsp;{/foreach}</h5>{/if}
	<h6 class="post-footer">
		作者:{$article.Author.Name}&nbsp;,&nbsp;分类:{$article.Category.Name}&nbsp;,&nbsp;浏览:{$article.ViewNums}&nbsp;,&nbsp;评论:{$article.CommNums}
	</h6>
</div>

{if !$article.IsLock}
{template:comments}
{/if}