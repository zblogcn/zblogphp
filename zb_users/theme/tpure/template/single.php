{*Template Name:文章页/单页模板*}
{template:header}
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="mask"></div>
        <div class="wrap">
            {if $article.Type==ZC_POST_TYPE_ARTICLE}
                {template:post-single}
            {else}
                {template:post-page}
            {/if}
        </div>
    </div>
    {template:footer}