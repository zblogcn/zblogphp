<?php
require_once '../../../zb_system/function/c_system_base.php';
require_once '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('qiniuyun')) {$zbp->ShowError(48);die();}
init_qiniu();
$blogtitle='七牛云存储';
if (count($_POST) > 0)
{
	$qiniu->cfg->water_enable = GetVars('qiniu-water-enable', 'POST');
	$qiniu->cfg->water_overwrite = GetVars('qiniu-water-overwrite', 'POST');
	$qiniu->cfg->water_dissolve = GetVars('qiniu-water-dissolve', 'POST');
	$qiniu->cfg->water_gravity = GetVars('qiniu-water-gravity', 'POST');
	$qiniu->cfg->water_dx = GetVars('qiniu-water-dx', 'POST');
	$qiniu->cfg->water_dy = GetVars('qiniu-water-dy', 'POST');
	$qiniu->save_config();
	if (count($_FILES) > 0)
	{
		move_uploaded_file($_FILES['qiniu-water-upload']['tmp_name'], dirname(__FILE__) . '/water.png');//先上传到本地
	}
	$zbp->SetHint('good');
	Redirect('water.php');
}

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<link rel="stylesheet" type="text/css" href="qiniu-style.css"/>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
    <?php qiniuyun_SubMenu(1);?>
  </div>
  <div id="divMain2">
    <form id="form-postsubmit" name="form-postsubmit" method="post" action="water.php" enctype="multipart/form-data">
      <table width="100%" style="padding:0px;margin:0px;" cellspacing="0" cellpadding="0" class="tableBorder">
        <tr>
          <th width="30%"><p align="center">设置</p></th>
          <th width="60%"><p align="center">内容</p></th>
        </tr>
        <tr>
          <td><p><b>· 开启水印</b><br/>
              <span class="note">&nbsp;</span></p></td>
          <td><input type="text" id="text-water-enable" name="qiniu-water-enable" class="checkbox" value="<?php echo qiniu_display_text('water_enable')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 覆盖原图</b><br/>
              <span class="note">&nbsp;慎选：开启后会使得上传速度大大降低</span></p></td>
          <td><input type="password" id="text-water-overwrite" name="qiniu-water-overwrite" class="checkbox" value="<?php echo qiniu_display_text('water_overwrite')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 水印图片</b><br/>
              <span class="note">&nbsp;直接在这里上传水印，图片为PNG格式。</p></td>
          <td><input type="file" id="text-water-upload" name="qiniu-water-upload" /></td>
        </tr>
        <tr>
          <td><p><b>· 水印透明度</b><br/>
              <span class="note">&nbsp;透明度，取值范围1-100，缺省值为100（完全不透明）</p></td>
          <td><input type="number" min="1" max="100" id="text-water-dissolve" name="qiniu-water-dissolve" value="<?php echo qiniu_display_text('water_dissolve')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 水印位置</b><br/>
              <span class="note">&nbsp;</p></td>
          <td><p>
              <?php output_radio('NorthWest');?>
              &nbsp;
              <?php output_radio('North');?>
              &nbsp;
              <?php output_radio('NorthEast');?>
              &nbsp; </p>
            <p>
              <?php output_radio('West');?>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              <?php output_radio('Center');?>
              <?php output_radio('East');?>
              &nbsp; </p>
            <p>
              <?php output_radio('SouthWest');?>
              &nbsp;
              <?php output_radio('South');?>
              &nbsp;
              <?php output_radio('SouthEast');?>
              &nbsp; </p></td>
        </tr>
        <tr>
          <td><p><b>· 横轴边距</b><br/>
              <span class="note">&nbsp;单位:像素(px)，缺省值为10</p></td>
          <td><input type="number" min="1" max="100" id="text-water-dx" name="qiniu-water-dx" value="<?php echo qiniu_display_text('water_dx')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 纵轴边距</b><br/>
              <span class="note">&nbsp;单位:像素(px)，缺省值为10</p></td>
          <td><input type="number" min="1" max="100" id="text-water-dy" name="qiniu-water-dy" value="<?php echo qiniu_display_text('water_dy')?>" /></td>
        </tr>
      </table>
      <br />
      <input name="" type="submit" class="button" value="保存"/>
    </form>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();

function output_radio($name)
{
	echo '<input type="radio" id="text-water-gravity-' . $name;
	echo '" name="qiniu-water-gravity" value="' . $name;
	echo '" ' . (strtolower($GLOBALS['qiniu']->water_gravity) == strtolower($name) ? ' checked="checked"' : '');
	echo '/><label for="text-water-gravity-' . $name . '">' . $name . '</label>';
}
?>
