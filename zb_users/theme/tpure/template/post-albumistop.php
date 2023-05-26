{* Template Name:图集列表单条置顶文章 *}
{if $page != '1' && $zbp->Config('tpure')->PostISTOPINDEXON == '1'}
{else}
<div class="item">
	{if $zbp->Config('tpure')->PostIMGON}
	<div class="albumimg{if $article->Metas->video} v{/if}">
		<a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}><em>{$lang['tpure']['istop']}</em>{if $type != 'category'}<span>{$article.Category.Name}</span>{/if}<img src="{if tpure_Thumb($article,1)}{tpure_Thumb($article,1)}{else}{$host}zb_users/theme/{$theme}/style/images/dot.jpg{/if}" alt="{$article.Title}" /></a>
	</div>
	{/if}
	<div class="albumcon">
		<a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}>{$article.Title}</a>
		<p>{if $zbp->Config('tpure')->PostINTRONUM}
            {php}$intro = preg_replace('/[\r\n\s]+/', ' ', trim(SubStrUTF8(TransferHTML($article->Intro,'[nohtml]'),$zbp->Config('tpure')->PostINTRONUM)).'...');{/php}
            {if $type==='search'}
                {$intro=preg_replace('/' . preg_quote(GetVars('q'),'/') . '/i',"<mark>$0</mark>",$intro)}
            {/if}
            {$intro}{else}{$article.Intro}{/if}
        </p>
	</div>
	<div class="albuminfo"><span>{tpure_TimeAgo($article->Time())}</span></div>
</div>
{/if}