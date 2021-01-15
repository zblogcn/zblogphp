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

foreach ($defined_route as $route_type => $route_note) {
    echo '<table class="tableFull tableBorder table_striped table_hover" id="tbdefault"><tbody><tr><th>'.$route_note.'</th></tr>';
    foreach ($zbp->routes[$route_type] as $key => $value) {
        echo '<tr><td title="点击查看详细信息" style="cursor:pointer;" onclick="$(this).find(\'div\').toggle();">'.$value['name'].' => ' . $value['function'] . '(';
        $s = '';
        foreach ($value['parameters'] as $key2 => $value2) {
            $s .=  '$'. $value2 . ', ';
        }
        echo trim(trim($s), ',');
        //echo $s;
        echo ')';
        echo '<div style="display:none;margin:1em;box-shadow: 0px 0px 5px gray;padding:1em;background-color:#f8f8f8;"><pre>'.var_export($value,true).'</pre></div>';
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
$(document).ready(function() {
    $('#list').html(write_list('filter'));
    $("#form1").bind("submit",function(){
        $("#result").html("Waiting...");
        $.post(
            "main.php?act=interface",
            {"interface":$("#interface").val()},
            function(data)
            {
                $("#result").html(data);
                bmx2table();
            }
        );
    })
});

ActiveTopMenu('zbpdk');
AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/ZBPDK/logo.png'; ?>");

</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
