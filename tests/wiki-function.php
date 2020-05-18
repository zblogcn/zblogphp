<?php

/**
 * 生成common函数的wiki
 *
 * @author Z-BlogPHP Team
 * @version
 */
require '../zb_system/function/c_system_base.php';
$zbp->Load();

echo '<hr/>
<a href="?common">common</a><br/>
<a href="?admin">admin</a><br/>
<a href="?plugin">plugin</a><br/>
<a href="?event">event</a><br/>
<a href="?debug">debug</a><br/>
<hr/>';

if ($_SERVER['QUERY_STRING'] == '') {
    die;
}

if ($_SERVER['QUERY_STRING']) {

}

$s = file_get_contents($zbp->path . 'tests\\' . $_SERVER['QUERY_STRING'] .'-structure.xml');

$xml = simplexml_load_string($s);

foreach ($xml->file->function as $function) {
    $br = '<br/>';
    $n = str_replace(['\\', '\\'], '', $function->full_name);
    $f = str_replace(['()'], '', $n);
    echo "==== {$n} ====<br/><br/><br/>";
    echo "=== 说明 ===<br/>";
    echo "{$function->docblock->description}<br/><br/>";
    echo "=== 参数与返回值 ===<br/>";
    $p = '';
    $pa = '';
    $r = '';
    foreach ($function->docblock->tag as $tag) {
        if ($tag->attributes()['name'] == 'package') {
            continue;
        }
        $d = $tag->attributes()['description'];
        $d = str_replace(['<p>', '</p>'], '', $d);
        if ($tag->attributes()['name'] == 'param') {
            echo "{$tag->attributes()['name']} ({$tag->attributes()['type']}) {$tag->attributes()['variable']}: {$d}<br/><br/>";
            $p .= ' * @param ' . $tag->attributes()['type'] . ' ' . $tag->attributes()['variable'] . '<br/>';
            $pa .= $tag->attributes()['variable'] . ',';
        }
        if ($tag->attributes()['name'] == 'return') {
            echo "{$tag->attributes()['name']} ({$tag->attributes()['type']})<br/><br/>";
            $p .= ' * @return ' . $tag->type;
        }
    }
    $pa = trim($pa, ',');
    echo "&lt;code php&gt;{$br}
/**{$br}
 * {$function->docblock->description}{$br}
 *{$br}
{$p}
 *{$br}
{$r}
 */{$br}
}{$br}
function {$f}({$pa}){$br}
&lt;/code&gt;{$br}
　{$br}
　{$br}";
}
