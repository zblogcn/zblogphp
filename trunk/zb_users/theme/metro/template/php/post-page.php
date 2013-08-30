<div class="post cate<?php  echo $article->Category->ID;  ?>  auth<?php  echo $article->Author->ID;  ?>">
       <div class="post_fu"></div>
      <div class="post_r">
        <div class="post_body">
          <h2><?php  echo $article->Title;  ?></h2>
          <div class="post_content">
            <?php  echo $article->Content;  ?>
          </div>
        </div>       
		<?php if (!$article->IsLock) { ?>

<?php if ($socialcomment) { ?>
<?php  echo $socialcomment;  ?>
<?php }else{  ?>

<div class="commentlist" style="overflow:hidden;">
<?php if ($article->CommNums>0) { ?>
<h4>评论列表:</h4>
<?php } ?>
<?php  include $this->GetTemplate('comments');  ?>		
</div>


<!--评论框-->
<?php  include $this->GetTemplate('commentpost');  ?>

<?php } ?>
		
		<?php } ?>
     </div>
     <div class="clear"></div>
</div>