<?php

//注册插件
RegisterPlugin("LinksManage", "ActivePlugin_LinksManage");

function ActivePlugin_LinksManage()
{
  Add_Filter_Plugin('Filter_Plugin_Admin_Header', 'LinksManage_Head');
  Add_Filter_Plugin('Filter_Plugin_Admin_CategoryMng_SubMenu', 'LinksManage_AddMenu');
  Add_Filter_Plugin('Filter_Plugin_Admin_PageMng_SubMenu', 'LinksManage_AddMenu');
  Add_Filter_Plugin('Filter_Plugin_Admin_ModuleMng_SubMenu', 'LinksManage_ModuleMenu');
  Add_Filter_Plugin('Filter_Plugin_Zbp_BuildTemplate', 'LinksManage_BuildTemp');
}

function LinksManage_BuildTemp(&$templates)
{
  // global $zbp;
  if (is_file(LinksManage_Path("u-temp"))) {
    $templates['lm-module-defend'] = file_get_contents(LinksManage_Path("u-temp"));
  } else {
    $templates['lm-module-defend'] = file_get_contents(LinksManage_Path("v-temp"));
  }
  $templates['lm-module-admin'] = file_get_contents(LinksManage_Path("tr"));
}

function LinksManage_ModuleMenu()
{
  global $zbp;

  $array = $zbp->GetModuleList(
    array('*'),
    array(array('=', 'mod_Source', 'plugin_LinksManage'))
  );
  $mods = array();
  foreach ($array as $mod) {
    $mods[] = $mod->FileName;
  }

  $str = '<a href="' . LinksManage_Path("main", "host") . '" class="LinksManage"><span class="m-left">新建链接模块</span></a>';

  $url = LinksManage_Path("main", "host");
  $mod = LinksManage_Path("bakfile") . '|' . implode('|', $mods);
  $str .= "<input class=\"js-mod\" type=\"hidden\" value=\"{$mod}\">";
  echo $str;
}
function LinksManage_Head()
{
  echo "<link rel=\"stylesheet\" href=\"" . LinksManage_Path("style", "host") . "\">";
  echo "<script src=\"" . LinksManage_Path("script", "host") . "\"></script>";
}
function LinksManage_AddMenu()
{
  global $zbp;
  echo '<a href="' . LinksManage_Path("main.php?edit=navbar", "host") . '" class="LinksManage"><span class="m-left">导航管理</span></a>';
}
function LinksManage_Path($file, $t = "path")
{
  global $zbp;
  $result = $zbp->$t . "zb_users/plugin/LinksManage/";
  switch ($file) {
    case "cache":
      return $zbp->usersdir . "cache/linksmanage/";
      break;
    case "u-temp":
      return $result . "usr/li.html";
      break;
    case "v-temp":
      return $result . "var/li.html";
      break;
    case "tr":
      return $result . "var/tr.html";
      break;
    case "new-tr":
      return $result . "var/new-tr.json";
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
      return $zbp->usersdir . "cache/linksmanage/backup/";
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
function InstallPlugin_LinksManage()
{
  global $zbp;

  $dir = LinksManage_Path("cache");
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
  }
  $dir = LinksManage_Path("bakdir");
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
  }
  $dir = LinksManage_Path("usr");
  if (!is_dir($dir)) {
    @mkdir($dir, 0755);
  }
  $links = explode('|', LinksManage_Path("bakfile"));
  foreach ($links as $mod) {
    $backup = $zbp->modulesbyfilename[$mod]->Content;
    $bakFile = LinksManage_Path("bakdir") . $mod . '.txt';
    if (!is_file($bakFile)) {
      file_put_contents($bakFile, $backup);
    }
  }
  // $filesList = array("temp");
  // foreach ($filesList as $key => $value) {
  //   $uFile = LinksManage_Path("u-{$value}");
  //   $vFile = LinksManage_Path("v-{$value}");
  //   if (!is_file($uFile)) {
  //     @mkdir(dirname($uFile));
  //     copy($vFile, $uFile);
  //   }
  // }
  $zbp->BuildTemplate();
}
function UninstallPlugin_LinksManage()
{
  global $zbp;
  $zbp->LoadModules();
  $links = explode('|', LinksManage_Path("bakfile"));
  foreach ($links as $mod) {
    $file = LinksManage_Path("bakdir") . $mod . '.txt';
    if (is_file($file)) {
      $backup = file_get_contents($file);
      $module = $zbp->modulesbyfilename[$mod];
      $module->Content = $backup;
      $module->Save();
      $zbp->AddBuildModule($module->FileName);
      unlink($file);
    }
  }
}
