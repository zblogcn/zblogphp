$(function() {
	$( "#accordion" ).accordion({
	    header: "h3",
	    collapsible: true
	});
	$( "#menu-edit-body-content" ).on("click",".item-edit",function() {
    	$(this).toggleClass('ui-icon-triangle-1-n').toggleClass('ui-icon-triangle-1-s');
    	$(this).parents('.menu-item-bar').next('.menu-item-settings').toggle('fast');
    });
    $('#menu-edit-body-content .nav-menu').nestedSortable({
			//listType: 'ul';
			forcePlaceholderSize: true,
			//handle: 'div',
			//helper:	'clone',
			items: 'li',
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div', //嵌套元素，可放空
			maxLevels: 4, //最大层数
			isTree: true, //树形
			expandOnHover: 700,
			startCollapsed: false,
			//doNotClear : true,
			//change: function(){
			//	console.log('Relocated Menu');
			//}
	});
});


function add_menu(){
   $( "#dialog" ).dialog({
      modal:true,
      width: 500
    });
}

function edit_menu(id){
	window.location.href="menuedit.php?id="+id;
}

function del_menu(id){
	window.location.href="main.php?del="+id;
}

function save_menusetting(){
	var sort_serialized = $('#menu-edit-body-content .nav-menu').nestedSortable('serialize',{ attribute:"sid"});

	$.post("save.php?type=save_menu", $("#menu-config").serialize()+'&'+sort_serialized+'&links='+ JSON.stringify(links_json) + '&tempid=' +tempid,
		function(data) {
			$( "#message" ).dialog();
			return false;
		});
}

//删除链接
function del_link(id,menuid){
	if($("li[sid='menuItem_" + id + "']").has("ol").length){
		alert('请先删除子链接再删除本链接');
	} else {
		// $.post(
		// 	"save.php?type=del_link",
		// 	{
		// 		"id":id,'menuid':menuid
		// 	},
		// 	function(data) {
		// 		if(data==1) {
		// 			alert('请先删除子链接再删除本链接');
		// 		} else {
		// 			$('li[sid$='+id+']').remove();
		// 			delete links_json['ID'+id];
		// 			//$( "#message" ).dialog();
		// 		}
		// 	return false;
		// });
		$('li[sid$='+id+']').remove();
		delete links_json['ID'+id];
	}
}

//编辑保存单链接
function save_link(id){
	var input_name = "input[name='menu-item[" + id + "][menu-item-";//url]']
	//$("input[name='menu-item[1452176482476][menu-item-url]']").val()
	links_json['ID' + id].title = $(input_name + "title]']").val();
	links_json['ID' + id].url = $(input_name + "url]']").val();
	links_json['ID' + id].newtable = $(input_name + "newtable]']").val();
	links_json['ID' + id].menuid = $("input[name='id']").val();

	links_json['ID' + id].icon = '';
	//var icon = $(input_name+"icon]']").val();

	//links_json['ID'+id] = {id:id,title:title,url:url,newtable:newtable,icon:icon,type:type,sysid:sysid,menuid:menuid};

	$.post(
		"save.php?type=save_link",
		links_json['ID' + id],
		function(data) {
			data = JSON.parse(data);
			$("li[sid='menuItem_" + id + "'] .menu-item-title").html(data.title);
			//if(data.new_check){
			//	save_sort();
			//}
			return false;
		});
}

//保存排序
function save_sort(){
	var menu_id = "menuid=" + $("input[name='id']").val();
	var sort_serialized = $('#menu-edit-body-content .nav-menu').nestedSortable('serialize',{ attribute:"sid"});
	$.post(
		"save.php?type=save_sort",
		menu_id + '&' + sort_serialized,
		function(data) {
			return false;
		});
}

//添加链接
function add_link(item,type,menuid){
	var foo = [];
	if (type !== "custom"){
		links = $(item).parent().prev().find("select");
		links.find(":selected").each(function(i, selected){
		  foo[i] = {
		  	"id":new Date().getTime() + Math.random().toString(10).substring(2,5),
		  	"title":$(selected).text(),
		  	"url":$(selected).val(),
		  	"newtable":0,
		  	"icon":"",
		  	"type":type,
		  	"sysid":$(selected).attr("sysid"),
		  	"menuid":menuid
		  };
		  create_item(foo[i],type,menuid);
		});
	} else {
		tempid++;
		foo[0] = {
		  	"id":new Date().getTime() + Math.random().toString(10).substring(2,5),
		  	"title":"新链接",
		  	"url":"http://",
		  	"newtable":0,
		  	"icon":"",
		  	"type":type,
		  	"sysid":tempid,
		  	"menuid":menuid
		}
		create_item(foo[0],type,menuid)
	}
	//TODO
}

function create_item(item,type,menuid){
	var display = "style='display: none;'";
	var readonly = "readonly='true'";
	if (type == "custom"){
		display = "";
		readonly = "";
	}

	links_json['ID'+item.id] = item;

	$('<li sid="menuItem_'+ item.id +'">'+
'		<div class="menu-item-bar">'+
'			<div class="menu-item-handle ui-sortable-handle">'+
'				<span class="item-title"><span class="menu-item-title">'+ item.title +'</span>'+
'				<span class="item-controls"> '+
'					<span class="item-type">'+ item.type +'</span>'+
'					<span class="item-edit ui-icon ui-icon-triangle-1-n"></span>'+
'				</span>'+
'			</div>'+
'		</div>'+
'		<div class="menu-item-settings form-horizontal" '+ display +'>'+
'			<p class="link-p">'+
'				<label class="link-edit" for="custom-menu-item-url">'+
'					<span>URL</span>'+
'					<input name="menu-item['+ item.id +'][menu-item-url]" type="text" '+ readonly +' class="code menu-item-textbox custom-menu-item-url" value="'+ item.url +'">'+
'				</label>'+
'			</p>'+
'			<p class="link-p">'+
'				<label class="link-edit" for="custom-menu-item-name">'+
'					<span>描述</span>'+
'					<input name="menu-item['+ item.id +'][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title custom-menu-item-url" value="'+ item.title +'">'+
'				</label>'+
'			</p>'+
'			<p class="link-p">'+
'				<label class="link-edit" for="custom-menu-item-name">'+
'					<span>新窗口打开 </span>'+
'					<input type="text" name="menu-item['+ item.id +'][menu-item-newtable]" class="checkbox" value="'+ item.newtable +'" style="display: none;">'+
'					<span class="imgcheck"></span>'+
'				</label>'+
'			</p>'+
'			<p class="button-controls">'+
'					<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" onclick="del_link('+ item.id +',\''+menuid+'\');return false;">删除链接</span></button>'+
'					<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" onclick="save_link('+ item.id +');return false;">保存链接</span></button>'+
'			</p>'+
'		</div>'+
'	</li>').appendTo("#menu-edit-body-content .nav-menu");

	// $.post("save.php?type=creat_link", item ,
	// 	function(data) {
	// 		console.log(data);
	// 		return false;
	// });
}

function reset_item(){
	$("#menu-edit-body-content .nav-menu").empty();
	links_json = {};
}

function save_config(){
		$.post(
		"save.php?type=save_config",
		links_json['ID' + id],
		function(data) {
			data = JSON.parse(data);
			$("li[sid='menuItem_" + id + "'] .menu-item-title").html(data.title);
			//if(data.new_check){
			//	save_sort();
			//}
			return false;
		});
}