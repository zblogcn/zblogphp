<?php
/**
 * Z-Blog with PHP.
 *
 * @author
 * @copyright (C) RainbowSoft Studio
 *
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';

ob_clean();
// @TODO: Configuable
?>
var zbp = new ZBP({
    bloghost: "<?php echo $zbp->host; ?>",
    ajaxurl: "<?php echo $zbp->ajaxurl; ?>",
    cookiepath: "<?php echo $zbp->cookiespath; ?>",
    lang: {
        error: {
            72: "<?php echo $lang['error']['72']; ?>",
            29: "<?php echo $lang['error']['29']; ?>",
            46: "<?php echo $lang['error']['46']; ?>"
        }
    },
    comment: {
        useDefaultEvents: true,
        inputs: {
            action: {
                getter: function () {
                    return $("#inpId").parent("form").attr("action");
                }
            },
            name: {
                selector: '#inpName',
                saveLocally: true,
                required: true,
                validateRule: /^[\.\_A-Za-z0-9\u4e00-\u9fa5@]+$/ig,
                validateFailedErrorCode: 72,
            },
            email: {
                selector: '#inpEmail',
                saveLocally: true,
                required: true,
                validateRule: /^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/ig,
                validateFailedErrorCode: 29,
            },
            homepage: {
                getter: function () {
                    var t = $('#inpHomePage').val();
                    return (!/^(.+)\:\/\//.test(t) && t !== "") ? 'http://' + t : t; 
                },
                saveLocally: true
            },
            postid: {
                selector: '#inpId',
                required: true
            },
            verify: {
                selector: '#inpVerify'
            },
            content: {
                selector: '#txaArticle',
                required: true,
                validateRule: /./ig,
                validateFailedErrorCode: 46,
            },
            replyid: {
                selector: '#inpRevID'
            },
            format: {
                getter: function () {return 'json';}
            }
        }
    }
});

var bloghost = zbp.options.bloghost;
var cookiespath = zbp.options.cookiepath;
var ajaxurl = zbp.options.ajaxurl;
var lang_comment_name_error = zbp.options.lang.error[72];
var lang_comment_email_error = zbp.options.lang.error[29];
var lang_comment_content_error = zbp.options.lang.error[46];

<?php
if (!isset($_GET['pluginonly'])) {
    ?>
$(function () {

    zbp.cookie.set("timezone", (new Date().getTimezoneOffset()/60)*(-1));
    var $cpLogin = $(".cp-login").find("a");
    var $cpVrs = $(".cp-vrs").find("a");
    var $addinfo = zbp.cookie.get("addinfo<?php echo str_replace('/', '', $zbp->cookiespath); ?>");
    if (!$addinfo){
        return ;
    }
    $addinfo = JSON.parse($addinfo);

    if ($addinfo.chkadmin){
        $(".cp-hello").html("<?php echo $zbp->lang['msg']['welcome']; ?> " + $addinfo.useralias + " (" + $addinfo.levelname  + ")");
        if ($cpLogin.length == 1 && $cpLogin.html().indexOf("[") > -1) {
            $cpLogin.html("[<?php echo $zbp->lang['msg']['admin']; ?>]");
        } else {
            $cpLogin.html("<?php echo $zbp->lang['msg']['admin']; ?>");
        }
    }

    if($addinfo.chkarticle){
        if ($cpLogin.length == 1 && $cpVrs.html().indexOf("[") > -1) {
            $cpVrs.html("[<?php echo $zbp->lang['msg']['new_article']; ?>]");
        } else {
            $cpVrs.html("<?php echo $zbp->lang['msg']['new_article']; ?>");
        }
        $cpVrs.attr("href", zbp.options.bloghost + "zb_system/cmd.php?act=ArticleEdt");
    }

});
<?php
}
foreach ($GLOBALS['hooks']['Filter_Plugin_Html_Js_Add'] as $fpname => &$fpsignal) {
    $fpname();
}

$s = ob_get_clean();
$m = 'W/' . md5($s);

header('Content-Type: application/x-javascript; charset=utf-8');

if ($zbp->option['ZC_JS_304_ENABLE']) {
    if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $m) {
        SetHttpStatusCode(304);
        die;
    }
    header('Etag: ' . $m);
}

$zbp->CheckGzip();
$zbp->StartGzip();

echo $s;

die();
?>
