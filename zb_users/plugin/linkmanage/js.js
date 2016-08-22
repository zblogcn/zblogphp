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
			change: function(){
				console.log('Relocated Menu');
			}
	});
});
function del_menu(id){
	window.location.href="main.php?del="+id;
}

function postsort(){
	serialized = $('#menu-edit-body-content .nav-menu').nestedSortable('serialize',{ attribute:"sid"});
	console.log(serialized);
	$.post("save.php?type=sort", $("#menuName").serialize()+'&'+serialized , function(data) {
		alert('保存成功');
		return false;
	});
}


function del_link(id,menuid){
	$.post("save.php?type=del_link", {"id":id,'menuid':menuid} , function(data) {if(data==1){alert('请先删除子链接再删除本链接');}else{window.location.href="main.php?id="+menuid;}return false;});
}

function save_link(id){
	var input_name = "input[name='menu-item["+id+"][menu-item-";//url]']
	//$("input[name='menu-item[1452176482476][menu-item-url]']").val()
	var id = id;
	var title = $(input_name+"title]']").val();
	var url = $(input_name+"url]']").val();
	var newtable = $(input_name+"newtable]']").val();
	//var img = $(input_name+"img]']").val();
	var img = '';

	$.post("save.php?type=save_link", { id:id,title:title,url:url,newtable:newtable,img:img} , function(data) {alert('保存成功');return false;});
}

function add_menu(){
   $( "#dialog" ).dialog({
      modal:true,
      width: 500
    });
}

function addsekectpartent(item,type,menuid){
	var foo = [];
	links = $(item).parent().prev().find("select");
	links.find(":selected").each(function(i, selected){
	  foo[i] = {
	  	"sid":new Date().getTime(),
	  	"title":$(selected).text(),
	  	"url":$(selected).val(),
	  	"newtable":0,
	  	"img":"",
	  	"type":type,
	  };
	  create_item(foo[i],menuid)
	});
	$.post("save.php?type=menu", { id:foo[0].sid,title:foo[0].title,url:foo[0].url,newtable:foo[0].newtable,img:foo[0].img,type:foo[0].type} , function(data) {return false;});
	console.log(foo);
}

function create_item(item,menuid){
	$('<li sid="menuItem_'+ item.sid +'">'+
'		<div class="menu-item-bar">'+
'			<div class="menu-item-handle ui-sortable-handle">'+
'				<span class="item-title"><span class="menu-item-title">'+ item.title +'</span> </span>'+
'				<span class="item-controls"> '+
'					<span class="item-type">'+ item.type +'</span>'+
'					<span class="item-edit ui-icon ui-icon-triangle-1-n"></span>'+
'				</span>'+
'			</div>'+
'		</div>'+
'		<div class="menu-item-settings" style="display: none;">'+
'			<p class="link-p">'+
'				<label class="link-edit" for="custom-menu-item-url">'+
'					<span>URL</span>'+
'					<input name="menu-item['+ item.sid +'][menu-item-url]" type="text" class="code menu-item-textbox custom-menu-item-url" value="'+ item.url +'">'+
'				</label>'+
'			</p>'+
'			<p class="link-p">'+
'				<label class="link-edit" for="custom-menu-item-name">'+
'					<span>链接文本</span>'+
'					<input name="menu-item['+ item.sid +'][menu-item-title]" type="text" class="regular-text menu-item-textbox input-with-default-title custom-menu-item-url" value="'+ item.title +'">'+
'				</label>'+
'			</p>'+
'<p class="link-p">'+
'											<label class="link-edit" for="custom-menu-item-name">'+
'												<span>新窗口打开链接</span>'+
'												<input type="text" name="menu-item['+ item.sid +'][menu-item-newtable]" class="checkbox" value="'+ item.newtable +'" style="display: none;">'+
'											</label>'+
'										</p>'+
'			<p class="button-controls">'+
'					<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" onclick="del_link('+ item.sid +',\''+menuid+'\');return false;">删除链接</span></button>'+
'					<button class="ui-button-primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text" onclick="save_link('+ item.sid +');return false;">保存链接</span></button>'+
'			</p>'+
'		</div>'+
'	</li>').appendTo("#menu-edit-body-content .nav-menu");

}
