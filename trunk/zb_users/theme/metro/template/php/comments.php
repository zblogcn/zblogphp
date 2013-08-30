<label id="AjaxCommentBegin"></label><!--评论输出-->
<?php  foreach ( $comments as $key => $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
<?php  include $this->GetTemplate('pagebar');  ?>
</div><label id="AjaxCommentEnd"></label>