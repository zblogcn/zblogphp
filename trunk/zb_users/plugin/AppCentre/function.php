<?php

function AppCentre_SubMenus($id){
//m-now

echo '<a href="main.php"><span class="m-left '.($id==1?'m-now':'').'">浏览在线应用</span></a>';
echo '<a href="app_check.php"><span class="m-left '.($id==2?'m-now':'').'">检查应用更新</span></a>';
echo '<a href="update.php"><span class="m-left '.($id==3?'m-now':'').'">系统更新与校验</span></a>';


echo '<a href="setting.php"><span class="m-right '.($id==4?'m-now':'').'">设置</span></a>';
echo '<a href="plugin_edit.php"><span class="m-right '.($id==5?'m-now':'').'">新建插件</span></a>';
echo '<a href="theme_edit.php"><span class="m-right '.($id==6?'m-now':'').'">新建主题</span></a>';
}


function Server_Open($method){
	global $zbp;

	switch ($method) {
		case 'view':
			$s=Server_SendRequest(APPCENTRE_URL .'?'. $_SERVER['QUERY_STRING']);
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'check':
			$check= '';
			$app=new app;			
			if($app->LoadInfoByXml('theme', $zbp->theme)==true){
				$check.=$app->id . ':' .$app->modified . ';';
			}
			foreach (explode('|',$zbp->option['ZC_USING_PLUGIN_LIST']) as  $id) {
				$app=new app;
				if($app->LoadInfoByXml('plugin', $id)==true){
					$check.=$app->id . ':' .$app->modified . ';';
				}
			}

			$s=Server_SendRequest(APPCENTRE_URL .'?check=' . urlencode($check));
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		default:
			# code...
			break;
	}

}



function Server_SendRequest($url){


$data=array();
$data=http_build_query($data);
$opts=array(
	'http'=>array(
		'method'=>'POST',
		'header'=>"Content-Type:application/x-www-form-urlencoded\r\n".
			'Content-Length: '.strlen($data)."\r\n".
			"Cookie: \r\n".
			"User-Agent: ZBlogPHP/" . substr(ZC_BLOG_VERSION,-6,6),
		'content'=>$data
	)
);
$content=stream_context_create($opts);

return @file_get_contents($url,false,$content);

}



?>