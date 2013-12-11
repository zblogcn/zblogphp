Main.Config = {
	BodyContent:".bodymain",	// 框选事件起始的dom元素
	FileBoxSelector:'.fileContiner',// dd
	FileBoxClass:".file",		// 文件选择器
	FileBoxClassName:"file",	// 文件选择器	
	FileBoxTittleClass:".title",// 文件名选择器
	SelectClass:".select",		// 选中文件选择器
	SelectClassName:"select",	// 选中文件选择器名称
	TypeFolderClass:'folderBox',// 文件夹标记选择器
	TypeFileClass:'fileBox',	// 文件标记选择器
	HoverClassName:"hover",		// hover类名
	FileOrderAttr:"number",		// 所有文件排序属性名
	navbar:'navbar',			// 头部导航栏选择器
	AnimateTime:200				// 动画时间设定
};
Main.Global = {
	fileListAll:'',				// 当前路径下文件对象集合,缓存起来便于全局使用
	fileListNum:0,				// 文件&文件夹总个数
	fileRowNum:0,				// 当前屏幕每行文件&文件夹个数
	topbar_height:40,			// 头部高度
	
	fileListSelect:'',			// 选择的文件
	fileListSelectNum:'',		// 选中的文件数。
	isIE:!-[1,],				// 是否ie
	isDragSelect:false			// 是否框选
};


$(document).ready(function () {
    $(".bodymain").click(function () {
    	if ($("#menuwin").css("display")=='block') {
    		$("#menuwin").css("display", "none");
    	}
    });
    $(".start").click(function () {
    	if ($("#menuwin").css("display")=='block') {
    		$("#menuwin").css("display", "none");
    	}else{
    		$("#menuwin").css("display", "block");
    	}
    });
    $("#menuwin").click(function () {
    	$("#menuwin").css("display", "none");
    });    

});

Main.SetSelect = {	
	init:function(){//初始化页面，缓存jquery所有文件对象		
		var $listAll = $(Main.Config.FileBoxClass);
		$listAll.each(function(index){
			$(this).attr(Main.Config.FileOrderAttr,index);
		});	
		Main.Global.fileListAll = $listAll;
		Main.Global.fileListNum = $listAll.length;
	},

	//选择处理
	select:function(){
		var $list = $(Main.Config.SelectClass);
		Main.Global.fileListSelect = $list;
		Main.Global.fileListSelectNum = $list.length;
		if ($list.length > 1) {				
			$list.removeClass("menufile menufolder menudesktop").addClass("menuMore");
		}
	},
	//获取文件&文件夹名字
	getObjName:function($obj){
		return $obj.find(".title").text();
	},
	//获取文件&文件夹类型 folder为文件夹，其他为文件扩展名
	getObjType:function($obj){			
		return $obj.find(".ico").attr("filetype");
	},
	//获取桌面系统图标类型
	getObjSystem:function($obj){			
		return $obj.find(".ico").attr("system");
	},	
	//已有的情况下，选择则标记右键菜单标记
	setMenu:function($obj){
		$obj.removeClass("menufile menufolder menudesktop").addClass("menuMore");
	},
	//反选，或者框选已经选择的则恢复右键菜单标记
	resumeMenu:function($obj){
		if ($obj.hasClass("fileBox")) {
			$obj.removeClass("menuMore").addClass("menufile");
		}
		if ($obj.hasClass("folderBox")) {
			$obj.removeClass("menuMore").addClass("menufolder");
		}
		if ($obj.hasClass("systemBox")) {
			$obj.removeClass("menuMore").addClass("menudesktop");
		}		
	},

	//清空选择，还原右键关联menu		
	clear:function(){
		if (Main.Global.fileListSelectNum == 0) return;
		var $list = Main.Global.fileListSelect;
		$list.removeClass(Main.Config.SelectClassName);
		$list.each(function(){
			if ($(this).hasClass("fileBox")) {
				$(this).removeClass("menuMore").addClass("menufile");
			}
			if ($(this).hasClass("folderBox")) {
				$(this).removeClass("menuMore").addClass("menufolder");
			}
			if ($(this).hasClass("systemBox")) {
				$(this).removeClass("menuMore").addClass("menudesktop");
			}					
		});
		Main.Global.fileListSelect = '';
		Main.Global.fileListSelectNum = 0;
	}
};

