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
    $zbpdk_allhooks = &$GLOBALS;
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
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('Route'); ?></div>
  <div id="divMain2">
    <form id="form1" onSubmit="return false">
<?php

$defined_route = array("default"=>'Default默认路由',"active"=>'Active动态路由',"rewrite"=>'Rewrite伪静路由');
$replace_array = array(
    '\'type\' =' => "//路由类型\r\n" . '\'type\' =',
    '\'name\' =' => "//路由名称(同类型下不可重复，否则会覆盖)\r\n" . '\'name\' =',
    '\'call\' =' => "//路由调用的函数(可以为'函数名'或是'变量名@方法名'或是'变量名::静态方法')\r\n" . '\'call\' =',
    '\'posttype\' =' => "//Post类型(文章为0，页面为1，以此类推)\r\n" . '\'posttype\' =',
    '\'prefix\' =' => "//prefix如指定的话，可以让不同Post类型在不同的prefix目录下被访问到而不是全挤在根目录下接受访问\r\n" . '\'prefix\' =',
    '\'get\' =' => "//只要有一个就可以匹配到本规则的\$_GET参数(可以为空数组即不指定参数)\r\n" . '\'get\' =',
    '\'not_get\' =' => "//必须排除的\$_GET参数(可以为空数组)\r\n" . '\'not_get\' =',
    '\'must_get\' =' => "//必须包含的\$_GET参数(可以为空数组)\r\n" . '\'get\' =',
    '\'parameters\' =' => "//从伪静规则匹配到的数组中取值传给call的参数(示例为array('cate'=>'id', 'page'=>'page') or array('post'=>array('id','alias'), 'page'=>'page') )\r\n" . '\'parameters\' =',
    '\'parameters_get\' =' => "//从\$_GET获取值传给call的参数()\r\n" . '\'parameters_get\' =',
    '\'parameters_with\' =' => "//固定传的call参数(先从\$_GET取值再从本条路由规则中取值并覆盖)\r\n" . '\'parameters_with\' =',
    '\'urlrule\' =' => "//伪静路由的原始规则\r\n" . '\'urlrule\' =',
    '\'urlrule_regex\' =' => "//如果指定了urlrule_regex就忽略编译原始规则\r\n" . '\'urlrule_regex\' =',
    '\'haspage\' =' => "//指示urlrule原始规则中含有page参数，这样伪静路由会执2次，一次有page一次没有page的匹配(指定了urlrule_regex无效)\r\n" . '\'haspage\' =',
);

foreach ($defined_route as $route_type => $route_note) {
    echo '<table class="tableFull tableBorder table_striped table_hover"><tbody><tr><th>'.$route_note.'</th></tr>';
    foreach ($zbp->routes[$route_type] as $key => $value) {
        echo '<tr><td title="点击查看详细信息" style="cursor:pointer;" onclick="$(this).find(\'div\').toggle();">['.$zbp->posttype[$value['posttype']]['name'].'] '.$value['name'].' => ' . $value['call'] . '(';
        $s = '';
        if (isset($value['parameters'])) {
            foreach ($value['parameters'] as $key2 => $value2) {
                if (is_integer($key2)) {
                    $s .=  '$'. $value2 . ', ';
                } else {
                    $s .=  '$'. $key2 . ', ';
                }
            }
        }
        if (isset($value['parameters_get'])) {
            foreach ($value['parameters_get'] as $key2 => $value2) {
                $s .=  '$'. $value2 . ', ';
            }
        }
        echo trim(trim($s), ',');
        //echo $s;
        echo ')';
        $t = var_export($value,true);
        foreach ($replace_array as $key => $value) {
            $t = str_replace('  ' . $key, $value, $t);
        }
        echo '<div style="display:none;margin:1em;box-shadow: 0px 0px 5px gray;padding:1em;background-color:#f8f8f8;"><pre>'.$t.'</pre></div>';
        echo '</td></tr>';
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