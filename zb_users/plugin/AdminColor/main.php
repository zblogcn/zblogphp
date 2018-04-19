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

if (GetVars('act') == 'save') {
    $zbp->Config('AdminColor')->LogoPath = (string) GetVars("ac_LogoPath");
    $zbp->Config('AdminColor')->BlodColor = (string) GetVars("ac_BlodColor");
    $zbp->Config('AdminColor')->NormalColor = (string) GetVars("ac_NormalColor");
    $zbp->Config('AdminColor')->LightColor = (string) GetVars("ac_LightColor");
    $zbp->Config('AdminColor')->HighColor = (string) GetVars("ac_HighColor");
    $zbp->Config('AdminColor')->AntiColor = (string) GetVars("ac_AntiColor");

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
              <table width="100%" border="0">
                <tr height="32">
                  <th colspan="2" align="center">设置
                    </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><br/><b>· 预置色彩方案</b><br/>
                      <span class="note">&nbsp;&nbsp; </span></p></td>
                  <td>
<?php  AdminColor_ColorButton(); ?>
                  </td>
                </tr>
                <tr height="32">
                  <td width="30%" align="left"><p><br/><b>· 替换Logo路径</b><br/>
                      <span class="note">&nbsp;&nbsp; Logo 为 200x70的PNG图片。</span></p></td>
                  <td>
<input id="ac_LogoPath" name="ac_LogoPath" type="text" value="<?php echo $zbp->Config('AdminColor')->LogoPath; ?>"  size="100"/><br/>
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
