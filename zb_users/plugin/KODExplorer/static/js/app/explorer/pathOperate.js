Main.PathOperate = (function() {
	var selectObj = '';
	var selectNum = 0;
	var path_not_allow	= ['/','\\',':','*','?','"','<','>','|'];//win文件名命不允许的字符
	var _hasSelect = function(){
		selectNum = Main.Global.fileListSelectNum;
		selectObj = Main.Global.fileListSelect
		if(selectNum == 0 || selectNum > 1){
			return false;
		}else{
			return true;
		}
	}

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

	//设置某个文件[夹]选中
	var _setSelectByFilename = function(filename,file_type) {
		var pos=_getArrayPos(filename,file_type);//获取所在位置
		$(Main.Global.fileListAll).eq(pos).addClass(Main.Config.SelectClassName);
		Main.SetSelect.select();
	};
	//查找json中，文件名所在的数组位置。
	var _arrayFind = function(data,key,str){
		var m=data.length;
		for(i=0;i<m;i++){
			if(data[i][key]==str) return data[i];
		}
	};

	//重名&新建  文件[夹]名是否存在检测()
	var _fileExist = function(filename){
		var list="";
		var is_exist=0;
		if (json_data['filelist']!=null) {
			list=_arrayFind(json_data['filelist'],'name',filename);//重名检测
			if(list!=null){	
				is_exist=1;
			}		
		}
		if (json_data['folderlist']!=null) {
			list=_arrayFind(json_data['folderlist'],'name',filename);//重名检测
			if(list!=null){	
				is_exist=1;
			}
		}
		return is_exist;
	}
	//获得文件名,同名则结尾自增  folder--folder(1)--folder(2)
	var _getName = function(filename,ext){
		var i = 0;
		if (ext == undefined) {//文件夹
			if(!_fileExist(filename)){
				return filename;
			}
			var lastname = filename+'(0)';
			while(_fileExist(lastname)){		
				i++;
				lastname = filename+'('+i+')';
			}
			return lastname;
		}else{
			if(!_fileExist(filename+'.'+ext)){
				return filename+'.'+ext;
			}
			var lastname = filename+'(0).'+ext;
			while(_fileExist(lastname)){		
				i++;
				lastname = filename+'('+i+').'+ext;
			}
			return lastname;			
		}
	};

	//得到json中，获取新建文件名  dom节点的位置。
	//新建文件(保持排序队形不变)
	var _getArrayPos = function(str,file_type){
		var list="";
		var foldernum=0;
		var i=0,j=0;
		//if (json_data['folderlist']==null && json_data['filelist']==null ){//空目录
		//	return -1;
		//}
		lists=json_data['folderlist'];
		if (lists != null) {//is null
			foldernum = lists.length;
		}	
		if (file_type=='folder')	{
			list=json_data['folderlist'];
			if (list == null) {//is null
				return 0;
			}
			else {
				foldernum = list.length;
				for (;i<foldernum; i++){
					if (list[i]['name']>=str){//知直到比str大，返回该位置
						break;
					}
				}
				return i;
			}
		}
		if(file_type=='file'){
			list=json_data['filelist'];
			if (list == null) {//is null
				return foldernum+0;
			}
			else {
				filenum = list.length;
				for (j=0;j<filenum; j++){
					if (list[j]['name']>=str){//直到比str大，返回该位置
						break;
					}
				}
				return foldernum+j;
			}
		}
		else{
			return 0;
		}
	};

	//新建文件
	var _newFile = function(newname_ext) {
		if (newname_ext == undefined) {
			newname_ext = 'txt';
		}
		var listhtml 	= "";
		var newname 	= "newfile";
		var selectname 	= "";
		var is_exist 	= 0;
		var newname 	= _getName(newname,newname_ext);
		var pos 		= _getArrayPos(newname,'file')-1;

		listhtml='<div class="file select menufile"  id="makefile">';
		listhtml+='<div class="'+newname_ext+' ico"></div>';
		listhtml+='<div  class="titleBox"><span class="title">';
		listhtml+='<div class="textarea"><textarea class="newfile" onpropertychange="this.style.height=this.scrollHeight-4 + \'px\'" oninput="this.style.height=this.scrollHeight-4 + \'px\'">'+newname+'</textarea></span></div></div><div style="clear:both;"></div></div>';

		if (pos<0){
			$(Main.Config.FileBoxSelector).html($(Main.Config.FileBoxSelector).html()+listhtml);		
		}else {
			$(listhtml).insertAfter(Main.Config.FileBoxSelector+" .file:eq("+pos+")");
		}

		// dom 
		var $textarea 	= $(".newfile");
		var textarea 	= $textarea.get(0);
		// 处理选中文件名部分
		var selectlen=newname.length-newname_ext.length-1;
		if(Main.Global.isIE){//IE
            var range = textarea.createTextRange();
            range.moveEnd('character', -textarea.value.length);         
            range.moveEnd('character', selectlen);
            range.moveStart('character', 0);
            range.select();
        }
        else{//firfox chrome ...
           textarea.setSelectionRange(0,selectlen);
        }

		$textarea.focus();
		$textarea.unbind('keydown').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 27){
				//捕获键盘事件 enter  esc
				stopPP(event);
				event.preventDefault();//阻止编辑器回车
				filename=$textarea.attr('value');//获取编辑器值
				selectname=filename;//重命名成功后设置状态为选中
				if (!_pathAllow(filename)) return;
				if(_fileExist(filename)){
					$("#makefile").remove();
					Main.Common.tips.tips('已存在该文件','warning');
				}
				else{
					filename=this_path+filename;
					filename=urlEncode(filename);
					$.ajax({
					  url: '?explorer/mkfile&path='+filename,
					  success: function(data) {
					  	Main.Common.tips.tips(data);
						Main.UI.f5();
						_setSelectByFilename(selectname,'file');
					  }
					});
				}
			}
			return true;
		});	
		$textarea.unbind('blur').blur(function(){		
			filename=$textarea.attr('value');//获取编辑器值
			selectname=filename;//重命名成功后设置状态为选中
			if (!_pathAllow(filename)) return;
			if(_fileExist(filename)){
				$("#makefile").remove();
				Main.Common.tips.tips('已存在该文件','warning');
				_newFile(newname_ext);
			}
			else{			
				filename=this_path+filename;
				filename=filename.replace('&','%26');
				$.ajax({
				  url: '?explorer/mkfile&path='+filename,
				  success: function(data) {
				  	Main.Common.tips.tips(data);
					Main.UI.f5();
				  }
				});		
			}
		});
	};



	// 树目录新建文件夹
	var _newFolderTree = function(path,filename,treeNode){
		if (!_pathAllow(filename)) return;
		path=path+urlEncode(filename);
		$.ajax({
			url: '?explorer/mkdir&path='+path,
			success: function(data) {
				Main.UI.f5();
		  		Main.Common.tips.tips(data);
		  		Main.UI.tree.refresh(treeNode.getParentNode());
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
				Main.UI.f5();
				Main.Common.tips.close(data);
				Main.UI.tree.refresh(treeNode.getParentNode());
			}
		});
	};
	// 树目录删除文件夹
	var _pathDeleteTree = function(path,treeNode,filename){
		var msg = '确认要删除 "'+filename+'"文件夹吗?';
		var delete_list = _getSelectJsonTree('delete_list',path);
		$.dialog({
			fixed: true,//不跟随页面滚动
			icon:'question',
			title:'删除文件夹',
			content: msg,
			ok:function() {
				$.ajax({
					url: '?explorer/deletePath',
					type:'POST',
					data:delete_list,
					beforeSend: function(){
						Main.Common.tips.loading('删除中...');
					},
					success: function(data) {
						Main.UI.f5();
						Main.Common.tips.close(data);
						Main.UI.tree.refresh(treeNode.getParentNode());
					}
				});
			}
		});
	};
	//包装数据请求
	var _getSelectJsonTree = function(param_name,path){
		var select_list = param_name+'=[{"type":"folder","file":"'+path+'"}]';
		return select_list;
	};
	//树目录复制
	var _pathCopyTree = function(path){
		$.ajax({
			url:'?explorer/pathCopy',
			type:'POST',
			data:_getSelectJsonTree('copy_list',path),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};
	//树目录剪切
	var _pathCuteTree = function(path){
		$.ajax({
			url:'?explorer/=pathCute',
			type:'POST',
			data:_getSelectJsonTree('cute_list',path),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};
	// 树目录粘贴
	var _pathPastTree = function(path){
		var url='?explorer/pathPast&path='+path;
		$.ajax({
			url:url,
			dataType:'json',
			beforeSend: function(){
				Main.Common.tips.loading("粘贴操作中...");
			},
			success:function(jsonback){
				Main.UI.f5();
				Main.Common.tips.close(jsonback['msg']);
			}
		});
	};
	// 树目录文件夹属性
	var _pathInfoTree = function(path){
		$.ajax({
			url:'?explorer/pathInfo&type=folder&path='+urlEncode(path),
			beforeSend: function(){
				Main.Common.tips.loading('获取中!  ');
			},
			success:function(data){
				Main.Common.tips.close('获取成功！');
		  		$.dialog({
		  			padding:5,
		  			fixed: true,//不跟随页面滚动
		  			title:'文件夹属性',
				    content:data				   
				});
			}
		});
	};	


	var _newFolder = function() {
		var newname="新建文件夹";
		var is_exist=0;
		var newname=_getName(newname);//如果重复，则自动追加字符
		var temp=_getArrayPos(newname,'folder');
		pos=(temp==0)?-1:(temp-1);

		var listhtml='<div class="file select menufolder" id="makefile">';
		listhtml+='<div class="folder ico" filetype="folder"></div>';
		listhtml+='<div  class="titleBox"><span class="title">';
		listhtml+='<div class="textarea"><textarea class="newfile" onpropertychange="this.style.height=this.scrollHeight-4 + \'px\'" oninput="this.style.height=this.scrollHeight-4 + \'px\'">'+newname+'</textarea></span></div></div><div style="clear:both;"></div></div>';
		
		if (pos==-1){//空目录时
			$(Main.Config.FileBoxSelector).html(listhtml+$(Main.Config.FileBoxSelector).html());		
		}else {
			$(listhtml).insertAfter(Main.Config.FileBoxSelector+" .file:eq("+pos+")");
		}
		$('.newfile').select();
		$('.newfile').focus();
		$('.newfile').unbind('keydown').keydown(function(event) {
			if (event.keyCode == 13 || event.keyCode == 27) {
				stopPP(event);
				event.preventDefault();//阻止编辑器回车
				var filename=$('.newfile').attr('value');//获取编辑器值
				var selectname=filename;

				_pathAllow(filename);
				if(_fileExist(filename)){
					$("#makefile").remove();
					Main.Common.tips.tips('已存在该文件','warning');
				}
				else{
					filename=this_path+filename;
					filename=urlEncode(filename);
					$.ajax({
					  url: '?explorer/mkdir&path='+filename,
					  success: function(data) {
					  	Main.Common.tips.tips(data);
					  	Main.UI.f5();
					  	_setSelectByFilename(selectname,'folder');
					  }
					});
				}
			}
		});
		$('.newfile').unbind('blur').blur(function(){//编辑框事件处理
			filename=$('.newfile').attr('value');//获取编辑器值
			_pathAllow(filename);
			if(_fileExist(filename)){
				$("#makefile").remove();
				Main.Common.tips.tips('已存在该文件夹','warning');
				_newFolder();
			}
			else{
				filename=this_path+filename;
				filename=urlEncode(filename);
				$.ajax({
				  url: '?explorer/mkdir&path='+filename,
				  success: function(data) {
				  	Main.Common.tips.tips(data);
				  	Main.UI.f5();
				  }
				});		
			}
		});
	};


	//获取文件夹属性
	var _pathInfo = function(thispath){
		var path="";
		var thistype="";
		var rname_to="";
		var filename = '';

		if (thispath == "thispath"){
			path	 = this_path;
			thistype = 'folder';
		}else {
			if (!_hasSelect()){
				_pathInfoMuti();
				return;
			}
			filename = Main.SetSelect.getObjName(selectObj);//名称缩略
			if(filename.length>30){
				filename=urlDecode(filename).substr(0,22)+"...";
			}
			path = this_path+filename;
			if (Main.SetSelect.getObjType(selectObj) == 'folder'){
				thistype = 'folder';
			}else {
				thistype = 'file';
			}
		}
		$.ajax({
			url:'?explorer/pathInfo&type='+thistype+'&path='+urlEncode(path),		
			beforeSend: function(){
				Main.Common.tips.loading('获取中!  ');
			},
			success:function(data){
				Main.Common.tips.close('获取成功！');
		  		$.dialog({
		  			padding:5,
		  			fixed: true,//不跟随页面滚动
		  			title:filename+'  属性',
				    content:data,
				    ok: function(){//ok按钮提交表单，修改文件夹名
						rname_to=$('.pathinfo div input').val();
						if (rname_to==filename){
							return true;
						}
						if (!_pathAllow(rname_to)){
							return true;
						}else{
							path	=this_path+urlEncode(filename);
							rname_to=this_path+urlEncode(rname_to);
							$.ajax({
								type: "POST", 
								url: '?explorer/pathRname',
								data: 'path='+path+'&rname_to='+rname_to,				  
								success: function(data) {
									Main.UI.f5();
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
	var _pathInfoMuti = function(){
		$.ajax({
			url:'?explorer/pathInfoMuti',
			type:'POST',
			data:_getSelectJson('info_list'),
			beforeSend: function(){
				Main.Common.tips.loading('获取中!  ');
			},			
			success:function(data){
				Main.Common.tips.close('获取成功');
				$.dialog({
		  			padding:5,
		  			fixed: true,//不跟随页面滚动
		  			title:' 属性',
				    content:data,
				    cancel: true
				});
			}
		});
	};
	var _getSelectJson = function(param_name){
		var select_list = param_name+'=[';
		Main.Global.fileListSelect.each(function(index){
			var filename = this_path + urlEncode(Main.SetSelect.getObjName($(this)));
			var pathtype=Main.SetSelect.getObjType($(this))=='folder' ? 'folder':'file';	
			select_list += '{"type":"'+pathtype+'","file":"'+filename+'"}';
			if (index < Main.Global.fileListSelectNum-1) {
				select_list += ',';
			}
		});
		select_list +=']';
		return select_list;
	}

	// 删除 文件|文件夹 & 包含批量删除
	var _pathDelete = function(){
		if (Main.Global.fileListSelectNum < 1) return;
		var msg = '';
		if (Main.Global.fileListSelectNum ==1) {
			var selectObj = Main.Global.fileListSelect;
			var filename=Main.SetSelect.getObjName(selectObj);
			var pathtype=Main.SetSelect.getObjType(selectObj);
			var cut_filename=filename;
			if(filename.length>30){
				cut_filename=filename.substr(0,13)+"..."+filename.substr(-5,5);
			}
			if(pathtype=="folder"){
		  		delete_path_info = _arrayFind(json_data['folderlist'],'name',filename);	
		  		msg='确认要删除该文件夹吗？<br/><b>文件夹名</b>：'+cut_filename+'<b><br/>最后修改时间</b>：'+delete_path_info['mtime'];
			}
			else{
		  		delete_path_info = _arrayFind(json_data['filelist'],'name',filename);
				msg = '确认删除要删除此文件吗？<br/><b>文件名</b>：'
				+ cut_filename
				+ '<b><br/>类型</b>：'
				+ delete_path_info['ext']
				+ '文件<br/><b>大小</b>：'
				+ delete_path_info['size_friendly']
				+ '<br/><b>最后修改时间</b>：'
				+ delete_path_info['mtime'];
			}			
		}else{
			msg = '确认要删除该【'+Main.Global.fileListSelectNum+'】项内容吗？';
		}

		var delete_list = _getSelectJson('delete_list');
		Main.SetSelect.clear();
		$.dialog({
			fixed: true,//不跟随页面滚动
			icon:'question',
			title:'确认删除？',
			content: msg,
			ok:function() {
				$.ajax({
					url: '?explorer/deletePath',
					type:'POST',
					data:delete_list,
					beforeSend: function(){						
						Main.Common.tips.loading('删除中...');
					},
					success: function(data) {
						Main.UI.f5();
						Main.Common.tips.close(data);
					}
				});
			}
		});
	};

	//复制
	var _pathCopy = function(){
		if (Main.Global.fileListSelectNum < 1) return;
		$.ajax({
			url:'?explorer/pathCopy',
			type:'POST',
			data:_getSelectJson('copy_list'),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};

	//剪切
	var _pathCute = function(){
		if (Main.Global.fileListSelectNum < 1) return;
		$.ajax({
			url:'?explorer/pathCute',
			type:'POST',
			data:_getSelectJson('cute_list'),
			success:function(data){
				Main.Common.tips.tips(data);
			}
		});
	};

	// 粘贴
	var _pathPast = function(){
		var url='?explorer/pathPast&path='+this_path;
		$.ajax({
			url:url,
			dataType:'json',
			beforeSend: function(){
				Main.Common.tips.loading("粘贴操作中...");
			},
			success:function(jsonback){
				Main.Common.tips.close(jsonback['msg']);
				if (jsonback['select']!='') {
					Main.UI.f5();
					$.each(jsonback['select'],function(index,val){
						_setSelectByFilename(val['name'],val['type']);
					});					
				}
			}
		});
	};

	// 粘贴
	var _pathCuteDrag = function(dragTo){		
		if (Main.Global.fileListSelectNum < 1) return;		
		$.ajax({
			url:'?explorer/pathCuteDrag',
			type:'POST',
			data:_getSelectJson('cute_list')+'&path='+dragTo,
			beforeSend: function(){
				Main.Common.tips.loading("移动操作中...");
			},
			success:function(data){
				Main.Common.tips.close(data);
				Main.UI.f5();
			}
		});
	};

	/** 设置textarea的选中区域 */ 
	var _setSelectRange = function(textarea, start, end){ 
		if (typeof textarea.createTextRange != 'undefined' ){ // IE 	
			var range = textarea.createTextRange();		
			range.moveStart( "character", 0);// 先把相对起点移动到0处 
			range.moveEnd( "character", 0); 
			range.collapse( true); // 移动插入光标到start处 
			range.moveEnd( "character", end); 
			range.moveStart( "character", start); 
			range.select(); 
		}
		else if (typeof textarea.setSelectionRange != 'undefined' ){ 
			textarea.setSelectionRange(start, end); 
			textarea.focus(); 
		}
	};	
	//重命名
	var _pathRname = function() {
		var rname_to 	= "";		
		var path 		= "";
		var selectname 	= "";//成功后选中的名称
		var selectid 	= Main.SetSelect.getObjName(selectObj);
		var selecttype 	= Main.SetSelect.getObjType(selectObj);		
		selecttype 		= (selecttype=='folder'?'folder':selecttype);
		$(selectObj).find(".title").html("<div class='textarea'><textarea id='pathRenameTextarea' id='pathRenameTextarea' onclick='stopPP(arguments[0])' onfocus='this.style.height=this.scrollHeight + \"px\"' onpropertychange='this.style.height=this.scrollHeight-4 + \"px\"' oninput='this.style.height=this.scrollHeight-4 + \"px\"'>"+$(selectObj).find(".title").text()+"</textarea><div>");
		
		var $textarea 	= $("#pathRenameTextarea");
		var textarea 	= $textarea.get(0);

		if (selecttype=='folder') {
			$textarea.select();
		}else{//若为文件，则只选中名称部分
			var selectlen=selectid.length-selecttype.length-1;
			if(Main.Global.isIE){//IE
	            var range = textarea.createTextRange();
	            range.moveEnd('character', -textarea.value.length);         
	            range.moveEnd('character', selectlen);
	            range.moveStart('character', 0);
	            range.select();
	        }
	        else{//firfox chrome ...
	           textarea.setSelectionRange(0,selectlen);
	        }
		}
		$textarea.unbind('focus').focus();
		$textarea.keydown(function(event) {
			if (event.keyCode == 13) {
				event.preventDefault();//阻止编辑器回车
				stopPP(event);
				rname_to=$textarea.attr('value');//获取编辑器值
				selectname=rname_to;
				if (!_pathAllow(rname_to)) return;
				if (rname_to!=selectid){
					path	=this_path+urlEncode(selectid);
					rname_to=this_path+urlEncode(rname_to);
					$.ajax({
						type: "POST", 
						url: '?explorer/pathRname',
						data: 'path='+path+'&rname_to='+rname_to,
						beforeSend:function(){
							Main.Common.tips.loading();
						},
						success: function(data) {
							Main.UI.f5();
							Main.Common.tips.close(data);
							_setSelectByFilename(selectname,selecttype);
						}
					});	
				}
				else{
					$(selectObj).find(".title").html(selectid);
				}
			}
			if ( event.keyCode == 27){
				$(selectObj).find(".title").html(selectid);
			}
		});	
		$textarea.unbind('blur').blur(function(){	
			rname_to=$('#pathRenameTextarea').attr('value');//获取编辑器值
			if (!_pathAllow(rname_to)) return;
			if (rname_to!=selectid){
				path	=this_path+urlEncode(selectid);
				rname_to=this_path+urlEncode(rname_to);
				$.ajax({
					type: "POST", 
					url: '?explorer/pathRname',
					data: 'path='+path+'&rname_to='+rname_to,	
					beforeSend:function(){
						Main.Common.tips.loading();
					},			  
					success: function(data) {
						Main.UI.f5();
						Main.Common.tips.close(data);
					}
				});	
			}
			else{
				$(selectObj).find(".title").html(selectid);
			}
		});
	};

	var _pathUpload = function(){
		$.dialog({
			id:'id_upload_file',
			title:'多文件上传',
			width:380,
			padding:0,
			height:310,
			content:'<iframe width="100%" height="100%" frameborder="0" src="?upload&save_path='+this_path+'"></iframe>'
		});
	};

	var _pathZip = function(){
		if (Main.Global.fileListSelectNum < 1) return;
		$.ajax({
			url:'?explorer/zip',
			type:'POST',
			data:_getSelectJson('zip_list'),
			beforeSend: function(){
				Main.Common.tips.loading('正在压缩...');
			},
			success:function(data){
				Main.Common.tips.close(data,2000);
				Main.UI.f5();
			}
		});
	};

	var _pathUnZip = function(){
		var filename=this_path+urlEncode(Main.SetSelect.getObjName(selectObj));
		var url='?explorer/unzip&path='+filename;
		$.ajax({
			url:url,
			beforeSend: function(){
				Main.Common.tips.loading('正在解压...');
			},
			success:function(data){
				Main.Common.tips.close(data,2000);
				Main.UI.f5();
			}
		});
	};

	//html5 拖拽上传。
	var _dragUpload = function() {
		var dragElement     = $(document);//drag_selector;
		var dragFileList	= '';
		if(!Main.Global.isIE){
			var timer,
				inState = false,
				isFile  = true,
				box_height = 200,
				box_width = 400;
	
	        $('body').append('<div id="" class="maskView" style="z-index:999999;display:none;position:absolute;left:0;top:0;right:0;bottom:0;vertical-align: middle;font-size:50px;text-align:center;color:rgba(255,255,255,0.6);background:rgba(0,0,0,0.4);"><div style="position:absolute;top:40%;text-align:center;width:100%;">松开即可上传</div></div>');
			var maskView= $('.maskView');

	        dragElement.on("dragover",function(e) {
	            stopPP(e);
	            if (!isFile && inState) return;
	            var tran = e.originalEvent.dataTransfer;
	            if (tran) {
	                var types = tran.types;
	                if (types.length) {
	                    if (types[types.length - 1].indexOf("Files") == -1) {
	                    	isFile = false;
	                    }else{                        	
	                    	isFile = true;
	                    	if (inState == false){
	                    		inState = true;
	                    		maskView.css('display','block');
	                    		Main.Common.tips.loading('松开即可上传  ');
	                    	}
	                    }
	                }
	            }
	            if (timer) window.clearTimeout(timer)
	        });
	        dragElement.on("dragleave",function(e) {
	            stopPP(e);
	            if (timer){
	            	window.clearTimeout(timer);
	            }
	            timer = window.setTimeout(function() {
	            	if(isFile){
		            	inState = false;
		            	isFile  = true;
		            	Main.Common.tips.close();
		            	maskView.css('display','none');
	            	}
	            },100);
	        });
	        dragElement.on("drop",function(e) {
	        	stopPP(e);
	        	if (inState && isFile) {
	        		inState = false;
	        		isFile = true;
	        		//Main.Common.tips.close('上传中');
					Main.Common.tips.loading('正在上传中  ');
	        		maskView.css('display','none');
	        		var fileList  = e.originalEvent.dataTransfer.files;				
					if(fileList.length === 0) return;		
					var file_html = '';
					for (var i = 0, file; file = fileList[i]; i++) {
						file_html += "<div class='fileupload' id='file_id"+i+"'><span class='name'>"+file.name+" [<i>"
						+Math.round(((file.size/1024)*100))/100+" KB</i>]</span><span class='status'></span></div>";				
					}
					$('<div class="file_list"></div>').appendTo('body');
					$('.file_list').html(file_html);

					if ($('.file_drag_upload').length >=1) {
						$('.file_drag_upload')
						.stop(true,true)
						.css('top',$(document).height()-$('.file_drag_upload').height()+box_height)
						.animate({top:'-='+box_height+'px'}, 700);
					}else{
						dialog = $.dialog({
							id:'file_drag_upload',
							title:'拖拽上传',
							width:box_width,
							height:box_height,
							content:$('.file_list').html(),
							left:'99%',
							padding:0,
							top:'100%',
							fixed: true
						});
					}

					//异步上传
					var this_file = -1;
					var this_file_upload = -1
					for (var i = 0, file; file = fileList[i]; i++) {//依次上传
						var xhr = new XMLHttpRequest();
						var upload_file = '?upload/html5Upload&save_path='+this_path;//html5文件上传目录
						xhr.open("post",upload_file,1);//采用异步方式
						xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
						xhr.upload.addEventListener("progress", function(e){					
							this_file_upload++;
							//Main.Common.tips.loading('正在上传中  ');
						}, false);
						xhr.addEventListener("load", function(e){
							this_file ++;
							var info = '上传成功';					
							var result = jQuery.parseJSON(e.target.responseText);
							if (result['success'] == '0') {
								Main.Common.tips.close('上传失败'+result['info']);
							}
							$('.file_list #file_id'+this_file+' .status').html(info);
							dialog.content($('.file_list').get(0));	
							if(this_file == fileList.length-1){
								Main.Common.tips.close('上传完成',500);
								Main.UI.f5();
								$('.file_drag_upload')
								.delay(1200)
								.animate({top:'+='+(box_height+100)+'px'}, 600,function(){
									art.dialog.list['file_drag_upload'].close();
								});
							}					
						}, false);
						var fd = new FormData();
						fd.append('xfile',file);
						xhr.send(fd);
					}
	        	}
	        });
		}
	};

	return{		
		newFile:_newFile,
		newFolder:_newFolder,
		pathUpload:_pathUpload,		
		pathDelete:_pathDelete,
		pathCute:_pathCute,
		pathCopy:_pathCopy,
		pathZip:_pathZip,
		pathPast:_pathPast,
		pathInfo:_pathInfo,
		pathCuteDrag:_pathCuteDrag,
		// 树目录新建文件夹
		newFolderTree:_newFolderTree,
		pathRnameTree:_pathRnameTree,
		pathDeleteTree:_pathDeleteTree,
		pathCopyTree:_pathCopyTree,
		pathCuteTree:_pathCuteTree,
		pathPastTree:_pathPastTree,
		pathInfoTree:_pathInfoTree,

		init:function(){_dragUpload();},
		pathRname:function(){if (_hasSelect()) {_pathRname();}},
		pathUnZip:function(){if (_hasSelect()) {_pathUnZip();}},
		pathCopySee:function(){//查看剪贴板
			$.ajax({
				url:'?explorer/pathCopySee',
				success:function(data){
					$.dialog({
						title:'查看剪贴板',
						padding:0,
						height:200,
						width:400,
						content:data
					});
				}
			});
		},
		play:function(type){
			var list = [];
			Main.Global.fileListSelect.each(function(index){
				var pathtype = Main.SetSelect.getObjType($(this));
				if (inArray(Main.Common.filetype['music'],pathtype)
					|| inArray(Main.Common.filetype['movie'],pathtype)) {
					var file_name = Main.SetSelect.getObjName($(this));
					list.push(file_name);
				}
			});
			CMPlayer.play(list,type);
		}
	}
})();

//__________________________________________________________________________________//

Main.PathOpen = (function() {
	var selectObj 	= '';
	var MyPicasa  	= new Picasa();
	var selectNum 	= 0;
	var ie 			= !-[1,];
	var _hasSelect = function(){
		selectNum = Main.Global.fileListSelectNum;
		selectObj = Main.Global.fileListSelect
		if(selectNum == 1){
			return true;
		}
		return false;
	};

	//打开目录。更新文件列表，ajax方式
	var _pathList = function(path){//
		path = urlEncode(path);
		if (path == this_path) return; //如果相同，则不加载。

		this_path = path;
		web_path  = path.replace(urlEncode(WEB_ROOT),'');
		web_path  = encodeURIComponent(urlDecode(web_path));//统一成js的url编码
		if (web_path.substr(0,1)!='/'){
			web_path='/'+web_path;
		}
		Main.UI.f5(true,true);
	};

	var _pathBack = function(){//后退操作
		$.ajax({
			dataType:'json',
			url:'?explorer/historyBack',
			async:false,	
			success:function(data){
				this_path = urlEncode(data['thispath']);
				json_data = data['list'];
				web_path  = this_path.replace(urlEncode(WEB_ROOT),'');
				web_path  = encodeURIComponent(urlDecode(web_path));//统一成js的url编码
				if (web_path.substr(0,1)!='/'){
					web_path='/'+web_path;
				}
				Main.Global.historyStatus = data['history_status'];
				Main.UI.f5(false,true);
			}
		});
	}
	var _pathNext = function(){//前进操作
		$.ajax({
			dataType:'json',
			url:'?explorer/historyNext',		
			success:function(data){
				this_path = urlEncode(data['thispath']);
				json_data = data['list'];
				web_path  = this_path.replace(urlEncode(WEB_ROOT),'');
				web_path = encodeURIComponent(urlDecode(web_path));//统一成js的url编码
				if (web_path.substr(0,1)!='/'){
					web_path='/'+web_path;
				}
				Main.Global.historyStatus = data['history_status'];
				Main.UI.f5(false,true);
			}
		});
	};

	//双击或者选中后enter 打开 执行事件
	var _open = function(hotKey){
		if(!_hasSelect()) return;		
		var thistype = Main.SetSelect.getObjType(selectObj);
		thistype     = thistype.toLowerCase();
		var thisfile = Main.SetSelect.getObjName(selectObj);
		if (thistype == 'folder'){//文件夹跳转
			var path = urlDecode(this_path) + thisfile +'/';
			_pathList(path);//更新文件列表
			return;
		}
		if (inArray(Main.Common.filetype['bindary'],thistype)) {//二进制文件，则下载
			_download();
			return;
		}
		if (inArray(Main.Common.filetype['image'],thistype)) {
			if (hotKey != 13) return; //双击操作打开
			if (list_type=='icon') {
				$(selectObj).find('.ico').dblclick();
			}else{
				$(selectObj).dblclick();
			}
			return;
		}
		if ((thistype!='html' && thistype !='htm' && thistype !='oexe')
			&& (inArray(Main.Common.filetype['code'],thistype) 
			|| inArray(Main.Common.filetype['text'],thistype)) ) {
			_openText();//代码文件，编辑
			return;
		}
		if (inArray(Main.Common.filetype['music'],thistype) 
			|| inArray(Main.Common.filetype['movie'],thistype) ) {
			Main.PathOperate.play(thistype);
			return;
		}
		var url="?explorer/fileOpen&type="+thistype+"&path="
		+web_path+urlEncode(thisfile);
		$.ajax({
			url:url,
			dataType:'json',
			success:function(data){
				$.dialog({  //跨窗口调用
					title:data.title,
					width:data.width,
					resize:true,
					fixed:true,
					height:data.height,
					content:data.content,
					padding:data.padding
				});
			}
		});
	};
	var _download = function(){
		if(!_hasSelect()) return;
		var filename=this_path+urlEncode(Main.SetSelect.getObjName(selectObj));
		var url='?explorer/fileDownload&path='+filename;
		Main.Common.tips.tips("即将开始下载");
		art.dialog.open(url,{title:false,time:0.1,width:0,height:0});	
	};
	//新的页面作为地址打开。鼠标右键，IE下打开
	var _openIE = function(){
		if(!_hasSelect()) return;
		var url=web_host+urlDecode(web_path)+encodeURIComponent(Main.SetSelect.getObjName(selectObj));
		if (Main.SetSelect.getObjType(selectObj) == "folder") {
			url += '/' ;
		}
		//art.dialog.open(url,{title:'浏览器',width:700,height:500});
		window.open(url);
	};

	var _openWindow = function(url,title,name) {
		if (name == undefined){
			name = 'openWindow'+Math.floor(Math.random()*1000);
		}
		//id 会在dialog控件中加入iframe的name
		art.dialog.open(url,{id:name,title:title,width:'80%',height:'75%',resize:true});
	};


	var _openEditor = function(path,title,ext){
		if (window.top.frames["OpenopenEditor"] == undefined) {
			path ='?editor/edit&type='+ext+'&filename='+path;
			_openWindow(path,title,'openEditor');
		}else{
			$('.openEditor').css('visibility','visible');
			FrameCall.top('OpenopenEditor','Main.Editor.add','"'+urlDecode(path)+'"');			
		}
	}

	//用text编辑
	var _openText = function(){
		if(!_hasSelect()) return;
		var thistype=Main.SetSelect.getObjType(selectObj);
		var file =Main.SetSelect.getObjName(selectObj);
		var title=file+'   编辑';
		var path = this_path+urlEncode(file);

		if (inArray(Main.Common.filetype['bindary'],thistype) ||
			inArray(Main.Common.filetype['music'],thistype) ||
			inArray(Main.Common.filetype['image'],thistype) ||
			inArray(Main.Common.filetype['movie'],thistype) ){
			$.dialog({title:false,content:'不是文本文件!',time:1});
			return;
		}

		var size = 0;//文件大小检测
		if (json_data['filelist']){
			for (var i=0; i<json_data['filelist'].length; i++){
				if (json_data['filelist'][i]['name']==Main.SetSelect.getObjName(selectObj)){
					size=json_data['filelist'][i]['size'];
				}
			}
		}
		if (size>1000000){//大概超过1M的文件，进行打开提示
			$.dialog({
				title:'警告!',
				icon: 'question',
				content:'文件大小超过1M,是否继续打开？',
				okVal:'继续打开',
				cancel:'取消',
				ok:function(){
					_openEditor(path,title,thistype);
				},
				cancle:function(){
					return false;
				}
			});
		}
		else {
			_openEditor(path,title);
		}
	};
	return{
		//如果为图片的话，双击打开被colorbox绑定，
		//为避免pathOpen中绑定无限循环,拆分成两个函数。其他方式则单独处理
		open:_open,
		init:function(){
			PicasaOpen = false;//全局变量，用于标记是否有幻灯片播放
			MyPicasa.init(".picasaImage");
			MyPicasa.initData();
		},
		initPicasaData:function(){
			PicasaOpen = false;
			MyPicasa.initData();
		},
		openText:_openText,
		openIE:_openIE,
		download:_download,
		pathList:_pathList,
		pathBack:_pathBack,
		pathNext:_pathNext
	}
})();
