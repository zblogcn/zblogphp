{template:header}
<body class="multi default">
{template:c_top}
		<div id="mission">{module:new}</div><ins></ins>
{foreach $articles as $article}
{if $article.IsTop}
{template:post-istop}
{else}
{template:post-multi}
{/if}
{/foreach}
		<div class="pager"><span class="pager-list">{template:pagebar}</span></div> 

		<span class="clear"></span><a href="{$feedurl}" class="feed-icon"><img src="{$host}zb_users/theme/garland/style/rss-sq.png" alt="聚合内容" title="聚合内容" height="16" width="16"></a>

{template:footer}