//__________________________________________________________________________________//
Main.UI = (function() {
	//ajax后重置数据、重新绑定事件(f5或者list更换后重新绑定)
	var _ajaxLive = function(){		
		Main.SetSelect.init();
		Main.PathOpen.initPicasaData();
		Main.UI.setStyle();
		_ieCss();
	}
	var _bindHotKey = function(){
		$(document).keydown(function (event){
			if (Main.UI.isImagePlay()) return true;
			if (Main.UI.isEdit()) return true;//编辑状态
			if (Main.RightMenu.isDisplay()) return true;			

			var isStopPP = false;//是否向上拦截冒泡
			//ctrl 组合键 console.log(event.keyCode)
			if (event.ctrlKey) {
				switch(event.keyCode){
					case 65://CTRL+A	全选
						//event.keyCode=0;
						//event.returnValue=false;//拦截向上消息冒泡
						Main.FileSelect.selectPos('all');break;
					case 67://CTRL+C 复制
						Main.PathOperate.pathCopy();
						break;
					case 88://CTRL+X 剪切
						Main.PathOperate.pathCute();
						break;
					case 83:isStopPP = true;break; 	// 屏蔽ctrl + s 
					case 86://CTRL+V 粘贴
						Main.SetSelect.clear();
						Main.PathOperate.pathPast();
						break;
					default:break;					
				}				
			}else if(event.shiftKey) {
				//console.log("shiftKey+"+event.keyCode);
			}else{
				isStopPP=true;
				switch (event.keyCode) {			
					case 35:Main.FileSelect.selectPos('end');break;
					case 36:Main.FileSelect.selectPos('home');break;
					case 37:Main.FileSelect.selectPos('left');isStopPP=false;break;
					case 38:Main.FileSelect.selectPos('up');break;
					case 39:Main.FileSelect.selectPos('right');isStopPP=false;break;
					case 40:Main.FileSelect.selectPos('down');break;
					case 13:Main.PathOpen.open(event.keyCode);isStopPP=false;break;//enter 打开文件==双击
					case 46:Main.PathOperate.pathDelete();break;
					case 113:Main.PathOperate.pathRname();break;//f2重命名
					default:isStopPP=false;break;
				}
			}
			if (isStopPP) {
				stopPP();
				event.keyCode=0;
				event.returnValue=false;//拦截向上消息冒泡				
			}
			return true;
		});		
	};

	var _ieCss = function(){
		if (!Main.Global.isIE 
			&& navigator.userAgent.indexOf("Firefox")<0) return;
		var top 	= 10;
		var left 	= 10;
		var width 	= 80;
		var height 	= 100;
		var margin 	= 10;

		var w_height= $(document).height() - 60;
		var col_num   = Math.floor((w_height-top)/(height+margin));
		var row=0,col=0,x=0,y=0;
		$('.fileContiner .file').css('position','absolute');
		$('.fileContiner .file').each(function(i){
			row = i%col_num;
			col = Math.floor(i/col_num);
			x = left + (width+margin)*col;
			y = top + (height+margin)*row;

			$(this).css({'left':x,'top':y});
		});
	}

	//列表排序操作。
	var _jsonSort = function(field,order){
		//如果传入0,则不修改排序方式
		json_sort_field = (field==0)?json_sort_field:field;
		json_sort_order = (order==0)?json_sort_order:order;	
		_f5(false,true);//使用本地列表
		$.ajax({
			url:'?setting/setListSort&field='+
				json_sort_field+'&order='+json_sort_order
		});
	};
	//
	var _getDesktopBox = function(desktop){
		var html="";
		html+="<div class='file systemBox menudesktop' title='"+desktop['name']+"'>";
		html+="<div class='"+desktop['type']+" ico' filetype='system' system='"+desktop['type']+"'></div>";
		html+="<div id='"+desktop['name']+"' class='titleBox'><span>"+desktop['name']+"</span></div></div>";
		return html;
	}

	//图标样式，文件夹模版填充
	var _getFolderBox = function(folder){
		var html="";
		html+="<div class='file folderBox menufolder' title='"+folder['name']+"'>";
		html+="<div class='folder ico' filetype='folder'></div>";
		html+="<div id='"+folder['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+folder['name']+"</span></div></div>";
		return html;
	}
	//图标样式，文件模版填充
	var _getFileBox = function(file){
		var html="";
		var filePath = web_host+urlDecode(web_path)+urlEncode(file['name']);
		var thumbPath = '?explorer/image&path='+this_path+urlEncode(file['name']);
		if (inArray(Main.Common.filetype['image'],file['ext'])) {//如果是图片，则显示缩略图，并绑定幻灯片插件。运行缓慢？？！
			html+="<div class='file fileBox menufile' title='"+file['name']+"' >";
			html+="<div picasa='"+filePath+"' thumb='"+thumbPath+"' title='"+file['name']+"' class='picasaImage picture ico' filetype='"+file['ext']+"' style='margin:3px 0 0 8px;background:url("+thumbPath+");'></div>";
			html+="<div id='"+file['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+file['name']+"</span></div></div>";
		}
		else{
			html+="<div class='file fileBox menufile' title='"+file['name']+"' >";
			html+="<div class='"+file['ext']+" ico' filetype='"+file['ext']+"'></div>";
			html+="<div id='"+file['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+file['name']+"</span></div></div>";
		}	
		return html;
	}

	//文件列表数据填充
	var _mainSetData = function(isFade){
		var listhtml="";//填充的数据
		var folderlist	= json_data['folderlist'];
		var filelist	= json_data['filelist'];
		var desktop		= json_data['desktop'];

		//排序方式重组json数据-------------------
		if (folderlist) {
			//如果排序字段为size或ext时，文件夹排序方式按照文件名排序
			if (json_sort_field=='size' || json_sort_field=='ext' ){
				folderlist= folderlist.sort(_sortBy('name',json_sort_order));
				json_data['folderlist']=folderlist;//同步到页面数据
			}
			else {
				folderlist= folderlist.sort(_sortBy(json_sort_field,json_sort_order));
				json_data['folderlist']=folderlist;//同步
			}
		}
		if (filelist){
			filelist = filelist.sort(_sortBy(json_sort_field,json_sort_order));	
			json_data['filelist']=filelist;//同步到页面数据
		}

		if (desktop) {
			desktop_len = desktop.length;
			for (var i=0; i < desktop_len; i++) {
				listhtml += _getDesktopBox(desktop[i]);
			}
		}	
		//end排序方式重组json数据-----------------------------
		//升序时，都是文件夹在上，文件在下，各自按照字段排序
		if (json_sort_order=='up'){	
			if (folderlist) {
				folderlist_len = folderlist.length;
				for (var i=0; i < folderlist_len; i++){
					listhtml += _getFolderBox(folderlist[i]);
				}
			}
			if (filelist) {
				filelist_len = filelist.length;
				for (var i=0; i < filelist_len; i++) {
					listhtml += _getFileBox(filelist[i]);
				}
			}			
		}else if(json_sort_order=='down'){//降序时，都是文件夹在下，文件在上，各自按照字段排序
			if (filelist) {
				filelist_len = filelist.length;
				for (var i=0; i < filelist_len; i++) {
					listhtml += _getFileBox(filelist[i]);
				}
			}
			if (folderlist) {
				folderlist_len = folderlist.length;
				for (var i=0; i < folderlist_len; i++){
					listhtml += _getFolderBox(folderlist[i]);
				}
			}
		}
		if (listhtml == '') {
			listhtml = '<div style="text-align:center;color:#aaa;">该文件夹为空，可以拖拽文件到该窗口上传。</div>'
		}
		listhtml += "<div style='clear:both'></div>";
		//填充到dom中-----------------------------------
		if (isFade){//动画显示,
			$(Main.Config.FileBoxSelector)
				.css("display",'none')
				.html(listhtml)
				.fadeIn(Main.Config.AnimateTime);
		}else{
			$(Main.Config.FileBoxSelector).html(listhtml);				
		}
		_ajaxLive();
	};
	//json 排序 filed:(string)排序字段，orderby:升降序。升序为-1，降序为1
	var _sortBy = function(filed,orderby) {
		var orderby=(orderby=='down')? -1 : 1;
		return function (a, b) {
			a = a[filed];
			b = b[filed];
			if (a < b) 	return orderby * -1;
			if (a > b) 	return orderby * 1;
		}
	}
	var _f5 = function(is_data_server,is_animate) {
		if(is_data_server == undefined) is_data_server = true; //默认每次从服务器取数据
		if(is_animate == undefined)		is_animate = false;	   //默认不用渐变动画
		if(!is_data_server){//采用当前数据刷新,用于显示模式更换
			_mainSetData(is_animate);
		}
		else{//获取服务器数据
			$.ajax({
				url:'?explorer/desktop',
				dataType:'json',
				//async:false,//同步阻塞.阻塞其他线程，等待执行完成。//解决重命名后设置选中
				success:function(data){
					if (data == false) {				
						Main.Common.tips.tips('目录不存在或没有权限访问该目录');
						$(Main.Config.FileBoxSelector).html('');
						return false;
					}
					json_data = data;
					_mainSetData(is_animate);
				},
				error:function(data){
					Main.Common.tips.tips('系统错误');	
					$(Main.Config.FileBoxSelector).html('');
				}
			});		
		}
	};
	return{	
		f5:_f5,
		setTheme:function(thistheme){
			//window.top.location.reload();
			var url = static_path+'style/skin/'+thistheme+'/app_desktop.css';
			$("#link_css_list").attr("href",url);
			FrameCall.top('OpenopenEditor','Main.Editor.setTheme','"'+thistheme+'"');
		},
		clearWindows:function(){//清除右键菜单，修改重命名状态
			Main.RightMenu.hidden();
			$('#pathRenameTextarea').blur();
		},
		isEdit:function(){
			var focusTagName = $(document.activeElement).get(0).tagName;
			if (focusTagName == 'INPUT' || focusTagName == 'TEXTAREA'){
				return true;
			}
			return false;
		},
		isImagePlay:function(){
			if ($('#PicasaView').css('display') == 'none') return false;
			return true;
		},
		init:function(){
			_f5(true,false);//生成文件列表
			_ieCss();
			_bindHotKey();
		
			// 设置类型
			$(Main.Config.FileBoxSelector).removeClass('fileList_list').addClass('fileList_icon');
			$('#list_type_list').html('');

			$("html").unbind('click').live('click',function (e) {
				if (Main.Global.isIE && Main.Global.isDragSelect) return;
				if (!e.shiftKey && !e.ctrlKey){
					Main.SetSelect.clear();
				}
			});
			
			$(window).bind("resize",function(){
				Main.UI.setStyle();//浏览器调整大小，文件列表区域调整高宽。
				if (PicasaOpen!=false) {
					PicasaOpen.setFrameResize();
				}
			});
		},
		fullScreen:function(){
			if ($('body').attr('fullScreen') == 'true') {
				Main.UI.exitfullScreen();
			}
			$('body').attr('fullScreen','true');
			var docElm = document.documentElement;			
            if (docElm.requestFullscreen) {
                docElm.requestFullscreen();
            }else if (docElm.mozRequestFullScreen) {
                docElm.mozRequestFullScreen();
            } else if (docElm.webkitRequestFullScreen) {
                docElm.webkitRequestFullScreen();
            }
		},
		exitfullScreen:function(){
			$('body').attr('fullScreen','false');
			if (document.exitFullscreen) {
			    document.exitFullscreen();
			}else if(document.mozCancelFullScreen) {
			    document.mozCancelFullScreen();
			}else if(document.webkitCancelFullScreen) {
			    document.webkitCancelFullScreen();
			}
		},

		setStyle:function(){//设置文件列表高宽。
			//main当前宽度所容纳每行文件个数。
			_ieCss();
			Main.Global.fileRowNum = (function(){
				var main_height=$(Main.Config.FileBoxSelector).height();//获取main主体的
				var file_height=
					parseInt($(Main.Config.FileBoxClass).css('height'))+
					parseInt($(Main.Config.FileBoxClass).css('border-top-width'))+
					parseInt($(Main.Config.FileBoxClass).css('border-bottom-width'))+
					parseInt($(Main.Config.FileBoxClass).css('margin-top'))+
					parseInt($(Main.Config.FileBoxClass).css('margin-bottom'));
				return parseInt(main_height/file_height);
			})();		
		},
		json_sort:function(field,order){
			//如果传入0,则不修改排序方式
			json_sort_field = (field==0)?json_sort_field:field;
			json_sort_order = (order==0)?json_sort_order:order;	
			_f5(false,false);//使用本地列表
			$.ajax({
				url:'?setting/setListSort&field='+
					json_sort_field+'&order='+json_sort_order
			});
		},
		//编辑器全屏
		editorFull:function(){
			var $frame = $('iframe[name=OpenopenEditor]');
			$frame.toggleClass('frame_fullscreen');
		}
	}
})();
