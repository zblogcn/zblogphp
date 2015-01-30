<?php
require '../../../zb_system/function/c_system_base.php';

require '../../../zb_system/function/c_system_admin.php';

require dirname(__FILE__) . '/function.php';

$zbp->Load();

$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

if (!$zbp->CheckPlugin('AppCentre')) {$zbp->ShowError(48);die();}

$blogtitle='应用中心-主题编辑';

if(GetVars('id')){
  $app = $zbp->LoadApp('theme',GetVars('id'));
  $mt=array();
  $ft=GetFilesInDir($zbp->path . '/zb_users/theme/' . $app->id . '/','php|inc|png');
  foreach($ft as $f){
    $mt[]=filemtime($f);
  }
  $ft=GetFilesInDir($zbp->path . '/zb_users/theme/' . $app->id . '/include/','php|inc|png');
  foreach($ft as $f){
    $mt[]=filemtime($f);
  }
  $ft=GetFilesInDir($zbp->path . '/zb_users/theme/' . $app->id . '/style/','php|inc|png');
  foreach($ft as $f){
    $mt[]=filemtime($f);
  }
  $ft=GetFilesInDir($zbp->path . '/zb_users/theme/' . $app->id . '/template/','php|inc|png');
  foreach($ft as $f){
    $mt[]=filemtime($f);
  }
  $ft=GetFilesInDir($zbp->path . '/zb_users/theme/' . $app->id . '/source/','php|inc|png');
  foreach($ft as $f){
    $mt[]=filemtime($f);
  }  
  rsort($mt);
  if(count($mt)==0)$mt[]=time();
  $app->modified = date('Y-m-d', reset($mt));
}else{
  $app = new App;
  $app->price=0;
  $app->version='1.0';
  $app->pubdate=date('Y-m-d',time());
  $app->modified=date('Y-m-d',time());
  $v=array_keys($zbpvers);
  $app->adapted=(string)end($v);
  $app->type='theme';
}

