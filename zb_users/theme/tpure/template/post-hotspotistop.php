{* Template Name:热点列表页置顶文章 *}
{if $page != '1' && $zbp->Config('tpure')->PostISTOPINDEXON == '1'}
{else}
<div class="post item">
    {if tpure_Thumb($article) != ''}<div class="postimg{if $article->Metas->video} v{/if}"><a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}><img src="{tpure_Thumb($article)}" alt="{$article.Title}" /></a></div>{/if}
    <div class="hotspotinfo{if !tpure_Thumb($article)} noimg{/if}">
        <h2><a href="{$article.Url}"{if $zbp->Config('tpure')->PostBLANKSTYLE == 2} target="_blank"{/if}>{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '0'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}{$article.Title}{if $zbp->Config('tpure')->PostMEDIAICONSTYLE == '1'}{if $article.Metas.audio}<span class="zbaudio"></span>{/if}{if $article.Metas.video}<span class="video"></span>{/if}{/if}</a><em class="istop">{$lang['tpure']['istop']}</em></h2>
        {$zbp->Config('tpure')->PostINTROSOURCE == '1' ? $introsource = $article->Content : $introsource = $article->Intro}
        <div class="intro{if tpure_Thumb($article) != ''} isimg{/if}">
            {if tpure_isMobile()}<a href="{$article.Url}">{/if}
                {if $zbp->Config('tpure')->PostINTRONUM}
                {php}$intro = preg_replace('/[\r\n\s]+/', ' ', trim(SubStrUTF8(TransferHTML($introsource,'[nohtml]'),$zbp->Config('tpure')->PostINTRONUM)).'...');{/php}
                {if $type==='search'}
                    {$intro=preg_replace('/' . preg_quote(GetVars('q'),'/') . '/i',"<mark>$0</mark>",$intro)}
                {/if}
                {$intro}{else}{$article.Intro}{/if}
            {if tpure_isMobile()}</a>{/if}
        </div>
        <div class="info">
            {php}
            $post_info = array(
                'user'=>'<a href="'.$article->Author->Url.'" rel="nofollow">'.$article->Author->StaticName.'</a>',
                'date'=>tpure_TimeAgo($article->Time()),
                'cate'=>'<a href="'.$article->Category->Url.'">'.$article->Category->Name.'</a>',
                'view'=>$article->ViewNums,
                'cmt'=>$article->CommNums,
                'edit'=>'<a href="'.$host.'zb_system/cmd.php?act=ArticleEdt&id='.$article->ID.'" target="_blank">'.$lang['tpure']['edit'].'</a>',
                'del'=>'<a href="'.$host.'zb_system/cmd.php?act=ArticleDel&id='.$article->ID.'&csrfToken='.$zbp->GetToken().'" data-confirm="'.$lang['tpure']['delconfirm'].'">'.$lang['tpure']['del'].'</a>',
            );
            $list_info = json_decode($zbp->Config('tpure')->PostLISTINFO,true);
            if(count((array)$list_info)){
                foreach($list_info as $key => $info){
                    if($info == '1'){
                        if($user->Level == '1'){
                            echo '<span class="'.$key.'">'.$post_info[$key].'</span>';
                        }else{
                            if($key == 'edit' || $key == 'del'){
                                echo '';
                            }else{
                                echo '<span class="'.$key.'">'.$post_info[$key].'</span>';
                            }
                        }
                    }
                }
            }else{
                echo '<span class="user"><a href="'.$article->Author->Url.'" rel="nofollow">'.$article->Author->StaticName.'</a></span>
                <span class="date">'.tpure_TimeAgo($article->Time()).'</span>
                <span class="view">'.$article->ViewNums.'</span>';
            }
            {/php}
        </div>
    </div>
</div>
{/if}