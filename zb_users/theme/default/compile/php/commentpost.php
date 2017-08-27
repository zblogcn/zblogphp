{* Template Name:评论发布框 *}
<div class="post" id="divCommentPost">
	<p class="posttop"><a name="comment">{if $user.ID>0}{$user.StaticName}{/if}{$lang['default']['add_reply']}:</a><a rel="nofollow" id="cancel-reply" href="#divCommentPost" style="display:none;"><small>{$lang['default']['cancel_reply']}</small></a></p>
	<form id="frmSumbit" target="_self" method="post" action="{$article.CommentPostUrl}" >
	<input type="hidden" name="inpId" id="inpId" value="{$article.ID}" />
	<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
{if $user.ID>0}
	<input type="hidden" name="inpName" id="inpName" value="{$user.StaticName}" />
	<input type="hidden" name="inpEmail" id="inpEmail" value="{$user.Email}" />
	<input type="hidden" name="inpHomePage" id="inpHomePage" value="{$user.HomePage}" />
{else}
	<p><input type="text" name="inpName" id="inpName" class="text" value="{$user.StaticName}" size="28" tabindex="1" /> <label for="inpName">{$lang['msg']['name']}(*)</label></p>
	<p><input type="text" name="inpEmail" id="inpEmail" class="text" value="{$user.Email}" size="28" tabindex="2" /> <label for="inpEmail">{$lang['msg']['email']}</label></p>
	<p><input type="text" name="inpHomePage" id="inpHomePage" class="text" value="{$user.HomePage}" size="28" tabindex="3" /> <label for="inpHomePage">{$lang['msg']['homepage']}</label></p>
{if $option['ZC_COMMENT_VERIFY_ENABLE']}
	<p><input type="text" name="inpVerify" id="inpVerify" class="text" value="" size="28" tabindex="4" /> <label for="inpVerify">{$lang['msg']['validcode']}(*)</label>
	<img style="width:{$option['ZC_VERIFYCODE_WIDTH']}px;height:{$option['ZC_VERIFYCODE_HEIGHT']}px;cursor:pointer;" src="{$article.ValidCodeUrl}" alt="" title="" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();"/>
	</p>
{/if}

{/if}
	<p><label for="txaArticle">{$lang['msg']['content']}(*)</label></p>
	<p><textarea name="txaArticle" id="txaArticle" class="text" cols="50" rows="4" tabindex="5" ></textarea></p>
	<p><input name="sumbit" type="submit" tabindex="6" value="提交" onclick="return zbp.comment.post()" class="button" /></p>
	</form>
	<p class="postbottom">{$lang['default']['reply_notice']}</p>
</div>