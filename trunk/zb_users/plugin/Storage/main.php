<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('Storage')) {$zbp->ShowError(48);die();}

$blogtitle='Storage';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
if (isset($_POST['Storage_Domain']) && $_POST['Storage_Domain'] != '') {
	$zbp->Config('Storage')->Storage_Domain = $_POST['Storage_Domain'];
	$zbp->SaveConfig('Storage');
}
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
 <form method="post">
            <div id="divMain2">
              <div class="content-box"><!-- Start Content Box -->
                <div class="content-box-header">
                  <ul class="content-box-tabs">
                    <li><a href="#tab1" class="default-tab current"><span>空间</span></a></li>
                  </ul>
                  <div class="clear"></div>
                </div>
                <div class="content-box-content">

<div class="tab-content default-tab" style="border:none;padding:0px;margin:0;" id="tab1">
	<table style="padding:0px;margin:0px;width:100%;">
		<tr><td><p><b>Domain Name</b></p></td><td><p><input id="Storage_Domain" name="Storage_Domain" style="width:600px;" type="text" value="<?php echo $zbp->Config('Storage')->Storage_Domain;?>" /></p></td></tr>

	</table>
</div>
</div>
              </div>
              <hr/>
			  <p><input type="submit" class="button" value="提交" id="btnPost" onclick="" /></p>
            </div>
          </form>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>