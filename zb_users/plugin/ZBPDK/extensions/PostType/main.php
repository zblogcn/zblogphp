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

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript">
<?php

if (isset($hooks)) {
    $zbpdk_allhooks = &$hooks;
} else {
    $zbpdk_allhooks = $GLOBALS;
}

?>
</script>
<style type="text/css">
td,th{text-indent:0}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('PostType'); ?></div>
  <div id="divMain2">
    <form id="form1" onSubmit="return false">
<?php

$defined_route = array("default"=>'Default默认路由',"active"=>'Active动态路由',"rewrite"=>'Rewrite伪静路由');
$replace_array = array(
    'name' => "名称<br/>",
    'classname' => "类名 - 默认是Post，但也可以是继承自BasePost类型的新类<br/>",   
    'template' => "类的模板名 - 分别是自身的模板 对应分类的模板 对应Tag的模板 列表的模板(含日期列表) 搜索页的模板<br/>",  
    'single_urlrule' => "类的Url原始规则 - 分别是自身单个规则 列表规则 分类列表规则 Tag列表规则 作者列表规则 日期列表规则 搜索列表规则<br/>",  
    'actions' => "权限命令数组 - 权限名称分别是 新建 编辑 删除 提交 公开发布 管理 全部管理 查看 搜索<br/>",
    'routes' => "路由数组<br/>",
);

foreach ($posttype as $id => $array) {
    echo '<table class="tableFull tableBorder table_striped table_hover"><thead><tr><th title="点击查看详细信息" style="cursor:pointer;" onclick="$(this).parentsUntil(\'table\').next().toggle();">'.ucfirst($array['name']).'类型<b style="font-weight:normal;"> (posttype = '.$id.')</b></th></tr></thead><tbody style="display:none;">';
    foreach ($array as $key => $value) {
        if (!is_array($value)){
            echo '<tr><td>' . GetValueInArray($replace_array, $key) .'"<b>'.$key.'</b>" => <b>' . $value . '</b>';
            echo '</td></tr>';
        } else {
            echo '<tr><td>' . GetValueInArray($replace_array, $key) .'"<b>'.$key.'</b>" => ';
            if ($key == 'routes') {
                $rs = array();
                foreach ($value as $k1 => $v2) {
                    $rs[$k1] = $v2;
                }
                $value = $rs;
            }
            $t = var_export($value, true);
            echo $t;
            echo '</td></tr>';
        }
    }
    echo '</tbody></table>';
}

?>
    </form>
    <div id="result"></div>
  </div>
</div>
<script type="text/javascript">

ActiveTopMenu('zbpdk');
AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png'; ?>");

</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
