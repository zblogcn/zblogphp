<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>

<div id="postcomments">
	<?php if ($article->CommNums>0) { ?>
	<h3 class="base-tit" id="comments">网友评论<b><?php  echo $article->CommNums;  ?></b>条</h3>
	<?php } ?>
	<label id="AjaxCommentBegin"></label>
	<!--评论输出-->
	<?php  foreach ( $comments as $key => $comment) { ?> 
	<?php  include $this->GetTemplate('comment');  ?>
	<?php  }   ?>	
	<div class="pagenav"><?php  include $this->GetTemplate('pagebar');  ?></div>
	<label id="AjaxCommentEnd"></label>
</div>  

<!--评论框-->
<?php if (!$article->IsLock) { ?>
<?php  include $this->GetTemplate('commentpost');  ?>
<?php } ?>

<?php } ?>