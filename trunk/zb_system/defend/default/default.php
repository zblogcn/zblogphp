{template:header}
	<link rel="alternate" type="application/rss+xml" href="{$host}feed.php" title="{$name}" />
</head>
<body class="multi default">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="{$host}">{$name}</a></h1>
			<h3 id="BlogSubTitle">{$subname}</h3>
		</div>
		<div id="divNavBar">
<ul>
{$modules['navbar'].Content}
</ul>
		</div>
		<div id="divMain">
{foreach $articles as $article}

<div class="post multi-post">
	<h4 class="post-date">{$article->Time('Y年m月d日')}</h4>
	<h2 class="post-title"><a href="{$article->PostTime}">{$article->Title}</a></h2>
	<div class="post-body">{$article->Intro}</div>
	<h5 class="post-tags"></h5>
	<h6 class="post-footer">
		作者:{$article->Author->Name} | 分类:{$article->Category->Name} | 浏览:{$article->ViewNums} | 评论:{$article->CommNums}
	</h6>
</div>

{/foreach}
<div class="post pagebar">{template:pagebar}</div>
		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}