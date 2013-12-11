//点击右键，获取元素menu的值，对应为右键菜单div的id值。实现通用。
//流程：给需要右键菜单的元素，加上menu属性，并赋值，把值作为右键菜单div的id值

Main.RightMenu = (function(){
	var fileMenuSelector   = ".menufile";
	var folderMenuSelector = ".menufolder";
	var selectMoreSelector = ".menuMore";
	var selectTreeSelector = ".menuTree";	

	var _init = function(){
		$('.context-menu-list').unbind("click").live("click",function(e){
			stopPP(e);
			return false;//屏蔽html点击隐藏
		});
		_bindBody();
		_bindFolder();
		_bindFile();
		_bindSelectMore();
		_bindTree();
		_initSelect();	

		$('body').contextmenu(function(e){
			Main.RightMenu.hidden();
			//return false;
		});
	};

	//初始化绑定筛选排序方式
	var _initSelect = function(){
		$('.set_set'+list_type).addClass('selected');
		$('.set_sort_'+json_sort_field).addClass('selected');
		$('.set_sort_'+json_sort_order).addClass('selected');
	};
	var _bindBody = function(){
		$.contextMenu({
			selector: Main.Config.BodyContent,
			zIndex:9999,
			callback: function(key, options) {_menuBody(key, options);},
			items: {
				"listIcon": {
					name: "查看(V)",
					accesskey: "v",
					icon:"eye-open",
					items:{
						"seticon":{name:"图标(I)",icon:"th",accesskey: "i",className:'menu_seticon set_seticon'},
						"setlist":{name:"列表(L)",icon:"list",accesskey: "l",className:'menu_seticon set_setlist'}
					}
				},
				"sortBy": {
					name: "排序方式(O)",
					accesskey: "o",
					icon:"sort",
					items:{
						"set_sort_name":{name:"名称",className:'menu_set_sort set_sort_name'},
						"set_sort_ext":{name:"类型",className:'menu_set_sort set_sort_ext'},
						"set_sort_size":{name:"大小",className:'menu_set_sort set_sort_size'},
						"set_sort_mtime":{name:"修改日期",className:'menu_set_sort set_sort_mtime'},
						"set_sort_up":{name:"递增(A)",icon:"sort-up",accesskey: "a",className:'menu_set_desc set_sort_up'},
						"set_sort_down":{name:"递减(D)",icon:"sort-down",accesskey: "d",className:'menu_set_desc set_sort_down'}
					}
				},
				"sep1":"--------",
				"refresh":{name:"刷新(E)",icon:"refresh",accesskey: "e"},
				"upload":{name:"上传(U)",icon:"upload",accesskey: "u"},
				"past":{name:"粘贴(P)",icon:"paste",accesskey: "p"},
				"copy_see":{name:"查看剪贴板(B)",icon:"eye",accesskey: "b"},
				"sep2":"--------",
				"newfolder":{name:"新建文件夹(F)",icon:"folder-close-alt",accesskey: "f"},
				"newfile":{name:"新建文本文件(T)",icon:"file-alt",accesskey: "t"},
				"newfileOther":{					
					name:"新建其他",					
					items:{
						"newfile_html":{name:"html文件"},
						"newfile_php":{name:"php文件"},
						"newfile_js":{name:"js文件"},
						"newfile_css":{name:"css文件"},
						"newfile_oexe":{name:"可执行程序",disabled: function(){return true;}}
					}
				},
				"sep3":"--------",
				"info":{name:"属性(R)",icon:"info",accesskey: "r"}
			}
		});
	};

	var _bindFolder = function(){
		$.contextMenu({
			zIndex:9999,
			selector: folderMenuSelector, 
			callback: function(key, options) {_menuFolder(key);},
			items: {
				"open":{name:"打开(O)",icon:"folder-open-alt",accesskey: "o"},
				"open_ie":{name:"浏览器中打开(B)",icon:"globe",accesskey: "b"},
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"past":{name:"剪切(T)",icon:"cut",accesskey: "t"},				
				"delete":{name:"删除(D)",icon:"trash",accesskey: "d"},
				"rname":{name:"重命名(M)",icon:"pencil",accesskey: "m"},
				"sep1":"--------",
				"set_fav":{name:"添加到收藏夹(I)",icon:"star",accesskey: "i"},
				"zip":{name:"zip压缩(Z)",icon:"folder-close",accesskey: "z"},
				"sep2":"--------",
				"info":{name:"属性(R)",icon:"info",accesskey: "r"}
			}
		});
	};

	var _bindFile = function(){
		$.contextMenu({
			zIndex:9999,
			selector: fileMenuSelector,
			callback: function(key, options) {_menuFile(key);},
			items: {
				"open":{name:"打开(O)",icon:"external-link",accesskey: "o"},
				"open_text":{name:"编辑(E)",icon:"edit",accesskey: "e"},
				"open_ie":{name:"浏览器中打开(B)",icon:"globe",accesskey: "b"},
				"newfileOther":{					
					name:"打开方式(H)",
					accesskey:'h',
					items:{
						"open_text":{name:"文本打开",icon:"edit"},
						"open_kindedit":{name:"其他",icon:"edit"}
					}
				},
				"sep1":"--------",
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"past":{name:"剪切(T)",icon:"cut",accesskey: "t"},				
				"delete":{name:"删除(D)",icon:"trash",accesskey: "d"},
				"rname":{name:"重命名(M)",icon:"pencil",accesskey: "m"},
				"sep2":"--------",
				"zip":{name:"zip压缩(Z)",icon:"folder-close",accesskey: "z"},
				"unzip":{name:"zip解压到当前(U)",icon:"folder-open-alt",accesskey: "u"},
				"down":{name:"下载(D)",icon:"download",accesskey: "d"},
				"sep3":"--------",
				"info":{name:"属性(R)",icon:"info",accesskey: "r"}
			}
		});
	};
	var _bindSelectMore = function(){
		$.contextMenu({
			zIndex:9999,
			selector: selectMoreSelector, 
			callback: function(key, options) {_menuMore(key);},
			items: {
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"cute":{name:"剪切(T)",icon:"cut",accesskey: "t"},				
				"delete":{name:"删除(D)",icon:"trash",accesskey: "d"},
				"sep1":"--------",
				"playmedia":{name:"加入播放列表(T)",icon:"music",accesskey: "p"},
				"zip":{name:"zip压缩(Z)",icon:"folder-close",accesskey: "z"},			
				"sep2":"--------",
				"info":{name:"属性(R)",icon:"info",accesskey: "r"}
			}
		});
	}
	var _bindTree = function(){
		$.contextMenu({
			zIndex:9999,
			selector: selectTreeSelector, 
			callback: function(key, options) {_menuTree(key);},
			items: {
				"refresh":{name:"刷新数据(E)",icon:"refresh",accesskey: "e"},
				"new":{name:"新建文件夹(N)",icon:"folder-close-alt",accesskey: "n"},	
				"rename":{name:"重命名(R)",icon:"pencil",accesskey: "r"},
				"sep1":"--------",
				"copy":{name:"复制(C)",icon:"copy",accesskey: "c"},
				"cute":{name:"剪切(T)",icon:"cut",accesskey: "t"},
				"sep2":"--------",
				"delete":{name:"删除(D)",icon:"trash",accesskey: "d"},
				"info":{name:"属性(I)",icon:"info",accesskey: "i"}
			}
		});
	}	

	//___________________________________________________________________________________

	var _menuBody = function(action) {
		switch(action){
		  	case 'back':
		  		Main.PathOpen.pathBack();
		  		break;
		  	case 'refresh':
		  		Main.UI.f5();break;
		  	case 'next':
		  		Main.PathOpen.pathNext();break;
		  	case 'seticon': //大图标显示
				$('.menu_seticon').removeClass('selected');
				$('.set_seticon').addClass('selected');
				Main.UI.setListType('icon');
				break;
		  	case 'setlist'://列表显示
				$('.menu_seticon').removeClass('selected');
				$('.set_setlist').addClass('selected');
				Main.UI.setListType('list');
				break;
		  	case 'set_sort_name'://排序方式，名称
				$('.menu_set_sort').removeClass('selected');
				$('.set_sort_name').addClass('selected');
				Main.UI.json_sort('name',0);
				break;
		  	case 'set_sort_ext'://排序方式，扩展名
				$('.menu_set_sort').removeClass('selected');
				$('.set_sort_ext').addClass('selected');
				Main.UI.json_sort('ext',0);
				break;
		  	case 'set_sort_size'://排序方式，大小
				$('.menu_set_sort').removeClass('selected');
				$('.set_sort_size').addClass('selected');
				Main.UI.json_sort('size',0);
				break;
		  	case 'set_sort_mtime'://排序方式，修改时间
				$('.menu_set_sort').removeClass('selected');
				$('.set_sort_mtime').addClass('selected');
				Main.UI.json_sort('mtime',0);
				break;
		  	case 'set_sort_up'://已有模式升序
				$('.menu_set_desc').removeClass('selected');
				$('.set_sort_up').addClass('selected');
				Main.UI.json_sort(0,'up');
				break;
		  	case 'set_sort_down'://已有模式降序
				$('.menu_set_desc').removeClass('selected');
				$('.set_sort_down').addClass('selected');
				Main.UI.json_sort(0,'down');
				break;
		  	case 'upload'://粘贴到当前文件夹
				Main.PathOperate.pathUpload();
				break;  
		  	case 'past'://粘贴到当前文件夹
				Main.PathOperate.pathPast();
				break;
		  	case 'copy_see'://查看剪贴板
				Main.PathOperate.pathCopySee();
				break;
		  	case 'newfolder'://新建文件夹
				Main.PathOperate.newFolder();
				break;
		  	case 'newfile'://新建文件
				Main.PathOperate.newFile();break;
			case 'newfile_html':
				Main.PathOperate.newFile('html');break;
			case 'newfile_php':
				Main.PathOperate.newFile('php');break;
			case 'newfile_js':
				Main.PathOperate.newFile('js');break;
			case 'newfile_css':
				Main.PathOperate.newFile('css');break;
			case 'newfile_oexe':
				Main.PathOperate.newFile('oexe');break;			
		  	case 'info'://当前文件夹熟悉
		  		Main.PathOperate.pathInfo("thispath");break;  	
		  	default:break;
	  	}
	};
	var _menuFile = function(action) {
		switch(action){
			case 'open'://默认方式打开
				Main.PathOpen.open(13);break;
			case 'open_ie'://浏览器中打开
				Main.PathOpen.openIE();break;
			case 'open_text'://txt打开
				Main.PathOpen.openText();break;
			case 'open_kindedit'://kindedit中打开
				break;
			case 'copy'://复制
				Main.PathOperate.pathCopy();	break;
			case 'cute'://剪切
				Main.PathOperate.pathCute();	break;
			case 'delete'://删除
				Main.PathOperate.pathDelete();break;
			case 'rname'://重命名
				Main.PathOperate.pathRname();break;
			case 'zip'://zip压缩
				Main.PathOperate.pathZip();break;
			case 'unzip'://zip解压
				Main.PathOperate.pathUnZip();break;
			case 'down'://下载
				Main.PathOpen.download();break;
			case 'info'://文件属性(文件名中&字符串不能作为URL参数，转换为%26)
				Main.PathOperate.pathInfo();break;
			default:break;
		}
	};
	var _menuFolder = function(action) {
		switch(action){
			case 'open'://打开
				Main.PathOpen.open();break;
			case 'open_ie'://浏览器中打开
				Main.PathOpen.openIE();break;
			case 'copy'://复制
				Main.PathOperate.pathCopy();break;
			case 'cute'://剪切
				Main.PathOperate.pathCute();break;	
			case 'delete'://删除
				Main.PathOperate.pathDelete();break;
			case 'rname'://重命名
				Main.PathOperate.pathRname();break;
			case 'set_fav'://添加到收藏夹
				if (Main.Global.fileListSelectNum != 1) {
					return;
				}else{
					var selectObj = Main.Global.fileListSelect;
				}
				var fileName = Main.SetSelect.getObjName(selectObj);
				var pram='&name='+fileName+'&path='+this_path+fileName+'/';
					Main.Common.setting('fav'+pram);
				break;
			case 'zip'://zip压缩
				Main.PathOperate.pathZip();break;
			case 'zipdown'://zip压缩后下载
				break;
			case 'info'://文件夹属性
				Main.PathOperate.pathInfo();break;	  	
			default:break;
		}
	};		
	var _menuMore = function(action) {//多选，右键操作
		switch(action){
			case 'copy':Main.PathOperate.pathCopy();break;
			case 'cute':Main.PathOperate.pathCute();break;
			case 'delete':Main.PathOperate.pathDelete();break;
			case 'playmedia':Main.PathOperate.play();break;
			case 'zip':Main.PathOperate.pathZip();break;
			case 'info':Main.PathOperate.pathInfo();break;
			default:break;
		}
	};
	
	var _menuTree = function(action) {//多选，右键操作
		switch(action){
			case 'refresh':Main.UI.tree.refresh();break;			
			case 'new':Main.UI.tree.add();break;
			case 'copy':Main.UI.tree.copy();break;
			case 'cute':Main.UI.tree.cute();break;
			case 'rename':Main.UI.tree.edit();break;
			case 'delete':Main.UI.tree.pathDelete();break;
			case 'info':Main.UI.tree.info();break;
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