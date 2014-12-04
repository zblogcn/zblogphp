<dl id="postcmt">
  <dt><a name="comment">发表评论</a><a rel="nofollow" id="cancel-reply" href="#comment" style="display:none;"><small>取消回复</small></a></dt>
  <dd>
  <h5><!--◎欢迎参与讨论，请在这里发表您的看法、交流您的观点。--></h5>
<figure><img src="<?php  echo $user->Avatar;  ?>" alt="来宾的头像" border="0"><?php if ($user->ID>0) { ?><b><?php  echo $user->StaticName;  ?></b><?php } ?></figure>
  <form id="frmSumbit" target="_self" method="post" action="<?php  echo $article->CommentPostUrl;  ?>">
	<input type="hidden" name="inpId" id="inpId" value="<?php  echo $article->ID;  ?>" />
	<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
<?php if ($user->ID>0) { ?>
	<input type="hidden" name="inpName" id="inpName" value="<?php  echo $user->Name;  ?>" />
	<input type="hidden" name="inpEmail" id="inpEmail" value="<?php  echo $user->Email;  ?>" />
	<input type="hidden" name="inpHomePage" id="inpHomePage" value="<?php  echo $user->HomePage;  ?>" />	
<?php }else{  ?>
    <p>
      <label>
        <input type="text" id="inpName" name="inpName" size="28" tabindex="2" required value="<?php  echo $user->Name;  ?>" />
        名称<sup>*</sup></label>
    </p>
    <p>
      <label>
        <input type="text" id="inpEmail" name="inpEmail" size="28" tabindex="3" value="<?php  echo $user->Email;  ?>" />
        邮箱</label>
    </p>
    <p>
      <label>
        <input type="text" id="inpHomePage" name="inpHomePage" size="28" tabindex="4" value="<?php  echo $user->HomePage;  ?>" />
        网址</label>
    </p>
	
<?php if ($option['ZC_COMMENT_VERIFY_ENABLE']) { ?>
	<p>
	  <label>
	    <input type="text" name="inpVerify" id="inpVerify" class="text" value="" size="28" tabindex="4" />
	    <img style="width:<?php  echo $option['ZC_VERIFYCODE_WIDTH'];  ?>px;height:<?php  echo $option['ZC_VERIFYCODE_HEIGHT'];  ?>px;cursor:pointer;" src="<?php  echo $article->ValidCodeUrl;  ?>" alt="" title="" onclick="javascript:this.src='<?php  echo $article->ValidCodeUrl;  ?>&amp;tm='+Math.random();"/>
		验证码(*)</label>
	</p>
<?php } ?>

<?php } ?>
	<!--verify-->
    <p>
      <textarea name="txaArticle" id="txaArticle" class="txt" cols="50" rows="4" tabindex="1" required></textarea>
    </p>
    <p>
      <button name="btnSumbit" type="submit" tabindex="6" onclick="return VerifyMessage()">提交</button>
    </p>
  </form>
  </dd>
</dl>