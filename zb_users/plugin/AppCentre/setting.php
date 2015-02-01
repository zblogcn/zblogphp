<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require dirname(__FILE__) . '/function.php';
$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心-设置';

if(GetVars('act')=='save'){

	$zbp->Config('AppCentre')->enabledcheck=(int)GetVars("app_enabledcheck");
	$zbp->Config('AppCentre')->checkbeta=(int)GetVars("app_checkbeta");
	$zbp->Config('AppCentre')->enabledevelop=(int)GetVars("app_enabledevelop");
	$zbp->Config('AppCentre')->enablegzipapp=(int)GetVars("app_enablegzipapp");
	$zbp->SaveConfig('AppCentre');

	$zbp->SetHint('good');
	Redirect('./setting.php');

}

if(GetVars('act')=='login'){

	$s=Server_Open('vaild');
	if($s){

		$zbp->Config('AppCentre')->username=GetVars("app_username");
		$zbp->Config('AppCentre')->password=$s;
		$zbp->SaveConfig('AppCentre');

		$zbp->SetHint('good','您已成功登录APP应用中心.');
		Redirect('./main.php');
		die;
	}else{
		$zbp->SetHint('bad','用户名或密码错误.');
		Redirect('./setting.php');
		die;
	}
}

if(GetVars('act')=='logout'){
	$zbp->Config('AppCentre')->username='';
	$zbp->Config('AppCentre')->password='';
	$zbp->SaveConfig('AppCentre');
	$zbp->SetHint('good','您已退出APP应用中心.');
	Redirect('./setting.php');
	die;
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(4);?></div>
  <div id="divMain2">

            <form action="?act=save" method="post">
              <table width="100%" border="0">
                <tr height="32">
                  <th colspan="2" align="center">设置
                    </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 启用自动检查更新</b><br/>
                      <span class="note">&nbsp;&nbsp;在进入后台时会检查应用更新和系统更新 </span></p></td>
                  <td><input id="app_enabledcheck" name="app_enabledcheck" type="text" value="<?php echo $zbp->Config('AppCentre')->enabledcheck; ?>" class="checkbox"/></td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 检查Beta版程序</b><br/>
                      <span class="note">&nbsp;&nbsp;若打开，则系统将检查最新测试版的Z-Blog更新</span></p></td>
                  <td><input id="app_checkbeta" name="app_checkbeta" type="text" value="<?php echo $zbp->Config('AppCentre')->checkbeta; ?>" class="checkbox"/></td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 启用开发者模式</b><br/>
                      <span class="note">&nbsp;&nbsp;启用开发者模式可以修改应用信息、导出应用和远程提交应用</span></p></td>
                  <td><input id="app_enabledevelop" name="app_enabledevelop" type="text" value="<?php echo $zbp->Config('AppCentre')->enabledevelop; ?>" class="checkbox"/></td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 导出经过GZip压缩的应用包</b><br/>
                      <span class="note">&nbsp;&nbsp;1.4以下版本不支持应用压缩包导入及导出</span></p></td>
                  <td><input id="app_enablegzipapp" name="app_enablegzipapp" type="text" value="<?php echo $zbp->Config('AppCentre')->enablegzipapp; ?>" class="checkbox"/></td>
                </tr>
              </table>
              <hr/>
              <p>
                <input type="submit" value="提交" class="button" />
              </p>
              <hr/>
            </form>

            <div class="divHeader2">开发者登录</div>
<?php if(!$zbp->Config('AppCentre')->username){ ?>
            <form action="?act=login" method="post">
              <table style="line-height:3em;" width="100%" border="0">
                <tr height="32">
                  <th  align="center">如果您是开发者，请在这里输入Z-Blog应用中心的开发者账号和密码，以用于身份验证。
                    </td>
                </tr>
                <tr height="32">
                  <td  align="center">用户名:
                    <input type="text" name="app_username" value="" style="width:40%"/></td>
                </tr>
                <tr height="32">
                  <td  align="center">密&nbsp;&nbsp;&nbsp;&nbsp;码:
                    <input type="password" name="app_password" value="" style="width:40%" /></td>
                </tr>
                <tr height="32" align="center">
                  <td align="center"><input type="submit" value="登陆" class="button" /></td>
                </tr>
              </table>
            </form>
<?php }else{ ?>
            <form action="?act=logout" method="post">
              <p>开发者 <b><?php echo $zbp->Config('AppCentre')->username; ?></b> 您好,您已经在当前客户端登录Z-BlogPHP官方网站-APP应用中心.</p>
              <p>
                <input name="submit" type="submit" value="退出登录" class="button" />
              </p>
            </form>
<?php }?>
	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>