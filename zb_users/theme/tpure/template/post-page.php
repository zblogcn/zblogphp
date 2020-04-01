<div class="content">
    <div class="block">
        <div class="post">
            <h1>{$article.Title}</h1>
            <div class="info">
                {php}
                $post_info = array(
                    'user'=>$article->Author->StaticName,
                    'date'=>tpure_TimeAgo($article->Time()),
                    'view'=>$article->ViewNums,
                    'cmt'=>$article->CommNums,
                );
                $page_info = json_decode($zbp->Config('tpure')->PostPAGEINFO,true);
                if(count($page_info)){
                    foreach($page_info as $key => $info){
                        echo $info==1?'<span class="'.$key.'">'.$post_info[$key].'</span>':'';
                    }
                }
                {/php}
            </div>
            <div class="single postcon">
                {$article.Content}
            </div>
        </div>
    </div>
{if !$article.IsLock && $zbp->Config('tpure')->PostPAGECMTON=='1'}
    {template:comments}
{/if}
</div>
<div class="sidebar">
    {template:sidebar4}
</div>