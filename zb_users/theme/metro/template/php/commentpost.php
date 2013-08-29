<div class="commentpost" id="comment">
    <h4>发表评论:</h4><?php if ($user->ID>0) { ?><?php  echo $user->Name#;  ?><?php } ?><small><a rel="nofollow" id="cancel-reply" href="#divCommentPost" style="display:none;">取消回复</a></small>
	<form id="commentform" target="_self" method="post" action="<?php  echo $article->CommentPostUrl#;  ?>" >
	<input type="hidden" name="postid" id="postid" value="<?php  echo $article->ID#;  ?>" />
	<input type="hidden" name="replyid" id="replyid" value="0" />
	<input type="hidden" name="verify" id="verify" value="" />	
<?php if ($user->ID>0) { ?>
	<input type="hidden" name="name" id="name" value="<?php  echo $user->Name;  ?>" />
	<input type="hidden" name="email" id="email" value="<?php  echo $user->Email;  ?>" />
	<input type="hidden" name="homepage" id="homepage" value="<?php  echo $user->HomePage;  ?>" />	
<?php }else{  ?>
	<p><input type="text" name="name" id="name" class="text" value="<?php  echo $user->Name;  ?>" size="28" tabindex="1" /> <label for="name">名称(*)</label></p>
	<p><input type="text" name="email" id="email" class="text" value="<?php  echo $user->Email;  ?>" size="28" tabindex="2" /> <label for="email">邮箱</label></p>
	<p><input type="text" name="homepage" id="homepage" class="text" value="<?php  echo $user->HomePage;  ?>" size="28" tabindex="3" /> <label for="homepage">网址</label></p>

<?php } ?>
	<!--verify-->
	<p><label for="content">正文(*)</label></p>
	<p><textarea name="content" id="content" class="text" cols="50" rows="4" tabindex="5" ></textarea></p>
	<p><input name="sumbit" type="submit" tabindex="6" value="提交" onclick="return VerifyMessage()" class="button" /></p>
	</form>
	<p class="postbottom">◎欢迎参与讨论，请在这里发表您的看法、交流您的观点。</p>
</div>