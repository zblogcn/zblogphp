<label id="cmt<?php  echo $comment->ID;  ?>"></label><ul class="msg" id="cmt<?php  echo $comment->ID;  ?>">
	<li class="msgname"><img class="avatar" src="<?php  echo $comment->Author->Avatar;  ?>" alt="" width="32"/>&nbsp;<span class="commentname"><a href="<?php  echo $comment->Author->HomePage;  ?>" rel="nofollow" target="_blank"><?php  echo $comment->Author->StaticName;  ?></a></span><br/><small>&nbsp;发布于&nbsp;<?php  echo $comment->Time();  ?>&nbsp;&nbsp;<span class="revertcomment"><a href="#comment" onclick="RevertComment('<?php  echo $comment->ID;  ?>')">回复该评论</a></span></small></li>
	<li class="msgarticle"><?php  echo $comment->Content;  ?>
<?php  foreach ( $comment->Comments as $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>
	</li>
</ul>