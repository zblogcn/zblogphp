Main.Config = {
	TreeId:"folderList",        // 目录树对象
	AnimateTime:200,			// 动画时间设定
	treeAjaxURL:"?explorer/folderListEditor"
};
Main.Global = {
	frameLeftWidth:200,			// 左边树目录宽度
	treeSpaceWide:15,			// 树目录层级相差宽度
	isIE:!-[1,],				// 是否ie
};

//__________________________________________________________________________________//
Main.UI = (function() {
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
			$('.resizeMask').css('display','block');
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
		};
		var __dragEnd = function(e){
			if (!isDraging) return false;
			isDraging = false;
			$drag.removeClass('active');
			Main.Global.frameLeftWidth = $('.frame-left').width();
			$('.resizeMask').css('display','none');
		};
	};
	var _bindToolbar = function(){
		$('.tools-left a').click(function(e){
			var action = $(this).attr('class');
			switch(action){
				case 'home':Main.Tree.init();break;
				case 'view':Main.Tree.view();break;
				case 'folder':Main.Tree.newfolder();break;
				case 'file':Main.Tree.newfile();break;
				case 'refresh':Main.Tree.init();break;
				default:break;
			}
		});
	};
	return{	
		init:function(){
			_bindFrameSizeEvent();
			_bindToolbar();
			Main.Tree.init();
			$("html").unbind('click').live('click',function (e) {
				Main.RightMenu.hidden();
				if (Main.Global.isIE && Main.Global.isDragSelect) return;
			});
		},
		setTheme:function(thistheme){
			var url = static_path+'style/skin/'+thistheme+'/app_editor.css';
			$("#link_css_list").attr("href",url);
			FrameCall.top('OpenopenEditor','Main.Editor.setTheme','"'+thistheme+'"');
		},
		//编辑器全屏
		editorFull:function(){
			var $frame = $('iframe[name=OpenopenEditor]');
			$frame.toggleClass('frame_fullscreen');
		}
	}
})();


