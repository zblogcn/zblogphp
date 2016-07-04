{* Template Name:评论验证码 *}
{if $option['ZC_COMMENT_VERIFY_ENABLE'] && !$zbp.CheckRights('NoValidCode')}
<p><input type="text" name="inpVerify" id="inpVerify" class="text" value="" size="28" tabindex="4" /> <label for="inpVerify">{$lang['msg']['validcode']}(*)</label><img style="width:{$option['ZC_VERIFYCODE_WIDTH']}px;height:{$option['ZC_VERIFYCODE_HEIGHT']}px;cursor:pointer;" src="{$article.ValidCodeUrl}" alt="" title="" onclick="javascript:this.src='{$article.ValidCodeUrl}&amp;tm='+Math.random();"/></p>
{/if}