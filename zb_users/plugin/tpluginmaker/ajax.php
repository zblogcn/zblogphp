<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
require dirname(__FILE__) . '/function.php';
$zbp->Load(); $action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('tpluginmaker')) {
    $zbp->ShowError(48);
    die();
}
$ary_before = unserialize($zbp->Config('tpluginmaker')->temp_data);
$dir = $usersdir . 'theme/' . $blogtheme . '/include/';
$file_list = array('include.php', 'main.php');
/*
 * 顺序：
 * 1. delete
 * 2. include && type
 * 3. rename
 */
$ary_tree = array();
foreach ($ary_before as $name => $value) {
    $tmp_name = explode('_', substr($name, strlen("tplugin_")));
    $file_name = explode($tmp_name[0] . '_', $name);
    $ary_tree[$tmp_name[0]][$file_name[1]] = $value;
}
// delete
if (isset($ary_tree['delete'])) {
    foreach ($ary_tree['delete'] as $name => $value) {
        @unlink($dir . str_replace('_', '.', $name));
        $zbp->Config('tpluginmaker_' . $blogtheme)->Del(str_replace('_', '.', $name));
    }
}

// include && type
if (isset($ary_tree['name'])) {
    foreach ($ary_tree['name'] as $name => $value) {
        $zbp->Config('tpluginmaker_' . $blogtheme)->$value = $ary_tree['include'][$name];
    }
}

// rename
if (isset($ary_tree['rename'])) {
    foreach ($ary_tree['rename'] as $name => $value) {
        $name = $ary_tree['name'][$name];
        $zbp->Config('tpluginmaker_' . $blogtheme)->$value = $zbp->Config('tpluginmaker_' . $blogtheme)->$name;
        $zbp->Config('tpluginmaker_' . $blogtheme)->Del($name);
    }
}

//Copy File
for ($i = 0; $i < count($file_list); $i++) {
    $temp = '';
    $file_dir = $usersdir . 'plugin/tpluginmaker/resources/' . $file_list[$i];
    $file_output = $usersdir . 'theme/' . $blogtheme . '/' . $file_list[$i];
    $temp = file_get_contents($file_dir);
    parse_single_tags($file_list[$i], $temp);
    parse_global_tags($file_list[$i], $temp);
    file_put_contents($file_output, $temp);
}
//Save Config
$zbp->SaveConfig('tpluginmaker_' . $blogtheme);
//var_dump($zbp->configs['tpluginmaker_' . $blogtheme]);
$zbp->SetHint('good');
//Redirect('main.php');
echo 'location.href="main.php";';

function parse_global_tags($file_name, &$html_data)
{
    $html_data = str_replace('/*TEMPLATE_NAME*/', $GLOBALS['blogtheme'], $html_data);
}

function parse_single_tags($file_name, &$html_data)
{
    switch ($file_name) {
        case 'main.php':
            $html_data = str_replace('/*TEMPLATE_TABLE_ROWS*/', main_table_rows(), $html_data);
            $html_data = str_replace('/*TEMPLATE_SAVE_CODE*/', main_save_code(), $html_data);
        break;

        default:
        break;
    }
}

function main_table_rows()
{
    global $ary_tree;
    $return = array();
    foreach ($ary_tree['name'] as $name => $value) {
        $return[] = '<tr>';
        $return[] = '<td scope="row">' . $ary_tree['include'][$name] . '</td>';
        $include_name = substr(preg_replace("/^\d+/s", '', md5('include_' . $value)), 0, 6);

        if ($ary_tree['type'][$name] == '1') {
            $return[] = '<td><textarea name="include_' . $include_name . '" style="width:98%">';
            $return[] = '<?p' . 'hp echo file_get_contents($dir . "' . $value . '") ?' . '>';
            $return[] = '</textarea></td>';
            $return[] = '<td>{/*TEMPLATE_NAME*/_Require(\'' . $value . '\')}</td>';
        } else {
            $return[] = '<td><input name="include_' . $include_name . '" type="file"/></td>';
            $return[] = '<td>{/*TEMPLATE_NAME*/_Url(\'' . $value . '\')}</td>';
        }
    }

    return implode('', $return);
}

function main_save_code()
{
    $return = array();
    global $ary_tree;
    foreach ($ary_tree['name'] as $name => $value) {
        $include_name = substr(preg_replace("/^\d+/s", '', md5('include_' . $value)), 0, 6);
        if ($ary_tree['type'][$name] == '1') {
            $return[] = '$' . $include_name . " = GetVars('include_$include_name', 'POST'); if ($$include_name != '') file_put_contents(\$dir . '$value', $$include_name);";
        } else {
            $return[] = "if(isset(\$_FILES['include_$include_name'])){" . '$' . $include_name . " = \$_FILES['include_$include_name']; if(is_uploaded_file($$include_name" . '[\'tmp_name\'])){$filename = \'' . $value . '\'; $filename = (TEMPLATE_/*TEMPLATE_NAME*/_IS_WINDOWS ? iconv(\'UTF-8\', \'GBK\', $filename) : $filename); move_uploaded_file($' . $include_name . "['tmp_name'], \$dir . \$filename);}}";
        }
    }

    return implode("\r\n", $return);
}
