<?php
/**
 * AppBuy MenuManage 子菜单
 * @id 选择导航id
 * return 导航菜单string
 */
function AppBuy_SubMenu($id){
	$arySubMenu = array(
		0 => array('订单管理', 'main.php', 'left', false),
		1 => array('设置', 'setting.php', 'right', true),
	);
	foreach($arySubMenu as $k => $v){
		echo '<a href="'.$v[1].'" '.($v[3]==true?'target="_blank"':'').'><span class="m-'.$v[2].' '.($id==$k?'m-now':'').'">'.$v[0].'</span></a>';
	}
}

function Show_Tips($tips, $url){
	$html = <<<html
	<!doctype html>
	<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>提示信息</title>
	<link href="zb_users/plugin/AppBuy/shop/static/css/style.css" rel="stylesheet" />
	</head>
	<body>
		<div class="wrap">
			<div class="show_tips_wrap">
				<div class="show_tips">
					<h2>{$tips}</h2>
					<meta http-equiv="refresh" content="3;url={$url}">
					<a href="{$url}">3秒后自动返回</a>
				</div>
			</div>
		</div>
	</body>
	</html>
html;
	echo $html;
	die();
}
?>