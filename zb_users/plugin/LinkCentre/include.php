<?php

//注册插件
RegisterPlugin("LinkCentre", "ActivePlugin_LinkCentre");

function ActivePlugin_LinkCentre()
{
    Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'LinkCentre_Head');
    Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu', 'LinkCentre_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu', 'LinkCentre_AddMenu');
    Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu', 'LinkCentre_ModuleMenu');
    Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'LinkCentre_BuidTemp');
}
function LinkCentre_BuidTemp(&$templates)
{
    // global $zbp;
    // if (is_file(LinkCentre_Path("u-temp"))) {
    $templates['Links_defend'] = file_get_contents(LinkCentre_Path("u-temp"));
    // }
    $templates['Links_admin'] = file_get_contents(LinkCentre_Path("tr"));
}
function LinkCentre_ModuleMenu()
{
    global $zbp;

    $array = $zbp->GetModuleList(
        array('*')
    );
    $mods = array();
    foreach ($array as $mod) {
        if ($mod->Type == 'ul') {
            if ($mod->Source == 'system' && !in_array($mod->HtmlID, array('navbar', 'link', 'misc', 'favorite'))) {
                continue;
            }
            $mods[] = $mod->FileName;
        }
    }

    $str = '<a href="' . LinkCentre_Path("main", "host") . '" class="LinkCentre"><span class="m-left">新建链接模块</span></a>';

    $url = LinkCentre_Path("main", "host");
    $mod = LinkCentre_Path("bakfile") . '|' . implode('|', $mods);
    $str .= "<input class=\"js-mod\" type=\"hidden\" value=\"{$mod}\">";
    echo $str;
}
function LinkCentre_Head()
{
    echo "<link rel=\"stylesheet\" href=\"" . LinkCentre_Path("style", "host") . "\">";
    echo "<script src=\"" . LinkCentre_Path("script", "host") . "\"></script>";
}
function LinkCentre_AddMenu()
{
    global $zbp;
    echo '<a href="' . LinkCentre_Path("main.php?edit=navbar", "host") . '" class="LinkCentre"><span class="m-left">导航管理</span></a>';
}
function LinkCentre_Path($file, $t = "path")
{
    global $zbp;
    $result = $zbp->$t . "zb_users/plugin/LinkCentre/";
    switch ($file) {
        case "u-temp":
            return $result . "usr/li.html";
            break;
        case "v-temp":
            return $result . "var/li.html";
            break;
        case "tr":
            return $result . "var/tr.html";
            break;
        case "style":
            return $result . "var/style.css";
            break;
        case "script":
            return $result . "var/script.js";
            break;
        case "usr":
            return $result . "usr/";
            break;
        case "bakdir":
            return $result . "backup/";
            break;
        case "bakfile":
            return 'navbar|favorite|link';
            break;
        case "main":
            return $result . "main.php";
            break;
        default:
            return $result . $file;
    }
}
function InstallPlugin_LinkCentre()
{
}

function UninstallPlugin_LinkCentre()
{
}
