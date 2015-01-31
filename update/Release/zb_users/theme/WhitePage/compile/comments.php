<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>

<?php if ($article->CommNums>0) { ?>
<ul class="msg msghead">
	<li class="tbname">评论列表:</li>
</ul>
<?php } ?>

<label id="AjaxCommentBegin"></label>

<!--评论输出-->
<?php  foreach ( $comments as $key => $comment) { ?>
<?php  include $this->GetTemplate('comment');  ?>
<?php }   ?>

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
<?php  include $this->GetTemplate('pagebar');  ?>
</div>
<label id="AjaxCommentEnd"></label>

<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>

<?php } ?>