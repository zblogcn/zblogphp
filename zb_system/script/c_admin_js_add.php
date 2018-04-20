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

?>
var zbp = new ZBP({
    bloghost: "<?php echo $zbp->host; ?>",
    ajaxurl: "<?php echo $zbp->ajaxurl; ?>",
    cookiepath: "<?php echo $zbp->cookiespath; ?>"
});

var bloghost = zbp.options.bloghost;
var cookiespath = zbp.options.cookiepath;
var ajaxurl = zbp.options.ajaxurl;

//*********************************************************
// 目的：    全选
// 输入：    无
// 返回：    无
//*********************************************************
function BatchSelectAll() {
    $("input[name='id[]']").click();
}
//*********************************************************




//*********************************************************
// 目的：
// 输入：    无
// 返回：    无
//*********************************************************
function BatchDeleteAll(objEdit) {

    objEdit=document.getElementById(objEdit);
    objEdit.value="";
    var aryChecks = document.getElementsByTagName("input");
    for (var i = 0; i < aryChecks.length; i++){
        if((aryChecks[i].type=="checkbox")&&(aryChecks[i].id.indexOf("edt")!==-1)){
            if(aryChecks[i].checked){
                objEdit.value=aryChecks[i].value+","+objEdit.value;
            }
        }
    }

}
//*********************************************************








//*********************************************************
// 目的：    ActiveLeftMenu
// 输入：    无
// 返回：    无
//*********************************************************
function ActiveLeftMenu(name){

    name="#"+name;
    $("#leftmenu li").removeClass("on");
    $(name).parent().addClass("on");
    var s=$(name).children("span").css("background-image");
    if(s!==undefined){
        s=s.replace("1.png","2.png");
        $(name).children("span").css("background-image",s);
    }

}
//*********************************************************




//*********************************************************
// 目的：    ActiveTopMenu
// 输入：    无
// 返回：    无
//*********************************************************
function ActiveTopMenu(name){

    name="#"+name;
    $("#topmenu li").removeClass("on");
    $(name).addClass("on");

}
//*********************************************************





//*********************************************************
// 目的：    表格斑马线
// 输入：    无
// 返回：    无
//*********************************************************
function bmx2table(){
    $("table:not(.table_striped)").addClass("table_striped");
    $("table:not(.table_hover)").addClass("table_hover");
};
//*********************************************************




//*********************************************************
// 目的：    CheckBox
// 输入：    无
// 返回：    无
//*********************************************************
function ChangeCheckValue(obj){

    $(obj).toggleClass('imgcheck-on');

    if($(obj).hasClass('imgcheck-on')){
        $(obj).prev('input').val('1');
        $(obj).next('.off-hide').show();
    }else{
        $(obj).prev('input').val('0');
        $(obj).next('.off-hide').hide();
    }

}
//*********************************************************




//*********************************************************
// 目的：    Notifications
// 输入：    无
// 返回：    无
//*********************************************************
function notify(s){
    if (window.webkitNotifications) {
        if (window.webkitNotifications.checkPermission() == 0) {
            var zb_notifications = window.webkitNotifications.createNotification('<?php echo $bloghost; ?>zb_system/image/admin/logo-16.png', '<?php echo $lang['msg']['notify']; ?>', s);
            zb_notifications.show();
            zb_notifications.onclick = function() {top.focus(),this.cancel();}
            zb_notifications.replaceId = 'Meteoric';
            setTimeout(function(){zb_notifications.cancel()},5000);
        } else {
            window.webkitNotifications.requestPermission(notify);
        }
    }
}
//*********************************************************



function statistic(s){
    $("#statloading").show();
    $("#updatatime").hide();
    $.get(s+"&tm="+Math.random(),{},
        function(data){
            $("#tbStatistic tr:first ~ tr").remove();
            $("#tbStatistic tr:first").after(data);
            //bmx2table();
            $("#statloading").hide();
            $("#updatatime").show();
        }
    );
}

function updateinfo(s){
    $("#infoloading").show();
    $.get(s+"&tm="+Math.random(),{},
        function(data){
            $("#tbUpdateInfo tr:first ~ tr").remove();
            $("#tbUpdateInfo tr:first").after(data);
            $("#infoloading").hide();
        }
    );
}


