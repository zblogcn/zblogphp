{* Template Name:公共底部(勿选) *}
<div class="footer">
	<div class="fademask"></div>
    <div class="wrap">
        <h3>{$copyright}</h3>
        <h4>Powered By {$zblogphpabbrhtml}. Theme by <a href="https://www.toyean.com/" target="_blank" title="拓源网">TOYEAN</a>.</h4>
        {$footer}
    </div>
</div>
<div class="edgebar">
{if $zbp->Config('tpure')->PostLANGON == '1'}
<a href="javascript:$.translatePage();" target="_self" id="zh_language" class="lang"></a>
<script>var cookieDomain = "{$host}";</script>
<script src="{$host}zb_users/theme/{$theme}/plugin/lang/zh_language.js"></script>
{/if}
{if $zbp->Config('tpure')->PostSETNIGHTON}<a href="javascript:;" target="_self" class="setnight"></a>{/if}
</div>