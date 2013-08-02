<div class="post multi-post">
	<h4 class="post-date">{$article->Time('Y年m月d日')}</h4>
	<h2 class="post-title"><a href="{$article->Url}">{$article->Title}</a></h2>
	<div class="post-body">{$article->Intro}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		作者:{$article->Author->Name} | 分类:{$article->Category->Name} | 浏览:{$article->ViewNums} | 评论:{$article->CommNums}
	</h6>
</div>

<#template:article_mutuality:begin#>
<ul class="msg mutuality">
	<li class="tbname"><#msg231#>:</li>
	<li class="msgarticle"><#template:article_mutuality#></li>
</ul>
<#template:article_mutuality:end#>


<#template:article_comment:begin#>
<ul class="msg msghead">
	<li class="tbname"><#msg211#>:</li>
</ul>
<#template:article_comment#>
<#template:article_comment_pagebar#>
<#template:article_comment:end#>

<#template:article_commentpost#>