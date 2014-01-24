<?php
$GLOBALS['zbpdk']->add_extension(array(
	'url' => 'main.php',
	'description' => '可以对blog_Config里的数据进行管理，用于调试TConfig类。',
	'id' => 'BlogConfig'
));

$GLOBALS['zbpdk']->submenu->add(array(
	'url' => 'BlogConfig/main.php',
	'float' => 'left',
	'id' => 'BlogConfig',
	'title' => 'BlogConfig'
));

function blogconfig_left()
{
	global $zbp;
	$html = '';
	foreach ($zbp->configs as $k => $v)
	{
		$html .= "<li><a id=\"$k\" href=\"javascript:;\" onclick=\"clk(this);run('open','$k');\">$k</a></li>";
	}
	return $html;
}

function blogconfig_exportlist($id)
{
	global $zbp;
	$html = '';
	$i = 0;
	$html .= '<div class="DIVBlogConfigtop"><span id="name">';
	$html .= $id . '</span><a href="javascript:;" onclick="run2(\'new\',\'' . $id . '\')">新建</a></div>';
	$html .= '<table width="100%" style="padding:0px;" cellspacing="0" cellpadding="0" id="configt">';
	$html .= '<tr height="32"><th width="25%">项</th><th>内容</th><th width="10%"></th></tr>';
	$data = $zbp->configs[$id]->Data;
	foreach ($data as $name => $value)
	{
		$name = TransferHTML($name,'[html-format]');
		$value = TransferHTML($value,'[html-format]');
		//echo $value;
		//echo "\n";
		$html .= '<tr height="32">';
		$html .= '<td><input type="hidden" value="' . $i . '"/><span id="txt' . $i . '">';
		$html .= $name;
		$html .= '</span></td><td onclick="$(\'#ta' . $i . '\').show();$(\'#show' . $i . '\').hide()">';
		$html .= '<span id="show' . $i . '">' . $value .'</span>';
		$html .= '<textarea id="ta' . $i . '" style="display:none;width:100%">' . $value . '</textarea></td>';
		$html .= '<td><a href="javascript:;" onclick="run2(\'edit\',\'' . $i . '\',\''. $id . '\')">';
		$html .= '<img src="../../../../../zb_system/image/admin/page_edit.png" alt="编辑" title="编辑" width="16" /></a>';
		$html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$html .= '<a onclick="if(window.confirm(\'单击“确定”继续。单击“取消”停止。\')){run2(\'del\',\''. $i . '\',\'' . $id . '\')};"';
		$html .= 'href="javascript:;" onclick="run2(\'del\',\'' . $i . '\',\'' . $id . '\')">';
		$html .= '<img src="../../../../../zb_system/image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td></tr>';
		$i++;
	}
	return $html;
}