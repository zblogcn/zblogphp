<div class="cmt block" id="comment">
    <div class="posttitle">
        <h4>{if $user.ID>0}{$user.StaticName}{/if} {$lang['tpure']['writecmt']}&nbsp;&nbsp;&nbsp;&nbsp;<button id="cancel-reply" class="cmtbtn">取消回复</button></h4>
    </div>
    <div class="comment">
        <div class="cmtimg"><img src="{$user.Avatar}"></div>
        <div class="cmtarea">
            <form id="frmSumbit" target="_self" method="post" action="{$article.CommentPostUrl}" >
			<input type="hidden" name="inpId" id="inpId" value="{$article.ID}" />
			<input type="hidden" name="inpRevID" id="inpRevID" value="0" />
			<textarea name="txaArticle" id="txaArticle" rows="5" tabindex="1"></textarea>
{if $user.ID>0}
			<input type="hidden" name="inpName" id="inpName" value="{$user.Name}" />
			<input type="hidden" name="inpEmail" id="inpEmail" value="{$user.Email}" />
			<input type="hidden" name="inpHomePage" id="inpHomePage" value="{$user.HomePage}" />	
{else}
            <div class="cmtform">
                <p><input type="text" name="inpName" id="inpName" class="text" size="28" tabindex="2"><label for="inpName">{$lang['tpure']['inp_name']}</label></p>
                <p><input type="text" name="inpEmail" id="inpEmail" class="text" size="28" tabindex="3"><label for="inpEmail">{$lang['tpure']['inp_email']}</label></p>
                <p><input type="text" name="inpHomePage" id="inpHomePage" class="text" size="28" tabindex="4"><label for="inpHomePage">{$lang['tpure']['inp_homepage']}</label></p>
                {if $option['ZC_COMMENT_VERIFY_ENABLE']}
				<p><input type="text" name="inpVerify" id="inpVerify" class="textcode" value="" size="28" tabindex="5" /><img src="{$article.ValidCodeUrl}" title="{$lang['tpure']['refresh_code']}" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();" class="imgcode" /><label for="inpVerify">{$lang['tpure']['inp_verify']}</label></p>
				{/if}
            </div>
{/if}
            <div class="cmtsubmit">
                <button type="button" name="btnSumbit" onclick="return zbp.comment.post()" class="cmtbtn" tabindex="6">{$lang['tpure']['cmt']}</button>
                <span>{$lang['tpure']['cmttip']}</span>
            </div>
            </form>
        </div>
    </div>
</div>
<script language="JavaScript" type="text/javascript">
var txaArticle = document.getElementById('txaArticle');
txaArticle.onkeydown = function quickSubmit(e) {
if (!e) var e = window.event;
if (e.ctrlKey && e.keyCode == 13){
return zbp.comment.post();
}
}
</script>