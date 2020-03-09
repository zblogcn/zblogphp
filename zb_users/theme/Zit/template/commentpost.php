<form id="postcmt" class="pane cmt clear" target="_self" method="post" action="{$article.CommentPostUrl}" >
  <input type="hidden" name="postid" id="inpId" value="{$article.ID}">
  <input type="hidden" name="replyid" id="inpRevID" value="0">
  <img src="{$user.Avatar}" alt="{$user.Name}" class="avatar">
  <cite>
    <b><label><input type="text" name="name" id="inpName" placeholder="{$msg.name}" title="{$msg.username}" value="{$user.Name}"></label></b>
    <small><label><input type="text" name="email" id="inpEmail" placeholder="your@email.addr" title="{$msg.email}" value="{$user.Email}"></label>
    <label><input type="text" name="homepage" id="inpHomePage" placeholder="https://your.home.page" title="{$msg.url}" value="{$user.HomePage}"></label></small>
  </cite>
  <textarea name="content" id="txaArticle" cols="50" rows="2"></textarea>
  {if $option['ZC_COMMENT_VERIFY_ENABLE']&&!$zbp.CheckRights('NoValidCode')}
  <span class="captcha"><label><input type="text" name="verify" id="inpVerify" placeholder="{$msg.captcha}"></label><img style="width:{$option['ZC_VERIFYCODE_WIDTH']}px;height:{$option['ZC_VERIFYCODE_HEIGHT']}px;cursor:pointer;" src="{$article.ValidCodeUrl}" title="{$msg.refresh}" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();"></span>
  {/if}
  <button type="submit">{$msg.submit}</button>
</form>