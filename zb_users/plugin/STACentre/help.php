<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}

if (!$zbp->CheckPlugin('STACentre')) {
    $zbp->ShowError(68);
    die();
}

$blogtitle = '静态管理中心';

if (count($_POST) > 0) {
    Redirect('./list.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader">
    <?php echo $blogtitle; ?></div>
  <div class="SubMenu">
    <a href="main.php">
      <span class="m-left">配置页面</span>
    </a>
    <a href="list.php">
      <span class="m-left">ReWrite规则</span>
    </a>
    <a href="help.php">
      <span class="m-right m-now">帮助</span>
    </a>
  </div>
  <div id="divMain2">
    <table width="100%" border="1">
      <tr height="32">
        <td>1. 本插件只能实现Z-Blog的伪静态配置，而无法实现完全静态。</td>
      </tr>
      <tr height="32">
        <td>2. 伪静态规则全部会影响到整站，根目录与子目录想要同时使用伪静态必须手动修改Rewrite规则。</td>
      </tr>
      <tr height="32">
        <td>
          3. ISAPI_Rewrite 2.x和3.x是由Helicon出品的伪静态组件，均分Lite和Full两个版本。Lite版本免费，Full版本收费。URL Rewrite为微软官方出品的免费的应用于IIS7+的伪静态组件。
        </td>
      </tr>
      <tr height="32">
        <td>
          4. Windows Server 2003(r2)一般使用IIS6+ISAPI Rewrite 2.x或3.x ， Windows Server 2008(r2)及2012一般使用IIS7、7.5、8+URL Rewrite组件。虚拟主机用户请咨询你的空间商。
          <a href="http://www.dbshost.cn/" target="_blank">DBS主机</a>
          目前使用ISAPI Rewrite 2.x。
        </td>
      </tr>
      <tr height="32">
        <td>
          5. VPS、独立服务器若未安装组件，可点击右边链接下载。
          <a href="http://www.helicontech.com/download-isapi_rewrite.htm" target="_blank">ISAPI_Rewrite 2.x</a>
          &nbsp; &nbsp;
          <a href="http://www.helicontech.com/download-isapi_rewrite3.htm" target="_blank">ISAPI_Rewrite 3.x</a>
          &nbsp; &nbsp;
          <a href="http://www.iis.net/downloads/microsoft/url-rewrite" target="_blank">URL Rewrite（需安装Microsoft Web Platform）</a>
        </td>
      </tr>
      <tr height="32">
        <td>
          6. 其他伪静态组件（如
          <a href="http://iirf.codeplex.com/" target="_blank">Ionics Isapi Rewrite Filter</a>
          ）未经测试，不保证生成的规则可以正常使用。
        </td>
      </tr>
    </table>

    <script type="text/javascript">ActiveLeftMenu("aPluginMng");</script>
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/STACentre/logo.png'; ?>");</script>
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>
