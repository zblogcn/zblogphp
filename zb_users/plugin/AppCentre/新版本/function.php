<?php

function AppCentre_SubMenus($id){
	//m-now
	global $zbp;

	
	echo '<a href="main.php"><span class="m-left '.($id==1?'m-now':'').'">浏览在线应用</span></a>';
	echo '<a href="main.php?method=check"><span class="m-left '.($id==2?'m-now':'').'">检查应用更新</span></a>';
	echo '<a href="update.php"><span class="m-left '.($id==3?'m-now':'').'">系统更新与校验</span></a>';


	if($zbp->Config('AppCentre')->username&&$zbp->Config('AppCentre')->password){
		echo '<a href="client.php"><span class="m-left '.($id==9?'m-now':'').'">我的应用仓库</span></a>';
	}else{
		echo '<a href="client.php"><span class="m-left '.($id==9?'m-now':'').'">登录应用商城</span></a>';
	}
	
	echo '<a href="setting.php"><span class="m-right '.($id==4?'m-now':'').'">设置</span></a>';
	echo '<a href="plugin_edit.php"><span class="m-right '.($id==5?'m-now':'').'">新建插件</span></a>';
	echo '<a href="theme_edit.php"><span class="m-right '.($id==6?'m-now':'').'">新建主题</span></a>';
}

function AppCentre_GetCheckQueryString(){
	global $zbp;
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
	return $check;
}

function Server_Open($method){
	global $zbp,$blogversion;

	switch ($method) {
		case 'down':
			Add_Filter_Plugin('Filter_Plugin_Zbp_ShowError','ScriptError',PLUGIN_EXITSIGNAL_RETURN);
			header('Content-type: application/x-javascript; Charset=utf-8');
			ob_clean();
			$s=Server_SendRequest(APPCENTRE_URL .'?down=' . GetVars('id','GET'));
			if(App::UnPack($s)){
				$zbp->SetHint('good','下载APP并解压安装成功!');
			};
			die();
			break;		
		case 'search':
			if(trim(GetVars('q','GET'))=='')continue;
			$s=Server_SendRequest(APPCENTRE_URL .'?search=' . urlencode(GetVars('q','GET')));
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'view':
			$s=Server_SendRequest(APPCENTRE_URL .'?'. GetVars('QUERY_STRING','SERVER'));
			if(strpos($s,'<!--developer-nologin-->')!==false){
				if($zbp->Config('AppCentre')->username){
					$zbp->Config('AppCentre')->username='';
					$zbp->Config('AppCentre')->password='';
					$zbp->SaveConfig('AppCentre');
				}
			}
			if(strpos($s,'app.zblogcn.com')===false)$zbp->ShowHint('bad','后台访问应用中心故障，不能登录和下载应用，请检查主机空间是否能远程访问app.zblogcn.com。');
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'check':
			$s=Server_SendRequest(APPCENTRE_URL .'?check=' . urlencode(AppCentre_GetCheckQueryString())) . '';
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'checksilent':
			header('Content-type: application/x-javascript; Charset=utf-8');
			ob_clean();
			$s=Server_SendRequest(APPCENTRE_URL .'?blogsilent=1'. ($zbp->Config('AppCentre')->checkbeta?'&betablog=1':'') .'&check=' . urlencode(AppCentre_GetCheckQueryString())) . '';
			if(strpos($s,';')!==false){
				$newversion=substr($s,0,6);
				$s=str_replace(($newversion.';'),'',$s);
				if((int)$newversion>(int)$blogversion){
					echo '$(".main").prepend("<div class=\'hint\'><p class=\'hint hint_tips\'>提示:Z-BlogPHP有新版本,请用APP应用中心插件的<a href=\'../../zb_users/plugin/AppCentre/update.php\'>“系统更新与校验”</a>升级'.$newversion.'版('. ($zbp->Config('AppCentre')->checkbeta?'Beta':'') .').</p></div>");';
				}
			}
			if($s!=0){
				echo '$(".main").prepend("<div class=\'hint\'><p class=\'hint hint_tips\'>提示:有'.$s.'个应用需要更新,请在应用中心的<a href=\'../../zb_users/plugin/AppCentre/main.php?method=check\'>“检查应用更新”</a>页升级.</p></div>");';
			}
			die();
			break;
		case 'vaild':
			$data=array();
			$data["username"]=GetVars("app_username");
			$data["password"]=md5(GetVars("app_password"));
			$s=Server_SendRequest(APPCENTRE_URL .'?vaild',$data);
			return $s;
			break;
		case 'logout':
			$zbp->Config('AppCentre')->username='';
			$zbp->Config('AppCentre')->password='';
			$zbp->SaveConfig('AppCentre');
			Redirect('main.php');
			break;
		case 'submitpre':
			$s=Server_SendRequest(APPCENTRE_URL .'?submitpre=' . urlencode(GetVars('id')));
			return $s;
		case 'submit':
			$app=New App;
			$app->LoadInfoByXml($_GET['type'],$_GET['id']);
			$data["zba"]=$app->Pack();
			$s=Server_SendRequest(APPCENTRE_URL .'?submit=' . urlencode(GetVars('id')),$data);
			return $s;
		case 'shoplist':
			$s=Server_SendRequest(APPCENTRE_URL .'?shoplist');
			echo str_replace('%bloghost%', $zbp->host . 'zb_users/plugin/AppCentre/main.php' ,$s);
			break;
		case 'apptype':
			$zbp->Config('AppCentre')->apptype=GetVars("type");
			$zbp->SaveConfig('AppCentre');
			Redirect('main.php');
			break;
		default:
			# code...
			break;
	}

}

