
$(document).ready(function() {
	$(".heartcomment").click(function() {
		$('.heartcomment-selected').removeClass('heartcomment-selected');
		$(this).addClass('heartcomment-selected').mouseover();

	}).mouseover(function() {
		$('.heartcomment-vote').removeClass('heartcomment-vote')
		var data = $(this).data("score");
		for(var i = 1; i <= parseInt(data); i++) {
			$(".r" + i + "-unit").addClass("heartcomment-vote");
		}
	}).mouseout(function() {
		$('.heartcomment-selected').mouseover();
	});
});

function VerifyMessage() {

	var strFormAction = $("#inpId").parent("form").attr("action");
	var strName = $("#inpName").val();
	var strEmail = $("#inpEmail").val();
	var strHomePage = $("#inpHomePage").val();
	var strVerify = $("#inpVerify").val();
	var strArticle = $("#txaArticle").val();
	var intReplyID = $("#inpRevID").val();
	var intPostID = $("#inpId").val();
	var intScore = $(".heartcomment-selected").data("score");
	
	var intMaxLen = 10000;
	

	if(strName == ""){
		alert(str01);
		return false;
	}
	else {
		re = new RegExp("^[\.\_A-Za-z0-9\u4e00-\u9fa5]+$");
		if (!re.test(strName)){
			alert(str02);
			return false;
		}
	}

	if(strEmail == ""){
		//alert(str01);
		//return false;
	}
	else{
		re = new RegExp("^[\\w-]+(\\.[\\w-]+)*@[\\w-]+(\\.[\\w-]+)+$");
		if (!re.test(strEmail)){
			alert(str02);
			return false;
		}
	}

	if(typeof(strArticle) == "undefined"){
		alert(str03);
		return false;
	}

	if(typeof(strArticle) == "string"){
		/*(if(strArticle==""){
			alert(str03);
			return false;
		}*/
		if(strArticle.length > intMaxLen)
		{
			alert(str03);
			return false;
		}
	}

	//ajax comment begin
	var strSubmit = $("#inpId").parent("form").find(":submit").val();
	$("#inpId").parent("form").find(":submit").val("Waiting...").attr("disabled","disabled").addClass("loading");

	$.post(strFormAction,
		{
		"isajax": true,
		"postid": intPostID,
		"verify": strVerify,
		"name": strName,		
		"email": strEmail,
		"content": strArticle,
		"homepage": strHomePage,
		"replyid": intReplyID,
		"score": intScore
		},
		function(data){
		
			$("#inpId").parent("form").find(":submit").removeClass("loading");
			$("#inpId").parent("form").find(":submit").removeAttr("disabled");
			$("#inpId").parent("form").find(":submit").val(strSubmit);
		
			var s =data;
			if((s.search("faultCode")>0)&&(s.search("faultString")>0))
			{
				alert(s.match("<string>.+?</string>")[0].replace("<string>","").replace("</string>",""))
			}
			else{
				var s =data;
				var cmt=s.match(/cmt\d+/);
				if(intReplyID==0){
					$(s).insertAfter("#AjaxCommentBegin");
				}else{
					$(s).insertAfter("#AjaxComment"+intReplyID);
				}
				window.location="#"+cmt;
				$("#txaArticle").val("");
				
				SaveRememberInfo();
				CommentComplete();
			}

		}
	);

	return false;
	//ajax comment end

}