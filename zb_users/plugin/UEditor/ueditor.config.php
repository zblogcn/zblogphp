<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

header("Content-type: application/x-javascript; charset=utf-8");
//Somecode here.

ob_clean();

$upload_dir = 'zb_users/upload/' . date('Y/m') . '/';
//$upload_path = $bloghost . $upload_dir;
$upload_path = '';
$upload_dir = $blogpath . $upload_dir;

//echo '/*' . $upload_dir . '*/' ;

$output_js = "(function(){var URL;URL = '{$bloghost}zb_users/plugin/UEditor/';window.UEDITOR_CONFIG = {";
$lang = strtolower($zbp->lang['lang']);
if (!is_dir('./lang/' . $lang)) {
    $lang = "zh-cn";
}

$array_config = array(
    'UEDITOR_HOME_URL' => 'URL',
    'HOST_URL'         => 'bloghost',
    'serverUrl'        => 'URL + "php/controller.php"',
    'toolbars'         => "[ " .
    "[ 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript','forecolor', 'backcolor', '|', " .
    "'insertorderedlist', 'insertunorderedlist','indent', 'justifyleft', 'justifycenter', 'justifyright','|', 'removeformat','formatmatch','autotypeset', 'pasteplain'], " .
    "['paragraph', 'fontfamily', 'fontsize','|', 'emotion','link','insertimage','scrawl','insertvideo', 'attachment','spechars', 'map','|', "
    . ($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE'] ? "'insertcode'," : '')
    . "'blockquote', 'wordimage','inserttable', 'horizontal','fullscreen']]",
    'shortcutMenu'           => "['fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'forecolor', 'backcolor']",
    'maximumWords'           => 1000000000,
    'wordCountMsg'           => '"当前已输入 {#count} 个字符" ',
    'initialContent'         => '"<p></p>"',
    'initialStyle'           => '"body{font-size:14px;font-family:微软雅黑,宋体,Arial,Helvetica,sans-serif;}"',
    'wordCount'              => 'true',
    'elementPathEnabled'     => 'true',
    'initialFrameHeight'     => '300',
    'imageMaxSize'           => $zbp->option['ZC_UPLOAD_FILESIZE'] * 1024 * 1024,
    'fileMaxSize'            => $zbp->option['ZC_UPLOAD_FILESIZE'] * 1024 * 1024,
    'toolbarTopOffset'       => '200',
    'sourceEditor'           => '\'' . ($zbp->option['ZC_CODEMIRROR_ENABLE'] ? 'codemirror' : 'textarea') . '\'',
    'theme'                  => '"default"',
    'themePath'              => 'URL +"themes/"',
    'lang'                   => '\'' . $lang . '\'',
    'langPath'               => 'URL+"lang/"',
    'codeMirrorJsUrl'        => 'URL+ "third-party/codemirror/codemirror.js"',
    'codeMirrorCssUrl'       => 'URL+ "third-party/codemirror/codemirror.css"',
    "maxUpFileSize"          => $zbp->option['ZC_UPLOAD_FILESIZE'],
    "allowDivTransToP"       => 'false',
    "catchRemoteImageEnable" => "false",
);

foreach ($array_config as $key => $value) {
    $output_js .= '"' . $key . '":' . $value . ',';
}

$output_js .= '"zb_full":""};';
$output_js .= '})();';

//Code here
echo $output_js;

die();

?>

