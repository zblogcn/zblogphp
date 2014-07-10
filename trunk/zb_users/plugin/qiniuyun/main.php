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
	$qiniu->cfg->access_token = GetVars('qiniu-accesstoken', 'POST');
	$qiniu->cfg->secret_key = GetVars('qiniu-secretkey', 'POST');
	$qiniu->cfg->bucket = GetVars('qiniu-bucket', 'POST');
	$qiniu->cfg->domain = GetVars('qiniu-domain', 'POST');
	$qiniu->cfg->cloudpath = GetVars('qiniu-cloudpath', 'POST');
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
    <?php qiniuyun_SubMenu(0);?>
  </div>
  <div id="divMain2">
    <form id="form-postsubmit" name="form-postsubmit" method="post" action="main.php">
      <table width="100%" style="padding:0px;margin:0px;" cellspacing="0" cellpadding="0" class="tableBorder">
        <tr>
          <th width="30%"><p align="center">设置</p></th>
          <th width="60%"><p align="center">内容</p></th>
        </tr>
        <tr>
          <td><p><b>· Access Token</b><br/>
              <span class="note">&nbsp;如果您没有七牛Access Token的话，可以点击<a href="https://portal.qiniu.com/setting/key" target="_blank">https://portal.qiniu.com/setting/key</a>这里去获取。</span></p></td>
          <td><input type="text" id="text-accesstoken" name="qiniu-accesstoken" value="<?php echo qiniu_display_text('access_token')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· Secret Key</b><br/>
              <span class="note">&nbsp;如果您没有七牛Secret Key的话，可以点击<a href="https://portal.qiniu.com/setting/key" target="_blank">https://portal.qiniu.com/setting/key</a>这里去获取。</span></p></td>
          <td><input type="password" id="text-secretkey" name="qiniu-secretkey" value="<?php echo qiniu_display_text('secret_key')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 空间名</b><br/>
              <span class="note">&nbsp;待存储数据的空间名（必须为公开）</p></td>
          <td><input type="text" id="text-bucket" name="qiniu-bucket" value="<?php echo qiniu_display_text('bucket')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 上传目录</b><br/>
              <span class="note">&nbsp;若为根目录请留空；前面不需要加“/”，且最后一位必须为“/”。</p></td>
          <td><input type="text" id="text-cloudpath" name="qiniu-cloudpath" value="<?php echo qiniu_display_text('cloudpath')?>" /></td>
        </tr>
        <tr>
          <td><p><b>· 绑定域名</b><br/>
              <span class="note">&nbsp;如果您要使用<?php echo qiniu_display_text('bucket')?>.qiniudn.com，请留空。</p></td>
          <td><input type="text" id="text-domain" name="qiniu-domain" value="<?php echo qiniu_display_text('domain')?>" /></td>
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
?>
