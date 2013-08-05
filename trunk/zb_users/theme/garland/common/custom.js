$(document).ready(function(){
	var havecateurl=false;
	if(typeof(cateurl)!="undefined"){
		if(cateurl!="<#article/category/id#>") havecateurl=true;
	}
	
	if(!$(".mutuality li").length){$("ul.mutuality").hide()}
	if(!$(".comment .even").length){$("h2.comment").hide()}
	$(".post-tags").each(function(){if($(this).find('a').length==0){$(this).hide()}});
	var s=document.location;
	$("#divNavBar a").each(function(){
		if(havecateurl){
			if(this.href==cateurl){$(this).addClass("on");return false;}
		}
		else{
			if(this.href==s.toString().split("#")[0]){$(this).addClass("on");return false;}
		}
	});
});
//本条留言DomID,本条留言class,内容class,评论框DomID,指定父ID
function ReComment(comId,comClass,mClass,frmId,i){
	$("#inpRevID").val(i);
	var comm=$('#'+comId),frm=$('#'+frmId),cancel=$("#cancel-reply"),temp = $('#temp-frm');
	if ( ! comm.length || ! frm.length || ! cancel.length)return;
	if ( ! temp.length ) {
			var div = document.createElement('div');
			div.id = 'temp-frm';
			div.style.display = 'none';
			frm.before(div);
	}
	if (comm.has('.'+comClass).length){comm.find('.'+comClass).first().before(frm);}
	else comm.find('.'+mClass).first().append(frm);
	frm.addClass("reply-frm");

	cancel.show();
	cancel.click(function(){
		$("#inpRevID").val(0);
		var temp = $('#temp-frm'), frm=$('#'+frmId);
		if ( ! temp.length || ! frm.length )return;
		temp.before(frm);
		temp.remove();
		$(this).hide();
		frm.removeClass("reply-frm");
		return false;
	});
	try { $('#txaArticle').focus(); }
	catch(e) {}
	return false;
}
//重写GetComments，防止评论框消失
function GetComments(logid,page){
	$('span.commentspage').html("Waiting...");
	$.get(str00+"zb_system/cmd.asp?act=CommentGet&logid="+logid+"&page="+page, function(data){
		$("#cancel-reply").click();
		$('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove();
		$('#AjaxCommentEnd').before(data);
	});
}

$(document).ready(function(){ 
	$("#mission").load(bloghost+"/zb_users/theme/garland/include/new.html");
});
