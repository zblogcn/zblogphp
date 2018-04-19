<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    exit();
}
if (!$zbp->CheckPlugin('changyan')) {
    $zbp->ShowError(48);
    exit();
}
$blogtitle = '畅言评论系统';
require $blogpath.'zb_system/admin/admin_header.php';
?>
<script type="text/javascript">
function bmx2table(){}
var ajaxurl = 'admin-ajax.php';
</script>
<?php
include_once dirname(__FILE__).'/scripts.html';
include_once dirname(__FILE__).'/header.html';
require $blogpath.'zb_system/admin/admin_top.php';

?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo changyan_SubMenus(); ?></div>
  <div id="divMain2">
<?php
if ($changyanPlugin->getOption('changyan_script')) {
    ?>
<div id="divMain3" class="margin" style="width:100%">
    <iframe id="rightBar_1" 
            name="rightBar_1" marginwidth="0" allowtransparency="true"
            src=<?php $script = $changyanPlugin->getOption('changyan_script');
    $appID = explode("'", $script);
    $appID = $appID[1];
    echo "http://changyan.sohu.com/login?type=audit&from=wpplugin&appid=".$appID; ?> frameborder="0"
            scrolling="yes"></iframe>
</div>
<?php
} else {
        Redirect('settings.php');
    }
?>

    <script type="text/javascript">ActiveLeftMenu("aChangYan");</script> 
    <script type="text/javascript">AddHeaderIcon("<?php echo $bloghost.'zb_users/plugin/changyan/logo.png'; ?>");</script> 
  </div>
</div>
<?php
require $blogpath.'zb_system/admin/admin_footer.php';

RunTime();
?>
