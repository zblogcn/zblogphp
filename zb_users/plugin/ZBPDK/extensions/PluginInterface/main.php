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
    switch ($_GET['act']) {
        case 'interface':
            $interface_name = GetVars("interface", "POST");
            preg_match("/^(Action|Filter|Response)_/i", $interface_name, $matches);
            $interface_type = strtolower($matches[1]);
            if ($interface_name != 'Filter_ZBPDK_Display_All') {
                plugininterface_formatfilter($interface_name);
            } else {
                plugininterface_getall();
            }

            echo '<table width="100%"><tr><td height="40">挂接口数量（共' . count($GLOBALS['zbdk_interface_defined_plugins']['filter']) . '个）</td></tr>';
            foreach ($GLOBALS['zbdk_interface_defined_plugins']['filter'] as $temp) {
                echo '<tr onclick="show_code(\'' . $temp['orig'] . '\',$(this).attr(\'_interface\'),this)" _interface="' . $temp['interface_name'] . '">';
                echo '<td height="40">' . TransferHTML($temp['output'], "[html-format]") . '</td></tr>';
            }
            echo '</table>';
            exit();
            break;
        case 'showcode':
            $func_name = GetVars("func", "POST");
            $interface_name = GetVars("if", "POST");
            echo TransferHTML(plugininterface_outputfunc($interface_name, $func_name), "[html-format][enter]");
            exit();
            break;
    }
}

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript">
<?php
$defined_interface = array(
    "action"   => array(),
    "filter"   => array(),
    "response" => array(),
);
if (isset($hooks)) {
    $zbpdk_allhooks = &$hooks;
} else {
    $zbpdk_allhooks = $GLOBALS;
}

foreach ($zbpdk_allhooks as $temp_name => $temp_value) {
    if (preg_match("/^(Action|Filter|Response)_/i", $temp_name, $matches)) {
        array_push($defined_interface[strtolower($matches[1])], '"' . $temp_name . '"');
    }
}

?>
var defined_interface = {
    "action":[<?php echo implode(",", $defined_interface['action']); ?>],
    "filter":["Filter_ZBPDK_Display_All",<?php echo implode(",", $defined_interface['filter']); ?>],
    "response":[<?php echo implode(",", $defined_interface['response']); ?>],
}
function write_list(type_name)
{
    var str = "" , p = defined_interface[type_name];
    for(var i=0; i<=p.length-1; i++){
        var o = p[i];
        str += "<option value='"+o+"'>"+o+"</option>"
    }
    return str;
}

function show_code(func_name,if_name,tr_obj)
{
    $.post("main.php?act=showcode",{"func":func_name,"if":if_name},function(data){$(tr_obj).attr("onclick","").find("td").html('<pre>'+data+'</pre>')})
}
</script>
<style type="text/css">
td,th{text-indent:0}
</style>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"><?php echo $zbpdk->submenu->export('PluginInterface'); ?></div>
  <div id="divMain2">
    <form id="form1" onSubmit="return false">
      <label for="interface">输入接口名</label>
      <input type="text" name="interface" id="interface" style="width:80%" value="Filter_ZBPDK_Display_All"/>
      <input type="submit" name="ok" id="ok" value="查看" onClick=""/>
      <p>或选择接口名：
        <select name="type" id="type" onclick="$('#list').html(write_list($(this).val()))">
          <!--<option value="action">Action</option>-->
          <option value="filter">Filter</option>
         <!-- <option value="response">Response</option>
          <option value="all">All</option>-->
        </select>
        <select name="list" id="list" style="width:80%" onclick="$('#interface').val($(this).val())">
        </select>
      </p>
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
