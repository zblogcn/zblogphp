<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require 'function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心-系统更新与校验';

$checkbegin=false;
$nowxml='';


if(GetVars('update','GET')!=''){
$url=APPCENTRE_SYSTEM_UPDATE . '?' . GetVars('update','GET') . '.xml';
$f=AppCentre_GetHttpContent($url);
  $xml=simplexml_load_string($f);
  if($xml){
	  foreach ($xml->children() as $file){
		$full=$zbp->path . str_replace('\\','/',$file['name']);
		$dir=dirname($full);
		if(!file_exists($dir . '/'))@mkdir($dir,0755,true);
		$f=base64_decode($file);
		@file_put_contents($full,$f);
	  }
	  $zbp->SetHint('good');
  }
  Redirect('./update.php');
}

if(GetVars('restore','GET')!=''){
  $file=base64_decode(GetVars('restore','GET'));
  $url=APPCENTRE_SYSTEM_UPDATE . '?' . substr(ZC_BLOG_VERSION,-6,6) . '\\' . $file;
  $f=AppCentre_GetHttpContent($url);
  $file=$zbp->path . str_replace('\\','/',$file);
  $dir=dirname($file);
  if(!file_exists($dir.'/'))@mkdir($dir,0755,true);
  @file_put_contents($file,$f);
  echo '<img src="'.$zbp->host.'zb_system/image/admin/ok.png" width="16" alt="" />';
  die();
}


if(GetVars('check','GET')=='now'){
  $r = AppCentre_GetHttpContent(APPCENTRE_SYSTEM_UPDATE . array_search(ZC_BLOG_VERSION,$zbpvers) .'.xml');
  //file_put_contents($zbp->usersdir . 'cache/now.xml', $r);
  $nowxml=$r;
  $checkbegin=true;
}


require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';


$newversion=AppCentre_GetHttpContent(APPCENTRE_SYSTEM_UPDATE . ($zbp->Config('AppCentre')->checkbeta==true?'?beta':''));

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(3);?></div>
  <div id="divMain2">

            <form method="post" action="">
              <table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
                <tr>
                  <th width='50%'>当前版本</th>
                  <th>最新版本</th>
                </tr>
                <tr>
                  <td align='center' id='now'>Z-BlogPHP <?php echo ZC_BLOG_VERSION?></td>
                  <td align='center' id='last'>Z-BlogPHP <?php echo $newversion;?></td>
                </tr>
              </table>
              <p>
                
<?php

$nowbuild=(int)$blogversion;
$newbuild=(int)substr($newversion,-6,6);

if($newbuild-$nowbuild>0){
	echo '<input id="updatenow" type="button" onclick="location.href=\'?update='.$nowbuild.'-'.$newbuild.'\'" value="升级新版程序" />';
}
?>
              </p>
			  <hr/>

              <div class="divHeader">校验当前版本的系统核心文件&nbsp;&nbsp;<span id="checknow"><a href="?check=now" title="开始校验"><img src="images/refresh.png" width="16" alt="校验" /></a></span></div>
			  <!--<div>进度<span id="status">0</span>%；已发现<span id="count">0</span>个修改过的系统文件。<div id="bar"></div></div>-->
              <table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
                <tr>
                  <th width='78%'>文件名</th>
                  <th id="_s">状态</th>
                </tr>
<?php
  $i=0;
//if (file_exists($zbp->usersdir . 'cache/now.xml')) {
if ($nowxml!=''){

  $i=0;
  libxml_use_internal_errors(true);
  //$xml=simplexml_load_file($zbp->usersdir . 'cache/now.xml');
  $xml=simplexml_load_string($nowxml);
  if($xml){
  foreach ($xml->children() as $file) {
  	if(file_exists($f=$zbp->path . str_replace('\\','/',$file['name']))){
		$f=file_get_contents($f);
	  	$newcrc32=substr(strtoupper(dechex(AppCentre_crc32_signed($f))),-8);
		$f=str_replace("\n","\r\n",$f);
		$newcrc32_2=substr(strtoupper(dechex(AppCentre_crc32_signed($f))),-8);
  	}else{
  		$newcrc32='';
		$newcrc32_2='';
  	}
	//echo PHP_INT_SIZE;
    if( ($newcrc32 == $file['crc32']) || ($newcrc32_2 == $file['crc32']) ){
      echo '<tr style="display:none;"><td><b>' . str_replace('\\','/',$file['name']) . '</b></td>';
    	$s='<img src="'.$zbp->host.'zb_system/image/admin/ok.png" width="16" alt="" />';
    }else{
      $i+=1;
      echo '<tr><td><b>' . str_replace('\\','/',$file['name']) . '</b></td>';
    	$s='<a href="javascript:void(0)" onclick="restore(\''.base64_encode($file['name']).'\',\'file'.md5($file['name']) .'\')" class="resotrefile button" title="还原系统文件"><img src="'.$zbp->host.'zb_system/image/admin/exclamation.png" width="16" alt=""></a>';
    }
    echo '<td class="tdCenter" id="file' . md5($file['name']) . '">' . $s . '</td></tr>';
  }
  }
  echo '<tr><th colspan="2">'.$i.'个文件不同或被修改过.</tr>';
  //@unlink($zbp->usersdir . 'cache/now.xml');
}
?>

              </table>
<?php if($i>0){?>
              <p>
                <input name="submit" type="button" id="autorestor" onclick="restoreauto();$(this).hide();" value="自动依次更新文件" class="button" />
              </p>
<?php }?>
              <p> </p>
            </form>
<script type="text/javascript">

$("#autorestor").bind('click', function(){restoresingle();$(this).hide()});

function restoresingle(){
  if($("a.resotrefile").get(0)){
    $("a.resotrefile").get(0).click();
    setTimeout("restoresingle()",1000);
  }
}


function restore(f,id){
	$.get(bloghost+"zb_users/plugin/AppCentre/update.php?restore="+f, function(data){
		//alert(data);
		$('#'+id).html(data);
	});
}
</script>            
	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>