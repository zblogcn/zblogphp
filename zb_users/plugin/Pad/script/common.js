var str01="名称格式不正确.";
var str02="邮箱格式不正确.";
var str03="内容为空或过长.";

//*********************************************************
// 目的：    回复留言
// 输入：    无
// 返回：    无
//*********************************************************
function RevertComment(i)
{

    if ($("#inpRevID").val()!=0) {
        return false;
    }

    $("#inpRevID").val(i);

    $("#AjaxComment"+i).next().after("<dl id=\"reply\" style=\"background-color:#f0f0f0;padding:10px;border-radius: 5px;\">"+$("#postcmt").html()+"</dl>");
    
    $("#postcmt").html("");
    
    $("#cancel-reply").show();
    
    $("#cancel-reply").prev().hide();
    
    $("#cancel-reply").bind("click", function () {
        $("#inpRevID").val(0);$(this).hide();$(this).prev().show();$("#postcmt").html($("#reply").html());$("#reply").remove();window.location.hash="#comment";return false; });

    window.location.hash="#comment";
}
//*********************************************************


function CommentComplete()
{
    $("#cancel-reply").click();
}
