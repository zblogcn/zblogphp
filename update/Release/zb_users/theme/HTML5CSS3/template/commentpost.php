<dl id="postcmt">
  <dt><a name="comment">发表评论</a><a rel="nofollow" id="cancel-reply" href="#comment" style="display:none;"><small>取消回复</small></a></dt>
  <dd>
  <h5><!--◎欢迎参与讨论，请在这里发表您的看法、交流您的观点。--></h5>
<figure><img src="{$user.Avatar}" alt="来宾的头像" border="0"></figure>
  <form id="frmSumbit" target="_self" method="post" action="{$article.CommentPostUrl}">
	<input type="hidden" name="inpId" id="inpId" value="{$article.ID}" />
	<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
    <p>
      <label>
        <input type="text" id="inpName" name="inpName" size="28" tabindex="2" required value="{$user.Name}" />
        名称<sup>*</sup></label>
    </p>
    <p>
      <label>
        <input type="text" id="inpEmail" name="inpEmail" size="28" tabindex="3" value="{$user.Email}" />
        邮箱</label>
    </p>
    <p>
      <label>
        <input type="text" id="inpHomePage" name="inpHomePage" size="28" tabindex="4" value="{$user.HomePage}" />
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