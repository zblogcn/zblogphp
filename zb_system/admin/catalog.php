<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require_once '../function/c_system_base.php';
require_once '../function/c_system_admin.php';

$zbp->Initialize();

$action='CategoryMng';
if (!CheckRights($action)) {throw new Exception("没有权限！！！");}

$blogtitle='分类管理';

require_once $blogpath . 'zb_system/admin/admin_header.php';
require_once $blogpath . 'zb_system/admin/admin_top.php';

?>
<?php
//不要吐槽，我会改的！！！

$cate = new Category();
$cate->LoadInfoByID(1);

print_r($cate->Data);

?>

<div id="divMain"> 
<div class="divHeader">分类管理</div>
<div class="SubMenu" style="display: block;">
<a href="../cmd.php?act=CategoryEdt"><span class="m-left">新建分类</span></a>
</div>
<div id="divMain2">
<table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
 <tbody>
  <tr class="color1">
   <th width="5%"></th>
   <th width="10%">ID</th>
   <th width="10%">排序</th>
   <th>名称</th>
   <th>别名</th>
   <th width="14%"></th>
  </tr>
  <tr class="color3">
   <td align="center"><img width="16" src="../image/admin/folder.png" alt="" /></td>
   <td>0</td>
   <td>0</td>
   <td><a href="http://iis.imzhou.com/zblog2/catalog.asp?cate=0" target="_blank">未分类</a></td>
   <td></td>
   <td align="center"><a href="../cmd.asp?act=CategoryEdt&amp;id=0" class="button"><img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a></td>
  </tr>
  <tr class="color2">
   <td align="center"><img width="16" src="../image/admin/folder.png" alt="" /></td>
   <td>1</td>
   <td>0</td>
   <td><a href="http://iis.imzhou.com/zblog2/catalog.asp?cate=1" target="_blank">文学</a></td>
   <td></td>
   <td align="center"><a href="../cmd.asp?act=CategoryEdt&amp;id=1" class="button"><img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);" href="../cmd.asp?act=CategoryDel&amp;id=1" class="button"><img src="../image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td>
  </tr>
  <tr class="color3">
   <td align="center"><img width="16" src="../image/admin/arrow_turn_right.png" alt="" /></td>
   <td>3</td>
   <td>0</td>
   <td><a href="http://iis.imzhou.com/zblog2/catalog.asp?cate=3" target="_blank">音乐</a></td>
   <td></td>
   <td align="center"><a href="../cmd.asp?act=CategoryEdt&amp;id=3" class="button"><img src="../image/admin/folder_edit.png" alt="编辑" title="编辑" width="16" /></a>&nbsp;&nbsp;&nbsp;&nbsp;<a onclick="return window.confirm(&quot;单击“确定”继续。单击“取消”停止。&quot;);" href="../cmd.asp?act=CategoryDel&amp;id=3" class="button"><img src="../image/admin/delete.png" alt="删除" title="删除" width="16" /></a></td>
  </tr>

 </tbody>
</table>
<p>&nbsp;</p>
</div>
<script type="text/javascript">ActiveLeftMenu("aCategoryMng");</script> 
</div>




</div>
<?php
require_once $blogpath . 'zb_system/admin/admin_footer.php';

$zbp->Terminate();

RunTime();
?>
