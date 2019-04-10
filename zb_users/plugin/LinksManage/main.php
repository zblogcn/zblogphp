<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
    die();
}
if (!$zbp->CheckPlugin('LinksManage')) {
    $zbp->ShowError(48);
    die();
}
InstallPlugin_LinksManage();
$act = GetVars('act', 'GET');
$suc = GetVars('suc', 'GET');
if (GetVars('act', 'GET') == 'save') {
    if (function_exists('CheckIsRefererValid')) {
        CheckIsRefererValid();
    }
  }
  //$fileName = GetVars('FileName', 'POST');
  //$file = LinksManage_Path("usr") . $fileName . ".json";
  //file_put_contents($file, json_encode($items));
  $mod->Metas->LM_json = $file;
  $outTpl = "Links_defend";
  if (isset($zbp->template->templates["Links_{$fileName}"])) {
    $outTpl = "Links_{$fileName}";
  }
  foreach ($items as $item) {
    $zbp->template->SetTags('id', $fileName);
    $zbp->template->SetTags('item', $item);
    $content .= $zbp->template->Output($outTpl);
  }
  $content = str_replace(array('target="" ', ' target=""', "\n"), "", CloseTags($content));
  $content = preg_replace('/>\s+</', "><", $content);
  $mod->Content = $content;
  $mod->Name = $_POST['Name'];
  $mod->FileName = $_POST['FileName'];
  $mod->HtmlID = $_POST['HtmlID'];
  $mod->Source = $_POST['Source'];
  $mod->IsHideTitle = (bool)$_POST['IsHideTitle'];
  if($_POST['IsDiv'] == 1)
    $mod->Type = 'div';
  else
    $mod->Type = 'ul';
  $mod->MaxLi = 0;
  FilterModule($mod);
  $mod->Save();
  $zbp->AddBuildModule($mod->FileName);
  $zbp->BuildModule();
  $zbp->SetHint('good');
  Redirect($_POST['stay'] == '1' ? $_SERVER['HTTP_REFERER'] : '../../../zb_system/cmd.php?act=ModuleMng');
}

$mod = new Module();
$mod->ID = 0;
$mod->Source = 'plugin_LinksManage';
$list = '<tr><td><input type="text" name="href[]" value="http://" size="30" /></td><td><input type="text" name="title[]" value="链接描述" size="30" /></td><td><input type="text" name="name[]" value="新名称" size="20" /></td><td><input type="text" name="target[]" class="checkbox" value="0" /></td><td><input type="text" name="sub[]" class="checkbox" value="0" /></td></tr>';

$islock = '';
$tree = null;
$delbtn = '';

