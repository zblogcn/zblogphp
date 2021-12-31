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
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('Route'); ?></div>
  <div id="divMain2">
    <form id="form1" onSubmit="return false">
<?php

$defined_route = array("active"=>'Active动态路由',"rewrite"=>'Rewrite伪静路由',"default"=>'Default默认路由');
$replace_array = array(
    '\'type\' =' => "//路由类型\r\n" . '\'type\' =',
    '\'name\' =' => "//路由名称(同类型下不可重复否则会覆盖，名称必须是post_类型名_打头，后面接路由作用名)\r\n" . '\'name\' =',
    '\'call\' =' => "//路由调用的函数(可以为'函数名'或是'变量名@方法名'或是'变量名::静态方法')\r\n" . '\'call\' =',
    '\'posttype\' =' => "//Post类型(必须，文章为0，页面为1等，必须是路由所属的PostType的ID值，如果不归属于某Post类型请设为null)\r\n" . '\'posttype\' =',
    '\'prefix\' =' => "//prefix如指定的话，可以让不同规则在不同的prefix前缀目录下被访问到\r\n" . '\'prefix\' =',
    '\'get\' =' => "//指定2个或2个以上参数(与must_get合并一起判断)如array('id','alias')那么只要有1个参数存在就可以,指定1个参数如array('page')则不要求page存在\r\n" . '\'get\' =',
    '\'not_get\' =' => "//必须排除的\$_GET参数(可以为空数组),如果是array('/.+/')就会禁止任何参数传入\r\n" . '\'not_get\' =',
    '\'must_get\' =' => "//必须包含的\$_GET参数(可以为空数组)\r\n" . '\'must_get\' =',
    '\'args\' =' => "//从伪静规则匹配到的数组中取值传给call的参数(示例为array('id', 'page') or array('cate@alias', 'page') )\r\n" . '\'args\' =',
    '\'args_get\' =' => "//从\$_GET获取值传给call的参数()\r\n" . '\'args_get\' =',
    '\'args_with\' =' => "//固定传的call参数(先从\$_GET取值再从本条路由规则中取值并覆盖)\r\n" . '\'args_with\' =',
    '\'urlrule\' =' => "//动态路由和伪静路由的原始规则(必须)\r\n" . '\'urlrule\' =',
    '\'abbr_url\' =' => "//指示规则可以被缩写为'域名/'或是'域名/目录/'\r\n" . '\'abbr_url\' =',
    '\'only_match_page\' =' => "//设为ture将强制匹配带page参数的url(不设或设为false的将会匹配一次带page一次不带page的)\r\n" . '\'only_match_page\' =',
    '\'request_method\' =' => "//Request Method为Http的请求访问，一般不设或是设为array('GET', 'POST')或是'GET'(只能GET不能POST)\r\n" . '\'request_method\' =',
    '\'to_permalink\' =' => "//如果是在动态路由下被访问到，允许跳转到Call里返回的固定链接url\r\n" . '\'to_permalink\' =',
    '\'suspended\' =' => "//为true时将挂起这条路由使路由系统忽略它\r\n" . '\'suspended\' =',
    '\'only_active\' =' => "//default类型的路由，为true时只在动态模式下生效\r\n" . '\'only_active\' =',
    '\'only_rewrite\' =' => "//default类型的路由，为true时只在伪静模式下生效\r\n" . '\'only_rewrite\' =',
    '\'verify_permalink\' =' => "//设为false时会跳过对比当前url和目标url是否一致\r\n" . '\'verify_permalink\' =',
);

foreach ($defined_route as $route_type => $route_note) {
    echo '<table class="tableFull tableBorder table_striped table_hover"><tbody><tr><th>'.$route_note.'</th></tr>';
    foreach ($zbp->routes as $key => $value) {if($value['type'] == $route_type){
        echo '<tr><td title="点击查看详细信息" style="cursor:pointer;" onclick="$(this).find(\'div\').toggle();">'.$value['name'].' => ' . $value['call'] . '(';
        $s = '';
        if (isset($value['args'])) {
            foreach ($value['args'] as $key2 => $value2) {
                if (is_integer($key2)) {
                    if (is_array($value2)) {
                        $s .= '$'. current($value2) . ', ';
                    } else {
                        $s .=  '$'. $value2 . ', ';
                    }
                } else {
                    $s .=  '$'. $key2 . ', ';
                }
            }
        }
        if (isset($value['args_get'])) {
            foreach ($value['args_get'] as $key2 => $value2) {
                $s .=  '$'. $value2 . ', ';
            }
        }
        echo trim(trim($s), ',');
        //echo $s;
        echo ')';
        $backargs = null;
        if (isset($value['args'])) {
            $backargs = $value['args'];
            unset($value['args']);
        }
        $t = var_export($value,true);
        if ($value['type'] == 'active') {
            $replace_array['\'type\' ='] = "//路由类型 (active类型不匹配规则，只从过滤\$_GET和从\$_GET中取值并传入Call，不匹配将跳出本规则进入下一条)\r\n" . '\'type\' =';
        }
        if ($value['type'] == 'rewrite') {
            $replace_array['\'type\' ='] = "//路由类型 (rewrite类型使用Route规则进行匹配，从规则中取得参数并传入Call，不匹配将跳出本规则进入下一条)\r\n" . '\'type\' =';
        }
        if ($value['type'] == 'default') {
            $replace_array['\'type\' ='] = "//路由类型 (default类型为默认路由，不检查Regex规则是否匹配，只会传入get参数就调用Call，前面不能匹配的规则都会进入默认路由，如果没有匹配到请返回false，让下一条路由生效！)\r\n" . '\'type\' =';
        }
        foreach ($replace_array as $key => $value) {
            $t = str_replace('  ' . $key, $value, $t);
        }
        if ($backargs !== null) {
            $t .= PHP_EOL . "// 从伪静规则匹配到的数组中取值传给call的参数(示例为array('id', 'page') or array('cate@alias', 'page') )或是更复杂的配置" . PHP_EOL . '\'args\' => ' . var_export($backargs,true);
        }
        echo '<div style="display:none;margin:1em;box-shadow: 0px 0px 5px gray;padding:1em;background-color:#f8f8f8;"><pre>'.$t.'</pre></div>';
        echo '</td></tr>';
    }}
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
