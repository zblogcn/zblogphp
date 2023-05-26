<article id="topic"{if $cfg->HideRand} class="hidem"{/if}>
  {if $type!=='index'}<nav id="path" class="kico-guide kico-gap"><a href="{$host}">{$msg.home}</a> | <a>{$title}</a></nav>{/if}
  {$skip=array()}
  {foreach $articles as $v}
    {$skip[]=$v->ID}
  {/foreach}
  {if !$pagebar||$pagebar&&$pagebar->Count-count($skip)<1}
    {$cfg->RandLog=false}
  {/if}
  {$where=array(array('not in','log_ID',$skip),array('=', 'log_Status', 0),array('=', 'log_IsTop', 0))}
{if $cfg->RandLog}
  {$randstr=$zbp->db->type==='sqlite'?'RANDOM()':'RAND()';}
  {if $type=='category'}{$where[]=array('=','log_CateID',$category->ID)}{/if}
  {$randlogs=$zbp->GetArticleList('',$where,array($randstr),4)}
  <ul>
  {foreach $randlogs as $log}
    <li class="log">
      <a href="{$log.Url}" title="{$msg.view}《{$log.Title}》{$msg.details}">
        <img src="{if $log.Cover}{$log.Cover}{else}{$host}zb_users/theme/Zit/style/bg.jpg{/if}" alt="{$log.Title}" class="cover{if !$log.Cover} hue{/if}">
        <span class="pane">
          <span class="zit">{if $log.Tags}{$log.FirstTag.Name}{else}{$log.Category.Name}{/if}</span>
          <b>{$log.Title}</b>
          <span><small class="kico-eye"><dfn>{$msg.views}</dfn>{$log.ViewNums}</small> <small class="kico-ping"><dfn>{$msg.comments}</dfn>{$log.CommNums}</small>
          </span>
        </span>
      </a>
    </li>
  {/foreach}
  </ul>
{else}
  {$cmtids=json_decode(isset($modules['zit_cmtids'])?$modules['zit_cmtids'].Content:'',true)}
  {if trim($cfg.CmtIds,',')}
    {$cmtids=explode(',',$cfg.CmtIds)}
  {/if}
{if $cmtids}
  {$cmtid=$cmtids[array_rand($cmtids,1)]}
  {$cmt=$zbp.GetCommentByID($cmtid)}
  {if $cmt.Post.Tags}
  <h4>
    {foreach $cmt.Post.Tags as $tag}
    <a href="{$tag.Url}" class="tag kico-hash">{$tag.Name}</a> 
    {/foreach}
    {$tag=null}
  </h4>
  {/if}
  <h2 class="kico-ping"><a href="{$cmt.Post.Url}" title="{$msg.view}《{$cmt.Post.Title}》{$msg.details}">{SubStrUTF8(TransferHTML($cmt.Content,'[nohtml]'),40)}...</a></h2>
  <p {if $cfg.HideIntro} class="hidem"{/if}>{SubStrUTF8(TransferHTML($cmt.Post.Content,'[nohtml]'),140)}...</p>
  <small>{if $cmt.Post.CommNums>1}{$msg.other} {$cmt.Post.CommNums-1} {$msg.commented}{else}{$msg.expect}{/if}</small>
  <a href="{$cmt.Post.Url}" title="{$msg.view}《{$cmt.Post.Title}》{$msg.details}" rel="nofollow" class="more">{$msg.join}<span class="zit">{$cmt.Post.ViewNums}{$msg.guys}</span>{$msg.crowds}</a>
  {$logs=null}
{else}
  <h2>{$msg.welcome}</h2>
  <small>{$msg.advice}</small>
  {$gbook=$zbp.GetPostByID(trim($cfg.GbookID))}
  <a href="{$gbook.Url}" rel="nofollow" class="more">{$msg.message}<span class="zit">{$msg.sofa}</span></a>
{/if}
{/if}
</article>