if(count($_POST)>0){

  $app->id=trim($_POST['app_id']);
  if(!CheckRegExp($app->id,"/^[A-Za-z0-9_]{3,30}/")) {$zbp->ShowError('ID名必须是字母数字和下划线组成,长度3-30字符.');die();}
  if(!GetVars('id')){
    $app2 = $zbp->LoadApp('theme',$app->id);
    if($app2->id) {$zbp->ShowError('已存在同名的APP应用.');die();}
    @mkdir($zbp->usersdir . 'theme/' . $app->id);
    @mkdir($zbp->usersdir . 'theme/' . $app->id . '/style');
    @mkdir($zbp->usersdir . 'theme/' . $app->id . '/compile');
    @mkdir($zbp->usersdir . 'theme/' . $app->id . '/template');
    @copy($zbp->usersdir . 'plugin/AppCentre/images/theme.png',$zbp->usersdir . 'theme/' . $app->id . '/screenshot.png');
    @file_put_contents($zbp->usersdir . 'theme/' . $app->id . '/style/style.css','');

    if(trim($_POST['app_path'])){
      $file = file_get_contents('tpl/main.html');
      $file = str_replace("<%appid%>", $app->id, $file);
      $path=$zbp->usersdir . 'theme/' . $app->id . '/' . trim($_POST['app_path']);
      @file_put_contents($path, $file);
    }
    if(trim($_POST['app_include'])){
      $file = file_get_contents('tpl/include.html');
      $file = str_replace("<%appid%>", $app->id, $file);
      $path=$zbp->usersdir . 'theme/' . $app->id . '/include.php';
      @file_put_contents($path, $file);
    }
  }

$app->name=trim($_POST['app_name']);
$app->url=trim($_POST['app_url']);
$app->note=trim($_POST['app_note']);
$app->adapted=$_POST['app_adapted'];
$app->version=(float)$_POST['app_version'];
if($app->version==1)$app->version='1.0';
$app->pubdate=date('Y-m-d',strtotime($_POST['app_pubdate']));
$app->modified=date('Y-m-d',time());

$app->author_name=trim($_POST['app_author_name']);
$app->author_email=trim($_POST['app_author_email']);
$app->author_url=trim($_POST['app_author_url']);
$app->source_name=trim($_POST['app_source_name']);
$app->source_email=trim($_POST['app_source_email']);
$app->source_url=trim($_POST['app_source_url']);

$app->path=trim($_POST['app_path']);
$app->include=trim($_POST['app_include']);
$app->level=(int)$_POST['app_level'];
$app->price=(float)$_POST['app_price'];

$app->advanced_dependency=trim($_POST['app_advanced_dependency']);
$app->advanced_rewritefunctions=trim($_POST['app_advanced_rewritefunctions']);
$app->advanced_conflict=trim($_POST['app_advanced_conflict']);

$app->description=trim($_POST['app_description']);

if(GetVars('app_sidebars_sidebar1')){
  $app->sidebars_sidebar1=$zbp->option['ZC_SIDEBAR_ORDER'];
}else{
  $app->sidebars_sidebar1='';
}
if(GetVars('app_sidebars_sidebar2')){
  $app->sidebars_sidebar2=$zbp->option['ZC_SIDEBAR2_ORDER'];
}else{
  $app->sidebars_sidebar2='';
}
if(GetVars('app_sidebars_sidebar3')){
  $app->sidebars_sidebar3=$zbp->option['ZC_SIDEBAR3_ORDER'];
}else{
  $app->sidebars_sidebar3='';
}
if(GetVars('app_sidebars_sidebar4')){
  $app->sidebars_sidebar4=$zbp->option['ZC_SIDEBAR4_ORDER'];
}else{
  $app->sidebars_sidebar4='';
}
if(GetVars('app_sidebars_sidebar5')){
  $app->sidebars_sidebar5=$zbp->option['ZC_SIDEBAR5_ORDER'];
}else{
  $app->sidebars_sidebar5='';
}


$app-> SaveInfoByXml();

  $zbp->SetHint('good', '提交成功！<a href="submit.php?type=theme&id=' . $app->id . '">现在立刻上传到应用中心！</a>');
  Redirect($_SERVER["HTTP_REFERER"]);
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<div id="divMain">

  <div class="divHeader"><?php echo $blogtitle;?></div>
<div class="SubMenu"><?php AppCentre_SubMenus(GetVars('id','GET')==''?6:'');?></div>
  <div id="divMain2">

<form method="post" action="">
  <table border="1" width="100%" cellspacing="0" cellpadding="0" class="tableBorder tableBorder-thcenter">
    <tr>
      <th width='28%'>&nbsp;</th>
      <th>&nbsp;</th>
    </tr>
    <tr>
      <td><p><b>· 主题ID</b><br/>
          <span class="note">&nbsp;&nbsp;主题ID为主题的目录名,且不能重复.ID名只能用字母数字和下划线的组合.</span></p></td>
      <td><p>&nbsp;
          <input id="app_id" name="app_id" style="width:550px;"  type="text" value="<?php echo $app->id;?>" <?php if($app->id)echo 'readonly="readonly"';?>  />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题名称</b></p></td>
      <td><p>&nbsp;
          <input id="app_name" name="app_name" style="width:550px;"  type="text" value="<?php echo $app->name;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题发布页面</b></p></td>
      <td><p>&nbsp;
          <input id="app_url" name="app_url" style="width:550px;"  type="text" value="<?php echo $app->url;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题简介</b></p></td>
      <td><p>&nbsp;
          <input id="app_note" name="app_note" style="width:550px;"  type="text" value="<?php echo $app->note;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 适用的最低要求 Z-Blog 版本</b></p></td>
      <td><p>&nbsp;
          <select name="app_adapted" id="app_adapted" style="width:400px;">
<?php echo AppCentre_CreateOptoinsOfVersion($app->adapted);?>
          </select>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题版本号</b></p></td>
      <td><p>&nbsp;
          <input id="app_version" name="app_version" style="width:550px;" type="number" step="0.1" value="<?php echo $app->version;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题首发时间</b><br/>
          <span class="note">&nbsp;&nbsp;日期格式为2012-12-12</span></p></td>
      <td><p>&nbsp;
          <input id="app_pubdate" name="app_pubdate" style="width:550px;"  type="text" value="<?php echo $app->pubdate;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题最后修改时间</b><br/>
          <span class="note">&nbsp;&nbsp;系统自动检查目录内文件的最后修改日期</span></p></td>
      <td><p>&nbsp;
          <input id="app_modified" name="app_modified" style="width:550px;"  type="text" value="<?php echo $app->modified;?>" readonly="readonly" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 作者名称</b></p></td>
      <td><p>&nbsp;
          <input id="app_author_name" name="app_author_name" style="width:550px;"  type="text" value="<?php echo $app->author_name;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 作者邮箱</b></p></td>
      <td><p>&nbsp;
          <input id="app_author_email" name="app_author_email" style="width:550px;"  type="text" value="<?php echo $app->author_email;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 作者网站</b></p></td>
      <td><p>&nbsp;
          <input id="app_author_url" name="app_author_url" style="width:550px;"  type="text" value="<?php echo $app->author_url;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 源作者名称</b> (可选)</p></td>
      <td><p>&nbsp;
          <input id="app_source_name" name="app_source_name" style="width:550px;"  type="text" value="<?php echo $app->source_name;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 源作者邮箱</b> (可选)</p></td>
      <td><p>&nbsp;
          <input id="app_source_email" name="app_source_email" style="width:550px;"  type="text" value="<?php echo $app->source_email;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 源作者网站</b> (可选)</p></td>
      <td><p>&nbsp;
          <input id="app_source_url" name="app_source_url" style="width:550px;"  type="text" value="<?php echo $app->source_url;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 内置插件管理页</b> (可选)<br/>
          <span class="note">&nbsp;&nbsp;习惯命名为main.php</span></p></td>
      <td><p>&nbsp;
          <input id="app_path" name="app_path" style="width:550px;"  type="text" value="<?php echo $app->path;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 内置插件嵌入页</b> (可选)<br/>
          <span class="note">&nbsp;&nbsp;只能命名为include.php</span></p></td>
      <td><p>&nbsp;
          <input id="app_include" name="app_include" style="width:550px;"  type="text" value="<?php echo $app->include;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 内置插件管理权限</b> (可选)</p></td>
      <td><p>&nbsp;
          <select name="app_level" id="app_level" style="width:200px;">
<?php echo CreateOptoinsOfMemberLevel(1)?>
          </select>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 主题定价</b></p></td>
      <td><p>&nbsp;
          <input id="app_price" name="app_price" style="width:550px;"  type="text" value="<?php echo $app->price;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 【高级】依赖插件（以|分隔）</b>(可选)</p></td>
      <td><p>&nbsp;
          <input id="app_advanced_dependency" name="app_advanced_dependency" style="width:550px;"  type="text" value="<?php echo $app->advanced_dependency;?>" />
        </p></td>
    </tr>
    <tr style="display:none;">
      <td><p><b>· 【高级】内置插件重写系统函数列表（以|分隔）</b>(可选)</p></td>
      <td><p>&nbsp;
          <input id="app_advanced_rewritefunctions" name="app_advanced_rewritefunctions" style="width:550px;"  type="text" value="<?php echo $app->advanced_rewritefunctions;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 【高级】内置插件冲突插件列表（以|分隔）</b>(可选)</p></td>
      <td><p>&nbsp;
          <input id="app_advanced_conflict" name="app_advanced_conflict" style="width:550px;"  type="text" value="<?php echo $app->advanced_conflict;?>" />
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 详细说明</b> (可选)</p></td>
      <td><p>&nbsp;
          <textarea cols="3" rows="6" id="app_description" name="app_description" style="width:550px;"><?php echo htmlspecialchars($app->description);?></textarea>
        </p></td>
    </tr>
    <tr>
      <td><p><b>· 侧栏配置导出</b> (可选)</p></td>
      <td><p>&nbsp;
          <label>
            <input type="checkbox" name="app_sidebars_sidebar1" value="1" <?php if($app->sidebars_sidebar1)echo 'checked="checked"';?>  />
            侧栏</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="checkbox" name="app_sidebars_sidebar2" value="1" <?php if($app->sidebars_sidebar2)echo 'checked="checked"';?>   />
            侧栏2</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="checkbox" name="app_sidebars_sidebar3" value="1" <?php if($app->sidebars_sidebar3)echo 'checked="checked"';?>   />
            侧栏3</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="checkbox" name="app_sidebars_sidebar4" value="1" <?php if($app->sidebars_sidebar4)echo 'checked="checked"';?>   />
            侧栏4</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>
            <input type="checkbox" name="app_sidebars_sidebar5" value="1" <?php if($app->sidebars_sidebar5)echo 'checked="checked"';?>   />
            侧栏5</label>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p></td>
    </tr>
  </table>
  <p> 提示:主题的缩略图是名为screenshot.png的<b>300x240px</b>大小的png文件,放在插件的目录下.</p>
  <p><br/>
    <input type="submit" class="button" value="提交" id="btnPost" onclick='' />
  </p>
  <p>&nbsp;</p>
</form>


	<script type="text/javascript">ActiveLeftMenu("aAppCentre");</script>
	<script type="text/javascript">AddHeaderIcon("<?php echo $bloghost . 'zb_users/plugin/AppCentre/logo.png';?>");</script>	
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>