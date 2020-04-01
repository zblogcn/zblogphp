{*Template Name:首页*}
{template:header}
<body class="{$type}">
<div class="wrapper">
    {template:navbar}
    <div class="main{if $zbp->Config('tpure')->PostFIXMENUON=='1'} fixed{/if}">
        <div class="banner display"{if $zbp->Config('tpure')->PostBANNER} style="background-image:url({$zbp->Config('tpure')->PostBANNER});"{/if}><div class="wrap"><h2>{$subname}</h2></div></div>
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
                {template:sidebar}
            </div>
        </div>
    </div>
    {template:footer}