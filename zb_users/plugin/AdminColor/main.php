<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('AdminColor')) {
    $zbp->ShowError(48);
    die();
}

$blogtitle = '后台配色器-设置';

if (isset($_GET['setcolor'])) {
    $zbp->Load();
    $action = 'root';
    if ($zbp->CheckRights($action)) {
        $i = (int) $_GET['setcolor'];
        $zbp->Config('AdminColor')->ColorID = $i;
        $zbp->Config('AdminColor')->BlodColor = (string) $GLOBALS['AdminColor_BlodColor'][$i];
        $zbp->Config('AdminColor')->NormalColor = (string) $GLOBALS['AdminColor_NormalColor'][$i];
        $zbp->Config('AdminColor')->LightColor = (string) $GLOBALS['AdminColor_LightColor'][$i];
        $zbp->Config('AdminColor')->HighColor = (string) $GLOBALS['AdminColor_HighColor'][$i];
        $zbp->Config('AdminColor')->AntiColor = (string) $GLOBALS['AdminColor_AntiColor'][$i];
        $zbp->SaveConfig('AdminColor');
        Redirect($zbp->host . 'zb_users/plugin/AdminColor/main.php');
        die();
    }
}

if (GetVars('act') == 'save') {
    CheckIsRefererValid();
    $zbp->Config('AdminColor')->LogoPath = (string) GetVars("ac_LogoPath");
    $zbp->Config('AdminColor')->BlodColor = (string) GetVars("ac_BlodColor");
    $zbp->Config('AdminColor')->NormalColor = (string) GetVars("ac_NormalColor");
    $zbp->Config('AdminColor')->LightColor = (string) GetVars("ac_LightColor");
    $zbp->Config('AdminColor')->HighColor = (string) GetVars("ac_HighColor");
    $zbp->Config('AdminColor')->AntiColor = (string) GetVars("ac_AntiColor");
    $zbp->Config('AdminColor')->HeaderPath = (string) GetVars("ac_HeaderPath");
    $zbp->Config('AdminColor')->SlidingButton = (bool) GetVars("ac_SlidingButton");
    $zbp->Config('AdminColor')->HeaderPathUse = (bool) GetVars("ac_HeaderPathUse");
    $zbp->Config('AdminColor')->TableShadow = (bool) GetVars("ac_TableShadow");
    //if ( $zbp->Config('AdminColor')->ColorID == 10 )
    //    $zbp->Config('AdminColor')->HeaderPathUse = true;
    $zbp->SaveConfig('AdminColor');

    $zbp->SetHint('good');
    Redirect('./main.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
echo '<link href="source/evol.colorpicker.css" rel="stylesheet" />
<script src="source/evol.colorpicker.min.js" type="text/javascript"></script>
<script src="source/custom.js" type="text/javascript"></script>
';

require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div id="divMain2">

            <form action="?act=save" method="post">
              <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCsrfToken(); ?>">
              <table width="100%" border="0">
                <tr height="32">
                  <th colspan="2" align="center">设置
                    </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 预置色彩方案</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<?php  AdminColor_ColorButton(); ?>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><br/><b>· 替换Logo路径</b><br/>
                      <span class="note">&nbsp;&nbsp; Logo 为 200x70的PNG图片。</span></p></td>
                  <td>
<input id="ac_LogoPath" name="ac_LogoPath" type="text" value="<?php echo $zbp->Config('AdminColor')->LogoPath; ?>"  size="50"/><br/>
                  </td>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 后台header背景路径</b></p></td>
                  <td>
<input id="ac_HeaderPath" name="ac_HeaderPath" type="text" value="<?php echo $zbp->Config('AdminColor')->HeaderPath; ?>"  size="50"/>
<input id="ac_HeaderPath" name="ac_HeaderPathUse" class="checkbox" type="text" value="<?php echo $zbp->Config('AdminColor')->HeaderPathUse; ?>" /><br/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 标准色</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<input id="ac_NormalColor" name="ac_NormalColor" type="text" value="<?php echo $zbp->Config('AdminColor')->NormalColor; ?>"size="20"/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 深色</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<input id="ac_BlodColor" name="ac_BlodColor" type="text" value="<?php echo $zbp->Config('AdminColor')->BlodColor; ?>"size="20"/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 浅色</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<input id="ac_LightColor" name="ac_LightColor" type="text" value="<?php echo $zbp->Config('AdminColor')->LightColor; ?>"size="20"/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 高光色</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<input id="ac_HighColor" name="ac_HighColor" type="text" value="<?php echo $zbp->Config('AdminColor')->HighColor; ?>"size="20"/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 反色</b>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<input id="ac_AntiColor" name="ac_AntiColor" type="text" value="<?php echo $zbp->Config('AdminColor')->AntiColor; ?>"size="20"/>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 开启表格阴影</b></p></td>
                  <td>
<input id="ac_TableShadow" name="ac_TableShadow" class="checkbox" type="text" value="<?php echo $zbp->Config('AdminColor')->TableShadow; ?>"  size="100"/><br/>
                  </td>
                <tr height="32">
                  <td width="30%" align="left"><p><b>· 显示收缩菜单</b></p></td>
                  <td>
<input id="ac_HeaderPath" name="ac_SlidingButton" class="checkbox" type="text" value="<?php echo $zbp->Config('AdminColor')->SlidingButton; ?>"  size="100"/><br/>
                  </td>
                </tr> 
              </table>
              <hr/>
              <p>
                <input type="submit" value="提交" class="button" />
              </p>
              <hr/>
            </form>

    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AdminColor/logo.png'; ?>");</script>
  </div>
</div>


<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
