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
		<?php  include $this->GetTemplate('comments');  ?>
		<?php } ?>
     </div>
     <div class="clear"></div>
</div>