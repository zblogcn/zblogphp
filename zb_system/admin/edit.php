<?php
/**
 * Z-Blog with PHP
 * @author
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->CheckGzip();
$zbp->Load();
$zbp->template->LoadTemplates();

$action = '';
if (GetVars('act', 'GET') == 'PageEdt') {
    $action = 'PageEdt';
}

if (GetVars('act', 'GET') == 'ArticleEdt') {
    $action = 'ArticleEdt';
}

if (!$zbp->CheckRights($action)) {
    $zbp->ShowError(6, __FILE__, __LINE__);
    die();
}

if (isset($_COOKIE['timezone'])) {
    $tz = GetVars('timezone', 'COOKIE');
    if (is_numeric($tz)) {
        date_default_timezone_set(GetTimeZonebyGMT($tz));
    }
    unset($tz);
}


$article = new Post;
$article->AuthorID = $zbp->user->ID;

$ispage = false;
if ($action == 'PageEdt') {
    $ispage = true;
    $article->Type = 1;
}

if (!$zbp->CheckRights('ArticlePub')) {
    $article->Status = ZC_POST_STATUS_AUDITING;
}

if (isset($_GET['id'])) {
    $article->LoadInfoByID((integer) GetVars('id', 'GET'));
}

if ($ispage) {
    $blogtitle = $lang['msg']['page_edit'];
    if (!$zbp->CheckRights('PageAll') && $article->AuthorID != $zbp->user->ID) {
        $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }
} else {
    $blogtitle = $lang['msg']['article_edit'];
    if (!$zbp->CheckRights('ArticleAll') && $article->AuthorID != $zbp->user->ID) {
        $zbp->ShowError(6, __FILE__, __LINE__);
        die();
    }
}

if ($article->Intro) {
    if (strpos($article->Content, '<!--more-->') !== false) {
        $article->Intro = '';
        $article->Content = str_replace('<!--more-->', '<hr class="more" />', $article->Content);
    } elseif (strpos($article->Intro, '<!--autointro-->') !== false) {
        $article->Intro = '';
    }
}

require ZBP_PATH . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript" src="../script/jquery.tagto.js"></script>
<script type="text/javascript" src="../script/jquery-ui-timepicker-addon.js"></script>
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Begin'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
<?php
require ZBP_PATH . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
<div class="divHeader2"><?php echo $ispage ? $lang['msg']['page_edit'] : $lang['msg']['article_edit']; ?></div>


<div class="SubMenu">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_SubMenu'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
</div>
<div id="divMain2" class="edit post_edit">
<form id="edit" name="edit" method="post" action="#">
  <div id="divEditLeft">
    <!-- 4号输出接口 -->
       <div id="response4" class="editmod2">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Response4'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
       </div>
    <div id="divEditTitle" class="editmod2">
      <input type="hidden" name="ID" id="edtID" value="<?php echo $article->ID; ?>" />
      <input type="hidden" name="Type" id="edtType" value="<?php echo $article->Type; ?>" />
      <!-- title( -->
        <div id="titleheader" class="editmod">
            <label for="edtTitle" class="editinputname" ><?php echo $lang['msg']['title'] ?></label>
            <div><input type="text" name="Title" id="edtTitle"  maxlength="<?php echo $option['ZC_ARTICLE_TITLE_MAX']; ?>" onBlur="if(this.value=='') this.value='<?php echo $lang['msg']['unnamed'] ?>'" onFocus="if(this.value=='<?php echo $lang['msg']['unnamed'] ?>') this.value=''" value="<?php echo $article->Title; ?>" /></div>
      </div>
      <!-- )title -->

    </div>

    <!-- 5号输出接口 -->
       <div id="response5" class="editmod2">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Response5'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
       </div>

    <div id="divContent"  class="editmod2" style="clear:both;">
        <div id='cheader' class="editmod editmod3"><label for="editor_content" class="editinputname" ><?php echo $lang['msg']['content'] ?></label>&nbsp;&nbsp;<span id="timemsg"></span><span id="msg2"></span><span id="msg"></span><span class="editinputname" ></span><script type="text/javascript" src="../cmd.php?act=misc&amp;type=autosave"></script></div>
        <div id='carea' style="margin:5px 0 0 0" class="editmod editmod3"><textarea id="editor_content" name="Content"><?php echo TransferHTML($article->Content, '[html-format]'); ?></textarea></div>
        <div id="contentready" style="display:none"><img alt="loading" id="statloading1" src="../image/admin/loading.gif"/>Waiting...</div>
    </div>

    <!-- 1号输出接口 -->
       <div id="response" class="editmod2">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Response'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
       </div>

    <br/>
      <!-- alias( -->
      <div id="alias" class="editmod2"><label for="edtAlias" class="editinputname" ><?php echo $lang['msg']['alias'] ?></label>
        <input type="text" name="Alias" id="edtAlias" maxlength="250" value="<?php echo $article->Alias; ?>" />
      </div>
      <!-- )alias -->
<?php if (!$ispage) {?>
        <!-- tags( -->
      <div id="tags" class="editmod2"><label  for="edtTag"  class='editinputname'><?php echo $lang['msg']['tags'] ?></label>
        <input type="text"  name="Tag" id="edtTag" value="<?php echo $article->TagsToNameString(); ?>" />
        (<?php echo $lang['msg']['use_commas_to_separate'] ?>) <a href="#" id="showtags"><?php echo $lang['msg']['show_common_tags'] ?></a></div>
      <!-- Tags -->
      <div id="ulTag" class="editmod2" style="display:none;">
        <div id="ajaxtags">Waiting...</div>
      </div>
      <!-- )tags -->

       <div id="insertintro" class="editmod2" style="padding-top:0.5em;paddding-bottom:0.5em;"><span>* <?php echo $lang['msg']['help_generate_summary'] ?><a href="" onClick="try{AutoIntro();return false;}catch(e){}">[<?php echo $lang['msg']['generate_summary'] ?>]</a></span></div>
<?php }?>

        <div id="divIntro" class="editmod2" <?php if (!$article->Intro) {
            echo 'style="display:none;"';
}?>>
       <div id="theader" class="editmod editmod3"><label for="editor_intro" class="editinputname" ><?php echo $lang['msg']['intro'] ?></label></div>
       <div id='tarea' style="margin:5px 0 0 0" class="editmod editmod3"><textarea id="editor_intro" name="Intro"><?php echo TransferHTML($article->Intro, '[html-format]'); ?></textarea></div>
       <div id="introready" style="display:none"><img alt="loading" id="statloading2" src="../image/admin/loading.gif"/>Waiting...</div>

    </div>
    <!-- 2号输出接口 -->
       <div id="response2" class="editmod2">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Response2'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
       </div>


  </div>
  <!-- divEditLeft -->

  <div id="divEditRight">
    <div id="divEditPost">
      <div id="divBox">
        <div id="divFloat">
          <div id='post' class="editmod">
            <input class="button" style="width:180px;height:38px;" type="submit" value="<?php echo $lang['msg']['submit'] ?>" id="btnPost" onclick='return checkArticleInfo();' />
          </div>

          <!-- cate --><?php if (!$ispage) {?>
          <div id='cate' class="editmod"> <label for="cmbCateID" class="editinputname" style="max-width:65px;text-overflow:ellipsis;"><?php echo $lang['msg']['category'] ?></label>
            <select style="width:180px;" class="edit" size="1" name="CateID" id="cmbCateID">
<?php echo OutputOptionItemsOfCategories($article->CateID); ?>
            </select>
          </div>
          <!-- cate --><?php }?>

          <!-- level -->
          <div id='level' class="editmod"> <label for="cmbPostStatus" class="editinputname" style="max-width:65px;text-overflow:ellipsis;"><?php echo $lang['msg']['status'] ?></label>
            <select class="edit" style="width:180px;" size="1" name="Status" id="cmbPostStatus" onChange="cmbPostStatus.value=this.options[this.selectedIndex].value">
<?php echo OutputOptionItemsOfPostStatus($article->Status); ?>
            </select>
          </div>
          <!-- )level -->

          <!-- template( -->

          <div id='template' class="editmod"> <label for="cmbTemplate" class="editinputname" style="max-width:65px;text-overflow:ellipsis;"><?php echo $lang['msg']['template'] ?></label>
            <select style="width:180px;" class="edit" size="1" name="Template" id="cmbTemplate" onChange="cmbTemplate.value=this.options[this.selectedIndex].value">
<?php echo OutputOptionItemsOfTemplate($article->Template); ?>
            </select>
          </div>
          <!-- )template -->

          <!-- user( -->
          <div id='user' class="editmod"> <label for="cmbUser" class="editinputname" style="max-width:65px;text-overflow:ellipsis;"><?php echo $lang['msg']['author'] ?></label>
            <select style="width:180px;" size="1" name="AuthorID" id="cmbUser" onChange="cmbUser.value=this.options[this.selectedIndex].value">
                <?php echo OutputOptionItemsOfMember($article->AuthorID); ?>
            </select>
          </div>
          <!-- )user -->

          <!-- newdatetime( -->
          <div id='newdatetime' class="editmod"> <label for="edtDateTime" class="editinputname" style="max-width:65px;text-overflow:ellipsis;"><?php echo $lang['msg']['date'] ?></label>
            <input type="text" name="PostTime" id="edtDateTime"  value="<?php echo $article->Time(); ?>" style="width:180px;"/>
            </div>

          <!-- )newdatetime -->

          <!-- Istop( --><?php if (!$ispage && $zbp->CheckRights('ArticleAll')) {?>
          <div id='istop' class="editmod">
            <label for="edtIstop" class="editinputname" ><?php echo $lang['msg']['top'] ?></label>
            <input id="edtIstop" name="IsTop" style="" type="text" value="<?php echo (int) $article->IsTop; ?>" class="checkbox"/>
<select style="width:80px;display:<?php echo $article->IsTop ? '' : 'none' ?>;" size="1" name="IstopType" id="edtIstopType" class="off-hide">
<option value="index" <?php echo strpos($article->TopType, 'index') !== false ? 'selected="selected"' : ''; ?>><?php echo $lang['msg']['top_index'] ?></option>
<option value="global" <?php echo strpos($article->TopType, 'global') !== false ? 'selected="selected"' : ''; ?>><?php echo $lang['msg']['top_global'] ?></option>
<option value="category" <?php echo strpos($article->TopType, 'category') !== false ? 'selected="selected"' : ''; ?>><?php echo $lang['msg']['top_category'] ?></option>
</select>
          </div><?php }?>

          <!-- )Istop -->

          <!-- IsLock( -->

          <div id='islock' class="editmod">
            <label for="edtIslock" class='editinputname'><?php echo $lang['msg']['disable_comment'] ?></label>
             <input id="edtIslock" name="IsLock" style="" type="text" value="<?php echo (int) $article->IsLock; ?>" class="checkbox"/>
          </div>
          <!-- )IsLock -->

          <!-- Navbar( --><?php if ($ispage) {?>
          <div id='AddNavbar' class="editmod">
          <label for="edtAddNavbar" class='editinputname'><?php echo $lang['msg']['add_to_navbar'] ?></label>
          <input type="text" name="AddNavbar" id="edtAddNavbar" value="<?php echo (int) $zbp->CheckItemToNavbar('page', $article->ID) ?>" class="checkbox" />
          </div><?php }?>
          <!-- )Navbar -->

          <!-- 3号输出接口 -->
          <div id="response3" class="editmod">
<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_Response3'] as $fpname => &$fpsignal) {
    $fpname();
}
?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- divEditRight -->

</form>
</div>

<?php
if ($ispage) {
    echo '<script type="text/javascript">ActiveLeftMenu("aPageMng");</script>';
} elseif ($article->ID == 0) {
    echo '<script type="text/javascript">ActiveLeftMenu("aArticleEdt");</script>';
} else {
    echo '<script type="text/javascript">ActiveLeftMenu("aArticleMng");</script>';
}
echo '<script type="text/javascript">AddHeaderIcon("' . $zbp->host . 'zb_system/image/common/new_32.png' . '");</script>';
?>

<script type="text/javascript">

var tag_loaded=false; //是否已经ajax读取过TAGS
var sContent="",sIntro="";//原内容与摘要
var isSubmit=false;//是否提交保存

var editor_api={
    editor: {
        content:{
            obj:{},
            get:function(){return ""},
            insert:function(){return ""},
            put:function(){return ""},
            focus:function(){return ""}
        },
        intro:{
            obj:{},
            get:function(){return ""},
      insert:function(){return ""},
            put:function(){return ""},
            focus:function(){return ""}
        }
    }
}

//文章内容或摘要变动提示保存
window.onbeforeunload = function(){
  if (!isSubmit && (editor_api.editor.content.get()!=sContent)) return "<?php echo $zbp->lang['error'][71]; ?>";
}

function checkArticleInfo(){
  if(isSubmit)return false;
  document.getElementById("edit").action="<?php echo $ispage ? '../cmd.php?act=PagePst' : '../cmd.php?act=ArticlePst' ?>";

  if(!editor_api.editor.content.get()){
    alert('<?php echo $zbp->lang['error'][70]; ?>');
    return false;
  }
  isSubmit=true;
}

//日期时间控件
$.datepicker.regional['<?php echo $lang['lang'] ?>'] = {
  closeText: '<?php echo $lang['msg']['close'] ?>',
  prevText: '<?php echo $lang['msg']['prev_month'] ?>',
  nextText: '<?php echo $lang['msg']['next_month'] ?>',
  currentText: '<?php echo $lang['msg']['current'] ?>',
  monthNames: ['<?php echo $lang['month']['1'] ?>','<?php echo $lang['month']['2'] ?>','<?php echo $lang['month']['3'] ?>','<?php echo $lang['month']['4'] ?>','<?php echo $lang['month']['5'] ?>','<?php echo $lang['month']['6'] ?>','<?php echo $lang['month']['7'] ?>','<?php echo $lang['month']['8'] ?>','<?php echo $lang['month']['9'] ?>','<?php echo $lang['month']['10'] ?>','<?php echo $lang['month']['11'] ?>','<?php echo $lang['month']['12'] ?>'],
  monthNamesShort: ['<?php echo $lang['month_abbr']['1'] ?>','<?php echo $lang['month_abbr']['2'] ?>','<?php echo $lang['month_abbr']['3'] ?>','<?php echo $lang['month_abbr']['4'] ?>','<?php echo $lang['month_abbr']['5'] ?>','<?php echo $lang['month_abbr']['6'] ?>','<?php echo $lang['month_abbr']['7'] ?>','<?php echo $lang['month_abbr']['8'] ?>','<?php echo $lang['month_abbr']['9'] ?>','<?php echo $lang['month_abbr']['10'] ?>','<?php echo $lang['month_abbr']['11'] ?>','<?php echo $lang['month_abbr']['12'] ?>'],
  dayNames: ['<?php echo $lang['week']['7'] ?>','<?php echo $lang['week']['1'] ?>','<?php echo $lang['week']['2'] ?>','<?php echo $lang['week']['3'] ?>','<?php echo $lang['week']['4'] ?>','<?php echo $lang['week']['5'] ?>','<?php echo $lang['week']['6'] ?>'],
  dayNamesShort: ['<?php echo $lang['week_short']['7'] ?>','<?php echo $lang['week_short']['1'] ?>','<?php echo $lang['week_short']['2'] ?>','<?php echo $lang['week_short']['3'] ?>','<?php echo $lang['week_short']['4'] ?>','<?php echo $lang['week_short']['5'] ?>','<?php echo $lang['week_short']['6'] ?>'],
  dayNamesMin: ['<?php echo $lang['week_abbr']['7'] ?>','<?php echo $lang['week_abbr']['1'] ?>','<?php echo $lang['week_abbr']['2'] ?>','<?php echo $lang['week_abbr']['3'] ?>','<?php echo $lang['week_abbr']['4'] ?>','<?php echo $lang['week_abbr']['5'] ?>','<?php echo $lang['week_abbr']['6'] ?>'],
  weekHeader: '<?php echo $lang['msg']['week_suffix'] ?>',
  dateFormat: 'yy-mm-dd',
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: true,
  yearSuffix: ' <?php echo $lang['msg']['year_suffix'] ?>  '
};
$.datepicker.setDefaults($.datepicker.regional['<?php echo $lang['lang'] ?>']);
$.timepicker.regional['<?php echo $lang['lang'] ?>'] = {
  timeOnlyTitle: '<?php echo $lang['msg']['time'] ?>',
  timeText: '<?php echo $lang['msg']['time'] ?>',
  hourText: '<?php echo $lang['msg']['hour'] ?>',
  minuteText: '<?php echo $lang['msg']['minute'] ?>',
  secondText: '<?php echo $lang['msg']['second'] ?>',
  millisecText: '<?php echo $lang['msg']['millisec'] ?>',
  currentText: '<?php echo $lang['msg']['current'] ?>',
  closeText: '<?php echo $lang['msg']['close'] ?>',
  timeFormat: 'HH:mm:ss',
  ampm: false
};
$.timepicker.setDefaults($.timepicker.regional['<?php echo $lang['lang'] ?>']);
$('#edtDateTime').datetimepicker({
  showSecond: true
  //changeMonth: true,
  //changeYear: true
});


//显示tags
$(document).click(function (event){$('#ulTag').slideUp("fast");});

$('#showtags').click(function (event) {
  event.stopPropagation();
  var offset = $(event.target).offset();
  $('#ulTag').css({ top: offset.top + $(event.target).height()+20+ "px", left: offset.left});
  $('#ulTag').slideDown("fast");
  if(tag_loaded==false){$.getScript('../cmd.php?act=misc&type=showtags');tag_loaded=true;}
  return false;
});
function AddKey(i) {
  var strKey=$('#edtTag').val();
  var strNow=","+i
  if(strKey==""){
    strNow=i
  }
  if(strKey.indexOf(strNow)==-1){
    strKey=strKey+strNow;
  }
  $('#edtTag').val(strKey);
}
function DelKey(i) {
  var strKey=$('#edtTag').val();
  var strNow="{"+i+"}"
  if(strKey.indexOf(strNow)!=-1){
    strKey=strKey.substring(0,strKey.indexOf(strNow))+strKey.substring(strKey.indexOf(strNow)+strNow.length,strKey.length)
  }
  $('#edtTag').val(strKey);
}

//提取摘要
function AutoIntro() {
  var s=editor_api.editor.content.get();
  if(s.indexOf("<hr class=\"more\" />")>-1){
    editor_api.editor.intro.put(s.split("<hr class=\"more\" />")[0]);
  }else{
      if(s.indexOf("<hr class=\"more\"/>")>-1){
      editor_api.editor.intro.put(s.split("<hr class=\"more\"/>")[0]);
    }else{
      i=<?php echo $zbp->option['ZC_ARTICLE_EXCERPT_MAX']; ?>;
      s=s.replace(/<[^>]+>/g,"");
      editor_api.editor.intro.put(s.substring(0,i));
    }
  }
    $("#divIntro").show();
    $('html,body').animate({scrollTop:$('#divIntro').offset().top},'fast');
}

//文章编辑提交区随动JS开始
var oDiv=document.getElementById("divFloat");
var H=0;var Y=oDiv;
while(Y){H+=Y.offsetTop;Y=Y.offsetParent;};
$(window).bind("scroll resize",function(){
  var s=document.body.scrollTop||document.documentElement.scrollTop;
  if(s>H){
    $("#divFloat").addClass("boxfloat");
  }
  else{
    $("#divFloat").removeClass("boxfloat");
  }
});



function editor_init(){
    editor_api.editor.content.obj=$('#editor_content');
    editor_api.editor.intro.obj=$('#editor_intro');
    editor_api.editor.content.get=function(){return this.obj.val()};
    editor_api.editor.content.put=function(str){return this.obj.val(str)};
    editor_api.editor.content.focus=function(){return this.obj.focus()};
    editor_api.editor.intro.get=function(){return this.obj.val()};
    editor_api.editor.intro.put=function(str){return this.obj.val(str)};
    editor_api.editor.intro.focus=function(){return this.obj.focus()};
    sContent=editor_api.editor.content.get();
}

</script>

<?php
foreach ($GLOBALS['hooks']['Filter_Plugin_Edit_End'] as $fpname => &$fpsignal) {
    $fpname();
}
?>

<script type="text/javascript">editor_init();</script>
</div>
<?php
require ZBP_PATH . 'zb_system/admin/admin_footer.php';

RunTime();
?>
