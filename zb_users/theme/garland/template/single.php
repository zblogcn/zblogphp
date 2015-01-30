{template:header}
<body class="multi default">
{template:c_top}
		<div id="mission">{$modules['new'].Content}</div><ins></ins>
{if $article.Type==ZC_POST_TYPE_ARTICLE}
{template:post-single}
{else}
{template:post-page}
{/if}
		<span class="clear"></span><a href="{$feedurl}" class="feed-icon"><img src="{$host}zb_users/theme/garland/style/rss-sq.png" alt="聚合内容" title="聚合内容" height="16" width="16"></a>

{template:footer}