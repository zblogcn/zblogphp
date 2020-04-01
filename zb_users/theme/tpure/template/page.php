{*Template Name:通栏文章模板无侧栏*}
{template:header}
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="mask"></div>
        <div class="wrap">
            {if $article.Type==ZC_POST_TYPE_ARTICLE}
                {template:post-widesingle}
            {else}
                {template:post-widepage}
            {/if}
        </div>
    </div>
    {template:footer}