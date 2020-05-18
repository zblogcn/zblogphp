<?php

/**
 * 生成插件列表的wiki代码
 *
 * @author Z-BlogPHP Team
 * @version
 */
require '../zb_system/function/c_system_base.php';
$zbp->Load();

$s = file_get_contents($zbp->path . 'zb_system\function\c_system_plugin.php');
$s = substr($s, 100);
$a = array();
preg_match_all("/<(.*?)>/is", $s, $a);
$b = array();
foreach ($a[1] as $s) {
    if ($s) {
        $b[] = $s;
    }
}
$br = '<br/>';

foreach ($b as $s) {
    if (stripos($s, ':') === false) {
        echo "　{$br}";
        echo "　{$br}";
        echo "===== {$s} =====<br/>";
        echo "　{$br}";
        echo "　{$br}";
    } else {
        $a = array();
        preg_match_all("/名称:(.*?)'/is", $s, $a);
        $r = $a[1];
        echo "==== {$r[0]} ====<br/>";
        $n = $r[0];
echo "&lt;code php&gt;{$br}
DefinePluginFilter('$n');{$br}
&lt;/code&gt;{$br}";
        echo "** 调用方法 **<br/>";
        $a = array();
        preg_match_all("/说明:(.*?)'/is", $s, $a);
        $r = $a[1];
        if (isset($r[0])) {
            echo "{$r[0]}<br/>";
        }
        echo "<br/>";
//<code php>Add_Filter_Plugin('Filter_Plugin_Admin_ArticleMng_SubMenu','demo');</code>
        echo "** 调用参数 **<br/>";
        $a = array();
        preg_match_all("/参数:(.*?)'/is", $s, $a);
        $r = $a[1];
        if (isset($r[0])) {
            echo "{$r[0]}<br/>";
        }
        echo "　{$br}";
        echo "　{$br}";
    }

    echo "<br/>";
    echo "<br/>";
}
