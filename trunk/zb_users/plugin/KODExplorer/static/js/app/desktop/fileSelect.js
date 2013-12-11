Main.FileSelect = (function(){
	var isSelect		= false;	// 	是否多选状态
	var isDraging		= false;	//	是否拖拽状态

	//初始化选择
	var _initSelect = function(){
		_bindDragEvent();
		_bindEvent();
		_bindSelectEvent();
	};
	var _bindEvent = function(){
		// 屏蔽对话框内操作
		$(Main.Config.FileBoxClass).live('mouseenter',function (e) {
			if (isDraging) {//hover,hover 到文件夹时则添加目标选择类
				if ($(this).hasClass(Main.Config.TypeFolderClass)
					&& !$(this).hasClass(Main.Config.SelectClassName)) {					
					$(this).addClass('selectDragTemp');
				}
			}			
			if(!isSelect && !isDraging){//框选时，由于ctrl重选时会反选有hover
				$(this).addClass(Main.Config.HoverClassName);	
			}
			$(this).unbind("mouseup").mouseup(function(e){
				//鼠标右键,有选中，且当前即为选中
				if(e.which==3 && !$(this).hasClass(Main.Config.SelectClassName)){
					Main.SetSelect.clear();
					$(this).addClass(Main.Config.SelectClassName);
					Main.SetSelect.select();
				}				
			}).unbind("mousedown").mousedown(function(e){
				Main.RightMenu.hidden();
				//if (Main.UI.isEdit()) return true;		
				if (e.which != 1) return true;

				if (!e.ctrlKey && !e.shiftKey && !$(this).hasClass(Main.Config.SelectClassName)) {
					Main.SetSelect.clear();
					$(this).addClass(Main.Config.SelectClassName);
					Main.SetSelect.select();
				}
				if(e.ctrlKey) {//ctrl 跳跃选择
					if ($(this).hasClass(Main.Config.SelectClassName)) {//已经选中则反选
						Main.SetSelect.resumeMenu($(this));//恢复右键菜单id
						$(this).removeClass(Main.Config.SelectClassName);					
					}else{
						Main.SetSelect.setMenu($(this));//修改右键菜单至多选
						$(this).addClass(Main.Config.SelectClassName);						
					}
					Main.SetSelect.select();
				}else if(e.shiftKey){//shift 连选
					var current = parseInt($(this).attr(Main.Config.FileOrderAttr));
					if (Main.Global.fileListSelectNum == 0) {
						_selectFromTo(0,current);
					}else{//有选中，则当前元素序号对比选中的最左和最右，
						var first = parseInt(Main.Global.fileListSelect.first().attr(Main.Config.FileOrderAttr));
						var last  = parseInt(Main.Global.fileListSelect.last().attr(Main.Config.FileOrderAttr));
						if (current < first) {
							//selectFromTo(current,last);
							_selectFromTo(current,first);
						}else if(current > last){
							//selectFromTo(first,current);
							_selectFromTo(last,current);
						}else if(current > first  && current < last){
							_selectFromTo(first,current);
						}
					}
				}				
			})
		}).unbind('mouseleave').live('mouseleave',function(){
			$(this).removeClass(Main.Config.HoverClassName);
			$(this).removeClass('selectDragTemp');
		}).unbind('click').live('click',function (e) {
			stopPP(e);//再次绑定，防止冒泡到html的click事件
			if (!e.ctrlKey && !e.shiftKey && $(this).hasClass(Main.Config.SelectClassName)) {
			//多选后再次直接点击,已经选中则反选
				Main.SetSelect.clear();
				$(this).removeClass(Main.Config.SelectClassName);
				$(this).addClass(Main.Config.SelectClassName);
				Main.SetSelect.select();				
			}
		});

		//双击事件
		$(Main.Config.FileBoxClass).unbind('dblclick').live('dblclick',function(e){//双击打开
			stopPP(e);
			if (e.altKey){
				Main.PathOperate.pathInfo();
			}else {
				Main.PathOpen.open();
			}
		});	
		$(Main.Config.FileBoxTittleClass).unbind('dblclick').live('dblclick',function (e) {
			Main.PathOperate.pathRname();//重命名
			stopPP(e);return false;
		});
	};

	// 拖拽——移动 select 
	var _bindDragEvent= function(){
		var delayTime = 100;
		var leftOffset= 50;
		var topOffset = 80-Main.Global.topbar_height;
		var $self;
		var startTime = 0;
		var hasStart  = false;
		var boxTop	  = 0;
		var boxLeft	  = 0;
		var screenHeight;
		var screenWidth;

		$(Main.Config.FileBoxClass).unbind('mousedown').live('mousedown',function(e){
			if (Main.UI.isEdit()) return true;
			if (e.which != 1 || isSelect) return true;
			$self = $(this);
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
			Main.RightMenu.hidden();
			isDraging = true;
			startTime = $.now();
			boxTop  = e.pageY;
			boxLeft = e.pageX;
			screenHeight = $(document).height();
			screenWidth  = $(document).width();
		};
		var __dragMove = function(e){
			if (!isDraging) return true;
			if (($.now() - startTime > delayTime)  && !hasStart) {
				__makeDragBox();
			}
			var x = (e.clientX >= screenWidth-50 ? screenWidth-50 : e.clientX);
			var y = (e.clientY >= screenHeight-50 ? screenHeight-50 : e.clientY);		
			x = (x <= 0 ? 0 : x);
			y = (y <= 0 ? 0 : y);
			x = x - leftOffset;
			y = y - topOffset;

			$('.draggable-dragging').css('left',x);
			$('.draggable-dragging').css('top',y);
			if(Main.Global.isIE){//ie 无法事件穿透则遍历对比鼠标位置
				$('.'+Main.Config.TypeFolderClass).each(function() {
			    	var mouseX = e.pageX;
			    	var mouseY = e.pageY;
			    	var offset = $(this).offset();
			    	var width = $(this).width();
			    	var height = $(this).height();		       
			    	if (mouseX > offset.left 
				       	&& mouseX < offset.left+width
				       	&& mouseY > offset.top
				       	&& mouseY < offset.top+height){
						$(this).addClass('selectDragTemp');
					}else{
						$(this).removeClass('selectDragTemp');
					}
			    });
			}
		};
		var __dragEnd = function(e){
			if (!isDraging) return false;
			isDraging = false;
			hasStart  = false;
			$('body').css('cursor','auto');
			$('.draggable-dragging').fadeOut(200,function(){
				$(this).remove();
			});
			if ($('.selectDragTemp').length != 0) {
				var dragTo = this_path+$('.selectDragTemp').attr('title')+'/';
				Main.PathOperate.pathCuteDrag(dragTo);
			}
		};
		var __makeDragBox = function(){
			hasStart = true;
			$('body').css('cursor','move');	
			//移动时会挡住下面元素，导致hover不可用，
			//webkit firfox下css属性 pointer-events: none;鼠标事件穿透可解决。
			var type = $self.find('.ico').attr('filetype');
			$('<div class="file draggable-dragging">'
				+'<div class="drag_number">'+Main.Global.fileListSelectNum+'</div>'
				+'<div class="ico" style="background:'+$self.find('.ico').css('background')+'"></div>'
			  +'</div>').appendTo('body');
		};
	};


	// 框选 select 
	var _bindSelectEvent = function(){
		var startX			= null;
		var startY			= null;
		var $selectDiv		= null;
		$(Main.Config.BodyContent).unbind('mousedown').live('mousedown',function(e){
			if (Main.UI.isEdit()) return true;
			if (isDraging || e.which != 1) return true;
			__dragSelectInit(e);
			if(this.setCapture){this.setCapture();}
			$(document).unbind('mousemove').mousemove(function(e) {__dragSelecting(e);});
			$(document).one('mouseup',function(e) {
				__dragSelectEnd(e);
				Main.Global.isDragSelect = true;
				if(this.releaseCapture) {this.releaseCapture();}
			});
		})
		//创建模拟 选择框，框选开始
		var __dragSelectInit = function(e) {
			if ($(e.target).parent().hasClass(Main.Config.FileBoxClassName)
				|| $(e.target).parent().parent().hasClass(Main.Config.FileBoxClassName)
				|| $(e.target).hasClass('fix')
			) return;
			
			Main.UI.clearWindows();
			if (!(e.ctrlKey || e.shiftKey)) Main.SetSelect.clear();
			if ($(e.target).hasClass("ico")==false){// 编辑状态不可选
				if ($('#selContainer').length == 0) {
					$('<div id="selContainer"></div>').appendTo(Main.Config.FileBoxSelector);
					$selectDiv = $('#selContainer');
				}				
				startX = e.pageX;
				startY = e.pageY-Main.Global.topbar_height;
				isSelect = true;
			}
		};		
		//框选，鼠标移动中
		var __dragSelecting= function(e) {
			if (!isSelect) return true;		
			if ($selectDiv.css('display') =="none" ){
				$selectDiv.css('display','');
			}
			var mouseX = e.pageX;
			var mouseY = e.pageY-Main.Global.topbar_height;
			$selectDiv.css({
				'left'	: Math.min(mouseX,  startX),
				'top'	: Math.min(mouseY,  startY),
				'width' : Math.abs(mouseX - startX),
				'height': Math.abs(mouseY - startY)
			});
			// ---------------- 框中选择关键算法 ---------------------
			var _l = $selectDiv.offset().left;
			var _t = $selectDiv.offset().top-Main.Global.topbar_height;
			var _w = $selectDiv.width(), _h = $selectDiv.height();
			var totalNum = Main.Global.fileListNum;

			for ( var i = 0; i < totalNum; i++) {
				var currentBox = Main.Global.fileListAll[i];
				var $currentBox= $(Main.Global.fileListAll[i]);
				var sl = currentBox.offsetWidth + currentBox.offsetLeft;
				var st = currentBox.offsetHeight + currentBox.offsetTop;
				if (sl > _l && st > _t
					&& currentBox.offsetLeft < _l + _w 
					&& currentBox.offsetTop < _t + _h) {
					if (!$currentBox.hasClass("selectDragTemp")) {
						if ($currentBox.hasClass("selectToggleClass")){
							continue;
						}
						if ($currentBox.hasClass(Main.Config.SelectClassName)) {
							$currentBox.removeClass(Main.Config.SelectClassName).addClass("selectToggleClass");
							Main.SetSelect.resumeMenu($currentBox);//恢复右键选择
							continue;
						}
						$currentBox.addClass("selectDragTemp");
					}							
				}else {
					$currentBox.removeClass("selectDragTemp");
					if ($currentBox.hasClass("selectToggleClass")) {
						$currentBox.addClass(Main.Config.SelectClassName).removeClass("selectToggleClass");
					}
				}
			}
		};
		//框选结束
		var __dragSelectEnd = function(e) {
			if (!isSelect) return false;
			$selectDiv.css('display','none');
			$('.selectDragTemp').addClass(Main.Config.SelectClassName).removeClass("selectDragTemp");
			$('.selectToggleClass').removeClass('selectToggleClass');//移除反选掉的div

			Main.SetSelect.select();
			isSelect = false;
			startX	 = null;
			startY	 = null;
		};
	};

	//获得选中文件【夹】相对位置的文件并返回
	var _getPosition = function(pose){
		var position = 0;						//选择的位置，默认为第一个
		var $list 	 = Main.Global.fileListSelect;//
		var listNum  = Main.Global.fileListSelectNum;
		var totalNum = Main.Global.fileListNum;	//总数目		

		var __icon_position = function(){
			var rowNum		= Main.Global.fileRowNum;	//一行的数目			
			if (Main.Global.fileListSelectNum == 1) {//只有一个为选中状态
				var thisNumber = parseInt($list.attr(Main.Config.FileOrderAttr));
				switch(pose){
					case "up":
						position = ((thisNumber <=0)? thisNumber:thisNumber - 1);
						break;
					case "left":
						position = ((thisNumber < rowNum)? 0:thisNumber-rowNum);
						break;
					case "down":
						position = ((thisNumber >=totalNum-1)? thisNumber:thisNumber + 1);
						break;
					case "right":
						position = ((thisNumber+rowNum >=totalNum-1)? totalNum-1:thisNumber+rowNum);
						break;
					default:break;
				}
			}else if(Main.Global.fileListSelectNum > 1){	//多个已选择的文件
				var firstNumber = parseInt($list.first().attr(Main.Config.FileOrderAttr));
				var lastNumber  = parseInt($list.last().attr(Main.Config.FileOrderAttr));
				switch(pose){
					case "up":
						position = position = ((firstNumber <=0)? firstNumber:firstNumber - 1);
						break;
					case "left":((firstNumber <=rowNum)? firstNumber:firstNumber-rowNum);
						break;
					case "down":
						position = ((lastNumber >=totalNum)? lastNumber:lastNumber + 1);
						break;
					case "right":
						position = ((lastNumber+rowNum >=totalNum)? lastNumber:lastNumber+rowNum);
						break;
					default:break;
				}
			}
		}
		__icon_position();
		return Main.Global.fileListAll.eq(position);
	};

	//设置选中
	var _setSelectAt = function(pos){
		var $select;
		switch (pos){
			case 'home':$select = Main.Global.fileListAll.first();break;
			case 'end':	$select = Main.Global.fileListAll.last(); break;		
			case 'left':
			case 'up':
			case 'right':
			case 'down':
				$select = _getPosition(pos);
				break;
			case 'all'://全选
				$select = Main.Global.fileListAll;break;
			default:break;
		}
		Main.SetSelect.clear();
		$select.addClass(Main.Config.SelectClassName);
		Main.SetSelect.select();		
	};

	//shift 选择，ctrl+上下左右选择
	var _selectFromTo = function(from,to){
		//console.log('from='+from+';to='+to);
		Main.SetSelect.clear();		
		for (var i = from; i <= to; i++) {
			$(Main.Global.fileListAll[i]).addClass(Main.Config.SelectClassName);
		}
		Main.SetSelect.select();
	}

	//对外提供调用方法
	return{
		init:_initSelect,
		selectPos:_setSelectAt
	}
})();

$(document).ready(function() {
	Main.UI.init();
	Main.PathOperate.init();
	Main.PathOpen.init();	
	Main.FileSelect.init();	
	Main.RightMenu.init();
});