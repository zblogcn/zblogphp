var Setting=(function(){
	//保存等处理，不同dom下都掉用tools，自动判断所在pageid，
	var tips = function(msg,icon){
		if (icon == undefined) icon = 'succeed';
		$.dialog({
			title:false,
			icon:icon,
			time:1,
			padding:0,
			content:"<div class='tips'>"+msg+"</div>"
		});
	};
	
	//保存等处理，不同dom下都掉用tools，自动判断所在pageid，
	var setTheme = function(){
		var thistheme=$('.theme .this .ico').attr('theme');
		$('#link_css_list').attr('href',static_path+'style/skin/'+thistheme+'/app_setting.css');
		FrameCall.father('Main.UI.setTheme','"'+thistheme+'"');
	};

	var setGoto = function (slider){
		if (slider == '') slider = 'user';
		$('.selected').removeClass('selected');
		$('ul.setting li#'+slider).addClass('selected');
		$.ajax({
			url:'?setting/slider&slider='+slider,
			beforeSend:function (data){
				$('.main').html("<img src='./static/images/loading.gif'/>");
			},
			success:function(data){
				$('.main').css('display','none');
				$('.main').html(data);
				$('.main').fadeIn('fast');
				if (slider=='fav'){//收藏夹，则首次初始化ajax数据
					favInit();
				}
			}
		});
	};

	// 收藏夹处理
	var favInit = function (){//初始化json数据
		$.ajax({
			url:'?fav/get',
			async:false,
			success:function(data){
				json_data=eval('('+data+')');
			}
		});
		var html="<tr class='tittle'>"+
				"<td class='name'>名称<span>(不允许重复)</span></td>"+
				"<td class='path'>地址<span>(绝对地址)</span></td>"+
				"<td class='action'>操作</td>"+
				"</tr>";
		var len=json_data.length;
		for (var i=0; i<len; i++){
			html+=
			"<tr class='favlist' name='"+json_data[i]['name']+"' path='"+json_data[i]['path']+"'>"+
			"	<td class='name'><input type='text' id='sname' value='"
				+json_data[i]['name']+"' /></td>"+
			"	<td class='path'><input type='text' id='spath' value='"
				+json_data[i]['path']+"' /></td>"+
			"	<td class='action'>"+
			"		<a href='#' onclick='' class='button edit'>保存修改</a>"+
			"		<a href='#' onclick='' class='button del'>删除</a>"+
			"	</td>"+
			"</tr>";
		}
		$('table#list').html(html);
		
		//首次含参数的方式进入
		if (favAddName!=''){
			var htmltr=	
			"<tr class='favlist' name='' path=''>"+
			"	<td class='name'><input type='text' id='sname' value='"+favAddName+"' /></td>"+
			"	<td class='path'><input type='text' id='spath' value='"+favAddPath+"' /></td>"+
			"	<td class='action'>"+
			"		<a href='#' onclick='' class='button addsave'>保存</a>"+
			"		<a href='#' onclick='' class='button addexit'>取消</a>"+
			"	</td>"+
			"</tr>";
			$(htmltr).insertAfter("table#list tr:last");
			favAddName = '';
			favAddPath = '';
		}
	};

	var bindEvent = function(){
		if (setting==undefined){setting='user';}
		if (setting.indexOf('&')>0) {
			// 添加收藏方式进入setting
			favAddName =  setting.split('&')[1].split('=')[1];
			favAddPath =  urlDecode(setting.split('&')[2].split('=')[1]);
			setting = 'fav';
		}
		
		$.get('?setting/slider&slider='+setting, function(data) {
			$('.main').html(data);
			if (setting=='fav'){//收藏夹，则首次初始化ajax数据
				favInit();
			}
		});
		$('ul.setting li#'+setting).addClass('selected');
		//是否在框架中【dialog or not】
		$('#body').css('top','0').find('.menu_left').css('top','0');;
		$('ul.setting li').click(function(){
			var slider=$(this).attr('id');
			setGoto(slider);
		});
		$('ul.setting li').hover(
			function(){	$(this).addClass('hover');},
			function(){	$(this).toggleClass('hover');}
		);
		$('.box .list').live(
			'hover',
			function(){	$(this).addClass('listhover');},
			function(){	$(this).toggleClass('listhover');}
		);

		//密码修改
		$("#upassword_new").live('keyup',function(){
			var password=$("#upassword_new").val();
			$.get('?user/md5&str='+password,function(data){
				$(".upasswordinfo").html('md5='+data);
			});
		});

		//播放器设置绑定事件
		$('.list').live('click',function(){
			$(this).parent().find('.this').removeClass('this');
			$(this).addClass('this');
		});

		$('.theme .list').live('click',function(){
			setTheme();
		});

		$('.wall .list').live('click',function(){
			obj=window.parent.document.body;//IE
			$(obj).find('.desktop').css(
				'background-image',
				'url(./static/images/wall_page/'+$('.wall .this .ico').attr('wall')+'.jpg)'
			);
		});

		//删除一条收藏记录
		$('.fav a.del').live('click',function(){
			var obj=$(this).parent().parent();//定位到tr
			var name=$(obj).attr('name');
			$.ajax({
				url:'?fav/del&name='+name,
				async:false,
				success:function(data){
					if (data=='0'){
						tips('删除失败','error');
					}else {
						tips('删除成功!','succeed');
						$(obj).detach();
						FrameCall.father('Main.UI.tree.init','""');
					}
				}
			});		
		});

		//取消添加
		$('.fav a.addexit').live('click',function(){
			var obj=$(this).parent().parent();//定位到tr
			$(obj).detach();
		});
		//添加一条收藏记录，后保存
		$('.fav a.addsave').live('click',function(){
			var obj=$(this).parent().parent();//定位到tr
			var name=$(obj).find('#sname').val();
			var path=$(obj).find('#spath').val();
			if (name=='' || path ==''){
				tips('内容不能为空','warning');
				return false;
			}
			$.ajax({
				url:'?fav/add&name='+name+'&path='+path,
				success:function(data){
					if (data=='0'){
						tips('添加失败！请注意名称是否重复','error');
					}else {
						tips('添加成功','succeed');
						$(obj).attr('name',name);
						$(obj).attr('path',path);
						var htmlaction=
						"<a href='#' onclick='' class='button edit'>保存修改</a>"+
						"&nbsp;<a href='#' onclick='' class='button del'>删除</a>";
						$(obj).find('td.action').html(htmlaction);
						FrameCall.father('Main.UI.tree.init','""');
					}			
				}
			});
		});

		//编辑一条收藏记录
		$('.fav a.edit').live('click',function(){
			var obj=$(this).parent().parent();//定位到tr
			var name=$(obj).attr('name');
			var name_to=$(obj).find('#sname').val();
			var path_to=$(obj).find('#spath').val();

			if (name_to=='' || path_to ==''){
				tips('内容不能为空','warning');
				return false;
			}
			$.ajax({
				url:'?fav/edit&name='+name+'&name_to='+name_to+'&path_to='+path_to,
				success:function(data){
					if (data=='0'){
						tips('编辑失败！请注意名称是否重复','error');
					}else {
						tips('编辑成功','succeed');
						$(obj).attr('name',name);
						FrameCall.father('Main.UI.tree.init','""');
					}	
				}
			});		
		});

		//添加收藏记录，dom操作。
		$('.fav a.add').live('click',function(){
			var htmltr=	
			"<tr class='favlist' name='' path=''>"+
			"	<td class='name'><input type='text' id='sname' value='' /></td>"+
			"	<td class='path'><input type='text' id='spath' value='' /></td>"+
			"	<td class='action'>"+
			"		<a href='#' onclick='' class='button addsave'>保存</a>"+
			"		<a href='#' onclick='' class='button addexit'>取消</a>"+
			"	</td>"+
			"</tr>";
			$(htmltr).insertAfter("table#list tr:last");
		});
	};

	// 设置子内容动作处理
	var tools = function (action){  		
		var page=$('.selected').attr('id');
		switch (page){
			case 'user'://修改用户名
				if (action=='name'){
					var uname=$('#name').val();
					$.ajax({
						url:'?user/changeUserName&name='+uname,
						success:function(data){
							var result = eval('('+data+')');
							tips(result['msg'],result['state']);
						}
					});	
				}else if(action=='password'){
					var password_now=$('#password_now').val();
					var password_new=$('#password_new').val();
					$.ajax({
						url:'?user/changeUserPassword&password_now='+password_now+'&password_new='+password_new,
						success:function(data){
							var result = eval('('+data+')');
							tips(result['msg'],result['state']);
						}
					});	
				}				
			break;
			case 'player':
				var music=$('.music .this .info').html();
				var movie=$('.movie .this .info').html();
				FrameCall.father('CMPlayer.changeTheme','"'+music+'","'+movie+'"');
				var geturl='?setting/setPlayer&musictheme='+music+'&movietheme='+movie;
				$.ajax({
					url:geturl,
					success:function(data){
						var result = eval('('+data+')');
						tips(result['msg'],result['state']);
					}
				});							
				break;
			case 'wall':
				var wall=$('.wall .this .ico').attr('wall');
				var geturl='?setting/setWall&wall='+wall;
				$.ajax({
					url:geturl,
					success:function(data){
						var result = eval('('+data+')');
						tips(result['msg'],result['state']);
					}
				});							
				break;
			case 'theme':
				var theme=$('.theme .this .ico').attr('theme');
				var geturl='?setting/setTheme&theme='+theme;
				$.ajax({
					url:geturl,
					success:function(data){
						tips("设置成功");
					}
				});							
				break;
			case 'editer':
				var value=$('.box .this .info').html();
				$.ajax({
					url:'?setting/setCodetheme&theme='+value,
					success:function(data){
						var result = eval('('+data+')');
						tips(result['msg'],result['state']);
					}
				});							
			break;
			default:break;
		}
	};
	// 对外提供的函数
	return{
		init:bindEvent,
		setGoto:setGoto,
		tools:tools,
		setTheme:setTheme		
	};
})();


$(document).ready(function() {
	Setting.init();
});
