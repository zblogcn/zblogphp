<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>
<!--评论输出-->

<dl id="comment">
  <dt>留言列表</dt>
  <dd>
<label id="AjaxCommentBegin"></label>
<?php  foreach ( $comments as $key => $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>

<!--评论翻页条输出-->
<nav>
<?php  include $this->GetTemplate('pagebar');  ?>
</nav>
<label id="AjaxCommentEnd"></label>
  </dd>
</dl>


<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>

<?php } ?>