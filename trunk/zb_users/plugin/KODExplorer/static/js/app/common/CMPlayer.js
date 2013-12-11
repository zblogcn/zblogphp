var CMPlayer = {
	_playType:'',//music,movie
	_skin:{
		ting:{path:'music/ting',width:410,height:530},
		beveled:{path:'music/beveled',width:350,height:200},
		kuwo:{path:'music/kuwo',width:480,height:200},
		manila:{path:'music/manila',width:320,height:400},
		mp3player:{path:'music/mp3player',width:320,height:400},
		qqmusic:{path:'music/qqmusic',width:300,height:400},
		somusic:{path:'music/somusic',width:420,height:137},
		xdj:{path:'music/xdj',width:595,height:235},

		//---适合视频播放
		webplayer:{path:'movie/webplayer',width:600,height:400},
		qqplayer:{path:'movie/qqplayer',width:600,height:400},
		tvlive:{path:'movie/tvlive',width:600,height:400},
		youtube:{path:'movie/youtube',width:600,height:400},
		vplayer:{path:'movie/vplayer',width:600,height:400}
	},
	// 创建播放器；动态获取皮肤以及对应大小尺寸
	_create:function(type){
		if(type == undefined) type = 'mp3';
		var playerSkin,playerTitle;	
		if (inArray(Main.Common.filetype['music'],type)) {
			CMPlayer._playType = 'music';
			playerSkin = CMPlayer._skin[musictheme];
			playerTitle= '音乐播放';
		}else {
			CMPlayer._playType = 'movie';
			playerSkin = CMPlayer._skin[movietheme];
			playerTitle= '视频播放器';
		}
		var htmlContent = 			
			'<object type="application/x-shockwave-flash" id="cmp_media" data="./static/js/cmp4/cmp.swf" width="100%" height="100%">'
			+	'<param name="movie" value="./static/js/cmp4/cmp.swf" />'
			+	'<param name="allowfullscreen" value="true" />'
			+	'<param name="allowscriptaccess" value="always" />'
			+	'<param name="flashvars" value="context_menu=2&auto_play=1&play_mode=1&skin=skins/'+playerSkin.path+'.zip" />'
			+	'<param name="wmode" value="transparent" />'
			+'</object>';
		var playerDialog = {
			id:'music_player',
			title:playerTitle,
			width:playerSkin.width,							
			height:playerSkin.height,
			content:htmlContent,
			resize:true,
			padding:0,
			fixed:true		
		}
		if (window.top.CMP){
			art.dialog.through(playerDialog);
		}else{
			$.dialog(playerDialog);
		}
	},
	// 文件数组创建播放器列表
	_makeList:function(fileList){
		var play_url,i,xml='';
		for (i = fileList.length - 1; i >= 0; i--) {
			play_url=web_path+encodeURIComponent(fileList[i]);
			if(!-[1,]){//ie
				play_url=web_path+encodeURI(fileList[i]);
			}
			play_url=play_url.replace(/%3A/g,':');
			play_url=play_url.replace(/%2F/g,'/');
			play_url=play_url.replace(/\+/g,' ');
			play_url=web_host+play_url;
			xml +='<list><m type="" src="'+play_url+'" label="'+fileList[i]+'"/></list>';
		};
		return xml;
	},
	//获取播放器
	_get:function(){
		if (window.top.CMP) {
			return window.top.CMP.get("cmp_media");
		}else{
			return CMP.get("cmp_media");
		}		
	},
	_insert:function(fileList){
		var new_list = CMPlayer._makeList(fileList);
		var cmpo = CMPlayer._get();
		if (cmpo) {
			cmpo.config('play_mode', 'normal');//写入配置,播放模式改为自动跳到next
			cmpo.list_xml(new_list,true);			
			if (fileList.length==1) {//若只有一首则加入到最后时，播放最后一首
				cmpo.sendEvent('view_play',cmpo.list().length);
			}else{
				cmpo.sendEvent('view_play');
			}							
		}
	},
	changeTheme:function (music,movie) {
		movietheme = movie;
		musictheme = music;	
		
		//如果存在播放器，则实时改变皮肤。
		var cmpo = CMPlayer._get();
		var playerSkin,player;		
		if (cmpo) {
			if (CMPlayer._playType == 'music') {
				playerSkin = CMPlayer._skin[music];
			}else{
				playerSkin = CMPlayer._skin[movie];
			}
			window.top.art.dialog.list['music_player'].size(playerSkin.width,playerSkin.height);
			cmpo.sendEvent("skin_load",'skins/'+playerSkin.path+'.zip');
		}
	},
	play:function(fileList,type){
		var cmpo = CMPlayer._get();
		if (!cmpo) {
			CMPlayer._create(type);
			setTimeout(function(){
				CMPlayer._insert(fileList);
			},1000);
		}else{
			CMPlayer._insert(fileList);
			$('.music_player').css('visibility','visible');
		}		
	}
};