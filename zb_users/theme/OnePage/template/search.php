{template:header}
<body class="multi {$type}">
<div class="background">
    <div class="background-image"></div>
    <div class="background-mask"></div>
</div>
<div class="mainmenu">
<ul>
{module:menu}
</ul>
</div>
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="{$host}">{$name}</a></h1>
			<h3 id="BlogSubTitle">{$subname}</h3>
		</div>
		<div id="divNavBar">
<hr/>
<ul>
{module:navbar}
</ul>
<hr/>
		</div>
		<div id="divMain">
<div class="post istop">
	<h2 class="post-title">{$article.Title}</h2>
</div>
{foreach $articles as $article}
{template:post-search}
{/foreach}
{if count($articles)>0}
<div class="pagebar">{template:pagebar}</div>
{/if}
		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}