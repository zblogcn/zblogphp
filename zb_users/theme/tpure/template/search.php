{*Template Name:搜索页模板*}
{template:header}
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="mask"></div>
        <div class="wrap">
            <div class="content">
                <div class="block">
                    <div class="contitle">
                        <h2>{if $type=='category'}{$category.Name}{elseif $type=='tag'}{$tag.Name}{elseif $type=='author'}{$author.Name}{else}{$title}{/if}</h2>
                    </div>
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
                {template:sidebar5}
            </div>
        </div>
    </div>
    {template:footer}