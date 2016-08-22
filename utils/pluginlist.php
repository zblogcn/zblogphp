<?php

$s = file_get_contents('../zb_system/function/c_system_plugin.php');

$matchs = array();

$i = preg_match_all("/[\*]{50}<.*?[\*]{50}>/s", $s, $matchs);

$matchs = $matchs[0];
echo '<pre>';
foreach ($matchs as $key => $value) {
    $t = $value;
    $t = str_replace('\'', '', $t);
    $t = str_replace('**************************************************<', '', $t);
    $t = str_replace('**************************************************>', '', $t);
    $t = str_replace("类型:Filter\r\n", '', $t);
    $t = str_replace("名称:", '====', $t);
    $t = str_replace("\r\n参数", "====\r\n参数", $t);
    $t = str_replace("\r\n说明", "\\\\\r\n说明", $t);
    $t = str_replace("\r\n调用", "\\\\\r\n调用", $t);
    $t = str_replace("调用:", "调用:\\\\", $t);
    echo $t;
}
echo '</pre>';
