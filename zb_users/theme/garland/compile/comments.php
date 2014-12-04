<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>

<label id="AjaxCommentBegin"></label>
<!--评论输出-->
<?php  foreach ( $comments as $key => $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>

<!--评论翻页条输出-->
<div class="pager">
<?php  include $this->GetTemplate('pagebar');  ?>
</div>
<label id="AjaxCommentEnd"></label>

<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>

<?php } ?>