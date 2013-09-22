//*********************************************************
// 目的：    回复留言
// 输入：    无
// 返回：    无
//*********************************************************
function RevertComment(i) {

	if($("#inpRevID").val()!=0)return false;

	$("#inpRevID").val(i);

	$("#AjaxComment"+i).next().after("<dl id=\"reply\">"+$("#postcmt").html()+"</dl>");
	
	$("#postcmt").hide("slow");
	
	$("#postcmt").html("");
	
	$("#cancel-reply").show();
	
	$("#cancel-reply").prev().hide("");
	
	$("#cancel-reply").bind("click", function(){ $("#inpRevID").val(0);$(this).hide();$(this).prev().show();$("#postcmt").html($("#reply").html());$("#reply").remove();$("#postcmt").show("slow");window.location.hash="#comment";return false; });

	$("#reply").show("slow");
	
	window.location.hash="#reply";
}
//*********************************************************


function CommentComplete(){
$("#cancel-reply").click();
}