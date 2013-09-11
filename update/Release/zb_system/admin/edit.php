<?php
/**
 * Z-Blog with PHP
 * @author 
 * @copyright (C) RainbowSoft Studio
 * @version 2.0 2013-07-05
 */

require '../function/c_system_base.php';
require '../function/c_system_admin.php';

$zbp->Load();

$action='';
if(GetVars('act','GET')=='PageEdt')$action='PageEdt';
if(GetVars('act','GET')=='ArticleEdt')$action='ArticleEdt';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}

$blogtitle=$lang['msg']['article_edit'];

$ispage=false;
if($action=='PageEdt'){$ispage=true;}

$article=new Post;
$article->AuthorID=$zbp->user->ID;

if(!$zbp->CheckRights('ArticlePub')){
  $article->Status=ZC_POST_STATUS_AUDITING;
}

if(isset($_GET['id'])){
  $article->LoadInfoByID((integer)GetVars('id','GET'));
}

if($ispage){
  $article->Type=1;
}else{
  $article->Type=0;
}

if($article->Intro){
  if(strpos($article->Content, '<!--more-->')!==false){
    $article->Intro='';
    $article->Content=str_replace('<!--more-->', '<hr class="more" />', $article->Content);
  }elseif(strpos($article->Content,$article->Intro)===0){
    $article->Intro='';
  }
}

require $blogpath . 'zb_system/admin/admin_header.php';
?>
<script type="text/javascript" src="../script/jquery.tagto.js"></script>
<script type="text/javascript" src="../script/jquery-ui-timepicker-addon.js"></script>
<?php
foreach ($GLOBALS['Filter_Plugin_Edit_Begin'] as $fpname => &$fpsignal) {$fpname();}
?>
<?php
require $blogpath . 'zb_system/admin/admin_top.php';
?>
<div id="divMain">
<div class="divHeader2"><?php echo $ispage?$lang['msg']['page_edit']:$lang['msg']['article_edit'];?></div>


<div class="SubMenu"></div>
<div id="divMain2" class="edit post_edit">
<form id="edit" name="edit" method="post" action="#">
  <div id="divEditLeft">
    <div id="divEditTitle">
      <input type="hidden" name="ID" id="edtID" value="<?php echo $article->ID;?>" />
      <input type="hidden" name="Type" id="edtType" value="<?php echo $article->Type;?>" />
      <!-- title( -->
		<div id='titleheader' class='editmod'>
			<label for="edtTitle" class="editinputname" ><?php echo $lang['msg']['title']?></label>
			<div><input type="text" name="Title" id="edtTitle"  maxlength="100" onBlur="if(this.value=='') this.value='<?php echo $lang['msg']['unnamed']?>'" onFocus="if(this.value=='<?php echo $lang['msg']['unnamed']?>') this.value=''" value="<?php echo $article->Title;?>" /></div>
      </div>
      <!-- )title --> 
  
    </div>
    

    
    <div id="divContent" style="clear:both;">
		<div id='cheader' class='editmod'><label for="editor_content" class="editinputname" ><?php echo $lang['msg']['content']?></label>&nbsp;&nbsp;<span id="timemsg"></span><span id="msg2"></span><span id="msg"></span><span class="editinputname" ></span><script type="text/javascript" src="../cmd.php?act=misc&amp;type=autosave"></script></div>
		<div id='carea' class='editmod'><textarea id="editor_content" name="Content"><?php echo $article->Content;?></textarea></div>
		<div id="contentready" style="display:none"><img alt="loading" id="statloading1" src="../image/admin/loading.gif"/>Watting...</div>

      <!-- alias( -->
      <div id='alias' class='editmod'><label for="edtAlias" class="editinputname" ><?php echo $lang['msg']['alias']?></label>
        <input type="text" name="Alias" id="edtAlias" maxlength="250" value="<?php echo $article->Alias;?>" />
      </div>
      <!-- )alias --> 

	    <!-- tags( --><?php if(!$ispage){?>
      <div id='tags' class='editmod'><label  for="edtTag"  class='editinputname'><?php echo $lang['msg']['tags']?></label>
        <input type="text"  name="Tag" id="edtTag" value="<?php echo $article->TagsToNameString();?>" />
        (<?php echo $lang['msg']['use_commas_to_separate']?>) <a href="#" id="showtags"><?php echo $lang['msg']['show_common_tags']?></a></div>
      <!-- Tags -->
      <div id="ulTag" style="display:none;">
        <div id="ajaxtags">Watting...</div>
      </div>
      <!-- )tags -->
	  
    <!-- 1号输出接口 -->
       <div id='response' class='editmod'>
<?php
foreach ($GLOBALS['Filter_Plugin_Edit_Response'] as $fpname => &$fpsignal) {$fpname();}
?>
	   </div>

       <div id='insertintro' class='editmod'><span><?php echo $lang['msg']['help_generate_summary']?><a href="" onClick="try{AutoIntro();return false;}catch(e){}">[<?php echo $lang['msg']['generate_summary']?>]</a></span></div>
       <?php }?>
		</div>   
		<div id="divIntro" <?php if(!$article->Intro){echo 'style="display:none;"';}?>>
       <div id='introheader' class='editmod'><label for="editor_intro" class="editinputname" ><?php echo $lang['msg']['intro']?></label></div>
       <textarea id="editor_intro" name="Intro"><?php echo $article->Intro;?></textarea>
       <div id="introready" style="display:none"><img alt="loading" id="statloading2" src="../image/admin/loading.gif"/>Watting...</div>
	   <hr/>
    </div>
    <!-- 2号输出接口 -->
       <div id='response2' class='editmod'>
