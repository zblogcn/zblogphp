<?php

include_once dirname(__FILE__) . '/function.php';
//注册插件
RegisterPlugin('upyun', 'ActivePlugin_upyun');

function ActivePlugin_upyun()
{
    //Add_Filter_Plugin('Filter_Plugin_Upload_Url', 'upyun_Return_Url');
    Add_Filter_Plugin('Filter_Plugin_Upload_SaveFile', 'upyun');
    Add_Filter_Plugin('Filter_Plugin_Upload_DelFile', 'upyun_Del');
    Add_Filter_Plugin('Filter_Plugin_Upload_SaveBase64File', 'upyun');
    Add_Filter_Plugin('Filter_Plugin_ViewPost_Template', 'upyun_replace');
}

function upyun($tmp, &$upload)
{
    global $zbp;
    $bucket = $zbp->Config('upyun')->upyun_bucket; //云文件夹
    // $filename = date('Ymd', time()).mt_rand(1000, 9999).'_'.mt_rand(0, 10000).'.'.GetFileExt($upload->SourceName);
    // $dir = 'zb_users/upload/'.date('Y/m/', time());
    // $object = $dir.$filename;    //构造云文件名
    // $file_path = $zbp->path.$object;//本地文件
    if (!file_exists($zbp->usersdir . $upload->Dir)) {
        @mkdir($zbp->usersdir . $upload->Dir, 0755, true);
    }

    if (PHP_SYSTEM === SYSTEM_WINDOWS) {
        $fn = iconv("UTF-8", $zbp->lang['windows_character_set'] . "//IGNORE", $upload->Name);
    } else {
        $fn = $upload->Name;
    }

    @move_uploaded_file($tmp, $zbp->usersdir . $upload->Dir . $fn); //上传到本地

    $object = str_replace($zbp->host, '/', $upload->Url);
    $file_path = str_replace($zbp->host, $zbp->path, $upload->Url);
    $operator_name = $zbp->Config('upyun')->upyun_operator_name;
    $operator_password = $zbp->Config('upyun')->upyun_operator_password;

    require_once dirname(__FILE__) . '/api/upyun.class.php';
    $upyun = new UpYun($bucket, $operator_name, $operator_password);

    $file_handler = fopen($file_path, 'r');
    $upyun->writeFile('/' . $object, $file_handler, true);
    fclose($file_handler);

    $GLOBALS['Filter_Plugin_Upload_SaveFile']['upyun'] = PLUGIN_EXITSIGNAL_RETURN;
    $GLOBALS['Filter_Plugin_Upload_SaveBase64File']['upyun'] = PLUGIN_EXITSIGNAL_RETURN;

    return true;
}

/**
 * @param $upload
 */
function upyun_Return_Url($upload)
{
    global $zbp;
    $file = new Upload();
    $file = $zbp->GetUploadByID($upload->ID);
    if ($zbp->Config('upyun')->upyun_save_on_local) {
        return $zbp->host . str_replace('/zb_user', 'zb_user', $file->Metas->upyun_url);
    } else {
        if ($zbp->Config('upyun')->upyun_enable_domain) {
            return $zbp->Config('upyun')->upyun_domain . $file->Metas->upyun_url;
        } else {
            return 'http://' . $file->Metas->upyun_bucket . '.b0.upaiyun.com' . $file->Metas->upyun_url;
        }
    }
}
/**
 * @param $upload
 */
function upyun_Del(&$upload)
{
    global $zbp;
    $bucket = $zbp->Config('upyun')->upyun_bucket;
    $operator_name = $zbp->Config('upyun')->upyun_operator_name;
    $operator_password = $zbp->Config('upyun')->upyun_operator_password;

    require_once dirname(__FILE__) . '/api/upyun.class.php';
    $upyun = new UpYun($bucket, $operator_name, $operator_password);
    $upyun->delete(str_replace($zbp->host, '/', $upload->Url));
    if (file_exists($upload->FullFile)) {
        @unlink($upload->FullFile);
    }

    $GLOBALS['Filter_Plugin_Upload_DelFile']['upyun_Del'] = PLUGIN_EXITSIGNAL_RETURN;

    return true;
}
function upyun_replace(&$template)
{
    global $zbp;
    $article = $template->GetTags('article');
    $host = preg_quote($zbp->host, '/');
    $extension = 'jpg|jpeg|gif|png|bmp';
    $upyun_url = $zbp->Config('upyun')->upyun_enable_domain ? $zbp->Config('upyun')->upyun_domain . '/' : 'http://' . $zbp->Config('upyun')->upyun_bucket . '.b0.upaiyun.com/';
    //$article->Content = preg_replace("/(<[img|link|script|a].*[src|href]=[\"\'])({$host})([^>\'\"]*\.(?:{$extension}))/iU", "\${1}$upyun_url\${3}", $article->Content);
    $article->Content = str_replace($zbp->host . 'zb_users/upload/', $upyun_url . 'zb_users/upload/', $article->Content);
    // $tail = '';
    // if ($zbp->Config('upyun')->upyun_enable_thumbnail) {
    //     $tail = $zbp->Config('upyun')->upyun_cutname . $zbp->Config('upyun')->upyun_ver_name;
    // }

    $zbp->template->SetTags('article', $article);
}
function InstallPlugin_upyun()
{
    global $zbp;
    if (!$zbp->Config('upyun')->HasKey('Version')) {
        $zbp->Config('upyun')->Version = '1.0';
        $zbp->Config('upyun')->upyun_bucket = 'zblogphp';
        $zbp->Config('upyun')->upyun_enable_domain = 0;
        $zbp->Config('upyun')->upyun_domain = 'http://upyun.zblogcn.com';
        $zbp->Config('upyun')->upyun_operator_name = '';
        $zbp->Config('upyun')->upyun_operator_password = '';
        $zbp->Config('upyun')->upyun_enable_thumbnail = 0;
        $zbp->Config('upyun')->upyun_cutname = '';
        $zbp->Config('upyun')->upyun_ver_name = '';
        $zbp->SaveConfig('upyun');
    }
}
function UninstallPlugin_upyun()
{
}
