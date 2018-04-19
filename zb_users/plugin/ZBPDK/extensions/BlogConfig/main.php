<?php
require '../../../../../zb_system/function/c_system_base.php';
require '../../../../../zb_system/function/c_system_admin.php';
require '../../zbpdk_include.php';
header("Cache-Control: no-cache, must-revalidate");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");

$zbp->Load();
$zbpdk = new zbpdk_t();
$zbpdk->scan_extensions();
//var_dump($zbpdk->objects);

$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('ZBPDK')) {
    $zbp->ShowError(48);
    die();
}

if (isset($_GET['act'])) {
    if (function_exists('CheckHTTPRefererValid') && !CheckHTTPRefererValid()) {
        return;
    }
    switch ($_GET['act']) {
        case 'open':
            echo blogconfig_exportlist($_GET['name']);
            exit();
            break;
        case 'readleft':
            echo blogconfig_left();
            exit();
            break;
        case 'rename':
            $sql = $zbp->db->sql->Update($zbp->table['Config'], array('conf_Name' => $_GET['edit']), array(array('=', 'conf_Name', $_GET['name'])));
            $zbp->db->Update($sql);
            echo '操作成功';
            exit();
            break;
        case 'del':
            $zbp->DelConfig($_GET['name']);
            echo '操作成功';
            exit();
            break;
        case 'new':
            $zbp->SaveConfig($_GET['name']);
            echo blogconfig_exportlist($_GET['name']);
            exit();
            break;
        default:
    }
}

if (isset($_POST['act'])) {
    if (function_exists('CheckHTTPRefererValid') && !CheckHTTPRefererValid()) {
        return;
    }
    switch ($_POST['act']) {
        case 'e_del':
            $zbp->configs[$_POST['name2']]->Del($_POST['name1']);
            $zbp->SaveConfig($_POST['name2']);
            echo blogconfig_exportlist($_POST['name2']);
            exit();
            break;
        case 'e_edit':
            $name1 = $_POST['name1'];
            $config = $zbp->configs[$_POST['name2']]->$name1;
            $value = $_POST['post'];
            if (gettype($config) == 'boolean') {
                $value = (bool) $value;
            } elseif (gettype($config) == 'integer') {
                $value = (int) $value;
            }
            $name1 = $_POST['name1'];
            $zbp->configs[$_POST['name2']]->$name1 = $value;
            $zbp->SaveConfig($_POST['name2']);
            echo blogconfig_exportlist($_POST['name2']);
            exit();
        default:
    }
}

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<link rel="stylesheet" href="BlogConfig.css" type="text/css" media="screen"/>
<link rel="stylesheet" href="../../css/jquery.contextMenu.css" type="text/css" media="screen"/>
<script type="text/javascript" src="../../js/jquery.contextMenu.js"></script>
<script type="text/javascript" src="BlogConfig.js"></script>
<script src="../../../../../zb_system/script/c_admin_js_add.php" type="text/javascript"></script>
<script src="../../../../../zb_system/script/jquery-ui.custom.min.js" type="text/javascript"></script>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('BlogConfig'); ?></div>
  <div id="divMain2">
    <div class="DIVBlogConfig">
      <div class="DIVBlogConfignav" name="tree" id="tree">
        <ul>
            <?php echo blogconfig_left(); ?>
        </ul>
        <script type="text/javascript">
        $(document).ready(function() {
            $.contextMenu({
                selector: '#tree ul li', 
                items: {
                    "open": {name: "打开"},
                    "rename": {name: "重命名"},
                    "del": {name: "删除"}
                }, 
                callback: function (key, options) {
//					console.log(this);
                    run(key, $(this).find("a").attr("id"));
                }
            });
        });
      </script></div>
      <div id="content" class="DIVBlogConfigcontent">
        <div class="DIVBlogConfigcontentbody">请选择</div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<script>ActiveTopMenu('zbpdk');</script>
<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png'; ?>");</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