<?php
foreach ($GLOBALS['Filter_Plugin_Edit_Response2'] as $fpname => &$fpsignal) {$fpname();}
?>
	   </div>

    
  </div>
  <!-- divEditLeft -->
  
  <div id="divEditRight">
    <div id="divEditPost">
      <div id="divBox">
        <div id="divFloat">
          <div id='post' class='editmod'>
            <input class="button" style="width:180px;height:38px;" type="submit" value="提交" id="btnPost" onclick='return checkArticleInfo();' />
          </div>

          <!-- cate --><?php if(!$ispage){ ?>
          <div id='cate' class='editmod'> <label for="cmbTemplate" class="editinputname" ><?php echo $lang['msg']['category']?></label>
            <select style="width:180px;" class="edit" size="1" name="CateID" id="cmbCateID">
<?php
foreach ($zbp->categorysbyorder as $id => $cate) {
  echo '<option ' . ($article->CateID==$cate->ID?'selected="selected"':'') . ' value="'. $cate->ID .'">' . $cate->SymbolName . '</option>';
}
?>
            </select>
          </div>
          <!-- cate --><?php } ?>

          <!-- level -->
          <div id='level' class='editmod'> <label for="cmbPostStatus" class="editinputname" ><?php echo $lang['msg']['status']?></label>
            <select class="edit" style="width:180px;" size="1" name="Status" id="cmbPostStatus" onChange="edtLevel.value=this.options[this.selectedIndex].value">
<?php echo CreateOptoinsOfPostStatus($article->Status);?>
            </select>
          </div>
          <!-- )level --> 
		  
          <!-- template( -->

          <div id='template' class='editmod'> <label for="cmbTemplate" class="editinputname" ><?php echo $lang['msg']['template']?></label>
            <select style="width:180px;" class="edit" size="1" name="Template" id="cmbTemplate" onChange="edtTemplate.value=this.options[this.selectedIndex].value">
<?php echo CreateOptoinsOfTemplate($article->Template);?>
            </select>
          </div>
          <!-- )template --> 
          
          <!-- user( -->
          <div id='user' class='editmod'> <label for="cmbUser" class="editinputname" ><?php echo $lang['msg']['author']?></label>
            <select style="width:180px;" size="1" name="AuthorID" id="cmbUser" onChange="edtAuthorID.value=this.options[this.selectedIndex].value">
				<?php echo CreateOptoinsOfMember($article->AuthorID);?>
            </select>
          </div>
          <!-- )user --> 
          
          <!-- newdatetime( -->
          <div id='newdatetime' class='editmod'> <label for="edtDateTime" class="editinputname" ><?php echo $lang['msg']['date']?></label>
            <input type="text" name="PostTime" id="edtDateTime"  value="<?php echo $article->Time();?>" style="width:171px;"/>
            </div>

          <!-- )newdatetime --> 
          
          <!-- Istop( --><?php if(!$ispage&&$zbp->CheckRights('ArticleAll')){?>
          <div id='istop' class='editmod'>    
            <label for="edtIstop" class="editinputname" ><?php echo $lang['msg']['top']?></label>
            <input id="edtIstop" name="IsTop" style="" type="text" value="<?php echo (int)$article->IsTop;?>" class="checkbox"/>
          </div><?php }?>

          <!-- )Istop --> 

          <!-- IsLock( -->

          <div id='islock' class='editmod'>
            <label for="edtIslock" class='editinputname'><?php echo $lang['msg']['disable_comment']?></label>
             <input id="edtIslock" name="IsLock" style="" type="text" value="<?php echo (int)$article->IsLock;?>" class="checkbox"/>
          </div>
          <!-- )IsLock --> 

          <!-- Navbar( --><?php if($ispage){?>
          <div id='AddNavbar' class='editmod'>
          <label for="edtAddNavbar" class='editinputname'><?php echo $lang['msg']['add_to_navbar']?></label>
          <input type="text" name="AddNavbar" id="edtAddNavbar" value="<?php echo (int)$zbp->CheckItemToNavbar('page',$article->ID)?>" class="checkbox" />
          </div><?php }?>
          <!-- )Navbar --> 
          
          <!-- 3号输出接口 -->
          <div id='response3' class='editmod'>
