{template:header}
<body class="multi {$type}">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a href="{$host}">{$name}</a></h1>
			<h2 id="BlogSubTitle">{$subname}</h2>
		</div>
		<div id="divNavBar">
<hr/>
<ul>
{module:navbar}
</ul>
<hr/>
		</div>
		<div id="divMain">
{foreach $articles as $article}

{if $article.TopType}
{template:post-istop}
{else}
{template:post-multi}
{/if}

{/foreach}
<div class="pagebar">{template:pagebar}</div>
		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}