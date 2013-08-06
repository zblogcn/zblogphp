{template:header}
	<link rel="alternate" type="application/rss+xml" href="{$feedurl}" title="{$name}" />
</head>
<body class="multi default">
{template:c_top}
		<div id="mission"></div><ins></ins>
{foreach $articles as $article}

{template:post-multi}

{/foreach}
		<div class="pager"><span class="pager-list">{template:pagebar}</span></div> 

		<span class="clear"></span><a href="{$feedurl}" class="feed-icon"><img src="{$host}zb_users/theme/garland/style/rss-sq.png" alt="聚合内容" title="聚合内容" height="16" width="16"></a>

{template:footer}