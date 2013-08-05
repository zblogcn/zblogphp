function pageScroll(){
    //把内容滚动指定的像素数（第一个参数是向右滚动的像素数，第二个参数是向下滚动的像素数）
    window.scrollBy(0,-100);
    //延时递归调用，模拟滚动向上效果
    scrolldelay = setTimeout('pageScroll()',100);
    //获取scrollTop值，声明了DTD的标准网页取document.documentElement.scrollTop，否则取document.body.scrollTop；因为二者只有一个会生效，另一个就恒为0，所以取和值可以得到网页的真正的scrollTop值
    var sTop=document.documentElement.scrollTop+document.body.scrollTop;
    //判断当页面到达顶部，取消延时代码（否则页面滚动到顶部会无法再向下正常浏览页面）
    if(sTop==0) clearTimeout(scrolldelay);
}

function ReComment_CallBack(){for(var i=0;i<=ReComment_CallBack.list.length-1;i++){ReComment_CallBack.list[i]()}}
ReComment_CallBack.list=[];
ReComment_CallBack.add=function(s){ReComment_CallBack.list.push(s)};
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
		ReComment_CallBack();
		return false;
	});
	try { $('#txaArticle').focus(); }
	catch(e) {}
	ReComment_CallBack();
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
	var havecateurl=false;
	var s=document.location;
	if(typeof(cateurl)!="undefined"){
		if(cateurl!="<#article/category/id#>") havecateurl=true;
	}
	$(".navbar a").each(function(){
		if(havecateurl){
			if(this.href==cateurl){$(this).addClass("on");return false;}
		}
		else{
			if(this.href==s.toString().split("#")[0]){$(this).addClass("on");return false;}
		}
	});

	if($(".commentlist .msg").length==0){$(".commentlist").hide()} //隐藏空评论
	if($(".commentpost").children().length==1){$(".commentpost").hide()} //关闭评论时隐藏
	try { var c=$.trim($(".mutuality_body").html());  if(!c){$(".mutuality").hide()} }catch(e) {}//隐藏空相关文章
	$(".post_tags").each(function(){if($(this).find('a').length==0){$(this).hide()}}); //隐藏空tags

});