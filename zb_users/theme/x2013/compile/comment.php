<label id="cmt<?php  echo $comment->ID;  ?>"></label><ol class="commentlist" id="cmt<?php  echo $comment->ID;  ?>">
	<li class="comment odd alt thread-odd thread-alt depth-<?php  echo $comment->ID;  ?>" id="comment-<?php  echo $comment->ID;  ?>">
		<div class="c-floor"><a href="#cmt<?php  echo $comment->ID;  ?>">#<?php  echo $key+1;  ?></span>
</a></div>
		<div class="c-avatar">
			<img class="avatar" src="http://0.gravatar.com/avatar/<?php echo md5($comment->Author->Email); ?>?s=36&amp;d=http%3A%2F%2F0.gravatar.com%2Favatar%2Fad516503a11cd5ca435acc9bb6523536%3Fs%3D55&amp;r=G" width="36" height="36">
		</div>
		<div class="c-main" id="div-comment-<?php  echo $comment->ID;  ?>">
			<div class="c-meta"><span class="c-author"><?php  echo $comment->Author->Name;  ?></span><?php  echo $comment->Time();  ?> <a class='comment-reply-link' href='#respond' onclick="RevertComment('<?php  echo $comment->ID;  ?>')">回复</a></div>
			<p><?php  echo $comment->Content;  ?>
<?php  foreach ( $comment->Comments as $key => $comment) { ?> 
	<?php  include $this->GetTemplate('comment');  ?>
<?php  }   ?>
			</p>
		</div>
	</li>
</ol>