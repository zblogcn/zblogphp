<?php
/*
 * @Name     : Zit Setting
 * @Author   : 吉光片羽
 * @Support  : jgpy.cn
 * @Create   : 2019-12-25 20:10:23
 * @Update   : 2020-02-19 13:18:48
 */

require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';

$zbp->Load();
$action = 'root';
if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6);
}
if (!$zbp->CheckPlugin('Zit')) {
    $zbp->ShowError(48);
}

$cfg = $zbp->Config('Zit');
$def = Zit_Defaults(1);
$msg = (object) $lang['Zit'];

if ($_POST) {
    CheckIsRefererValid();

    if (isset($_POST['update'])) {
        foreach ($def as $k=>$v) {
            if (!$cfg->HasKey($k)) {
                $cfg->$k = $v;
            }
        }
        foreach ($cfg->GetData() as $k=>$v) {
            if (!isset($def[$k])) {
                $cfg->DelKey($k);
            }
        }
    } else {
        foreach ($_POST as $k=>$v) {
            if ($cfg->HasKey($k)) {
                $cfg->$k = $v;
            }
        }
        $cfg->Custom = 1;
    }

    $cfg->Save();
    $zbp->SetHint('good');
    $zbp->BuildModule();
    redirect('main.php');
}

$blogtitle = 'Zit ' . $msg->setting;

$cfgData = $cfg->GetData();
$diffKeys1 = array_diff_key($def, $cfgData);
$diffKeys2 = array_diff_key($cfgData, $def);

if (!empty($diffKeys1) || !empty($diffKeys2)) {
    $submit = '<button type="submit" class="btn update" name="update">' . $msg->update . '</button>';
} else {
    $submit = '<button type="submit" class="btn">' . $msg->submit . '</button>';
}

require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';

