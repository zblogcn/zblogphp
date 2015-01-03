<div class="post" id="divCommentPost">
	<p class="posttop"><a name="comment"><?php if ($user->ID>0) { ?><?php  echo $user->StaticName;  ?><?php } ?>发表评论:</a><a rel="nofollow" id="cancel-reply" href="#divCommentPost" style="display:none;"><small>取消回复</small></a></p>
	<form id="frmSumbit" target="_self" method="post" action="<?php  echo $article->CommentPostUrl;  ?>" >
	<input type="hidden" name="inpId" id="inpId" value="<?php  echo $article->ID;  ?>" />
	<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
<?php if ($user->ID>0) { ?>
	<input type="hidden" name="inpName" id="inpName" value="<?php  echo $user->Name;  ?>" />
	<input type="hidden" name="inpEmail" id="inpEmail" value="<?php  echo $user->Email;  ?>" />
	<input type="hidden" name="inpHomePage" id="inpHomePage" value="<?php  echo $user->HomePage;  ?>" />
<?php }else{  ?>
	<p><input type="text" name="inpName" id="inpName" class="text" value="<?php  echo $user->Name;  ?>" size="28" tabindex="1" /> <label for="inpName">名称(*)</label></p>
	<p><input type="text" name="inpEmail" id="inpEmail" class="text" value="<?php  echo $user->Email;  ?>" size="28" tabindex="2" /> <label for="inpEmail">邮箱</label></p>
	<p><input type="text" name="inpHomePage" id="inpHomePage" class="text" value="<?php  echo $user->HomePage;  ?>" size="28" tabindex="3" /> <label for="inpHomePage">网址</label></p>
<?php if ($option['ZC_COMMENT_VERIFY_ENABLE']) { ?>
	<p><input type="text" name="inpVerify" id="inpVerify" class="text" value="" size="28" tabindex="4" /> <label for="inpVerify">验证码(*)</label>
	<img style="width:<?php  echo $option['ZC_VERIFYCODE_WIDTH'];  ?>px;height:<?php  echo $option['ZC_VERIFYCODE_HEIGHT'];  ?>px;cursor:pointer;" src="<?php  echo $article->ValidCodeUrl;  ?>" alt="" title="" onclick="javascript:this.src='<?php  echo $article->ValidCodeUrl;  ?>&amp;tm='+Math.random();"/>
	</p>
<?php } ?>

<?php } ?>
	<p><label for="txaArticle">正文(*)</label></p>
	<p><textarea name="txaArticle" id="txaArticle" class="text" cols="50" rows="4" tabindex="5" ></textarea></p>
	<p><input name="sumbit" type="submit" tabindex="6" value="提交" onclick="return VerifyMessage()" class="button" /></p>
	</form>
	<p class="postbottom">◎欢迎参与讨论，请在这里发表您的看法、交流您的观点。</p>
</div>