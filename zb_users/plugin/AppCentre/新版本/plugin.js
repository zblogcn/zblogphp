

$(document).ready(function(){ 

$("#divMain2").prepend("<form class='search' name='edit' id='edit' method='post' enctype='multipart/form-data' action='"+bloghost+"zb_users/plugin/AppCentre/app_upload.php'><p>本地上传并安装插件zba文件:&nbsp;<input type='file' id='edtFileLoad' name='edtFileLoad' size='40' />&nbsp;&nbsp;&nbsp;&nbsp;<input type='submit' class='button' value='提交' name='B1' />&nbsp;&nbsp;<input class='button' type='reset' value='重置' name='B2' />&nbsp;</p></form>")

if(app_enabledevelop){

$("tr").each(function(){
	$(this).append("<td class='td15' align='center'></td>");
});
$("tr").first().children().last().append("<b>开发者模式</b>");

}

$(".plugin").each(function(){

	var t=$(this).data("pluginid");
	if(t==undefined)t=$(this).find("strong").html();
	var s=""
	s=s+"<a class=\"button\"  href='"+bloghost+"zb_users/plugin/AppCentre/plugin_edit.php?id="+t+"' title='编辑该插件信息'><img height='16' width='16' src='"+bloghost+"zb_users/plugin/AppCentre/images/application_edit.png'/></a>";

	s=s+"&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"button\"  href='"+bloghost+"zb_users/plugin/AppCentre/app_pack.php?type=plugin&id="+t+"' title='导出该插件' target='_blank'><img height='16' width='16' src='"+bloghost+"zb_users/plugin/AppCentre/images/download.png'/></a>";

	if(app_username){
		s=s+"&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"button\" href='"+bloghost+"zb_users/plugin/AppCentre/submit.php?type=plugin&amp;id="+t+"' title='上传应用到官方网站应用中心' target='_blank'><img height='16' width='16' src='"+bloghost+"zb_users/plugin/AppCentre/images/drive-upload.png'/></a>";
	}


	
	if(app_enabledevelop){
	
		$(this).parent().children().last().append(s);

	}

	if(!$(this).hasClass("plugin-on")){
		$(this).parent().children().eq(4).append("&nbsp;&nbsp;&nbsp;&nbsp;<a class=\"button\"  href='"+bloghost+"zb_users/plugin/AppCentre/app_del.php?type=plugin&id="+t+"' title='删除该插件' onclick='return window.confirm(\"单击“确定”继续。单击“取消”停止。\");'><img height='16' width='16' src='"+bloghost+"zb_users/plugin/AppCentre/images/delete.png'/></a>");
	}else{
	};

});



});