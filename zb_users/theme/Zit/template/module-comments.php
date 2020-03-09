{foreach $comments as $cmt}
<li class="discuz"><a href="{$cmt.Post.Url}#cmt{$cmt.ID}" title="{$cmt.Name}{$msg.discuss}《{$cmt.Post.Title}》"><img src="{$cmt.Author.Avatar}" alt="{$cmt.Name}"> <b>{$cmt.Name}</b>：{SubstrUTF8(TransferHTML($cmt.Content, '[noenter][nohtml]'),30)} <q>{SubstrUTF8($cmt.Post.Title,20)}</q></a></li>
{/foreach}