?>
<style>
.pane{margin-top:3em;}
.pane .zit{top:-1em;left:1em;}
.pane p{padding:.5em;transition:all 1s;}
.pane p:hover{background:#f7f8f9;margin:0 -1em;padding:.5em 1.5em;}
.pane dfn{font:bold 1em arial;display:inline-block;width:9em;}
.pane small{color:#789;margin-left:.5em}
.pane input,
.pane textarea{border:1px solid #cde;line-height:2;border-radius:.2em;padding:.3em;width:26em;vertical-align:middle;}
.pane textarea{height:4em;overflow:auto;font:1em/1.5 arial;}
.btn{background:#39c;padding:.6em 1em;color:#fff;border:none;font-size:1.2em;border-radius:.5em;font-weight:bold;cursor:pointer;margin-left:1em;}
.btn:hover{box-shadow:0 0 3em rgba(0,0,0,.1) inset}
.update{background:#8c1}
.disabled{cursor:not-allowed;color:#999!important;opacity:.7;}
textarea.disabled,
select.disabled{background:#eee;}
.required{border-color:#f36!important;}
</style>
<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle; ?></div>
  <div class="SubMenu"></div>
<div id="divMain2">
<?php
echo <<<FORM
<form id="edit" method="post" action="">
  <input type="hidden" name="csrfToken" value="{$zbp->GetCsrfToken()}">
  <div class="pane">
    <h3 class="zit">{$msg->appearance}</h3>
    <p><dfn>{$msg->logo}</dfn> <input type="text" name="Logo" value="{$cfg->Logo}"> <small>{$msg->logo_tip}</small></p>
    <p><dfn>{$msg->motto}</dfn> <input type="text" name="Motto" value="{$cfg->Motto}"> <small>{$msg->motto_tip}</small></p>
    <p><dfn>{$msg->cover}</dfn> <input type="text" name="Cover" placeholder="{$msg->cover_place}" value="{$cfg->Cover}"> <small>{$msg->cover_tip}</small></p>
    <p><dfn>{$msg->profile}</dfn> <input type="text" class="checkbox" name="Profile" value="{$cfg->Profile}"> <small>{$msg->profile_tip}</small></p>
    <p><dfn>{$msg->listtags}</dfn> <input type="text" class="checkbox" name="ListTags" value="{$cfg->ListTags}"> <small>{$msg->listtags_tip}</small></p>
    <p><dfn>{$msg->hideintro}</dfn> <input type="text" class="checkbox" name="HideIntro" value="{$cfg->HideIntro}"> <small>{$msg->hideintro_tip}</small></p>
    <p><dfn>{$msg->mobileside}</dfn> <input type="text" class="checkbox" name="MobileSide" value="{$cfg->MobileSide}"> <small>{$msg->mobileside_tip}</small></p>
    <p><dfn>{$msg->sidemods}</dfn> <textarea name="SideMods" placeholder="{$msg->sidemods_place}">{$cfg->SideMods}</textarea> <small>{$msg->sidemods_tip}</small></p>
    <p>
  <script>
  function refreshColor() {
    var color = $( "#slider" ).slider( "value" );
    $("input[name$='ColorChange']").val(color);
    $("input[name$='ColorBanner']").css( "filter", "hue-rotate("+color+"deg)" );
  }
  $(function() {
    $( "#slider" ).slider({max: 360,value: '{$cfg->ColorChange}',slide:refreshColor,change:refreshColor});
  });
  </script>
    <dfn>{$msg->colorchange}</dfn> <input type="hidden" name="ColorChange" value="{$cfg->ColorChange}"><input type="text" name="ColorBanner" style="background-image:url($cfg->Cover);filter:hue-rotate({$cfg->ColorChange}deg)" disabled="disabled"> <small>{$msg->colorchange_tip}</small>
     <div id="slider"></div>
    </p>
  </div>
  <div class="pane">
    <h3 class="zit">{$msg->rand}</h3>
    <p><dfn>{$msg->cmtids}</dfn> <textarea name="CmtIds">{$cfg->CmtIds}</textarea> <small>{$msg->cmtids_tip}</small></p>
    <p><dfn>{$msg->gbook}</dfn> <input type="text" name="GbookID" value="{$cfg->GbookID}" required> <small>{$msg->gbook_tip}</p>
  </div>
  <div class="pane">
    <h3 class="zit">{$msg->seo}</h3>
    <p><dfn>{$msg->description}</dfn> <textarea name="Description">{$cfg->Description}</textarea> <small>{$msg->description_tip}</small></p>
    <p><dfn>{$msg->keywords}</dfn> <textarea name="Keywords">{$cfg->Keywords}</textarea> <small>{$msg->keywords_tip}</small></p>
    <p><dfn>{$msg->relatedtitle}</dfn> <input type="text" name="RelatedTitle" value="{$cfg->RelatedTitle}" required></p>
    <p><dfn>{$msg->commenttitle}</dfn> <input type="text" name="CommentTitle" value="{$cfg->CommentTitle}" required></p>
  </div>
  {$submit}
</form>
FORM;
?>
</div>
</div>
<script type="text/javascript">
ActiveTopMenu("topmenu_Zit");
ActiveLeftMenu("aThemeMng");
$(function(){
  if($("button.update")[0]){
    $(":text,textarea,select").addClass("disabled").prop("disabled",true);
    $(".imgcheck").addClass("disabled").click(function(){
      return false;
    });
    $(".main").prepend('<div class="hint"><p class="hint_bad"><?php echo $msg->update_tip; ?></p></div>');
  }
  $("#calc").blur(function(){
    var nums=this.value.split("*"),num=parseFloat(nums[0])||1;
    if(!this.value||this.value==0) num=0;
    if(nums[1]) num=num*(parseFloat(nums[1])||1);
    this.value=Math.abs(num);
  });
  $("#edit").submit(function(){
    var pass=true;
    $(this).find("[required]").each(function(){
      if(!this.value){
        $(this).addClass("required");
        pass=false;
      }
    });
    if($(this).find("button.update")[0]) pass=true;
    if(!pass) alert("<?php echo $msg->required; ?>");
    return pass;
  });
  $(".required").on("keyup blur",function(){
    $(this)[this.value?"removeClass":"addClass"]("required");
  });
});
</script>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>