Main.Tree = {
	init:function(){
		$.ajax({
			url:'?explorer/folderListEditor&root=1&this_path='+rootPath,
			success:function(data){
				var tree_json = eval('('+data+')');
				$.fn.zTree.init($("#folderList"), Main.Tree.setting,tree_json);
			}
		});
		$('.ztree .switch').unbind('mouseenter').live('mouseenter',function(){
			$(this).addClass('switch_hover');
		}).unbind('mouseleave').live('mouseleave',function(){
			$(this).removeClass('switch_hover');
		});
	},
	setting:{
		async: {
			enable: true,
			url:Main.Config.treeAjaxURL,
			autoParam:["name=name","father","this_path",'type'],
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
				return treeNode.level >= 0;
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
			onClick:function(event, treeId,treeNode){						
				if (treeNode.iconSkin =='doc') {
					var filePath = treeNode.father+treeNode.name;
					Main.Editor.open(filePath);
				}
			},			
			//右键click
			onRightClick:function(event, treeId,treeNode){
				var zTree = $.fn.zTree.getZTreeObj("folderList");
				zTree.selectNode(treeNode);
				var treeNode = zTree.getSelectedNodes()[0];
				if (!treeNode.father && !treeNode.this_path) return;
				if (treeNode.type == 'folder') {
					Main.RightMenu.show('.menuTreeFolder',event.clientX, event.clientY);
				}else if (treeNode.type == 'file') {
					Main.RightMenu.show('.menuTreeFile',event.clientX, event.clientY);
				}else if (treeNode.type == 'root'){
					Main.RightMenu.show('.menuTreeRoot',event.clientX, event.clientY);
				}
			},			
			onRename:function(event, treeId,treeNode){
				var zTree = $.fn.zTree.getZTreeObj("folderList");
				if (treeNode.father == undefined
					&& treeNode.this_path == undefined
					&& treeNode.children == undefined
				){//新建目录
					var path,parent = treeNode.getParentNode();							
					if (!parent.father && !parent.this_path) return;
					if (parent.this_path) path=parent.this_path;
					if (parent.father) path=parent.father+parent.name;

					if (parent.children.length >0) {
						for (var i = parent.children.length - 2; i >= 0; i--) {
							if(treeNode.name == parent.children[i].name){
								Main.Common.tips.tips('名称重复!');
								zTree.removeNode(treeNode);
								return;
							}
						};
					}
					Main.PathOperate.newPathTree(path,treeNode);
				}else if (treeNode.father != undefined){
					Main.PathOperate.pathRnameTree(treeNode.father,
						treeNode.beforeName,treeNode.name,treeNode);
				}
			}
			// beforeDrag: function(treeId, treeNodes){
			// 	for (var i=0,l=treeNodes.length; i<l; i++) {
			// 		if (treeNodes[i].drag === false) return false;
			// 	}
			// 	return true;
			// },
			// beforeDrop: function(treeId, treeNodes, targetNode, moveType){
			// 	return targetNode ? targetNode.drop !== false : true;
			// },
			// onDrop:function(event, treeId, treeNodes, targetNode, moveType){
			// 	var path = '',path_to='';
			// 	var treeNode = treeNodes[0];
			// 	if (!treeNode.father && !treeNode.this_path) return;

			// 	path = treeNode.father+urlEncode(treeNode.name);
			// 	path_to = targetNode.father+urlEncode(targetNode.name)+'/';
			// 	Main.PathOperate.pathDragTree(path,treeNode.type,path_to,targetNode);
			// }
		}
	},

	// 刷新结点
	refresh:function(treeNode){
		var zTree = $.fn.zTree.getZTreeObj("folderList");
		if (treeNode == undefined) treeNode=zTree.getSelectedNodes()[0];
		zTree.reAsyncChildNodes(treeNode, "refresh");
	},
	
	//右键操作
	newfile:function(){				
		var zTree = $.fn.zTree.getZTreeObj("folderList"),
			treeNode = zTree.getSelectedNodes()[0],newNode;
		
		if (!treeNode || treeNode.type=='file') treeNode = zTree.getNodes()[0];
		newNode = {name:"newfile.txt",pId:treeNode.id,'type':'file','iconSkin':'doc'};

		//zTree.expandNode(treeNode, true, true, true);
		if(treeNode.children != undefined){
			if (treeNode) treeNode = zTree.addNodes(treeNode,newNode);
			if (treeNode) zTree.editName(treeNode[0]);					
		}else{
			zTree.reAsyncChildNodes(treeNode, "refresh");
		}
	},
	//右键操作
	newfolder:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),
			treeNode = zTree.getSelectedNodes()[0],newNode;
		if (!treeNode || treeNode.type=='file') treeNode = zTree.getNodes()[0];
		newNode = {name:"新建文件夹",pId:treeNode.id,'type':'folder'};

		//zTree.expandNode(treeNode, true, true, true);
		if(treeNode.children != undefined){
			if (treeNode) treeNode = zTree.addNodes(treeNode,newNode);
			if (treeNode) zTree.editName(treeNode[0]);					
		}else{
			zTree.reAsyncChildNodes(treeNode, "refresh");
		}
	},			
	edit:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),
			treeNode = zTree.getSelectedNodes()[0];
		if(!treeNode) return;
		zTree.editName(treeNode);
		treeNode.beforeName = treeNode.name;
	},
	view:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),
			treeNode = zTree.getSelectedNodes()[0];
		if (!treeNode) treeNode = zTree.getNodes()[0];
		if (treeNode.father) path = treeNode.father+urlEncode(treeNode.name);
		if (treeNode.this_path) path = treeNode.this_path;
		$.dialog.open('?/explorer&path='+path+'/',{
			resize:true,
			fixed:true,
			title:treeNode.name + '--管理',
			width:880,
			height:550
		});
	},
	copy:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (!treeNode.father && !treeNode.this_path) return;
		if (treeNode.father) path = treeNode.father+urlEncode(treeNode.name);
		if (treeNode.this_path) path = treeNode.this_path;
		Main.PathOperate.pathCopyTree(path,treeNode.type);
	},
	cute:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (!treeNode.father && !treeNode.this_path) return;
		if (treeNode.father) path = treeNode.father+urlEncode(treeNode.name);
		if (treeNode.this_path) path = treeNode.this_path;
		Main.PathOperate.pathCuteTree(path,treeNode.type);
	},
	past:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (!treeNode.father && !treeNode.this_path) return;
		if (treeNode.father) path = treeNode.father+urlEncode(treeNode.name)+'/';
		if (treeNode.this_path) path = treeNode.this_path+'/';
		Main.PathOperate.pathPastTree(treeNode,path);
	},			
	pathDelete:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];
		if(!treeNode) return;
		if(!treeNode.father) {
			$.dialog({fixed: true,resize: false,icon:'warning',drag: true,title:'提示',content: '系统文件夹，不能删除',ok:true});
		}else{
			var path = treeNode.father + urlEncode(treeNode.name);
			Main.PathOperate.pathDeleteTree(path,treeNode,treeNode.name);
		}
	},
	download:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (treeNode.type != 'file') return;
		Main.PathOperate.download(treeNode.father + urlEncode(treeNode.name));
	},
	openIE:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (treeNode.type != 'file') return;
		Main.PathOperate.openIE(treeNode.father + urlEncode(treeNode.name));
	},

	info:function(){
		var zTree = $.fn.zTree.getZTreeObj("folderList"),path,
			treeNode = zTree.getSelectedNodes()[0];

		if (!treeNode.father && !treeNode.this_path) return;				
		Main.PathOperate.pathInfoTree(treeNode);
	}
}






