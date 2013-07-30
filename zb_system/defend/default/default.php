{template:header}
	<link rel="alternate" type="application/rss+xml" href="{$host}feed.php" title="{#ZC_BLOG_TITLE#}" />
</head>
<body class="multi default">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="{$host}">{$blogtitle}</a></h1>
			<h3 id="BlogSubTitle">{$blogsubtitle}</h3>
		</div>
		<div id="divNavBar">
<ul>
{$modules['navbar'].Content}
</ul>
		</div>
		<div id="divMain">
{for $i=0;$i<=10;$i++}
{$j=$i}
{/for}
<div class="post pagebar">{template:pagebar}</div>
		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}