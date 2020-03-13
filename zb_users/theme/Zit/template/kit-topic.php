<article id="topic">
  {if $type!=='index'}<nav id="path" class="kico-dao kico-gap"><a href="{$host}">{$msg.home}</a> | <a>{$title}</a></nav>{/if}
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
    <a href="{$tag.Url}" class="tag">{$tag.Name}</a> 
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
</article>