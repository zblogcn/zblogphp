
<?php

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

header("Content-type: application/x-javascript; charset=utf-8"); 
//Somecode here.
    

$zbp->Initialize();

ob_clean();

//$action='CategoryEdt';
//if (!$zbp->CheckRights($action)) {throw new Exception($lang['error'][6]);}


$upload_dir = str_replace('\\','/',$zbp->option['ZC_UPLOAD_DIRECTORY']."/".date('y/m')) . '/';
$upload_path = $bloghost . $upload_dir;
$upload_dir = $blogpath . $upload_dir;
//echo $upload_dir;

$output_js="(function(){var URL;URL = '{$bloghost}zb_system/admin/ueditor/';window.UEDITOR_CONFIG = {";

$array_config = array(
	'UEDITOR_HOME_URL' => 'URL',
    'imageUrl' => ' URL+"asp/imageUp.asp"',
    'imageNoFlashUrl' => ' URL+"asp/uploadWithoutFlash.asp"',
    'imagePath' => "\"{$upload_path}\"",
    'imageFieldName' => ' "edtFileLoad"',
    'fileUrl' => ' URL+"asp/fileUp.asp"',
    'filePath' => "\"{$upload_path}\"",
    'fileFieldName' => ' "edtFileLoad"',
    'catchRemoteImageEnable' => ' false',
    'imageManagerUrl' => 'URL+"asp/imageManager.asp"',
    'imageManagerPath' => "\"{$bloghost}\"",
    'wordImageUrl' => ' URL+"asp/imageUp.asp"',
    'wordImagePath' => "\"{$upload_path}\"",
    'wordImageFieldName' => '"edtFileLoad"',
    'getMovieUrl' => 'URL+"asp/getMovie.asp"',
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
    'scrawlUrl' => ' URL+"asp/scrawlUp.asp"',
    'scrawlPath' => "\"{$upload_path}\"",
	'scrawlFieldName' => '"edtFileLoad"',
	'maxImageSideLength' => '2147483647',
	'sourceEditor' => '\''.($zbp->option['ZC_CODEMIRROR_ENABLE']?'codemirror':'textarea').'\'',
	'theme' => '"default"',
    'themePath' => 'URL +"themes/"',
	'lang' => '\''.$zbp->option['ZC_EDITORLANG'].'\'',
	'langPath' => 'URL+"../../../zb_users/language/ue-lang/"',
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

