{template:header}
<body class="single {$type}">
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
{$modules['navbar'].Content}
</ul>
<hr/>
		</div>
		<div id="divMain">
{if $article.Type==ZC_POST_TYPE_ARTICLE}
{template:post-single}
{else}
{template:post-page}
{/if}
		</div>
		<div id="divSidebar">
{template:sidebar}
		</div>
{template:footer}