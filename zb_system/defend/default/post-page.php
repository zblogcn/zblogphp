<div class="post page">
	<h2 class="post-title">{$article.Title}</h2>
	<div class="post-body">{$article.Content}</div>
</div>

{if $article.CommNums>0}
<#评论输出#>
{/if}

{if !$article.IsLock}
<#评论框#>
{/if}