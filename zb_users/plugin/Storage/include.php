<?php

//注册插件
RegisterPlugin("Storage", "ActivePlugin_Storage");

function ActivePlugin_Storage()
{
    Add_Filter_Plugin('Filter_Plugin_Upload_Url', 'Storage_Return_Url');
    Add_Filter_Plugin('Filter_Plugin_Upload_SaveFile', 'Storage');
    Add_Filter_Plugin('Filter_Plugin_Upload_DelFile', 'Storage_Del');
    // Add_Filter_Plugin('Filter_Plugin_Upload_SaveBase64File','Storage');
}

$domainname = $zbp->Config('Storage')->Storage_Domain;

/**
 * @param $tmp
 * @param $upload
 */
function Storage($tmp, &$upload)
{
    global $zbp,$domainname;
    $filename = date("Ymd", time()) . mt_rand(1000, 9999) . '_' . mt_rand(0, 1000) . '.' . GetFileExt($upload->SourceName);
    $object = date("Y/m/", time()) . $filename;    //构造云文件名

    $upload->Name = $filename;

    $s = new SaeStorage();
    $url = $s->upload($domainname, $object, $tmp);

    $upload->Metas->Storage_URL = $object;

    $GLOBALS['Filter_Plugin_Upload_SaveFile']['Storage'] = PLUGIN_EXITSIGNAL_RETURN;
}

/**
 * @param $upload
 */
function Storage_Del(&$upload)
{
    global $zbp,$domainname;
    $s = new SaeStorage();
    $url = $s->delete($domainname, $upload->Metas->Storage_URL);
    $GLOBALS['Filter_Plugin_Upload_DelFile']['Storage_Del'] = PLUGIN_EXITSIGNAL_RETURN;
}

/**
 * @param $upload
 *
 * @return null
 */
function Storage_Return_Url($upload)
{
    global $zbp,$domainname;
    $file = new Upload();
    $file = $zbp->GetUploadByID($upload->ID);
    $s = new SaeStorage();
    $url = $s->getUrl($domainname, $file->Metas->Storage_URL);

    return $url;
}

function InstallPlugin_Storage()
{
    global $zbp;
    if (!$zbp->Config('Storage')->HasKey('Version')) {
        $zbp->Config('Storage')->Version = '1.0';
        $zbp->Config('Storage')->Storage_Domain = 'imzhou';
        $zbp->SaveConfig('Storage');
    }
}
function UninstallPlugin_Storage()
{
}
