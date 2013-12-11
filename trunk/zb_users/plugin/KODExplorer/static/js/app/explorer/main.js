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

	treeAjaxURL:"?explorer/folderList&firstpath=",//树目录请求
	TreeId:"folderList",        // 目录树对象
	AnimateTime:200				// 动画时间设定
};
Main.Global = {
	fileListAll:'',				// 当前路径下文件对象集合,缓存起来便于全局使用
	fileListNum:0,				// 文件&文件夹总个数
	fileRowNum:0,				// 当前屏幕每行文件&文件夹个数
	frameLeftWidth:200,			// 左边树目录宽度
	treeSpaceWide:10,			// 树目录层级相差宽度
	topbar_height:40,			// 头部高度

	fileListSelect:'',			// 选择的文件
	fileListSelectNum:'',		// 选中的文件数。
	isIE:!-[1,],				// 是否ie
	isDragSelect:false,			// 是否框选
	historyStatus:{back:1,next:0}	// 是否可以前进后退操作状态
};


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
			$list.removeClass("menufile").removeClass("menufolder").addClass("menuMore");
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
	//已有的情况下，选择则标记右键菜单标记
	setMenu:function($obj){
		$obj.removeClass("menufile menufolder").addClass("menuMore");
	},
	//反选，或者框选已经选择的则恢复右键菜单标记
	resumeMenu:function($obj){
		if ($obj.hasClass("fileBox")) {
			$obj.removeClass("menuMore").addClass("menufile");
		}
		if ($obj.hasClass("folderBox")) {
			$obj.removeClass("menuMore").addClass("menufolder");
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
	}
	//文件列表 列表模式和图标模式切换,
	//动态加载css,本页面json刷新。
	var _setListType = function (thistype,firstRun){
		list_type=thistype;
		if (firstRun == undefined) firstRun = false;		
		$('.tools-right a.this').removeClass('this');
		$('#set_'+thistype).addClass('this');

		if (thistype=='list') {
			$(Main.Config.FileBoxSelector).removeClass('fileList_icon').addClass('fileList_list');
			$('#list_type_list').html(
				'<div id="main_title">'+
					'<div class="filename" field="name">名称<span></span></div>'+
					'<div class="filetype" field="ext">类型<span></span></div>'+
					'<div class="filesize" field="size">大小<span></span></div>'+
					'<div class="filetime" field="mtime">修改日期<span></span></div>'+
					'<div style="clear:both"></div>'+
				'</div>'
			);

		}else{
			$(Main.Config.FileBoxSelector).removeClass('fileList_list').addClass('fileList_icon');
			$('#list_type_list').html('');
		}

		//同步到右键菜单
		$('.menu_seticon').removeClass('selected');
		$('.set_set'+list_type).addClass('selected');
		_f5(false,firstRun);
	};
	//标题栏排序方式点击
	var _bindEventSort = function(){
		$('#main_title div').unbind('click').live('click',function(){
			if ($(this).attr('id')=='up'){
				$(this).attr('id','down');
			}
			else $(this).attr('id','up');
			_jsonSort($(this).attr('field'),$(this).attr('id'));
		});
	};
	var _bindEventTools = function(){
		$('.tools a').unbind('click').live('click',function(){
			var todo = $(this).attr('id');
			_toolsAction(todo);
		});
	};	
	var _bindEventTheme = function(){
		//主题切换
		$(".theme_list").mouseleave(function (){
			$(this).css("display","none");
		});
		$(".theme_list li").mouseenter(function () {
			var theme="";
			$(this).addClass("themehover");	
			$(this).click(function(){//点击选中
				$(".theme_list").css("display","none");
				theme=$(this).attr("theme");
				$.ajax({
					url: '?setting/setTheme&theme='+theme,
					success: function(data) {
						Main.UI.setTheme(theme);
					}
				});
				$(".theme_list .this").toggleClass('this');
				$(this).addClass('this');
			});
		}).mouseleave(function (){
			$(this).toggleClass("themehover");
		});	
	};


	var _bindFrameSizeEvent= function(){
		var isDraging 		= false;
		var mouseFirst		= 0;
		var leftwidthFirst 	= 0;

		var $left = $('.frame-left');
		var $drag = $('.frame-resize');
		var $right= $('.frame-right');

		$drag.unbind('mousedown').live('mousedown',function(e){
			if (e.which != 1) return true;
			__dragStart(e);
            //事件 在 window之外操作，继续保持。
			if(this.setCapture) this.setCapture();
			$(document).mousemove(function(e) {__dragMove(e);});
			$(document).one('mouseup',function(e) {				
				__dragEnd(e);
				if(this.releaseCapture) {this.releaseCapture();}
				stopPP(e);return false;
			});
		});
		var __dragStart = function(e){
			isDraging = true;
			mouseFirst = e.pageX;
			leftwidthFirst = $('.frame-left').width();
			$drag.addClass('active');
		};
		var __dragMove = function(e){
			if (!isDraging) return true;

			var mouseOffset = e.pageX - mouseFirst;
			var offset = leftwidthFirst+mouseOffset;
			if (offset < 50) offset = 50;
			if (offset > $(document).width()-200) offset = $(document).width()-200;

			$left.css('width',offset);
			$drag.css('left',offset-5);
			$right.css('left',offset+1);
			Main.UI.setStyle();
		};
		var __dragEnd = function(e){
			if (!isDraging) return false;
			isDraging = false;
			$drag.removeClass('active');
			Main.Global.frameLeftWidth = $('.frame-left').width();
		};
	};

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
					case 8:Main.PathOpen.pathBack();break;						
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
	//针对排序方式更新标题栏显示
	var _jsonSortTitle = function(){
		var up='<i class="font-icon icon-chevron-up"></i>';
		var down='<i class="font-icon icon-chevron-down"></i>';
		$('#main_title .this')
			.toggleClass('this')
			.attr('id','')
			.find('span')
			.html("");		
		$('#main_title div[field='+json_sort_field+']')
			.addClass('this')
			.attr('id',json_sort_order)
			.find('span')
			.html(eval(json_sort_order));
	};
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
		if (inArray(Main.Common.filetype['image'],file['ext'])) {//如果是图片，则显示缩略图
			html+="<div class='file fileBox menufile' title='"+file['name']+"' >";
			html+="<div picasa='"+filePath+"' thumb='"+thumbPath+"' title='"+file['name']+"' class='picasaImage picture ico' filetype='"+file['ext']+"' style='margin:3px 0 0 8px;background:url("+thumbPath+");'></div>";
			html+="<div id='"+file['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+file['name']+"</span></div></div>";
		}else{
			html+="<div class='file fileBox menufile' title='"+file['name']+"' >";
			html+="<div class='"+file['ext']+" ico' filetype='"+file['ext']+"'></div>";
			html+="<div id='"+file['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+file['name']+"</span></div></div>";
		}	
		return html;
	}
	//---------------------------------------
	//列表样式，文件夹模版填充
	var _getFolderBoxList = function(folder){
		var html="";
		html+="<div class='file folderBox menufolder' title='"+folder['name']+"(双击打开)'>";
		html+="	<div class='folder ico' filetype='folder'></div>";
		html+="	<div id='"+folder['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+folder['name']+"</span></div>";
		html+="	<div class='filetype'>文件夹</div>";
		html+="	<div class='filesize'></div>";
		html+="	<div class='filetime'>"+folder['mtime']+"</div>";
		html+="	<div style='clear:both'></div>";
		html+="</div>";
		return html;
	}
	//列表样式，文件模版填充
	var _getFileBoxList = function(file){
		var html="";
		var filePath = web_host+urlDecode(web_path)+urlEncode(file['name']);

		var thumbPath = '?explorer/image&path='+this_path+urlEncode(file['name']);
		if (inArray(Main.Common.filetype['image'],file['ext'])) {//如果是图片，则显示缩略图，并绑定幻灯片插件
			html+="<div picasa='"+filePath+"' thumb='"+thumbPath+"' title='"+file['name']+"' class='picasaImage file fileBox menufile'>";
		}else {
			html+="<div class='file fileBox menufile'  title='"+file['name']+"(双击打开)'>";				
		}

		html+="	<div class='"+file['ext']+" ico' filetype='"+file['ext']+"'></div>";	
		html+="	<div id='"+file['name']+"' class='titleBox'><span class='title' title='双击重命名'>"+file['name']+"</span></div>";
		html+="	<div class='filetype'>"+file['ext']+"  文件</div>";
		html+="	<div class='filesize'>"+file['size_friendly']+"</div>";
		html+="	<div class='filetime'>"+file['mtime']+"</div>";
		html+="	<div style='clear:both'></div>";
		html+="</div>";
		return html;
	};

	//文件列表数据填充
	var _mainSetData = function(isFade){
		var listhtml="";//填充的数据
		var folderlist	= json_data['folderlist'];
		var filelist	= json_data['filelist'];		
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
		//end排序方式重组json数据-----------------------------
		//升序时，都是文件夹在上，文件在下，各自按照字段排序
		if (json_sort_order=='up'){
			if (list_type=='list') {//列表方式DOM填充
				if (folderlist) {
					folderlist_len = folderlist.length;
					for (var i=0; i < folderlist_len; i++){
						listhtml += _getFolderBoxList(folderlist[i]);
					}
				}
				if (filelist) {
					filelist_len = filelist.length;
					for (var i=0; i < filelist_len; i++) {
						listhtml += _getFileBoxList(filelist[i]);
					}
				}
			}else if(list_type=='icon') {//图标方式填充
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
			}
		}else if(json_sort_order=='down'){//降序时，都是文件夹在下，文件在上，各自按照字段排序
			if (list_type=='list') {//列表方式DOM填充
				if (filelist) {
					filelist_len = filelist.length;
					for (var i=0; i < filelist_len; i++) {
						listhtml += _getFileBoxList(filelist[i]);
					}
				}
				if (folderlist) {
					folderlist_len = folderlist.length;
					for (var i=0; i < folderlist_len; i++){
						listhtml += _getFolderBoxList(folderlist[i]);
					}
				}
			}else if(list_type=='icon') {//图标方式填充
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

		if (list_type=='list') {//列表奇偶行css设置
			$(Main.Config.FileBoxSelector+" .file:nth-child(2n)").addClass('file2');
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
		_jsonSortTitle();//更新列表排序方式dom		
		if(!is_data_server){//采用当前数据刷新,用于显示模式更换
			_mainSetData(is_animate);
		}
		else{//获取服务器数据
			$.ajax({
				url:'?explorer/pathList&path='+this_path,
				dataType:'json',
				async:false,//同步阻塞.阻塞其他线程，等待执行完成。//解决重命名后设置选中
				beforeSend:function(){
					$('.tools-left .msg').fadeIn(100);
				},
				success:function(data){
					if (data == false || data == '-1') {
						$('.tools-left .msg').fadeOut(100);				
						Main.Common.tips.tips('目录不存在或没有权限访问该目录');
						$(Main.Config.FileBoxSelector).html('');
						return false;
					}
					json_data = data;
					Main.Global.historyStatus = json_data['history_status'];
					_mainSetData(is_animate);
					$('.tools-left .msg').fadeOut(100);
				},
				error:function(data){
					Main.Common.tips.tips('系统错误');
					$('.tools-left .msg').fadeOut(100);
					$(Main.Config.FileBoxSelector).html('');
				}
			});		
		}

		Main.UI.header.updateHistoryStatus();
		Main.UI.header.addressSet(urlDecode(this_path));//header地址栏更新		
	};
	//头部操作控制器。
	var _toolsAction = function(what){
		switch (what){
			case 'newfile':Main.PathOperate.newFile();break;
			case 'newfolder':Main.PathOperate.newFolder();break;
			case 'upload':Main.PathOperate.pathUpload();break;	
			case 'set_icon':
				if(!$('#set_icon').hasClass('this')){
					$.ajax({
						url:'?setting/setIcon',
						success:function(data){
							_setListType('icon');
						}
					});		
				}
				break;
			case 'set_list':
				if(!$('#set_list').hasClass('this')){
					$.ajax({
						url:'?setting/setList',		
						success:function(data){
							_setListType('list');
						}
					});
				}
				break;
			case 'set_theme':
				$(".theme_list").css("display","block");break;
			default:break;
		}
	};
	return{	
		f5:_f5,
		setListType:_setListType,
		setTheme:function(thistheme){
			//window.top.location.reload();
			var url = static_path+'style/skin/'+thistheme+'/app_explorer.css';
			$("#link_css_list").attr("href",url);
			FrameCall.top('OpenopenEditor','Main.Editor.setTheme','"'+thistheme+'"');
			FrameCall.father('Main.UI.setTheme','"'+thistheme+'"');
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
			//是否在框架中【dialog or not】
			if (self != top) {
				Main.Global.topbar_height = 0;		
				$('.navbar').remove();
				$('.frame-header').css('top','0px');
				$('.frame-main').css('top','50px');
			}


			_f5(true,false);//生成文件列表
			_bindEventSort();
			_bindEventTheme();
			_bindEventTools();
			_bindHotKey();
			_bindFrameSizeEvent();			
			Main.UI.header.bindEvent();
			Main.UI.tree.init();
			$(window).bind("resize",function(){
				Main.UI.setStyle();//浏览器调整大小，文件列表区域调整高宽。
				Main.UI.header.set_width();
				if (PicasaOpen!=false) {
					PicasaOpen.setFrameResize();
				}
			});
			_setListType(list_type,true);
			$("html").unbind('click').live('click',function (e) {
				Main.RightMenu.hidden();
				if (Main.Global.isIE && Main.Global.isDragSelect) return;
			});
		},	
		setStyle:function(){//设置文件列表高宽。
			if (list_type=='list') {
				Main.Global.fileRowNum = 1;
			}else{
				//main当前宽度所容纳每行文件个数。
				Main.Global.fileRowNum = (function(){
					var main_width=$(Main.Config.FileBoxSelector).width();//获取main主体的
					var file_width=
						parseInt($(Main.Config.FileBoxClass).css('width'))+
						parseInt($(Main.Config.FileBoxClass).css('border-left-width'))+
						parseInt($(Main.Config.FileBoxClass).css('border-right-width'))+
						parseInt($(Main.Config.FileBoxClass).css('margin-right'));
					return parseInt(main_width/file_width);		
				})();					
			}	
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
		},
		// setting 对话框
		setting:function(setting){
			if (setting == undefined) setting = '';	
			if (window.top.frames["Opensetting_mode"] == undefined) {
				$.dialog.open('?setting#'+setting,{
					id:'setting_mode',
					fixed:true,
					title:'系统设置',
					width:880,
					resize:true,
					height:550
				});
			}else{
				FrameCall.top('Opensetting_mode','setGoto','"'+setting+'"');
			}
		},

		// 头部操作
		header:{
			bindEvent:function(){								
				//地址栏点击，更换地址。
				$("#yarnball li a").unbind('click').live('click',function(e) {
					stopPP(e);
					var path = $(this).attr('title');
					Main.PathOpen.pathList(path);
				});

				$("#yarnball").unbind('click').live('click',function(){
					$("#yarnball").css('display','none');
					$("#yarnball_input").css('display','block');
					$("#yarnball_input input").focus();
				});
				$("#yarnball_input input").unbind('blur').live('blur',function(){
					Main.UI.header.gotoPath();
				});
				// 头部功能绑定
				$('.header-content a').click(function(e){
					stopPP(e);
					var action = $(this).attr('id');
					switch (action){
						case 'history_back':
							if (!$('#history_back').hasClass('nouse')){
								Main.PathOpen.pathBack("");
							}
							break;
						case 'history_next':
							if (!$('#history_next').hasClass('nouse')){
								Main.PathOpen.pathNext("");
							}
							break;
						case 'refresh':
							Main.UI.f5(true,true);
							Main.UI.tree.init();
							break;
						case 'home':Main.UI.tree.gotoPath(HOME);break;
						case 'go':Main.UI.header.gotoPath();break;
						case 'up':Main.UI.header.gotoFather();break;
						case 'setting':Main.Common.setting();break;
						case 'logout':
							window.location='?user/logout';
							break;
						default:break;
					}
					return false;
				});
			},

			keydown:function(e){
				if (e.keyCode == 13) {
					Main.UI.header.gotoPath();
				}
			},
			//更新地址栏
			addressSet:function(path){
				$("input.path").val(path);
				$("#yarnball_input").css('display','none');
				$("#yarnball").css('display','block');

				 //地址可点击html拼装，与input转换
				var __set_address = function(address) {	
					var add_first = '<li class="yarnlet first"><a title="@1@" style="z-index:{$2};"><span class="left-yarn"></span>{$3}</a></li>\n';
					var add_more = '<li class="yarnlet "><a title="@1@" style="z-index:{$2};">{$3}</a></li>\n';
					address = address.replace(/\/+/g,'/');
					var arr = address.split('/');
					if (arr[arr.length - 1] == '') {
						arr.pop();
					}		
					var this_address = arr[0]+'/';		
					var li = add_first.replace(/@1@/g,this_address);
					li = li.replace('{$2}',arr.length);
					li = li.replace('{$3}',arr[0]);
					var html = li;

					for (var i=1,z_index=arr.length-1; i<arr.length; i++,z_index--){
						this_address += arr[i]+'/';
						li = add_more.replace(/@1@/g,this_address);
						li = li.replace('{$2}',z_index);
						li = li.replace('{$3}',arr[i]);
						html += li;
					}
					return '<ul class="yarnball">'+html+'</ul>';
				};
				$("#yarnball").html(__set_address(path));
				Main.UI.header.set_width();
			},
			//自适应宽度
			set_width:function(){
				$(".yarnball").stop(true,true);
				var box_width = $('#yarnball').innerWidth()-3;
				var need_width = 0;
				$('#yarnball li a').each(function(index){
					need_width += $(this).outerWidth()+ parseInt($(this).css('margin-left'));
				});
				var m_width = (box_width-30) - need_width;
				if(m_width<0){
					$(".yarnball")
						.css('width',box_width - m_width +'px')
						.css('left', m_width+'px');
				}else{
					$(".yarnball").css({'left':'3px','width':box_width +'px'});
				}
			},

			//地址栏enter或者 点击go按钮，main更换地址
			gotoPath:function(){
				var url=$("input.path").val();//保持文件夹最后有一个/
				url = url.replace(/\\/g,'/');
				$("input.path").val(url);
				if (url.substr(url.length-1,1)!='/'){
					url+='/';
				}
				Main.PathOpen.pathList(url);
				Main.UI.header.addressSet(urlDecode(this_path));
			},

			// 更改前进后退状态
			updateHistoryStatus:function(){
				if (Main.Global.historyStatus['back']==0) {
					$('#history_back').addClass("nouse");
				}else{
					($('#history_back').removeClass("nouse"));
				}				
				if (Main.Global.historyStatus['next']==0){
					$('#history_next').addClass("nouse");
				}else{
					$('#history_next').removeClass("nouse");
				}
			},

			//转到上层目录
			gotoFather:function(){
				var path=$("input.path").val();
				var len=path.length-1;
				var gopath='';
				var count=(path.split('/')).length-1;
				if (count==1){//只出现一次'/'，即为根目录，上层目录是自己
					gopath=path;	
				}
				else{
					if (path.substr(len,1)=='/'){
						len=len-1;
					}
					for (var i=len; i>0; i--){
						if (path.substr(len,1)!='/'){
							len--;
						}else {
							break;
						}
					}
					gopath=path.substr(0,len+1);		
				}
				$("input.path").val(gopath);
				Main.PathOpen.pathList(gopath);
			}
		},
		// 目录树操作
		tree:{
			setting:{
				async: {
					enable: true,
					url:Main.Config.treeAjaxURL,//直接上次拿到的json变量。
					autoParam:["name=name","father","this_path"]
				},
				edit: {
					enable: true,
					showRemoveBtn: false,
					showRenameBtn: false,
					drag:{
						isCopy:true,
						isMove:true,
						prev:false,
						inner:true,
						next:false
					}
				},
				view: {
					showLine: false,
					selectedMulti: false,
					dblClickExpand: false,
					dblClickExpand: function(treeId, treeNode) {
						return treeNode.level > 0;
					},// 双击 展开&折叠
					addDiyDom: function(treeId, treeNode) {
						var spaceWidth = Main.Global.treeSpaceWide;
						var switchObj = $("#" + treeNode.tId + "_switch"),
						icoObj = $("#" + treeNode.tId + "_ico");
						switchObj.remove();
						icoObj.before(switchObj);

						if (treeNode.level >= 1) {
							var spaceStr = "<span class='space' style='display: inline-block;width:" + (spaceWidth * treeNode.level)+ "px'></span>";
							switchObj.before(spaceStr);
						}
					}
				},
				callback: {//事件处理回调函数
					beforeClick: function(treeId, treeNode) {
						if (treeNode.level == 0 ) {
							var zTree = $.fn.zTree.getZTreeObj("folderList");
							zTree.expandNode(treeNode);
							return false;
						}
						return true;
					},
					onClick: function(event, treeId,treeNode){
						var url = '';
						if (treeNode.this_path != undefined) {//直接路径
							url = urlEncode(treeNode.this_path);
						}else{
							url=treeNode.father+urlEncode(treeNode.name)+'/'
						}
						//前端统一成js的url编码方式,和php的url编码不同
						url=urlDecode(url);
						Main.PathOpen.pathList(url);
					},
					//右键click
					onRightClick:function(event, treeId,treeNode){
						var zTree = $.fn.zTree.getZTreeObj("folderList");
						zTree.selectNode(treeNode);
						var treeNode = zTree.getSelectedNodes()[0];
						if (!treeNode.father && !treeNode.this_path) return;
						Main.RightMenu.show('.menuTree',event.clientX, event.clientY);						
					},
					onRename:function(event, treeId,treeNode){
						var zTree = $.fn.zTree.getZTreeObj("folderList");
						if (treeNode.father == undefined
							&& treeNode.this_path == undefined
							&& treeNode.children == undefined
						){//新建目录
							var path,parent = treeNode.getParentNode(),
								newNode = {name:"新建文件夹",pId:treeNode.id};
							if (!parent.father && !parent.this_path) return;							
							if (parent.this_path) path=parent.this_path;
							if (parent.father) path=parent.father+parent.name+'/';

							if (parent.children.length >0) {
								for (var i = parent.children.length - 2; i >= 0; i--) {
									if(treeNode.name == parent.children[i].name){
										Main.Common.tips.tips('名称重复!');
										zTree.removeNode(treeNode);
										return;
									}
								};
							}
							Main.PathOperate.newFolderTree(path,treeNode.name,treeNode);
						}else if (treeNode.father != undefined){
							Main.PathOperate.pathRnameTree(treeNode.father,treeNode.beforeName,treeNode.name,treeNode);
						}
					},
					beforeDrag: function(treeId, treeNodes){
						for (var i=0,l=treeNodes.length; i<l; i++) {
							if (treeNodes[i].drag === false) return false;
						}
						return true;
					},
					beforeDrop: function(treeId, treeNodes, targetNode, moveType){
						return targetNode ? targetNode.drop !== false : true;
					},
					onDrop:function(event, treeId, treeNodes, targetNode, moveType){
    					Main.UI.tree.refresh(targetNode);
					}
				}
			},
			init:function(){
				$.ajax({
					url:'?explorer/folderList',
					success:function(data){
						var tree_json = eval('('+data+')');
						$.fn.zTree.init($("#folderList"), Main.UI.tree.setting,tree_json);
					}
				});
				$('.ztree .switch').unbind('mouseenter').live('mouseenter',function(){
					$(this).addClass('switch_hover');
				}).unbind('mouseleave').live('mouseleave',function(){
					$(this).removeClass('switch_hover');
				});
			},
			gotoPath:function(path){//显示列表	
				Main.PathOpen.pathList(path);
				Main.UI.tree.setting.async.url = Main.config.treeAjaxURL+urlEncode(path);
				$.fn.zTree.init($("#folderList"), Main.UI.tree.setting);
			},

			// 刷新结点
			refresh:function(treeNode){
				var zTree = $.fn.zTree.getZTreeObj("folderList");
				if (treeNode == undefined) treeNode=zTree.getSelectedNodes()[0];
				zTree.reAsyncChildNodes(treeNode, "refresh");
			},
			
			//右键操作
			add:function(){				
				var zTree = $.fn.zTree.getZTreeObj("folderList"),
					treeNode = zTree.getSelectedNodes()[0],
					newNode = {name:"新建文件夹",pId:treeNode.id};

				if (treeNode) treeNode = zTree.addNodes(treeNode,newNode);
				if (treeNode) zTree.editName(treeNode[0]);
			},
			edit:function(){
				var zTree = $.fn.zTree.getZTreeObj("folderList"),
					treeNode = zTree.getSelectedNodes()[0];
				if(!treeNode) return;
				zTree.editName(treeNode);
				treeNode.beforeName = treeNode.name;
			},
			copy:function(){
			},
			cute:function(){
			},
			pathDelete:function(){
				var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
					nodes = zTree.getSelectedNodes(),
					treeNode = nodes[0];
				if(!treeNode) return;
				if(!treeNode.father) {
					$.dialog({fixed: true,resize: false,icon:'warning',drag: true,title:'提示',content: '系统文件夹，不能删除',ok:true});
				}else{
					var path = treeNode.father + urlEncode(treeNode.name);
					Main.PathOperate.pathDeleteTree(path,treeNode,treeNode.name);
				}
			},
			info:function(){
				var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
					nodes = zTree.getSelectedNodes()[0];

				if (!nodes.father && !nodes.this_path) return;
				if (nodes.father) path = nodes.father+urlEncode(nodes.name);
				if (nodes.this_path) path = nodes.this_path;
				Main.PathOperate.pathInfoTree(path);
			}
		}
	}
})();
