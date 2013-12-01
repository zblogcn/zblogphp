<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('x2013')) {$zbp->ShowError(48);die();}
$blogtitle='主题配置';

if($zbp->Config('x2013')->FirstInstall == true){
	$zbp->Config('x2013')->FirstInstall = '0';
	$zbp->SaveConfig('x2013');
	Redirect('about.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

if(isset($_POST['DisplayFeed']) && $_POST['DisplayFeed'] != ''){
	foreach($_POST as $k => $v){$$k = $v;}
	if($SetWeiboSina != ''){
		if ($SetWeiboSina == ($zbp->Config('x2013')->SetWeiboSina)){
			$tips = '新浪微博地址未更改；';
		}else{
			$zbp->Config('x2013')->SetWeiboSina = $SetWeiboSina;
			$tips = '新浪微博地址设置成功；';
		}
	}else{
		$zbp->Config('x2013')->SetWeiboSina = '';
		$zbp->ShowHint('bad', '新浪微博地址为空，前台将不显示此图标.');
		$tips = '';
	}
	if($SetWeiboQQ != ''){
		if ($SetWeiboQQ == ($zbp->Config('x2013')->SetWeiboQQ)){
			$tips .= '腾讯微博地址未更改；';
		}else{
			$zbp->Config('x2013')->SetWeiboQQ = $SetWeiboQQ;
			$tips .= '腾讯微博地址设置成功；';
		}
	}else{
		$zbp->Config('x2013')->SetWeiboQQ = '';
		$zbp->ShowHint('bad', '腾讯微博地址为空，前台将不显示此图标.');
	}
	$zbp->Config('x2013')->DisplayFeed = $DisplayFeed;
	$zbp->Config('x2013')->SetMailKey = $SetMailKey;
	$zbp->SaveConfig('x2013');
	if(isset($tips)){$zbp->ShowHint('good', $tips);}
	//var_dump($_POST);
}
?>
<style>
input.text{background:#FFF;border:1px double #aaa;font-size:1em;padding:0.25em;}
p{line-height:1.5em;padding:0.5em 0;}
</style>
<div id="divMain">
	<div class="divHeader"><?php echo $blogtitle;?></div>
  	<div class="SubMenu"><?php x2013_SubMenu(0);?></div>
	<div id="divMain2">
	<form id="form1" name="form1" method="post">
    <table width="100%" style='padding:0px;margin:0px;' cellspacing='0' cellpadding='0' class="tableBorder">
  <tr>
    <th width='20%'><p align="center">设置</p></th>
    <th width='70%'><p align="center">内容</p></th>
    
  </tr>
  <tr>
    <td><b><label for="SetWeiboSina"><p align="center">新浪微博</p></label></b></td>
    <td><p align="left"><input name="SetWeiboSina" type="text" id="SetWeiboSina" size="100%" value="<?php echo $zbp->Config('x2013')->SetWeiboSina;?>" /></p></td>
    
  </tr>
  <tr>
    <td><b><label for="SetWeiboQQ"><p align="center">腾讯微博</p></label></b></td>
    <td><p align="left"><input name="SetWeiboQQ" type="text" id="SetWeiboQQ" size="100%" value="<?php echo $zbp->Config('x2013')->SetWeiboQQ;?>" /></p></td>
  </tr>
  <tr>
    <td><b><label for="DisplayFeed"><p align="center">是否显示邮件订阅</p></label></b></td>
    <td><p align="left"><input id="DisplayFeed" name="DisplayFeed" style="display:none;" type="text" value="<?php echo $zbp->Config('x2013')->DisplayFeed;?>" class="checkbox"></p></td>
  </tr>
  <tr>
    <td><b><label for="SetMailKey"><p align="center"><a href="http://list.qq.com/" target="_black">QQMail邮件订阅key</a></p></label></b></td>
    <td><p align="left"><input name="SetMailKey" type="text" id="SetMailKey" size="100%" value="<?php echo $zbp->Config('x2013')->SetMailKey;?>" /></p></td>
  </tr>  
</table>
 <br />
   <input name="" type="Submit" class="button" value="保存"/>
    </form>
	</div>
</div></div>

<script type="text/javascript">ActiveTopMenu("topmenu_x2013");</script> 
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>