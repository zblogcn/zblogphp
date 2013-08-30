<label id="AjaxCommentBegin"></label>
       <div class="commentlist" style="overflow:hidden;">
       <h4>评论列表:</h4>

<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>
<!--评论输出-->
<?php  foreach ( $comments as $key => $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>

<!--评论翻页条输出-->
<div class="pagebar commentpagebar">
<?php  include $this->GetTemplate('pagebar');  ?>
</div>

<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>

<?php } ?>

</div><label id="AjaxCommentEnd"></label>