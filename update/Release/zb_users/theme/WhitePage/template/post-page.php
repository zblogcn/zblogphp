<div class="post page">
	<h2 class="post-title">{$article.Title}</h2>
	<div class="post-body">{$article.Content}</div>
</div>

{if !$article.IsLock}
{template:comments}
{/if}