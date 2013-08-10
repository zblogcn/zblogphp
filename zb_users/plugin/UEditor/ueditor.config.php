
<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

header("Content-type: application/x-javascript; charset=utf-8"); 
//Somecode here.

ob_clean();

$upload_dir = 'zb_users/upload/' . date('Y/m') . '/';
$upload_path = $bloghost . $upload_dir;
$upload_dir = $blogpath . $upload_dir;

#echo '/*' . $upload_dir . '*/' ;

$output_js="(function(){var URL;URL = '{$bloghost}zb_users/plugin/UEditor/';window.UEDITOR_CONFIG = {";

$array_config = array(
	'UEDITOR_HOME_URL' => 'URL',
    'imageUrl' => ' URL+"php/imageUp.php"',
    'imageNoFlashUrl' => ' URL+"php/uploadWithoutFlash.php"',
    'imagePath' => "\"{$upload_path}\"",
    'imageFieldName' => ' "edtFileLoad"',
    'fileUrl' => ' URL+"php/fileUp.php"',
    'filePath' => "\"{$upload_path}\"",
    'fileFieldName' => ' "edtFileLoad"',
    'catchRemoteImageEnable' => ' false',
    'imageManagerUrl' => 'URL+"php/imageManager.php"',
    'imageManagerPath' => "\"{$bloghost}\"",
    'wordImageUrl' => ' URL+"php/imageUp.php"',
    'wordImagePath' => "\"{$upload_path}\"",
    'wordImageFieldName' => '"edtFileLoad"',
    'getMovieUrl' => 'URL+"php/getMovie.php"',
	'toolbars' => "[ [ 'source', '|', 'undo', 'redo', '|', 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript','forecolor', 'backcolor', '|', 'insertorderedlist', 'insertunorderedlist','indent', 'justifyleft', 'justifycenter', 'justifyright','|', 'removeformat','formatmatch','autotypeset', 'searchreplace','pasteplain'],[ 'fontfamily', 'fontsize','|', 'emotion','link','music','insertimage','scrawl','insertvideo', 'attachment','spechars','|', 'map', 'gmap','|', "
				  . ($zbp->option['ZC_SYNTAXHIGHLIGHTER_ENABLE']?"'insertcode',":'')
				  . "'blockquote', 'wordimage','inserttable', 'horizontal','fullscreen']]",
	'shortcutMenu' => "['fontfamily', 'fontsize', 'bold', 'italic', 'underline', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist']",
	'maximumWords' => 1000000000,
	'wordCountMsg' => '"当前已输入 {#count} 个字符" ',
	'initialContent' => '"<p></p>"',
	'initialStyle' => '"body{font-size:14px;font-family:微软雅黑,宋体,Arial,Helvetica,sans-serif;}"',
	'wordCount' => 'true',
	'elementPathEnabled' => 'true',
	'initialFrameHeight' => '300',
	'toolbarTopOffset' => '200',
    'scrawlUrl' => ' URL+"php/scrawlUp.php"',
    'scrawlPath' => "\"{$upload_path}\"",
	'scrawlFieldName' => '"edtFileLoad"',
	'maxImageSideLength' => '2147483647',
	'sourceEditor' => '\''.($zbp->option['ZC_CODEMIRROR_ENABLE']?'codemirror':'textarea').'\'',
	'theme' => '"default"',
    'themePath' => 'URL +"themes/"',
	'lang' => '\'zh-cn\'',
	'langPath' => 'URL+"lang/"',
	'codeMirrorJsUrl' => 'URL+ "third-party/codemirror/codemirror.js"',
	'codeMirrorCssUrl' => 'URL+ "third-party/codemirror/codemirror.css"',
	"maxUpFileSize" => $zbp->option['ZC_UPLOAD_FILESIZE']/(1024*1024),
	"allowDivTransToP" => 'false'
);


foreach ($array_config as $key => $value) {
	$output_js .= '"' . $key . '":' . $value . ',';
}

$output_js .= '"zb_full":""}})();';


//Code here
echo $output_js;

die();

?>

