<div class="content wide">
    <div class="block">
        <div class="post">
            <h1>{$article.Title}</h1>
            <div class="info">
                {php}
                $post_info = array(
                    'user'=>$article->Author->StaticName,
                    'date'=>tpure_TimeAgo($article->Time()),
                    'cate'=>'<a href="'.$article->Category->Url.'" target="_blank">'.$article->Category->Name.'</a>',
                    'view'=>$article->ViewNums,
                    'cmt'=>$article->CommNums,
                );
                $article_info = json_decode($zbp->Config('tpure')->PostARTICLEINFO,true);
                if(count($article_info)){
                    foreach($article_info as $key => $info){
                        echo $info==1?'<span class="'.$key.'">'.$post_info[$key].'</span>':'';
                    }
                }else{
                    echo '<span class="user">{$article.Author.StaticName}</span>
                    <span class="date">{tpure_TimeAgo($article.Time())}</span>
                    <span class="view">{$article.ViewNums}</span>';
                }
                {/php}
            </div>
            <div class="single">
                {$article.Content}
                {if count($article.Tags)>0}
                <div class="tags">
                    {$lang['tpure']['tags']}
                    {foreach $article.Tags as $tag}<a href='{$tag.Url}' title='{$tag.Name}'>{$tag.Name}</a>{/foreach}
                </div>
                {/if}
            </div>
        </div>
        <div class="pages">
            <a href="{$article.Category.Url}" class="backlist">{$lang['tpure']['backlist']}</a>
            <p>{if $article.Prev}{$lang['tpure']['prev']}<a href="{$article.Prev.Url}" class="single-prev">{$article.Prev.Title}</a>{else}<span>{$lang['tpure']['noprev']}</span>{/if}</p>
            <p>{if $article.Next}{$lang['tpure']['next']}<a href="{$article.Next.Url}" class="single-next">{$article.Next.Title}</a>{else}<span>{$lang['tpure']['nonext']}</span>{/if}</p>
        </div>
    </div>
{template:mutuality}
{if !$article.IsLock && $zbp->Config('tpure')->PostARTICLECMTON=='1'}
    {template:comments}
{/if}
</div>