Main.PathOperate = (function() {
	var path_not_allow	= ['/','\\',':','*','?','"','<','>','|'];//win文件名命不允许的字符
	//检测文件名是否合法，根据操作系统，规则不一样
	//win 不允许  / \ : * ? " < > |，lin* 不允许 ‘、’
	var _pathAllow = function(path){
		if (_strHasChar(path,path_not_allow)){
			$.dialog({
				title:false,
				time:1,
				icon:'warning',
				content:'命名不允许出现:<br/>/ \ : * ? " < > |'
			});
			return false;
		}
		else {
			return true;
		}
	};
	//字符串中检验是否出现某些字符，check=['-','=']
	var _strHasChar = function(str,check){
		var len=check.length;
		var reg="";
		for (var i=0; i<len; i++){
			if(str.indexOf(check[i])>0){
				return true;
			}
		}
		return false;
	};
	// 树目录新建文件夹
	var _newPathTree = function(path,treeNode){
		if (!_pathAllow(treeNode.name)) return;
		path=path+'/'+urlEncode(treeNode.name);
		var action = '';

		if (treeNode.type == 'folder') {
			action = 'mkdir';
		}else if (treeNode.type == 'file') {
			action = 'mkfile';
		}
		$.ajax({
			url: '?explorer/'+action+'&path='+path,
			success: function(data) {
		  		Main.Common.tips.tips(data);
		  		Main.Tree.refresh(treeNode.getParentNode());
			}
		});
	};
	// 树目录重命名文件夹
	var _pathRnameTree = function(path,from,to,treeNode){
		if (from == to) return;
		if (!_pathAllow(to)) return;
		rname_from = path+urlEncode(from);
		rname_to   = path+urlEncode(to);
		$.ajax({
			type: "POST", 
			url: '?explorer/pathRname',
			data: 'path='+rname_from+'&rname_to='+rname_to,
			beforeSend:function(){
				Main.Common.tips.loading();
			},
			success: function(data) {
				Main.Common.tips.close(data);
				Main.Tree.refresh(treeNode.getParentNode());
			}
		});
	};
	// 树目录删除文件夹
	var _pathDeleteTree = function(path,treeNode,filename){
		var msg = '确认要删除 "'+filename+'"吗?';
		var delete_list = _getSelectJsonTree('delete_list',path,treeNode.type);
		$.dialog({
			fixed: true,//不跟随页面滚动
			resize: false,//调整大小
			icon:'question',
			drag: true,//拖曳
			title:'删除提示',
			content: msg,
			ok:function() {
				isDeleteDialog=0;
				$.ajax({
					url: '?explorer/deletePath',
					type:'POST',
					data:delete_list,
					beforeSend: function(){
						Main.Common.tips.loading('删除中...');
					},
					success: function(data) {
						Main.Common.tips.close(data);
						Main.Tree.refresh(treeNode.getParentNode());
					}
				});
			}
		});
	};
	//包装数据请求
	var _getSelectJsonTree = function(param_name,path,type){
		var select_list = param_name+'=[{"type":"'+type+'","file":"'+path+'"}]';
		return select_list;
	};
	//树目录复制
	var _pathCopyTree = function(path,type){
		$.ajax({
			url:'?explorer/pathCopy',
			type:'POST',
			data:_getSelectJsonTree('copy_list',path,type),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};

	//拖拽剪切
	var _pathDragTree = function(path,type,path_to,targetNode){
		$.ajax({
			url:'?explorer/pathCuteDrag',
			type:'POST',
			data:_getSelectJsonTree('cute_list',path_to,type)+'&path='+path,
			success:function(data){
				Main.Common.tips.tips(data);
				Main.Tree.refresh(targetNode);
			}
		});
	};


	//树目录剪切
	var _pathCuteTree = function(path,type){
		$.ajax({
			url:'?explorer/pathCute',
			type:'POST',
			data:_getSelectJsonTree('cute_list',path,type),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};
	// 树目录粘贴
	var _pathPastTree = function(treeNode,path){
		var url='?explorer/pathPast&path='+urlEncode(path);
		$.ajax({
			url:url,
			dataType:'json',
			beforeSend: function(){
				Main.Common.tips.loading("粘贴操作中...");
			},
			success:function(jsonback){
				Main.Tree.refresh(treeNode);
				Main.Common.tips.close(jsonback['msg']);
			}
		});
	};
	// 树目录文件夹属性
	var _pathInfoTree = function(treeNode){
		var type = treeNode.type;
		var filename = treeNode.name;
		var path = '';		
		if (treeNode.father) path = treeNode.father+urlEncode(filename);
		if (treeNode.this_path) path = treeNode.this_path;

		$.ajax({
			url:'?explorer/pathInfo&type='+type+'&path='+urlEncode(path),		
			beforeSend: function(){
				Main.Common.tips.loading('获取中!  ');
			},
			success:function(data){
				Main.Common.tips.close('获取成功！');
		  		$.dialog({
		  			padding:5,
		  			fixed: true,//不跟随页面滚动
				    drag: true,//拖曳
				    resize:false,
		  			title:filename+' 属性',
				    content:data,
				    ok: function(){//ok按钮提交表单，修改文件夹名
						rname_to=$('.pathinfo div input').val();
						if (rname_to==filename){
							return true;
						}
						if (!_pathAllow(rname_to)){
							return true;
						}else{				
							path	=treeNode.father+urlEncode(filename);
							rname_to=treeNode.father+urlEncode(rname_to);
							$.ajax({
								type: "POST", 
								url: '?explorer/pathRname',
								data: 'path='+path+'&rname_to='+rname_to,				  
								success: function(data) {
									Main.Tree.refresh(treeNode.getParentNode());
									Main.Common.tips.tips(data);
								}
							});						
						}
						return true;
				    },
				    cancel: true
				});
			},
			error:false//请求出错处理
		});
	};

	var _download = function(path){
		var url='?explorer/fileDownload&path='+urlEncode(path);
		Main.Common.tips.tips("即将开始下载");
		art.dialog.open(url,{title:false,time:0.1,width:0,height:0});	
	};
	//新的页面作为地址打开。鼠标右键，IE下打开
	var _openIE = function(path){
		var url = web_host+path.replace(rootPath,'');
		art.dialog.open(url,{title:'文件浏览',width:700,height:500,resize:true});
	};
	return{
		// 树目录新建文件夹
		newPathTree:_newPathTree,
		pathDragTree:_pathDragTree,
		pathRnameTree:_pathRnameTree,
		pathDeleteTree:_pathDeleteTree,
		download:_download,
		openIE:_openIE,

		pathCopyTree:_pathCopyTree,
		pathCuteTree:_pathCuteTree,
		pathPastTree:_pathPastTree,
		pathInfoTree:_pathInfoTree,
	}
})();






//点击右键，获取元素menu的值，对应为右键菜单div的id值。实现通用。
//流程：给需要右键菜单的元素，加上menu属性，并赋值，把值作为右键菜单div的id值

Main.RightMenu = (function(){
	var selectRoot   = ".menuTreeRoot";	
	var selectFolder = ".menuTreeFolder";
	var selectFile   = ".menuTreeFile";
	var _init = function(){
		$('.context-menu-list').unbind("click").live("click",function(e){
			stopPP(e);
			return false;//屏蔽html点击隐藏
		});
		$(Main.Config.BodyContent).contextmenu(function(e){
			Main.RightMenu.hidden();
			return true;
		});//屏蔽工具栏右键

		_bindRoot();
		_bindFolder();
		_bindFile();		
	};
	
	var _bindRoot = function(){
		$.contextMenu({
			zIndex:9999,
			selector: selectRoot, 
			callback: function(key, options) {_menuRoot(key);},
			items: {	
				"refresh":{name:"刷新数据(E)",icon:"refresh",accesskey: "e"},
				"newfolder":{name:"新建文件夹(N)",icon:"folder-close-alt",accesskey: "n"},
				"newfile":{name:"新建文本文件(N)",icon:"file-alt",accesskey: "n"},				
				"sep1":"--------",
				"past":{name:"粘贴(P)",icon:"paste",accesskey: "p"},
				"sep3":"--------",
				"remove":{name:"关闭菜单(Q)",icon:"remove",accesskey: "q"}
			}
		});
	};
	var _menuRoot = function(action) {//文件操作
		switch(action){
			case 'refresh':Main.Tree.refresh();break;
			case 'newfolder':Main.Tree.newfolder();break;
			case 'newfile':Main.Tree.newfile();break;
			case 'past':Main.Tree.past();break;
			case 'info':Main.Tree.info();break;
			case 'remove':;break;
			default:break;
		}
	};

	var _bindFolder = function(){
		$.contextMenu({
			zIndex:9999,
			selector: selectFolder, 
			callback: function(key, options) {_menuFolder(key);},
			items: {
				"refresh":{name:"刷新数据(E)",icon:"refresh",accesskey: "e"},				
				"newfolder":{name:"新建文件夹(N)",icon:"folder-close-alt",accesskey: "n"},
				"newfile":{name:"新建文本文件(N)",icon:"file-alt",accesskey: "n"},				
				"rename":{name:"重命名(R)",icon:"pencil",accesskey: "r"},
				"sep1":"--------",
				"view":{name:"管理目录(C)",icon:"laptop",accesskey: "v"},
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"cute":{name:"剪切(T)",icon:"cut",accesskey: "t"},
				"past":{name:"粘贴(P)",icon:"paste",accesskey: "p"},
				"sep2":"--------",
				"delete":{name:"删除(D)",icon:"trash",accesskey: "d"},
				"info":{name:"属性(I)",icon:"info",accesskey: "i"},
				"sep3":"--------",
				"remove":{name:"关闭菜单(Q)",icon:"remove",accesskey: "q"}
			}
		});
	};
	var _menuFolder = function(action) {//右键操作
		switch(action){
			case 'refresh':Main.Tree.refresh();break;
			case 'newfolder':Main.Tree.newfolder();break;
			case 'newfile':Main.Tree.newfile();break;

			case 'view':Main.Tree.view();break;
			case 'copy':Main.Tree.copy();break;
			case 'cute':Main.Tree.cute();break;
			case 'past':Main.Tree.past();break;
			case 'rename':Main.Tree.edit();break;
			case 'delete':Main.Tree.pathDelete();break;
			case 'info':Main.Tree.info();break;
			case 'remove':;break;
			default:break;
		}
	};
	var _bindFile = function(){
		$.contextMenu({
			zIndex:9999,
			selector: selectFile, 
			callback: function(key, options) {_menuFile(key);},
			items: {	
				"rename":{name:"重命名(R)",icon:"pencil",accesskey: "r"},
				"sep1":"--------",
				"download":{name:"下载(D)",icon:"download",accesskey: "d"},
				"openIE":{name:"浏览器打开(B)",icon:"globe",accesskey: "b"},
				"sep2":"--------",
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"cute":{name:"剪切(T)",icon:"cut",accesskey: "t"},
				"delete":{name:"删除(R)",icon:"trash",accesskey: "r"},
				"sep3":"--------",				
				"info":{name:"属性(I)",icon:"info",accesskey: "i"},
				"remove":{name:"关闭菜单(Q)",icon:"remove",accesskey: "q"}
			}
		});
	};
	var _menuFile = function(action) {//文件操作
		switch(action){
			case 'rename':Main.Tree.edit();break;

			case 'download':Main.Tree.download();break;
			case 'openIE':Main.Tree.openIE();break;

			case 'copy':Main.Tree.copy();break;
			case 'cute':Main.Tree.cute();break;
			case 'past':Main.Tree.past();break;
			
			case 'delete':Main.Tree.pathDelete();break;
			case 'info':Main.Tree.info();break;
			case 'remove':;break;
			default:break;
		}
	};
	return{
		init:_init,
		show:function(select,left,top){
			Main.RightMenu.hidden();
			$(select).contextMenu({x:left, y:top});
		},
		isDisplay:function(){//检测是否有右键菜单
			var display = false;
			$('.context-menu-list').each(function(){
				if($(this).css("display") !="none"){
					display = true;
				}
			});
			return display;
		},
		hidden:function(){
			$visibleMenu = $('.context-menu-list').filter(':visible');
 			$visibleMenu.trigger('contextmenu:hide');
		}
	}
})();

Main.Editor = (function(){
	_getFileType = function(path){
		var str = path.split(".");
		return str[str.length - 1];
	};
	_open = function(path){
		var ext = _getFileType(path);
		if (inArray(Main.Common.filetype['code'],ext) 
			|| inArray(Main.Common.filetype['text'],ext) ) {
			FrameCall.top('OpenopenEditor','Main.Editor.add','"'+path+'"');
			return;
		}else{
			$.dialog({
				id:'unkonwFile',
				fixed: true,
				resize: false,
				icon:'warning',
				time:1,
				title:false,
				content: '不是文本文件！'
			});
		}
	};
	return{
		open:_open
	}
})();

$(document).ready(function() {
	Main.UI.init();
	Main.RightMenu.init();
});
