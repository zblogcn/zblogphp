<?php
/**
 * ZBPDK子扩展
 * Debugger配置页.
 *
 * @author 心扬 <chrishyze@gmail.com>
 */
require_once '../../../../../zb_system/function/c_system_base.php';
require_once '../../../../../zb_system/function/c_system_admin.php';
header("Cache-Control: no-cache, must-revalidate");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Pragma: no-cache");
$zbp->Load();
if (!$zbp->CheckRights('root')) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('ZBPDK')) {
    $zbp->ShowError(48);
    die();
}

//检测配置提交
if (strtoupper($_SERVER['REQUEST_METHOD']) == 'POST') {
    if (isset($_POST['show_in_front'])) {
        $zbp->Config('ZBPDK_Debugger')->show_in_front = $_POST['show_in_front'];
    }
    if (isset($_POST['show_in_admin'])) {
        $zbp->Config('ZBPDK_Debugger')->show_in_admin = $_POST['show_in_admin'];
    }
    $zbp->SaveConfig('ZBPDK_Debugger');
}

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
    <div class="divHeader">
        <?php echo $blogtitle; ?>
    </div>
    <div class="SubMenu">
        <?php echo $zbpdk->submenu->export('Debugger'); ?>
    </div>
    <div id="divMain2">
        <form id="edit" name="edit" method="post" action="main.php">
            <table class="tableFull tableBorder tableBorder-thcenter">
                <tr>
                    <th class="td25">项目</th>
                    <th>设置</th>
                    <th>说明</th>
                </tr>
                <tr>
                    <td>
                        <p><b>前台显示调试信息</b></p>
                    </td>
                    <td>
                        <p><input id="show_in_front" name="show_in_front" class="checkbox" type="text" value="<?php echo $zbp->Config('ZBPDK_Debugger')->show_in_front; ?>"></p>
                    </td>
                    <td rowspan="2">
                        <p><span class="note">启用后可以点击页面右下角的“调试信息”按钮打开调试器。</span></p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <p><b>后台显示调试信息</b></p>
                    </td>
                    <td>
                        <p><input id="show_in_admin" name="show_in_admin" class="checkbox" type="text" value="<?php echo $zbp->Config('ZBPDK_Debugger')->show_in_admin; ?>"></p>
                    </td>
                </tr>
            </table>
            <hr>
            <input type="hidden" name="csrfToken" value="<?php echo $zbp->GetCSRFToken(); ?>">
            <p>
                <input type="submit" class="button" value="<?php echo $lang['msg']['submit'] ?>">
                <a href="main.php?act=show_all_interface">查看所有已挂载接口</a>
            </p>
        </form>

        <?php 
        if (isset($_GET['act']) && $_GET['act'] == 'show_all_interface') {
            echo '<table class="debug-table" style="margin-bottom: 50px;"><thead><tr><th>接口</th><th>挂载函数</th><th>对应插件</th><th>操作</th></tr></thead><tbody>';
            foreach ($GLOBALS as $key => $value) {
                if (preg_match("/^Filter/i", $key, $matches)) {
                    foreach ($GLOBALS[$key] as $k => $v) {
                        if (function_exists($k)) {
                            echo '<tr><td>' . $key . '</td><td>' . $k . '</td><td class="center">' . get_plugin_name_debugger($k) . '</td><td class="center"><span class="debug-plg-detail" interface="' . $key . '" func="' . $k . '" title="查看/隐藏详情">ⅰ</span></td></tr><tr style="display:none"><td colspan="4"></td></tr>';
                        }
                    }
                }
            }
            echo '</tbody></table>';
        }
        if ($zbp->Config('ZBPDK_Debugger')->show_in_admin != '1') {
            echo '<style>@import url(' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/Debugger/style.css);</style>
                <script src="' . $zbp->host . 'zb_users/plugin/ZBPDK/extensions/Debugger/script.js" type="text/javascript"></script>';
        }
        ?>
    </div>
</div>

<script>
    ActiveTopMenu("zbpdk");
</script>
<script>
    AddHeaderIcon("<?php echo $bloghost; ?>zb_users/plugin/ZBPDK/logo.png");
</script>

<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