function AddHeaderIcon(s){
  $("div.divHeader,div.divHeader2").first().css({"background-image":"url('"+s+"')"});
}


function AutoHideTips(){
    if($("p.hint:visible").length>0){
        $("p.hint:visible").delay(10000).hide(1500,function(){});
    }
}

function ShowCSRFHint() {
    $('.main').prepend('<div class="hint"><p class="hint hint_bad"><?php echo $lang['error']['94']; ?></p></div>'.replace('%s', $('meta[name=csrfExpiration]').attr('content')));
}


//*********************************************************
// 目的：
//*********************************************************
$(document).ready(function(){

    // Content box tabs:
    $('.content-box .content-box-content div.tab-content').hide(); // Hide the content divs
    $('ul.content-box-tabs li a.default-tab').addClass('current'); // Add the class "current" to the default tab
    $('.content-box-content div.default-tab').show(); // Show the div with class "default-tab"

    $('.content-box ul.content-box-tabs li a').click( // When a tab is clicked...
        function() {
            $(this).parent().siblings().find("a").removeClass('current'); // Remove "current" class from all tabs
            $(this).addClass('current'); // Add class "current" to clicked tab
            var currentTab = $(this).attr('href'); // Set variable "currentTab" to the value of href of clicked tab
            $(currentTab).siblings().hide(); // Hide all content divs
            $(currentTab).show(); // Show the content div with the id equal to the id of clicked tab
            return false;
        }
    );

    //斑马线化表格（老版本兼容代码）
    bmx2table();

    if($('.SubMenu').find('span').length>0){
        $('.SubMenu').show();
    }

    //checkbox
    $('input.checkbox').css("display","none");
    $('input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
    $('input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');


    $("body").on("click","span.imgcheck", function(){ChangeCheckValue(this)});

    //batch
    $("#batch a").bind("click", function(){ BatchContinue();$("#batch p").html("<?php echo $lang['msg']['batch_operation_in_progress']; ?>");});

    $(".SubMenu span.m-right").parent().css({"float":"right"});

    $("img[width='16']").each(function(){if($(this).parent().is("a")){$(this).parent().addClass("button")}});

    if ($("div.divHeader,div.divHeader2").first().css("background-image") == "none") { 
        AddHeaderIcon("<?php echo $bloghost ?>zb_system/image/common/window.png");
    }

    AutoHideTips();

    SetCookie("timezone",(new Date().getTimezoneOffset()/60)*(-1));

    var s = $("div.divHeader,div.divHeader2").first().css("background-image");
    if(s != undefined && s.indexOf("none.gif") != -1 ){
        AddHeaderIcon(bloghost + "zb_system/image/common/window.png");
    }

    var startTime = new Date().getTime();
    var csrfInterval = setInterval(function () {
        var timeout = $('meta[name=csrfExpiration]').attr('content') || 1; // Re-get expiration value every time
        var timeDiff = new Date().getTime() - startTime;
        if (timeDiff > Math.floor(timeout) * 60 * 60 * 1000) {
            ShowCSRFHint();
            clearInterval(csrfInterval);
        }
    }, 30 * 60 * 1000);
});


var SetCookie = function () { return zbp.cookie.set.apply(null, arguments); };
var GetCookie = function () { return zbp.cookie.get.apply(null, arguments); };
var LoadRememberInfo = function () { zbp.userinfo.output.apply(null); return false;};
var SaveRememberInfo = function () { zbp.userinfo.saveFromHtml.apply(null); return false;};
var RevertComment = function () { zbp.comment.reply.apply(null, arguments); return false;} ;
var GetComments = function () { zbp.comment.get.apply(null, arguments); return false;} ;
var VerifyMessage = function () { zbp.comment.post.apply(null); return false;};


<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Admin_Js_Add'] as $fpname => &$fpsignal) {
    $fpname();
}

$s = ob_get_clean();
$m = 'W/' . md5($s);

header('Content-Type: application/x-javascript; charset=utf-8');
if ($zbp->option['ZC_JS_304_ENABLE']) {
    header('Etag: ' . $m);
    if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $m) {
        SetHttpStatusCode(304);
        die;
    }
}

$zbp->CheckGzip();
$zbp->StartGzip();

echo $s;

die();
?>
