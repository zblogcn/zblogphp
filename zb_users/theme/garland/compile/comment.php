<label id="cmt<?php  echo $comment->ID;  ?>"></label>﻿<div class="comment even" id="cmt<?php  echo $comment->ID;  ?>">
	<div class="clear-block">
		<span class="submitted"><?php  echo $comment->Time();  ?> — <a href="<?php  echo $comment->Author->HomePage;  ?>" title="<?php  echo $comment->Author->HomePage;  ?>"  rel="nofollow" ><?php  echo $comment->Author->StaticName;  ?></a>  <a href="#cmt<?php  echo $comment->ID;  ?>" onclick="RevertComment('<?php  echo $comment->ID;  ?>')" class="comment_reply" title="回复<?php  echo $comment->Author->StaticName;  ?>">回复</a></span>
		<h3><a href="#cmt<?php  echo $comment->ID;  ?>" class="active"><?php  echo $key+1;  ?></a> .</h3>
		<div class="content">
		  <?php  echo $comment->Content;  ?>
<?php  foreach ( $comment->Comments as $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>
		</div>
	</div>
</div>
