<label id="cmt<?php  echo $comment->ID;  ?>"></label><blockquote id="cmt<?php  echo $comment->ID;  ?>">
	<figure><img src="<?php  echo $comment->Author->Avatar;  ?>" alt="<?php  echo $comment->Author->StaticName;  ?>" /></figure>
    <cite><b><a href="<?php  echo $comment->Author->HomePage;  ?>" rel="nofollow" target="_blank"><?php  echo $comment->Author->StaticName;  ?></a></b> <time>发表时间 <?php  echo $comment->Time();  ?></time></cite>
	<q><?php  echo $comment->Content;  ?>
&nbsp;&nbsp;<i class="revertcomment"><a href="#reply" onclick="RevertComment('<?php  echo $comment->ID;  ?>')">回复</a></i>
<?php  foreach ( $comment->Comments as $comment) { ?> 
<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>
	</q>
</blockquote>