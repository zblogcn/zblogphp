<?php  foreach ( $comments as $comment) { ?> 
<!--评论输出-->
<?php  include $this->GetTemplate('comment');  ?>

<?php  }   ?>


<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>