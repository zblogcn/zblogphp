{php}
if($zbp->CheckPlugin('Gravatar')){
    $default_url = $zbp->Config('Gravatar')->default_url;
}else{
    $default_url = '';
}
{/php}
<div class="cmt block" id="divCommentPost">
    <div class="posttitle">
        <h4>{$lang['tpure']['writecmt']}<button id="cancel-reply" class="cmtbtn">{$lang['tpure']['cancelreply']}</button></h4>
    </div>
    <div class="comment">
        <div id="cmtimg" class="cmtimg"><img src="{tpure_MemberAvatar($user)}" alt="{$user.Name}" /><p>{if $user.ID>0}{$user.StaticName}{/if}</p></div>
        <div class="cmtarea">
            <form id="frmSubmit" target="_self" method="post" action="{$article.CommentPostUrl}" >
            <input type="hidden" id="gravatar" value="{$default_url}" />
			<input type="hidden" name="inpId" id="inpId" value="{$article.ID}" />
			<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
			<textarea name="txaArticle" id="txaArticle" rows="3" tabindex="1"></textarea>
{if $user.ID>0}
			<input type="hidden" name="inpName" id="inpName" value="{$user.StaticName}" />
            <input type="hidden" name="inpEmail" id="inpEmail" value="{$user.Email}" />
            <input type="hidden" name="inpHomePage" id="inpHomePage" value="{$user.HomePage}" />
{else}
            <div class="cmtform">
                <p><input type="text" name="inpName" id="inpName" class="text" size="28" tabindex="2" value="{if $user.ID>0}{$user.StaticName}{/if}"><label for="inpName">{$lang['tpure']['inp_name']}</label></p>
                {if $zbp->Config('tpure')->PostCMTMAILON}<p><input type="text" name="inpEmail" id="inpEmail" class="text" size="28" tabindex="3"><label for="inpEmail">{$lang['tpure']['inp_email']}</label></p>{else}<input type="hidden" name="inpEmail" id="inpEmail" />{/if}
                {if $zbp->Config('tpure')->PostCMTSITEON}<p><input type="text" name="inpHomePage" id="inpHomePage" class="text" size="28" tabindex="4"><label for="inpHomePage">{$lang['tpure']['inp_homepage']}</label></p>{else}<input type="hidden" name="inpHomePage" id="inpHomePage" />{/if}
                {if $option['ZC_COMMENT_VERIFY_ENABLE']}
				<p><input type="text" name="inpVerify" id="inpVerify" class="textcode" value="" size="28" tabindex="5" /><img src="{$article.ValidCodeUrl}" title="{$lang['tpure']['refresh_code']}" alt="{$lang['tpure']['refresh_code']}" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();" class="imgcode" /><label for="inpVerify">{$lang['tpure']['inp_verify']}</label></p>
				{/if}
            </div>
{/if}
            <div class="cmtsubmit">
                <button type="submit" name="btnsubmit" onclick="return zbp.comment.post()" class="cmtbtn" tabindex="6">{$lang['tpure']['cmt']}</button>
                <span>{$lang['tpure']['cmttip']}</span>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
var txaArticle = document.getElementById('txaArticle');
txaArticle.onkeydown = function quickSubmit(e) {
if (!e) var e = window.event;
if (e.ctrlKey && e.keyCode == 13){
return zbp.comment.post();
}
}
</script>