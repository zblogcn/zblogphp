<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('x2013')) {
    $zbp->ShowError(48);
    die();
}
$blogtitle = '主题配置';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if (isset($_GET['act']) && $_GET['act'] == 'save') {
    $navcontent = str_replace('{$name}', $zbp->name, $_POST['inpContent']);
    $navcontent = str_replace($zbp->host, '{$host}', $navcontent);
    $zbp->Config('x2013')->NavBar = $navcontent;
    $zbp->SaveConfig('x2013');
    $zbp->ShowHint('good');
}
?>
<style>p{line-height:1.5em;padding:0.5em 0;}</style>
<link href="style/plugin/modern.css" rel="stylesheet">
<script type="text/javascript" src="style/plugin/accordion.js"></script>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle; ?></div>
  	<div class="SubMenu"><?php x2013_SubMenu(1); ?></div>
	<div id="divMain2">
		<div class="widget-list ui-droppable" style="min-width: 830px;">
		<div class="widget-list-header">添加导航链接</div>
		<div class="widget-list-note">请点击选择您要添加的链接类型</div>
			<ul data-role="accordion" class="accordion span10">
                    <li>
                        <a href="#">文章</a>
                        <div>
                           <div class="input-control select">
                                    <select multiple="1" size="10" id="post">
										<?php x2013_get_link('post'); ?>
                                    </select>
                                </div>
								<input type="button" value="添加" onclick="addsekectpartent('post')"/>
                        </div>
                    </li>
                    <li class="">
                        <a href="#">独立页面</a>
                        <div style="display: none;">
                           <div class="input-control select">
                                    <select multiple="1" size="8" id="page">
										<?php x2013_get_link('page'); ?>
                                    </select>
                                </div>
								<input type="button" value="添加" onclick="addsekectpartent('page')"/>
                        </div>
                    </li>
                    <li>
                        <a href="#">分类</a>
                        <div>
                            <div class="input-control select">
                                    <select multiple="1" size="8" id="cate">
										<?php x2013_get_link('category'); ?>
                                    </select>
                                </div>
								<input type="button" value="添加" onclick="addsekectpartent('cate')"/>
                        </div>
                    </li>
					<li>
                        <a href="#">Tags标签</a>
                        <div>
                            <div class="input-control select">
                                    <select multiple="1" size="10" id="tag">
										<?php x2013_get_link('tags'); ?>
                                    </select>
                                </div>
								<input type="button" value="添加" onclick="addsekectpartent('tag')"/>
                        </div>
                    </li>
                    <li>
                        <a href="#">自定义链接</a>
                        <div>
                            <div class="input-control text">
								<input type="url" id="addurl" placeholder="输入网址" style="width:50%"><input type="text"  id="addtitle" placeholder="输入标题" style="width:20%"><input type="button" value="添加" onclick="addurlpartent()">
							 </div>
                        </div>
                    </li>
                </ul>

		</div>

		<div class="siderbar-list">
			<div class="siderbar-drop" id="siderbar">
			<div class="siderbar-header">拖动链接进行排序</div>
			<div  class="siderbar-sort-list ui-sortable">
			<div class="widget widget_source_other ">
			<div class="page-sidebar">
			  <ul id="connect"><?php echo $zbp->Config('x2013')->NavBar; ?></ul>
			</div>
			<div class="clear"></div>
			  <div id="result">
			  <form id="form1" name="form1" method="post" action="?act=save"  onsubmit="return verify()">
			  <div style="display:none;"><textarea name="inpContent" id="inpContent"></textarea></div>
			  <input type="submit" name="button" id="btn" value="确认修改"/>
			  </form>
			  </div>
			</div>
			</div></div>
		</div>
	</div>
	<div class="clear"></div>
	
<script type="text/javascript">
	$(document).ready(function(){
		$("#connect").sortable();
		$("#connect").disableSelection();

		$("#connect li").live('mouseenter mouseleave', function(event) {
		  if (event.type == 'mouseenter') {
			$(this).append("<span class='del icon-cancel' onclick='del(this)' title='删除'></span>");
		  } else {
			$(this).find(".del").remove();
		  }
		});

	});

	function del(item){
		$(item).parent().remove();
	}

	function verify(){
		var result = document.getElementById("connect").innerHTML;
		//alert(result);
		document.getElementById("inpContent").value=result;
		//document.getElementById("form1").action="?act=Save";
		return true
	}
	function addsekectpartent(vartype){
		var result = document.getElementById("connect").innerHTML;

		if(vartype=="post"){
			$("#post option:selected").each(function() {
				result = result+"<li class='menu-item'><a href='"+$(this).val()+"'>"+$(this).text()+"</a></li>";
			});
		}else if(vartype=="page"){
			$("#page option:selected").each(function() {
				result = result+"<li class='menu-item'><a href='"+$(this).val()+"'>"+$(this).text()+"</a></li>";
			});
		}else if(vartype=="cate"){
			$("#cate option:selected").each(function() {
				result = result+"<li class='menu-item'><a href='"+$(this).val()+"'>"+$(this).text()+"</a></li>";
			});
		}else if(vartype=="tag"){
			$("#tag option:selected").each(function() {
				result = result+"<li class='menu-item'><a href='"+$(this).val()+"'>"+$(this).text()+"</a></li>";
			});
		}
		//alert(result);
		document.getElementById("connect").innerHTML = result;
	}
	function addurlpartent(){
		var addurl = document.getElementById("addurl").value;
		var addtitle = document.getElementById("addtitle").value;
		var result = document.getElementById("connect").innerHTML;
		result = result+"<li class='menu-item'><a href='"+addurl+"'>"+addtitle+"</a></li>";
		document.getElementById("connect").innerHTML = result;
	}

</script>	
</div>
<script type="text/javascript">ActiveTopMenu("topmenu_x2013");</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>