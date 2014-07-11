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
	$qiniu->cfg->thumbnail_quality = GetVars('qiniu-thumbnail-quality', 'POST');
	$qiniu->cfg->thumbnail_longedge = GetVars('qiniu-thumbnail-longedge', 'POST');
	$qiniu->cfg->thumbnail_shortedge = GetVars('qiniu-thumbnail-shortedge', 'POST');
	$qiniu->cfg->thumbnail_cut = GetVars('qiniu-thumbnail-cut', 'POST');
	$qiniu->save_config();
	$zbp->SetHint('good');
	Redirect('main.php');
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
    <?php qiniuyun_SubMenu(2);?>
  </div>
  <div id="divMain2">
  <p>缩略图不直接适用于文章，必须在模板中调用。模板调用代码是{qiniuyun_thumbnail_url($article.Content)}</p>
    <form id="form-postsubmit" name="form-postsubmit" method="post" action="thumbnail.php">
      <table width="100%" style="padding:0px;margin:0px;" cellspacing="0" cellpadding="0" class="tableBorder">
        <tr>
          <th width="30%"><p align="center">设置</p></th>
          <th width="60%"><p align="center">内容</p></th>
        </tr>
        <tr>
          <td><p><b>· 图像质量</b><br/>
              <span class="note">&nbsp;取值范围：1-100，缺省为85。</p></td>
          <td><input type="number" min="1" max="100" id="text-thumbnail-quality" name="qiniu-thumbnail-quality" value="<?php echo qiniu_display_text('thumbnail_quality')?>" /></td>
        </tr>
        <tr>
          <td><p><b>·  缩略图长边最小长度</b><br/>
              <span class="note">&nbsp;进行等比缩放直到长边或短边等于缩略图长度</p></td>
          <td><input type="number" id="text-thumbnail-longedge" name="qiniu-thumbnail-longedge" value="<?php echo qiniu_display_text('thumbnail_longedge')?>" /></td>
        </tr>
        <tr>
          <td><p><b>·  缩略图短边最小长度</b><br/>
              <span class="note">&nbsp;进行等比缩放直到长边或短边等于缩略图长度</p></td>
          <td><input type="number" id="text-thumbnail-shortedge" name="qiniu-thumbnail-shortedge" value="<?php echo qiniu_display_text('thumbnail_shortedge')?>" /></td>
        </tr>
        <tr>
          <td><p><b>·  裁剪</b><br/>
              <span class="note">&nbsp;打开则进行等比缩放后，居中裁剪；关闭则等比缩放后某一边可能超出长度。</p></td>
          <td><input type="text" class="checkbox" id="text-thumbnail-cut" name="qiniu-thumbnail-cut" value="<?php echo qiniu_display_text('thumbnail_cut')?>" /></td>
        </tr>
      </table>
      <br />
      <input name="" type="submit" class="button" value="保存"/>
      <br />
      <br />
    </form>
  </div>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>
