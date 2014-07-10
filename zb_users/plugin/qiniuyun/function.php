<?php
function qiniuyun_SubMenu($id)
{
	$arySubMenu = array(
		0 => array('七牛账户设置', 'main.php', 'left', false),
		1 => array('水印设置', 'water.php', 'left', false)
	);

	
	foreach($arySubMenu as $k => $v)
	{
		echo '<a href="'. $v[1] . '" ' . ($v[3] ? 'target="_blank"' : '');
		echo '><span class="m-' . $v[2] . ' ' . ($id == $k ? 'm-now' : '' );
		echo '">' . $v[0] . '</span></a>';
	}
}


function qiniu_display_text($param)
{
	echo TransferHTML($GLOBALS['qiniu']->cfg->$param, '[textarea]');
}

function qiniu_test_image($url)
{
	return preg_match("/\.jpe?g|gif|png|svg|bmp|tiff$/i", $url);
}