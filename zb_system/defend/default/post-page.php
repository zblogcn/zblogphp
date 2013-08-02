<div class="post multi-post">
	<h4 class="post-date">{$article->Time('Y年m月d日')}</h4>
	<h2 class="post-title"><a href="{$article->Url}">{$article->Title}</a></h2>
	<div class="post-body">{$article->Intro}</div>
</div>
<#template:article_comment:begin#>
<ul class="msg msghead">
	<li class="tbname"><#msg211#>:</li>
</ul>
<#template:article_comment#>
<#template:article_comment_pagebar#>
<#template:article_comment:end#>

<#template:article_commentpost#>