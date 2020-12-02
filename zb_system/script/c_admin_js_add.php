<?php
/**
 * Z-Blog with PHP.
 *
 * @author  Z-BlogPHP Team
 * @version 2.0 2013-06-14
 */
require '../function/c_system_base.php';

ob_clean();

?>
var zbp = new ZBP({
    bloghost: "<?php echo $zbp->host; ?>",
    blogversion: "<?php echo $zbp->version; ?>",
    ajaxurl: "<?php echo $zbp->ajaxurl; ?>",
    cookiepath: "<?php echo $zbp->cookiespath; ?>",
    comment: {
        useDefaultEvents: false,
        inputs: { }
    }
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
        s=s.replace("1.svg","2.svg");
        s=s.replace("1.gif","2.gif");
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
};
//*********************************************************




//*********************************************************
// 目的：    CheckBox
// 输入：    无
// 返回：    无
//*********************************************************
function ChangeCheckValue(obj){

    if ($(obj).hasClass("imgcheck-disabled")) {
        return;
    }
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
    $("#statistic i").addClass("loading-status");
    $("#updatatime").hide();

    $.ajax({
        type: "GET",
        url: s+"&tm="+Math.random(),
        data: {},
        error: function(xhr, exception){
            if( xhr.status == "500") {
                 alert('<?php echo $lang['msg']['refresh_cache']; ?>\n\r<?php echo $lang['msg']['operation_failed']; ?>');
            }
            setTimeout(function () {
                $("#statistic i").removeClass("loading-status");
            }, 500);
            $("#updatatime").show();
        },
        success: function(data){
            $("#tbStatistic tr:first ~ tr").remove();
            $("#tbStatistic tr:first").after(data);
            setTimeout(function () {
                $("#statistic i").removeClass("loading-status");
            }, 500);
            $("#updatatime").show();
        }
    });

}

function updateinfo(s){
    $("#tbUpdateInfo i").addClass("loading-status");
    $.get(s+"&tm="+Math.random(),{},
        function(data){
            $("#tbUpdateInfo tr:first ~ tr").remove();
            $("#tbUpdateInfo tr:first").after(data);
            setTimeout(function () {
                $("#tbUpdateInfo i").removeClass("loading-status");
            }, 500);
        }
    );
}


function AddHeaderIcon(s){
    var element = $("div.divHeader,div.divHeader2").first();
    element.css({"background-image":"url('"+s+"')"});
    element.html('<span>'+element.text()+'</span>');
}

function AddHeaderFontIcon(icon_class){
    var element = $("div.divHeader,div.divHeader2").first();
    element.css("background-image", "url(\"" + zbp.options.bloghost + "zb_system/image/admin/none.gif\")");
    var text = element.text();
    element.html('<i class="'+icon_class+'"></i> <span>'+text+'</span>');
}


function AutoHideTips(){
    $("p.hint:visible").each(function(i){
        if ( !$(this).hasClass("hint_always") ){
            $(this).delay($(this).attr("data-delay")).hide(1500,function(){});
        }
    });
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

    if($('.SubMenu').find('span').length>0){
        $('.SubMenu').show();
    }

    //checkbox
    $('input.checkbox[value="1"]').after('<span class="imgcheck imgcheck-on"></span>');
    $('input.checkbox[value!="1"]').after('<span class="imgcheck"></span>');
    $("input.checkbox").each(function(i){
        $(this).next("span").css("display",$(this).css("display"));
        $(this).next("span").attr("alt",$(this).attr("alt"));
        $(this).next("span").attr("title",$(this).attr("title"));
        if($(this).attr("disabled")=="disabled"){
            $(this).next("span").addClass("imgcheck-disabled");
        }
    });
    $('input.checkbox').css("display","none");

    $("body").on("click","span.imgcheck", function(){ChangeCheckValue(this)});

    //batch
    $("#batch a").bind("click", function(){ BatchContinue();$("#batch p").html("<?php echo $lang['msg']['batch_operation_in_progress']; ?>");});

    $(".SubMenu span.m-right").parent().css({"float":"right"});

    $("img[width='16']").each(function(){if($(this).parent().is("a")){$(this).parent().addClass("button")}});

    if ($("div.divHeader,div.divHeader2").first().css("background-image") == "none") {
        AddHeaderFontIcon("icon-window-fill");
    }

    AutoHideTips();

    SetCookie("timezone",(new Date().getTimezoneOffset()/60)*(-1));

    var s = $("div.divHeader,div.divHeader2").first().css("background-image");
    if ( $("div.divHeader i,div.divHeader2 i").length <= 0 && (s != undefined && s.indexOf("none.gif") != -1) ) {
        AddHeaderFontIcon("icon-window-fill");
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
header('Etag: ' . $m);

if (isset($_SERVER["HTTP_IF_NONE_MATCH"]) && $_SERVER["HTTP_IF_NONE_MATCH"] == $m) {
    if (isset($zbp->option['ZC_JS_304_ENABLE']) && $zbp->option['ZC_JS_304_ENABLE']) {
        SetHttpStatusCode(304);
        die;
    }
}

echo $s;

die();
