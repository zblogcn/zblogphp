var editors			= {};
var editor_current	= undefined;
var editor_current_id= '';
var Main			= {};
var animate_time	= 160;
Main.Tap = (function(){
	var _bindTab = function(){
		$('.edit_tab .tab').live('mouseenter',function (e) {
			if (!$(this).hasClass('this')){
				$(this).addClass('hover');
			}
			$(this).unbind("mousedown").mousedown(function(e){
				if (!$(this).hasClass('this') && e.target.nodeName !='A'){
					$(this).removeClass('hover').addClass('this');
					Main.Editor.select($(this).attr('id'));
					//return false;
				}
			});
		}).unbind('mouseleave').live('mouseleave',function(){
			$(this).removeClass('hover');
		}).unbind('dblclick').live('dblclick',function(){
			//双击关闭标签
			Main.Editor.remove($(this).attr('id'));
		});

		$('.edit_tab .tab .close').live('click',function (e) {
			var id = $(this).parent().attr('id');
			Main.Editor.remove(id);
			stopPP(e);
		});
	};
	
	// 拖拽——移动
	var _bindDrag = function(){
		var $self,$tabs,$drag,
			isDraging = false,
			isDragInit= false,
			first_left= 0,
			box_left  = 0,				
			tab_width = 0,
			tab_margin= 0,
			tab_parent_width= 0,
			tab_parent_left = 0,
			current_animate_id;	//标签切换，当前动画所在的标签
		$('.edit_tab .tab').unbind('mousedown').live('mousedown',function(e){
			if (e.target.nodeName == 'A') {
				return ;
			}else if(e.target.nodeName == 'SPAN') {
				var id = $(e.target).parent().attr('id');
				$self = $('.edit_tab #'+id);
			}else {
				$self = $(this);
			}

			isDraging = true;
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
			isDragInit = true,
			first_left = e.pageX;
			$tab_parent  = $('.edit_tab');
			$tabs = $('.edit_tab .tab');
			$(".draggable-dragging").remove();
			$drag = $self.clone().addClass("draggable-dragging").prependTo('body');
							
			tab_margin= parseInt($tabs.css('margin-right'));
			tab_parent_width = $tab_parent.width();
			tab_parent_left  = $tab_parent.get(0).getBoundingClientRect().left;
			tab_parent_left  = tab_parent_left+$(window).scrollLeft();
			box_left = $self.get(0).getBoundingClientRect().left;
			tab_width = parseInt($tabs.css('width'));

			var top = $self.get(0).getBoundingClientRect().top-parseInt($self.css('margin-top'));
			var left = e.clientX - first_left + box_left;

			$('body').prepend("<div class='dragMaskView'></div>");
			$drag.css({'width':tab_width+'px','top':top,'left':left});
			$self.css('opacity',0);
		};
		var __dragMove = function(e){
			if (!isDraging) return;
			if(isDragInit==false){
				__dragStart(e);
			}

			var left = e.clientX - first_left + box_left;
			if (left < tab_parent_left 
				|| left > tab_parent_left+tab_parent_width-tab_width){
				return;// 拖出边界则不处理
			}
			$drag.css('left',left);
			$tabs.each(function(i) {
				var t_left = $(this).get(0).getBoundingClientRect().left;
				if (left > t_left && left < t_left+tab_width/2+tab_margin){
					if ($self.attr('id') == $(this).attr('id')) {
						return;//当前区域移动，没有超过左右过半
					}
					__change($(this).attr('id'),'left');
				}
				if (left > t_left-tab_width/2+tab_margin && left < t_left){
					if ($self.attr('id') == $(this).attr('id')) {
						return;//当前区域移动，没有超过左右过半
					}
					__change($(this).attr('id'),'right');
				}
			});
		};
		// 标签交换位置
		var __change  = function(id,change){
			//chrome标签类似动画，动画进行中，，且为当前标签动画则继续	
			if ($self.is(":animated") 
				&& current_animate_id == id){
				return;
			}
			//处理上次动画结束事物
			current_animate_id = id;
			$self.stop(true,true);
			$('.insertTemp').remove();
			$tabs = $('.edit_tab .tab');
			
			var temp,width = $self.width();
			var $move = $('.edit_tab #'+id);
			var	$insert = $self.clone(true).insertAfter($self)
				.css({'margin-right':'0px','border':'none'}).addClass('insertTemp');

			if (change == 'left') {
				$self.after($move).css('width','0px');				
			}else{
				$self.before($move).css('width','0px');
				$move.before($insert);
			}					
			$self.animate({'width':width+'px'},animate_time);
			$insert.animate({'width':'0px'},animate_time,function(){
				$(this).remove();
				$tabs = $('.edit_tab .tab');
			});
		};

		var __dragEnd = function(e){
			//if (!isDraging) return false;
			isDraging = false;
			isDragInit= false;
			startTime = 0;
			$('.dragMaskView').remove();
			
			 // 点击事件回调会影响两个事件：选择，和拖拽弹起，
			 //此处后执行，设置需要再次设置焦点
			 editor_current.focus();
			if ($drag == undefined) return;

			box_left = $self.get(0).getBoundingClientRect().left;
			$drag.animate({left:box_left+'px'},animate_time,function(){
				$self.css('opacity',1);
				$(this).remove();						
			});
		};
	};

	var resetWidth = function(action){
		var max_width	= 122;
		var reset_width = max_width;
		var $tabs		= $('.edit_tab .tab');
		var full_width	= $('.edit_tab .tabs').width()-4;
		var margin		= parseInt($tabs.css('margin-right')) + parseInt($tabs.css('border-right'));
		var add_width	= parseInt($('.edit_tab .add').outerWidth())+margin*2;
		var count		= $tabs.length;	
		//不用变化的个数
		var max_count = Math.floor((full_width-add_width)/(max_width+margin));
		if (count > max_count) {
			reset_width = Math.floor((full_width - add_width)/count) - margin;
		}
		switch (action) {
			case 'add':
				$('.edit_tab .tabs .this').css('width','0')
				.animate({'width':reset_width+'px'},animate_time);
			case 'remove':
				$tabs.animate({width:reset_width+'px'},animate_time);
				break;
			case 'resize':
				$tabs.css('width',reset_width+'px');
				break;
			default:break;
		}
	}

	return {
		resetWidth:resetWidth,
		init:function(){
			$(window).bind("resize",function(){
				resetWidth('resize');
			});
			_bindTab();
			_bindDrag();
		}				
	};
})();

Main.Editor = (function(){
	var jsonData = {};
	var _config = {
		html: 	['htmlmixed'],
		htm: 	['htmlmixed'],
		css: 	['css'],
		less: 	['less'],
		scss: 	['css'],
		sass: 	['sass'],
		md: 	['markdown'],
		js: 	['javascript'],
		xml: 	['xml'],
		sh: 	['shell'],
		yml: 	['yaml'],
		vb: 	['vbscript'],
		vbs: 	['vbscript'],
		tpl: 	['smartymixed'],
		rb: 	['ruby'],
		php: 	['php'],
		sql: 	['sql'],
		pl: 	['perl'],
		py: 	['python'],
		go: 	['go'],
		lua: 	['lua'],
		erl: 	['erlang'],
		lsp: 	['commonlisp'],

		c: 	 	['clike','text/x-csrc'],
		cpp: 	['clike','text/x-c++src'],
		h: 	 	['clike','text/x-c++src'],
		m: 	 	['clike','text/x-c++src'],
		java: 	['clike','text/x-java'],
		jsp: 	['clike','text/x-java'],
		cs: 	['clike','text/x-csharp'],
		asp: 	['clike','text/x-csharp'],
		as: 	['clike','clike']
	};

	// 通过属性查找。
	var editorFind = function(value,type){
		if (value==undefined || type==undefined) {
			return '';
		}
		var obj;
		for (obj in editors){
			if (editors[obj][type] == value){
				return editors[obj].id;
			}
		}
		return '';
	};			
	
	var initData = function(filename){
		if (filename == undefined) {
			var selectID = 'id_'+Date.parse(new Date())+'_'+Math.ceil(Math.random()*1000);	
			jsonData = {
				id:			selectID,
				name:		'newfile.txt',
				filename:	'',
				charset:	'utf-8',
				mode:		['xml'],
				content:	'<textarea id="text_'+selectID+'"></textarea>'
			};
			initEditor();
			return;
		}

		filename = urlEncode(filename);
		$.ajax({
			dataType:'json',
			url:'./?editor/fileGet&filename='+filename,
			beforeSend: function(){
				$.dialog({
					id:'id_fileget',
					icon:'loading',
					//top:'50%',
					left:'50%',
					padding:10,
					title:false,
					content:'数据获取中...'
				});
			},
			success: function(data) {
				$.dialog({id:'id_fileget'}).close();
				var selectID = 'id_'+Date.parse(new Date())+'_'+Math.ceil(Math.random()*1000);
				jsonData = {
					id:			selectID,
					name:		data.name,
					filename:	data.filename,
					charset:	data.charset,
					mode:		_config[data.ext],
					content:	data.content
				};
				initEditor();
			}
		});		
	};
	var initEditor = function(){				
		var html_tab = 
		'<div class="tab" id="'+jsonData.id+'" title="'+jsonData.filename+'">'+
		'	<span>'+jsonData.name+'</span>'+
		'	<a href="javascript:void(0);" class="close icon-remove-sign"></a>'+
		'	<div style="clear:both;"></div>'+
		'</div>';
		$(html_tab).insertBefore('.edit_tab .add');
		var html_body = '<div id="'+jsonData.id+'" class="tab" ><textarea id="text_'+jsonData.id+'"></textarea></div>';
		$('.edit_body .tabs').append(html_body);
		$('.edit_body .tabs '+'#text_'+jsonData.id).text(jsonData.content);

		select(jsonData.id);
		Main.Tap.resetWidth('add');
		
		var this_editor = CodeMirror.fromTextArea(document.getElementById('text_'+jsonData.id), {
			lineNumbers: true,
			styleActiveLine: true,
			indentUnit: 7,
			tabMode: "shift",	
			enterMode: "keep",
			indentWithTabs: true,
			autofocus:true,
			matchBrackets: true,
			theme: codetheme,	
			lineWrapping: true,//自动换行
			//readOnly:true,//只读
			onKeyEvent: function() {//zendCoding 插件
				return zen_editor.handleKeyEvent.apply(zen_editor, arguments);
			},
			foldGutter: {
		    	rangeFinder: new CodeMirror.fold.combine(
		    		CodeMirror.fold.brace, CodeMirror.fold.comment)
		    },    
		    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
		    autoCloseBrackets: true,
			extraKeys: {
				"Ctrl-S": function() {				
					Main.Editor.save(editor_current_id);
					return false;
				}
			}
		});


		if (jsonData.mode != undefined) {
			if(jsonData.mode[0] == 'clike'){
				this_editor.setOption("mode",jsonData.mode[1]);
			}else{
				this_editor.setOption("mode",jsonData.mode[0]);
			}
			CodeMirror.autoLoadMode(this_editor,jsonData.mode[0]);			
		}
		this_editor.setOption('lineWrapping',true);//设置自动换行,为刷新全屏
		this_editor.focus();
		editor_current = this_editor;
		editor_current_id = jsonData.id;

		editors[jsonData.id] = {
			id:jsonData.id,
			name:jsonData.name,
			charset:jsonData.charset,
			filename:jsonData.filename,
			editor:this_editor
		};
	};

	//选中
	var select = function(selectID,exist) {
		if(selectID == undefined || selectID =='') return;

		//添加最初标签，或者标签不存在
		$('.edit_tab .this').removeClass('this');
		$('.edit_tab #'+selectID).addClass('this');
		$('.edit_body .this').removeClass('this');
		$('.edit_body div#'+selectID).addClass('this');

		if (editors[selectID] != undefined){
			editor_current_id = selectID;
			editor_current = editors[selectID].editor;
			editor_current.focus();
		}
		if (exist == true) {
			$('.edit_tab .this')
				.stop(true,true)
				.animate({"opacity":0.3},100)
				.animate({"opacity":0.8},100)
				.animate({"opacity":0.5},40)
				.animate({"opacity":1},40,function(){
					editor_current.focus();
				});
		}
	};

	//设置所有主题
	var resetTheme = function(){
		for (obj in editors){
			if (editors[obj]['filename'] != ''){
				editors[obj].editor.setOption('theme',codetheme);//codemirror主题设置
			}
		}
	};

	//检测是否修改
	var isChanged = function(edit_this){
		if(edit_this.doc.history.lastTime!=0 &&
		   edit_this.doc.history.lastSaveTime != edit_this.doc.history.lastTime){
			return true;
		}
		return false;	
	};


	var _saveSend = function(editorID,filename,isDelete){
		var edit_this = editors[editorID].editor;
		var html = edit_this.getValue();
		html=html.replace(/&/g,'-@$@-');//post数据之前,&会引起歧义，so转义。
		html=html.replace(/\+/g,'-($)-');//post数据之前,加号会引起歧义，so转义。
		$.ajax({
			type:'POST',
			async:false,
			url:'?editor/fileSave',
			data:'path='+filename+'&charset='+edit_this.charset+'&filestr='+html,
			beforeSend: function(){
				$.dialog({
					id:'id_filesave',
					icon:'loading',
					padding:10,
					title:false,
					content:'数据发送中...'
				});
			},
			success:function(data){
				// 保存成功 记录上次保存时的修改时间。
				edit_this.doc.history.lastSaveTime = edit_this.doc.history.lastTime;
				$.dialog({id:'id_filesave'}).close();
				$.dialog({
					title:false,
					icon:'succeed',
					padding:10,
					time:0.7,
					content:'保存成功!'+data
				});
				if (isDelete) {
					_removeData(editorID);
				}
			}
		});
		editor_current.focus();
	}

	//新建并保存
	var _new2save = function(editorID,isDelete){
		var filename= 'D:/t.txt';
		_saveSend(editorID,filename,isDelete);
	}

	// 编辑保存，如果是新建标签则新建文件，询问保存路径。
	var save = function(editorID,isDelete){
		var edit_this = editors[editorID].editor;
		if(!isChanged(edit_this)) return;
		if(edit_this == undefined || edit_this == '') {
			$.dialog({
				icon:'warning',
				padding:10,
				title:false,
				content:'数据出错！'
			});
			return;
		}
		// 通过标签建立的文件。
		if (filename == '') {
			new2save();return;
		};
		var filename= urlEncode(editors[editorID].filename);
		_saveSend(editorID,filename,isDelete);
	};

	var saveall = function(){
		for (var obj in editors){
			Main.Editor.save(editors[obj].id);
		}
	};
	
	//安全删除标签，先检测该文档是否修改。
	var removeSafe = function(editorID) {
		var edit_this = editors[editorID].editor;
		if (isChanged(edit_this)) {
			$.dialog({
				title:'警告!',
				resize:false,
				background: '#fff',
   				opacity: 0.4,
				lock:true,
				icon: 'question',
				content:'文件尚未保存,是否保存？',
				padding:30,
				button:[
					{name:'保存',focus:true,callback:function(){
						save(editorID,true);
					}},
					{name:'不保存',callback:function(){
						_removeData(editorID);
					}},
					{name:'取消',callback:function(){
						editor_current.focus();
					}}
				]
			});
		}else{
			_removeData(editorID);
		}
	}


	//删除
	var _removeData = function(editorID) {
		delete editors[editorID];
		var changeID = '';
		var $tabs    = $('.edit_tab .tab');
		if ($('.edit_tab #'+editorID).hasClass('this')){
			if ($tabs.length > 1) {
				if ($($tabs[0]).attr('id') == editorID) {
					changeID = $($tabs[1]).attr('id');
				}else{
					$tabs.each(function(i){
						var temp_id = $(this).attr('id');
						if (temp_id == editorID){return false;}//跳出该循环。
						changeID = temp_id;
					});
				}						
			}
			if(changeID !=''){
				//先显示下一个body，避免闪烁
				$('.edit_body div#'+changeID).addClass('this');
			}

			$('.edit_body div#'+editorID).remove();					
			$('.edit_tab #'+editorID).animate({width:0},animate_time,function(){
				$('.edit_tab #'+editorID).remove();
				Main.Tap.resetWidth('remove');
				select(changeID);
			});					
		}else{
			$('.edit_body div#'+editorID).remove();
			$('.edit_tab #'+editorID).animate({width:0},animate_time,function(){
				$('.edit_tab #'+editorID).remove();
				Main.Tap.resetWidth('remove');
			});
		}
	};
	var setTheme = function(thistheme){
		var url = static_path+'style/skin/'+thistheme+'/app_code_edit.css';
		$("#link_css_list").attr("href",url);
	};
	//----------------------------------------
	return {
		select:select,
		remove:removeSafe,
		save:save,
		saveall:saveall,
		setTheme:setTheme,
		resetTheme:resetTheme,
		add:function(filename){
			var id   = editorFind(filename,'filename');
			if (id  != ''){//已存在
				select(id,true);
			}else{
				initData(filename);
			}
		}
	};
})();


Main.Toolbar=(function(){
	var updatePreview = function() {
		$('#preview').css('display','block');
		var previewFrame = document.getElementById('preview');
		var preview =  previewFrame.contentDocument ||  previewFrame.contentWindow.document;
		preview.open();
		preview.write(editor_current.getValue());
		preview.close();
	};
	var bindEvent = function(){
		//主题选择
		$(".dropbox").mouseleave(function (){
			$(this).css("display","none");
		});
		//跳转到行。
		$('#gotoline .button').live('click',function(){
			var line=$("#gotoline input").val();
			editor_current.setCursor(line-1, 0, 1);
		});
		

		$("#fontsize li").mouseenter(function () {
			$(this).addClass("lihover");	
			$(this).unbind('click').click(function(){//点击选中
				var val=$(this).text();
				$('a.font span').text(val);
				$('.CodeMirror-scroll').css('font-size',val);
				$('.dropbox').css("display","none");	

				$("#fontsize li.this").removeClass('this');
				$(this).addClass('this');	
				editor_current.focus();
			});
		}).mouseleave(function (){
			$(this).toggleClass("lihover");
		});

		//主题修改
		$("#codetheme li").mouseenter(function () {
			$(this).addClass("lihover");	
			$(this).unbind('click').click(function(){//点击选中
				var val=$(this).attr('theme');
				codetheme = val;
				Main.Editor.resetTheme();
				$('a.codetheme span').text($(this).html());

				$('.dropbox').css("display","none");
				$.ajax({
					url:'?setting/setCodetheme&theme='+val,
					success:function(data){
						var result = eval('('+data+')');
						$.dialog({
							title:false,
							icon:result['state'],
							content:result['msg'],
							time:1
						});
					}
				});
				$("#codetheme li.this").removeClass('this');
				$(this).addClass('this');
				editor_current.focus();
			});
		}).mouseleave(function (){
			$(this).toggleClass("lihover");
		});
	};
	
	var bindTools = function(){
		$('.tools a').click(function(e){
			if (editor_current == undefined) return;
			var action = $(this).attr('class');
			switch (action) {
				case 'save':Main.Editor.save(editor_current_id);break;
				case 'saveall':Main.Editor.saveall();break;
				case 'pre'	:editor_current.undo();break;
				case 'next'	:editor_current.redo();break;
				case 'find'	:editor_current.execCommand('find');break;
				case 'gotoline':
					$.dialog({
						title:"跳转",
						id:'gotoline',
						top:32,
						left:160,
						padding:20,
						content:'<div id="gotoline"><span>跳转到:</span><input value="" type="text"/><a class="button">go</a></div>'
					});
					break;
				case 'font':
					if ($('#fontsize').css('display')=='block') {
						$('#fontsize').fadeOut(100);
					}else{
						$('#fontsize').fadeIn(100);
					}
					break;
				case 'codetheme':
					if ($('#codetheme').css('display')=='block') {
						$('#codetheme').fadeOut(100);
					}else{
						$('#codetheme').fadeIn(100);
					}
					break;			
				case 'wordbreak'://设置与取消自动换行。
					if (editor_current.getOption('lineWrapping')) {
						editor_current.setOption('lineWrapping',false);
					}else{
						editor_current.setOption('lineWrapping',true);
					}
					break;
				case 'comment'://注释&反注释
					var range = {from: editor_current.getCursor(true),to:editor_current.getCursor(false)};
					editor_current.commentRange(isComment, range.from, range.to);
					break;
				case 'tabbeautify':editor_current.indentSelection("smart");break;//table对齐
				case 'about':
					$.dialog.open('http://player.youku.com/player.php/sid/XMTM4NDQwNzgw/v.swf',{id:'setting_mode',title:'zendCoding 使用帮助',width:910,height:580,resize:true});
					break;

				case 'max':FrameCall.father('Main.UI.editorFull',"''");break;
				case 'close':Main.Editor.remove(editor_current_id);break;
				default:break;
			}
			stopPP(e);
			editor_current.focus();
		});
	};			
	return{
		init:function(){
			bindEvent();
			bindTools();
		}
	};
})();

$(document).ready(function() {
	Main.Tap.init();
	Main.Toolbar.init();
	if (frist_file != '') {
		Main.Editor.add(frist_file);
	}
});