if ($edit = GetVars('edit', 'GET')) {
  if (!empty($edit)) {
    $mod = $zbp->modulesbyfilename[$edit];
  }
  $file_contents = $mod->Metas->LM_json;
  if (strlen( $file_contents)>0 && $items = json_decode($file_contents)) {
    $list = '';
    foreach ($items as $item) {
      $zbp->template->SetTags('item', $item);
      $list .= $zbp->template->Output("Links_admin");
    }
    $file = LinksManage_Path("usr") . $edit . ".json";
    if (is_file($file) && $items = json_decode(file_get_contents($file))) {
        $list = '';
        foreach ($items as $item) {
            $zbp->template->SetTags('item', $item);
            $list .= $zbp->template->Output("Links_admin");
        }
    } else {
        $content = $mod->Content;
        preg_match('/<\/ul><\/li>/i', $content, $tree);
        if ($tree) {
            $content = str_replace(array('<ul>', '</ul></li>'), array("</li>\n", ''), $content);
        }
        $preg = array(
      'tag'    => '/<li.*?<\/li>/',
      'sub'    => '/<li.*?class=[\'|\"](.*?)[\'|\"]/i',
      'href'   => '/<a.*?href=[\'|\"](.*?)[\'|\"]/i',
      'target' => '/<a.*?target=[\'|\"](.*?)[\'|\"]/i',
      'name'   => '/<a.*?>(.*?)<\/a>/i', 'title'=> '/<a.*?title=[\'|\"](.*?)[\'|\"]/i', );
        $link = array();
        preg_match_all($preg['tag'], $content, $tag);
        foreach ($tag[0] as $key=> $val) {
            foreach ($preg as $k => $v) {
                preg_match($v, $val, $m);
                if (count($m) > 1) {
                    if ($k == 'name') {
                        $m[1] = preg_replace('/<img.*?[\/]>/i', '', $m[1]);
                    }
                    if ($k == 'sub') {
                        $m[1] = !preg_match('/sub/i',
      $m[1]) ? '' : 'LinksManageSub';
                    }
                    $link[$k][$key] = $m[1];
                } else {
                    $link[$k][$key] = '';
                }
            }
        }
        if ($link) {
            $list = '';
            foreach ($link['tag'] as $k=> $v) {
                $list .= '<tr class="' . $link['sub'][$k] . '">
          <td><input name="href[]" value="' . $link['href'][$k] . '" size="30" /></td>
          <td><input name="title[]" value="' . $link['title'][$k] . '" size="30" /></td>
          <td><input name="name[]" value="' . $link['name'][$k] . '" size="20" /></td>
          <td><input name="target[]" value="' . ($link['target'][$k] ? 1 : 0) . '" class="checkbox" /></td>
          <td><input name="sub[]" value="' . ($link['sub'][$k] ? 1 : 0) . '" class="checkbox" /></td>
        <tr>';
            }
        }
    }
    if ($mod->Source == 'system' || $mod->Source == 'theme') {
        $islock = 'readonly="readonly"';
    }
    $delbtn = $mod->Source === 'plugin_LinksManage' ? '&nbsp;<a title="删除当前模块"
    onclick="return window.confirm(\'' . $zbp->lang['msg']['confirm_operating'] . '\');"
    href="' . BuildSafeCmdURL('act=ModuleDel&amp;source=theme&amp;filename=' . $mod->FileName) . '"><img
      src="' . $zbp->host . 'zb_system/image/admin/delete.png" alt="删除" title="删除" width="16"></a>' : '';
}

$blogtitle = '链接管理';
$blogtitle .= $mod->Name !== "" ? "（{$mod->Name}）" : "";
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
    <form id="edit" name="edit" method="post" action="<?php echo BuildSafeURL('main.php?act=save'); ?>">
      <input name="ID" type="hidden" value="<?php echo $mod->ID; ?>" />
      <input name="Source" type="hidden" value="<?php echo $mod->Source; ?>" />
      <table class="tableFull tableBorder tableBorder-thcenter">
        <thead>
          <tr>
            <th>链接</th>
            <th>描述</th>
            <th>名称</th>
            <th class="td10"><abbr title="是否在新窗口打开">新窗</abbr></th>
            <th class="td10"><abbr title="作为前一个一级链接的二级链接">二级</abbr></th>
          </tr>
        </thead>
        <tbody id="LinksManageList">
          <?php echo $list; ?>
        </tbody>
        <tfoot>
          <tr id="LinksManageAdd">
            <td colspan="5" class="tdCenter"><input type="button" class="button js-add"
                value="添加项目">已有项目可拖动排序或删除</td>
          </tr>
          <tr id="LinksManageDel">
            <td colspan="5" class="tdCenter">拖入这里删除</td>
          </tr>
          <tr class="LinksManageAdd">
            <td><input type="text" name="href[]" value="http://" size="30" /></td>
            <td><input type="text" name="title[]" value="链接描述" size="30" /></td>
            <td><input type="text" name="name[]" value="新名称" size="20" /></td>
            <td><input type="text" name="target[]" class="checkbox" value="0" /></td>
            <td><input type="text" name="sub[]" class="checkbox" value="0" /></td>
          </tr>
        </tfoot>
      </table>
      <table class="tableFull tableBorder tableBorder-thcenter">
        <tr>
          <th><?php echo $lang['msg']['name'] ?>（简明的中文标识）</th>
          <th><?php echo $lang['msg']['filename'] ?>（非中文且文件命名可用）</th>
          <th><?php echo $lang['msg']['htmlid'] ?>（HTML规范的元素ID）</th>
          <th class="td10"><?php echo $lang['msg']['hide_title'] ?></th>
          <th class="td10"><abbr title="手工输入HTML代码，插件不接管内容。">切换为DIV型[?]</abbr></th>
          <th class="td10"><?php echo $lang['msg']['del'] ?></th>
          <th class="td10 hidden"><abbr title="关闭树形则采用嵌套格式，即二级菜单默认隐藏">树形[?]</abbr></th>
        </tr>
        <tr>
          <td><input id="edtName" size="20" name="Name" maxlength="50" type="text"
              value="<?php echo $mod->Name; ?>" /></td>
          <td><input id="edtFileName" size="20" name="FileName" type="text"
              value="<?php echo $mod->FileName; ?>" <?php echo $islock ?> /></td>
          <td><input id="edtHtmlID" size="20" name="HtmlID" type="text" value="<?php echo $mod->HtmlID; ?>" />
          </td>
          <td class="tdCenter"><input type="text" id="IsHideTitle" name="IsHideTitle" class="checkbox" value="<?php echo $mod->IsHideTitle; ?>"/></td>
          <td class="tdCenter"><input type="text" id="IsDiv" name="IsDiv" class="checkbox" value="<?php echo $mod->Type == 'div'; ?>"/></td>
          <td class="tdCenter"><?php echo $delbtn ?></td>
          <td class="hidden"><input type="text" name="tree" class="checkbox"
              value="<?php echo $tree ? 0 : 1; ?>" /></td>
        </tr>
      </table>
      <p>
        <input type="submit" class="button" value="<?php echo $lang['msg']['submit'] ?>"
          onclick="return checkInfo();" />
        <input type="text" name="stay" class="checkbox" value="0" /> 提交后返回本页
        <?php if (isset($bakUrl)) {
    ?>
        <a title="查看备份" href="<?php echo $bakUrl; ?>" target="_blank">查看备份（<?php echo $mod->FileName; ?>）</a>
        <?php
} ?>
      </p>
      <hr style="border-top:1px solid #ddd!important;visibility:visible;"/>
      <p>对于每个li，会默认添加 "文件名-item" 作为类名，当前为：<?php echo "{$mod->FileName}-item";?></p>
      <p>默认模板路径：<?php echo LinksManage_Path("u-temp");?></p>
      <p>(暂未实现)自定义模板路径：<?php echo LinksManage_Path("usr/{$mod->FileName}.li");?></p>
      <p>模板编译时会加<b>"Links_"</b>前缀，默认模板编译为<b>Links_defend</b></p>
    </form>
  </div>
</div>
<script>
function checkInfo() {
  if (!$("#edtName").val()) {
    alert("<?php echo $lang['error']['72'] ?>");
    return false;
  }
  if (!$("#edtFileName").val()) {
    alert("<?php echo $lang['error']['75'] ?>");
    return false;
  }
  if (!$("#edtHtmlID").val()) {
    alert("<?php echo $lang['error']['76'] ?>");
    return false;
  }
}
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
