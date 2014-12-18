$(document).ready(function() {
	var s = document.location;
	$("#divNavBar a").each(function() {
		if (this.href == s.toString().split("#")[0]) {
			$(this).addClass("on");
			return false;
		}
	});
});


//重写了common.js里的同名函数
function RevertComment(i) {
	$("#inpRevID").val(i);
	var frm = $('#divCommentPost'),
		cancel = $("#cancel-reply"),
		temp = $('#temp-frm');


	var div = document.createElement('div');
	div.id = 'temp-frm';
	div.style.display = 'none';
	frm.before(div);

	$('#AjaxComment' + i).before(frm);

	frm.addClass("reply-frm");

	cancel.show();
	cancel.click(function() {
		$("#inpRevID").val(0);
		var temp = $('#temp-frm'),
			frm = $('#divCommentPost');
		if (!temp.length || !frm.length) return;
		temp.before(frm);
		temp.remove();
		$(this).hide();
		frm.removeClass("reply-frm");
		return false;
	});
	try {
		$('#txaArticle').focus();
	} catch (e) {}
	return false;
}

//重写GetComments，防止评论框消失
function GetComments(logid, page) {
	$('span.commentspage').html("Waiting...");
	$.get(str00 + "zb_system/cmd.php?act=CommentGet&logid=" + logid + "&page=" + page, function(data) {
		$('#AjaxCommentBegin').nextUntil('#AjaxCommentEnd').remove();
		$('#AjaxCommentEnd').before(data);
		$("#cancel-reply").click();
	});
}


function CommentComplete() {
	$("#cancel-reply").click();
}