{*Template Name:列表页模板*}
{template:header}
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="mask"></div>
        <div class="wrap">
            <div class="content">
                <div class="block">
                    {foreach $articles as $article}
                        {if $article.IsTop}
                        {template:post-istop}
                        {else}
                        {template:post-multi}
                        {/if}
                    {/foreach}
                </div>
                <div class="pagebar">
                    {template:pagebar}
                </div>
            </div>
            <div class="sidebar">
                {template:sidebar2}
            </div>
        </div>
    </div>
    {template:footer}