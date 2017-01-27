<div id="respond" class="no_webshot">
	<form id="frmSumbit" target="_self" method="post" action="{$article.CommentPostUrl}">
		<input type="hidden" name="inpId" id="inpId" value="{$article.ID}" />
		<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
		<h3 class="base-tit">发表我的评论</h3>
		<div class="comt">
			<div class="comt-avatar">
				<img class="avatar" src="{$zbp.user.Avatar}" width="36" height="36">
			</div>
			<div class="comt-box">
				<textarea class="comt-area" name="txaArticle" id="txaArticle" cols="100%" rows="3" tabindex="5" onkeydown="if(event.ctrlKey&amp;&amp;event.keyCode==13){document.getElementById('submit').click();};"></textarea>
				<div class="comt-ctrl">
{if $option['ZC_COMMENT_VERIFY_ENABLE']}
					&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="inpVerify" id="inpVerify" value="" tabindex="4"/><span style="color:#F00;">验证码(*)</span><img style="border:1px solid #746969;height:18px;vertical-align:middle;margin-left: 10px;" src="{$article.ValidCodeUrl}" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();"/>
{/if}
					<input class="comt-submit" type="submit" name="submit" id="submit" tabindex="6" onclick="return zbp.comment.post()" value="发布评论" />
				</div>
			</div>
				{if $user.ID>0}
					<input type="hidden" name="inpName" id="inpName" value="{$user.Name}" />
					<input type="hidden" name="inpEmail" id="inpEmail" value="{$user.Email}" />
					<input type="hidden" name="inpHomePage" id="inpHomePage" value="{$user.HomePage}"/>
				{else}
			<div class="comt-comterinfo" id="comment-author-info" >
				<h4>Hi，您需要填写昵称和邮箱！</h4>
				<ul>
					<li><label for="inpName">昵称</label><input class="ipt" type="text" name="inpName" id="inpName" value="" size="28" tabindex="1"><span>必填项</span></li>
					<li><label for="inpEmail">邮箱</label><input class="ipt" type="mail" name="inpEmail" id="inpEmail" value="" size="28" tabindex="2"></li>
					<li class="comt-comterinfo-url"><label for="inpHomePage">链接</label><input class="ipt" type="url" name="inpHomePage" id="inpHomePage" value="" size="42" tabindex="3"></li>
					<script type="text/javascript">LoadRememberInfo();</script>

				</ul>
			</div>
				{/if}
		</div>
	</form>
</div>