function Server_SendRequest($url,$data=array(),$u='',$c=''){
	global $zbp;

	$un=$zbp->Config('AppCentre')->username;
	$ps=$zbp->Config('AppCentre')->password;
	$c.=' apptype=' . urlencode($zbp->Config('AppCentre')->apptype) . '; ';
	if($un&&$ps){
		$c.="username=".urlencode($un) ."; password=".urlencode($ps);
	}
	
	$u='ZBlogPHP/' . substr(ZC_BLOG_VERSION,-6,6) . ' '. GetGuestAgent();
	
	if(!class_exists('NetworkFactory',false))
		if(class_exists('Network'))
			return Server_SendRequest_Network($url,$data,$u,$c);
	if(function_exists("curl_init") && function_exists('curl_exec'))return Server_SendRequest_CUrl($url,$data,$u,$c);
	if(!ini_get("allow_url_fopen"))return "";
	
	if($data){//POST
		$data=http_build_query($data);
		$opts=array(
			'http'=>array(
				'method'=>'POST',
				'header'=>"Content-Type:application/x-www-form-urlencoded\r\n".
					'Content-Length: '.strlen($data)."\r\n".
					"Cookie: ".$c."\r\n",
				'user_agent'=> $u,
				'content'=>$data
			)
		);
		$content=stream_context_create($opts);
	}else{//GET
		$opts=array(
			'http'=>array(
				'method'=>'GET',
				'header'=>"Cookie: ".$c."\r\n",
				'user_agent'=> $u,
			)
		);
		$content=stream_context_create($opts);
	}

	if(function_exists('ini_set'))ini_set('default_socket_timeout',120);
	
	if(extension_loaded('zlib')){
		return file_get_contents('compress.zlib://'.$url,false,$content);
	}else{
		return file_get_contents($url,false,$content);
	}
}

function Server_SendRequest_CUrl($url,$data=array(),$u,$c){
	global $zbp;

	$ch = curl_init($url);
	if(extension_loaded('zlib')){
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_TIMEOUT, 120);
	curl_setopt($ch, CURLOPT_USERAGENT, $u);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
	}
	if($c)curl_setopt($ch,CURLOPT_COOKIE,$c);
	
	if($data){//POST
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	}else{//GET
	}
	
	$r = curl_exec($ch);
	curl_close($ch);
	
	return $r;
}

function Server_SendRequest_Network($url,$data=array(),$u,$c){
	global $zbp;

	$ajax = Network::Create();
	if(!$ajax) throw new Exception('主机没有开启访问外部网络功能');

	if($data){//POST
		$ajax->open('POST',$url);
		$ajax->enableGzip();
		$ajax->setTimeOuts(120,120,0,0);
		$ajax->setRequestHeader('User-Agent',$u);
		$ajax->setRequestHeader('Cookie',$c);
		$ajax->send($data);
	}else{
		$ajax->open('GET',$url);
		$ajax->enableGzip();
		$ajax->setTimeOuts(120,120,0,0);
		$ajax->setRequestHeader('User-Agent',$u);
		$ajax->setRequestHeader('Cookie',$c);
		$ajax->send();
	}
	
	return $ajax->responseText;
}

function AppCentre_CreateOptoinsOfVersion($default){
	global $zbp;

	$s=null;
	$array=$GLOBALS['zbpvers'];
	krsort($array);
	$i=0;
	foreach ($array as $key => $value) {
		$i += 1;
		if(($i == 1) or strpos($value,'Beta')===False)
			$s .= '<option value="' . $key . '" ' . ($default==$key?'selected="selected"':'') . ' >' . $value . '</option>';
	}
	return $s;
}

function AppCentre_GetHttpContent($url){

	if(function_exists("GetHttpContent"))return GetHttpContent($url);

	$r = null;
	if (function_exists("curl_init")) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		if(ini_get("safe_mode")==false && ini_get("open_basedir")==false){
			curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		}
		$r = curl_exec($ch);
		curl_close($ch);
	} elseif (ini_get("allow_url_fopen")) {
		$r = file_get_contents($url);
	}

	return $r;
}

function AppCentre_crc32_signed($num){ 
    $crc = crc32($num); 
    if($crc & 0x80000000){ 
        $crc ^= 0xffffffff; 
        $crc += 1; 
        $crc = -$crc; 
    } 
    return $crc; 
} 