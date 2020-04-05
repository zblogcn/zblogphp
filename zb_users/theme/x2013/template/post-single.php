<article class="article">
	<header class="article-header">
		<h1 class="article-title">{$article.Title}</h1>
		<p class="article-meta">
			{$article.Time('m月d日')}
			分类: <a href="{$article.Category.Url}" title="查看 {$article.Category.Name} 中的全部文章" rel="category tag">{$article.Category.Name}</a>
			<a class="comm" href="{$article.Url}#comments" title="查看 {$article.Title} 的评论">{$article.CommNums}条评论</a>
			<span class="view">{$article.ViewNums} ℃</span>
		</p>
	</header>
	<div class="db_post">{$x2013_adheader}</div>
	<div class="article-entry">{$article.Content}</div>
	<div class="db_post" style="margin:-15px 0 15px 0;">{$x2013_adfooter}</div>

	{if $article.Tags}
	<br/>
	<footer class="article-footer"><div class="article-tags">标签:
	{foreach $article.Tags as $tag}
	<a href="{$tag.Url}">{$tag.Name}</a>
	{/foreach}
	</div></footer>
	{/if}

</article>
{template:comments}