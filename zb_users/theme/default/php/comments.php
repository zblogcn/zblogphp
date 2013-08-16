<!--评论输出-->
<label style="display:none;" id="AjaxCommentBegin"></label>
<?php  foreach ( $comments as $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>
<label style="display:none;" id="AjaxCommentEnd"></label>

<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>