<?php
foreach ($GLOBALS['Filter_Plugin_Edit_Response3'] as $fpname => &$fpsignal) {$fpname();}
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
if($ispage){
  echo '<script type="text/javascript">ActiveLeftMenu("aPageMng");</script>';
}elseif($article->ID==0){
  echo '<script type="text/javascript">ActiveLeftMenu("aArticleEdt");</script>';
}else{
  echo '<script type="text/javascript">ActiveLeftMenu("aArticleMng");</script>';
}
  echo '<script type="text/javascript">AddHeaderIcon("'. $zbp->host . 'zb_system/image/common/new_32.png' . '");</script>';
?>

<script type="text/javascript">

var tag_loaded=false; //是否已经ajax读取过TAGS
var sContent="",sIntro="";//原内容与摘要
var isSubmit=false;//是否提交保存

var editor_api={
	editor:	{
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
  if (!isSubmit && (editor_api.editor.content.get()!=sContent)) return "<?php echo $zbp->lang['error'][71];?>";
}

function checkArticleInfo(){
  document.getElementById("edit").action="<?php echo $ispage?'../cmd.php?act=PagePst':'../cmd.php?act=ArticlePst'?>";

  if(!editor_api.editor.content.get()){
    alert('<?php echo $zbp->lang['error'][70];?>');
    return false
  }

  isSubmit=1;
}

//日期时间控件
$.datepicker.regional['zh-cn'] = {
  closeText: '完成',
  prevText: '上个月',
  nextText: '下个月',
  currentText: '现在',
  monthNames: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
  monthNamesShort: ['一月','二月','三月','四月','五月','六月','七月','八月','九月','十月','十一月','十二月'],
  dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
  dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
  dayNamesMin: ['日','一','二','三','四','五','六'],
  weekHeader: '周',
  dateFormat: 'yy-mm-dd',
  firstDay: 1,
  isRTL: false,
  showMonthAfterYear: true,
  yearSuffix: ' 年  '
};
$.datepicker.setDefaults($.datepicker.regional['zh-cn']);
$.timepicker.regional['zh-cn'] = {
  timeOnlyTitle: '时间',
  timeText: '时间',
  hourText: '小时',
  minuteText: '分钟',
  secondText: '秒钟',
  millisecText: '毫秒',
  currentText: '现在',
  closeText: '完成',
  timeFormat: 'hh:mm:ss',
  ampm: false
};
$.timepicker.setDefaults($.timepicker.regional['zh-cn']);
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
		editor_api.editor.intro.put(s.substring(0,250));
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

//选择模板
function selectlogtemplate(c){

}
function selectlogtemplatesub(a){
	$("#cmbTemplate").find("option[value='"+a+"']").attr("selected","selected");
}

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
foreach ($GLOBALS['Filter_Plugin_Edit_End'] as $fpname => &$fpsignal) {$fpname();}
?>

<script type="text/javascript">editor_init();</script>
</div>
<?php
require $blogpath . 'zb_system/admin/admin_footer.php';

RunTime();
?>