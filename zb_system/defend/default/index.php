{* Template Name:首页及列表页 * Template Type:list,index *}
{template:header}
<body class="multi {$type}">
<div id="divAll">
	<div id="divPage">
	<div id="divMiddle">
		<div id="divTop">
			<h1 id="BlogTitle"><a title="{$name}" href="{$host}">{$name}</a></h1>
			<h3 id="BlogSubTitle">{$subname}</h3>
		</div>
		<div id="divNavBar">
<ul>
{module:navbar}
</ul>
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