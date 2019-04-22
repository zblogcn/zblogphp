<dl id="postcmt">
  <dt><a name="comment">发表评论</a><a rel="nofollow" id="cancel-reply" href="#comment" style="display:none;"><small>取消回复</small></a></dt>
  <dd>
  <form id="commentform" target="_self" method="post" action="<?php  echo $article->CommentPostUrl; ?>&amp;mod=pad">
    <input type="hidden" name="inpId" id="inpId" value="<?php  echo $article->ID; ?>" />
    <input type="hidden" name="inpRevID" id="inpRevID" value="0" />
    <p>
      <label>
        <input type="text" id="inpName" name="inpName" size="28" tabindex="2" required value="<?php  echo $user->Name; ?>" />
        名称<sup>*</sup></label>
    </p>
    <p>
      <label>
        <input type="text" id="inpEmail" name="inpEmail" size="28" tabindex="3" value="<?php  echo $user->Email; ?>" />
        邮箱</label>
    </p>
    <p>
      <label>
        <input type="text" id="inpHomePage" name="inpHomePage" size="28" tabindex="4" value="<?php  echo $user->HomePage; ?>" />
        网址</label>
    </p>
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
