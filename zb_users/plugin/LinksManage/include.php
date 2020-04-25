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
  // 接管AddItemToNavbar
  Add_Filter_Plugin('Filter_Plugin_PostPage_Succeed', 'LinksManage_AddItemToNavbar');
  Add_Filter_Plugin('Filter_Plugin_PostCategory_Succeed', 'LinksManage_AddItemToNavbar');
  Add_Filter_Plugin('Filter_Plugin_PostTag_Succeed', 'LinksManage_AddItemToNavbar');
}
function LinksManage_AddItemToNavbar($obj)
{
  $input = GetVars('AddNavbar', 'POST');
  if ($input == 0) {
    return;
  }
  $item = LinksManage_GetNewItem();
  $item->href = $obj->Url;
  $item->text = $item->title = isset($obj->Name) ? $obj->Name : $obj->Title;
  // $item->more["type"] = get_class($obj);
  // $item->more["id"] = $obj->ID;
  LinksManage_AddItem2Mod($item, "navbar");
}
function LinksManage_BuildTemp(&$templates)
{
  // global $zbp;
  $uFile = LinksManage_Path("u-temp");
  $vFile = LinksManage_Path("v-temp");
  if (is_file($uFile)) {
    $uFileCon = file_get_contents($uFile);
    $templates['lm-module-defend'] = $uFileCon;
    // 2020-04-20
    if (strpos($uFileCon, '{$item.name}')) {
      unlink(LinksManage_Path("u-temp"));
    }
  } else {
    $vFileCon = file_get_contents($vFile);
    $templates['lm-module-defend'] = $vFileCon;
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
function LinksManage_AddItem2Mod($item, $fileName)
{
  global $zbp;
  $mod = $zbp->GetModuleByFileName($fileName);
  $items = (array) json_decode($mod->Metas->LM_json);
  $singlal = "";
  // json在插件编辑页保存过才会生成
  if (empty($items)) {
    return;
  }
  foreach ($items as $temp) {
    if (json_encode($temp) == json_encode($item)) {
      $singlal = "break";
      break;
    }
  }
  if ($singlal !== "break") {
    $items[] = $item;
    $mod->Metas->LM_json = json_encode($items);
  }
  $mod->Content = LinksManage_GenModCon($items, $fileName);
  $mod->Save();
}
function LinksManage_GenModCon($items, $fileName)
{
  global $zbp;
  $outTpl = "lm-module-defend";
  if (isset($zbp->template->templates["lm-module-{$fileName}"])) {
    $outTpl = "lm-module-{$fileName}";
  }
  $content = "";
  foreach ($items as $item) {
    if (isset($item->ico) && !empty($item->ico)) {
      $item->ico = "<i class=\"{$item->ico}\"></i>";
    } else {
      $item->ico = "";
    }
    $zbp->template->SetTags('item', $item);
    $zbp->template->SetTags('id', $fileName);
    $content .= $zbp->template->Output($outTpl);
  }
  $content = str_replace(array('target="" ', ' target=""', "\n"), "", CloseTags($content));
  $content = preg_replace('/>\s+</', "><", $content);
  return $content;
}
function LinksManage_GetNewItem()
{
  return json_decode(file_get_contents(LinksManage_Path("new-tr")));
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
