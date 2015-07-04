function heartVote(vote,id){
	var s=$("div.heart-vote").find("b").text();
	var t=$("div.heart-vote").find("i").text();
	$("div.heart-vote").find("i").text("打分中.....");
	$("div.heart-vote").find("b").css("visibility","hidden");
	$.post(bloghost + "zb_users/plugin/HeartVote/vote.php",
		{
		"vote":vote,
		"id":id
		},
		function(data){
			if(data.indexOf("|")==-1){
				alert(data);
				$("div.heart-vote").find("i").text(s);
				$("div.heart-vote").find("i").text(t);
			}
			else{
				var i=data.split("|")[0];
				var j=data.split("|")[1];
				showVote(i,j);
			}
			$("div.heart-vote").find("b").css("visibility","visible");
		}
	);
}

function showVote(vote,id){
			if(!vote){vote=0}
			$("div.heart-vote").find("li.current-rating").css("width",30*vote+"px");
			$("div.heart-vote").find("b").text(vote);
			$("div.heart-vote").find("i").text("分/"+ id + "个投票");
}