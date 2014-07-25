<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require 'function.php';

$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('em2zbp')) {$zbp->ShowError(48);die();}

$blogtitle='em2zbp';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

$function_list = array(
	"convert_article_table" => array(
		"msg" => "文章表转换",
		"ajax" => true
	),
	"convert_comment_table" => array(
		"msg" => "评论表转换",
		"ajax" => true
	),
	"convert_attachment_table" => array(
		"msg" => "附件表转换",
		"ajax" => true
	),
	"convert_category_table" => array(
		"msg" => "分类表转换",
		"ajax" => true
	),
	"convert_tag_table" => array(
		"msg" => "Tag表转换",
		"ajax" => true
	),
	"convert_user_table" => array(
		"msg" => "用户表转换",
		"ajax" => true
	),
	"upgrade_comment_id" => array(
		"msg" => "评论RootID更新",
		"ajax" => false
	),
	"upgrade_category_count" => array(
		"msg" => "分类文章计数修正",
		"ajax" => false
	),
	"upgrade_tag_rebuild" => array(
		"msg" => "Tag表重建",
		"ajax" => false
	),
	"upgrade_user_rebuild" => array(
		"msg" => "用户文章计数修正及密码重置",
		"ajax" => false
	),
);
$func = GetVars('func', 'GET');
if ($func == 'drop_emlog')
{
	ob_clean();
	exit(drop_emlog());
}
if (GetVars('act', 'GET') == 'progress')
{
	
	if (!isset($function_list[$func]) && $func != 'finish_convert') exit;
	if (GetVars('ajax', 'GET') == 'true')
	{
		ob_clean();
		exit($func(GetVars('prefix', 'GET')));
	}
}
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu"> </div>
  <div id="divMain2">
<?php
if (GetVars('act', 'GET') == 'progress')
{
	$next_tag = false; $next = '';
	if (isset($function_list[$func]))
		foreach($function_list as $name => $value)
		{
			if ($next_tag)
			{
				$next = $name;
				break;
			}
			if ($name == $func) $next_tag = true;
		}
	
	if ($next == '')
	{
		$next = 'finish_convert';
		$zbp->SetHint('good', '数据从emlog转移成功！');
	}
	
	//echo 
	if ($func != 'finish_convert')
	{
		echo '<p>正在执行操作：' . $function_list[$func]['msg'] . '...</p>';
		echo $func(GetVars('prefix', 'GET'));
		echo '<script>location.href="' . "convert.php?step=" . ((int)GetVars('step', 'GET') + 1) . "&act=progress&prefix=" . htmlspecialchars(GetVars('prefix', 'GET')) . "&func=" . $next;
		echo '"</script>';
	}
	else
	{
		finish_convert();
	}
}
else
{
?><script type="text/javascript">
(function(){
	var functions = <?php echo json_encode($function_list);?>,
		function_index = [];
		function_pos = 0;
	
	for(var _name in functions)
	{
		function_index[function_pos++] = _name;
	}
	function_pos = 0;
	
	var queue = function(index)
	{
		var pos = function_index[index],
			url = "convert.php?step=" + index + "&act=progress&prefix=<?php echo htmlspecialchars(GetVars('prefix', 'GET'));?>&func=" + pos;
		$("<div>Step " + (index + 1) + ". " + functions[pos].msg + "...</div>").appendTo($("#divMain2"));
		if (functions[pos].ajax)
			$.get(url + "&ajax=true", function(data){
				function_pos++;
				if (function_pos == functions.length) return;
				return queue(function_pos);
			});
		else
			location.href = url;
	}
	
	
	queue(0);
})();
</script><?php
}
?>
<script type="text/javascript">
(function(){
	$(".href-ajax").click(function(){
		var that = $(this),
			href = that.attr("href"),
			text = that.text();
		
		that.text('请稍候...').removeAttr("href").unbind("click").click(function(){return false});
		$.get(href, function(data){
			that.text(data);
		});
		return false;
	});
})();
</script>

  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
